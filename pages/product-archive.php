<?php
defined( 'ABSPATH' ) || die( 'Nice Try!' );

$is_pro_plugin_active = in_array( 'woo-product-faq-pro/product-faq-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) ), true );
$is_license_active    = false;
if ( $is_pro_plugin_active && function_exists( 'faq_pro_is_license_active' ) ) {
	$is_license_active = faq_pro_is_license_active();
}

if ( isset( $_POST['save_woo_afaq'] ) && check_admin_referer( 'save_woo_afaq_data', 'woo_afaq_nonce' ) && current_user_can( 'manage_options' ) ) {
	if ( $is_pro_plugin_active && ! $is_license_active ) {
		echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Please activate your license to use Pro features.', 'product-faq-for-woocommerce' ) . '</p></div>';
	} else {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- wp_unslash() is applied here; every group/question/answer is sanitized below.
		$raw_faq_groups = isset( $_POST['faq_groups'] ) && is_array( $_POST['faq_groups'] ) ? wp_unslash( $_POST['faq_groups'] ) : [];
		$faq_groups     = [];

		foreach ( $raw_faq_groups as $group ) {
			if ( empty( $group['archive_type'] ) || empty( $group['archive_terms'] ) ) {
				continue;
			}

			$archive_type  = sanitize_text_field( (string) $group['archive_type'] );
			$archive_terms = array_values( array_filter( array_map( 'intval', (array) $group['archive_terms'] ) ) );

			$faqs = [];
			if ( ! empty( $group['faqs'] ) && is_array( $group['faqs'] ) ) {
				foreach ( $group['faqs'] as $faq ) {
					$question = sanitize_text_field( (string) ( $faq['question'] ?? '' ) );
					$answer   = wp_kses_post( (string) ( $faq['answer'] ?? '' ) );

					if ( '' !== $question && '' !== $answer ) {
						$faqs[] = compact( 'question', 'answer' );
					}
				}
			}

			$faq_groups[] = [
				'archive_type'  => $archive_type,
				'archive_terms' => $archive_terms,
				'faqs'          => $faqs,
			];
		}

		update_option( 'woo_afaq_global_groups', $faq_groups );

		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'FAQ groups saved successfully!', 'product-faq-for-woocommerce' ) . '</p></div>';
	}
}

$bulk_upgrade_url = woo_faq_utm_url( 'plugins/', 'pro-upgrade', 'bulk-faq-banner', 'product-faq' );
?>

<div class="wrap">
	<div class="fbs-product-archive-faq">
		<div class="archive-header">
			<h1>🚀 <?php esc_html_e( 'Bulk FAQ Management', 'product-faq-for-woocommerce' ); ?></h1>
			<p class="archive-description"><?php esc_html_e( 'Create FAQ groups that automatically apply to products based on categories, tags, or custom taxonomies. Answers support rich HTML links, bold/italic text, lists, and line breaks.', 'product-faq-for-woocommerce' ); ?></p>
		</div>

		<?php if ( ! $is_pro_plugin_active ) : ?>
			<div class="pro-upgrade-banner">
				<div class="pro-banner-content">
					<div class="pro-banner-icon">⭐</div>
					<div class="pro-banner-text">
						<h3><?php esc_html_e( 'Unlock Unlimited Bulk FAQ Groups & Category AI Generation', 'product-faq-for-woocommerce' ); ?></h3>
						<p><?php esc_html_e( 'Upgrade to Product FAQ Pro for unlimited category/tag FAQ groups, 1-click Bulk AI FAQ generation, Shop/Category Archive FAQs, and priority support.', 'product-faq-for-woocommerce' ); ?></p>
					</div>
					<div class="pro-banner-action">
						<a href="<?php echo esc_url( $bulk_upgrade_url ); ?>" target="_blank" rel="noopener noreferrer" class="pro-banner-button">
							<?php esc_html_e( 'Explore Pro ↗', 'product-faq-for-woocommerce' ); ?>
						</a>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<div class="archive-form-container">
			<form method="post" action="" <?php echo ( $is_pro_plugin_active && ! $is_license_active ) ? 'style="opacity: 0.5; pointer-events: none;"' : ''; ?>>
				<?php wp_nonce_field( 'save_woo_afaq_data', 'woo_afaq_nonce' ); ?>
				<div id="faq-groups-container"></div>
				<div class="form-actions">
					<button type="button" class="button button-secondary fbs-add-faq-group" <?php echo ( $is_pro_plugin_active && ! $is_license_active ) ? 'disabled' : ''; ?>>
						<span class="dashicons dashicons-plus-alt"></span>
						<?php esc_html_e( 'Add FAQ Group', 'product-faq-for-woocommerce' ); ?>
					</button>
					<input type="submit" name="save_woo_afaq" class="button button-primary" value="<?php esc_attr_e( 'Save All FAQs', 'product-faq-for-woocommerce' ); ?>" <?php echo ( $is_pro_plugin_active && ! $is_license_active ) ? 'disabled' : ''; ?>>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Templates -->
<script type="text/html" id="fbs-faq-group-template">
	<div class="fbs-faq-archive-group">
		<div class="fbs-group-header">
			<h2 class="fbs-group-title">
				<span class="dashicons dashicons-category"></span> <?php esc_html_e( 'FAQ Group', 'product-faq-for-woocommerce' ); ?>
			</h2>
			<button type="button" class="fbs-archive-remove-faq-group" title="<?php esc_attr_e( 'Remove Group', 'product-faq-for-woocommerce' ); ?>">
				<span class="dashicons dashicons-no-alt"></span> <?php esc_html_e( 'Remove Group', 'product-faq-for-woocommerce' ); ?>
			</button>
		</div>

		<div class="fbs-group-body">
			<div class="fbs-group-settings">
				<div class="fbs-settings-field">
					<label class="fbs-settings-label"><?php esc_html_e( 'Archive Type', 'product-faq-for-woocommerce' ); ?></label>
					<div class="fbs-settings-input-wrap">
						<select class="archive-type" name="faq_groups[_INDEX_][archive_type]">
							<option value=""><?php esc_html_e( 'Select Archive Type', 'product-faq-for-woocommerce' ); ?></option>
							<?php
							$taxonomies = apply_filters(
								'woo_faq_archive_taxonomies',
								[
									'product_cat' => __( 'Category', 'product-faq-for-woocommerce' ),
									'product_tag' => __( 'Tag', 'product-faq-for-woocommerce' ),
								]
							);
							foreach ( $taxonomies as $slug => $label ) {
								echo '<option value="' . esc_attr( $slug ) . '">' . esc_html( $label ) . '</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="fbs-settings-field archive-term-row" style="display:none;">
					<label class="fbs-settings-label"><?php esc_html_e( 'Term', 'product-faq-for-woocommerce' ); ?></label>
					<div class="fbs-settings-input-wrap">
						<div class="search-term-wrapper">
							<input type="text" class="archive-term regular-text" name="" placeholder="<?php esc_attr_e( 'Search and select terms...', 'product-faq-for-woocommerce' ); ?>" />
							<div class="term-suggestions"></div>
						</div>
						<div class="selected-terms"></div>
					</div>
				</div>
			</div>

			<div class="fbs-archive-faq-items"></div>

			<div class="group-actions">
				<button type="button" class="button fsb-archive-add-faq-item">
					<span class="dashicons dashicons-plus-alt"></span> <?php esc_html_e( 'Add New FAQ', 'product-faq-for-woocommerce' ); ?>
				</button>
				<?php if ( $is_pro_plugin_active && $is_license_active ) : ?>
					<button type="button" class="button button-secondary fbs-bulk-ai-generate-btn" style="background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: #ffffff; border: none; font-weight: 600; padding: 0 14px; height: 32px; display: inline-flex; align-items: center; gap: 6px; border-radius: 4px; margin-left: 10px; cursor: pointer; vertical-align: middle;">
						<span>✨</span> <?php esc_html_e( 'Generate with AI', 'product-faq-for-woocommerce' ); ?>
					</button>
				<?php endif; ?>
			</div>
		</div>
	</div>
</script>

<script type="text/html" id="fbs-archive-faq-item-template">
	<div class="fbs-archive-faq-item">
		<div class="fbs-faq-item-header">
			<span class="fbs-faq-item-title">
				<span class="dashicons dashicons-editor-help"></span> <?php esc_html_e( 'FAQ Item', 'product-faq-for-woocommerce' ); ?>
			</span>
			<button type="button" class="fbs-archive-remove-faq-item" title="<?php esc_attr_e( 'Remove FAQ', 'product-faq-for-woocommerce' ); ?>">
				<span class="dashicons dashicons-no-alt"></span>
			</button>
		</div>
		<div class="fbs-faq-item-body">
			<div class="fbs-field-group">
				<label class="fbs-field-label"><?php esc_html_e( 'Question', 'product-faq-for-woocommerce' ); ?></label>
				<input type="text" name="faq_groups[_GROUP_INDEX_][faqs][_FAQ_INDEX_][question]" class="regular-text" placeholder="<?php esc_attr_e( 'Enter your question here...', 'product-faq-for-woocommerce' ); ?>" />
			</div>
			<div class="fbs-field-group">
				<label class="fbs-field-label"><?php esc_html_e( 'Answer', 'product-faq-for-woocommerce' ); ?></label>
				<textarea name="faq_groups[_GROUP_INDEX_][faqs][_FAQ_INDEX_][answer]" rows="3" class="large-text" placeholder="<?php esc_attr_e( 'Enter your answer here (HTML links, bold/italic, lists & line breaks supported)...', 'product-faq-for-woocommerce' ); ?>"></textarea>
			</div>
		</div>
	</div>
</script>

<?php
$saved_data = get_option( 'woo_afaq_global_groups', [] );

if ( ! empty( $saved_data ) && is_array( $saved_data ) ) {
	foreach ( $saved_data as $g_index => &$group ) {
		$archive_type = $group['archive_type'] ?? '';
		$term_names   = [];

		if ( ! empty( $group['archive_terms'] ) && taxonomy_exists( $archive_type ) ) {
			foreach ( $group['archive_terms'] as $term_id ) {
				$term = get_term( $term_id, $archive_type );
				if ( $term && ! is_wp_error( $term ) ) {
					$term_names[ $term_id ] = $term->name;
				}
			}
		}

		$group['term_names'] = $term_names;
	}
	unset( $group );
	?>

	<script>
	jQuery(document).ready(function ($) {
		const groupTemplate = $('#fbs-faq-group-template').html();
		const faqTemplate = $('#fbs-archive-faq-item-template').html();
		const savedGroups = <?php echo wp_json_encode( $saved_data ); ?>;

		$.each(savedGroups, function (gIndex, group) {
			let groupHtml = groupTemplate.replace(/_INDEX_/g, gIndex);
			const $group = $(groupHtml);

			$group.find('select.archive-type').val(group.archive_type);
			$group.find('.archive-term-row').show();

			const selectedContainer = $group.find('.selected-terms');
			const termNames = group.term_names || {};
			if (Array.isArray(group.archive_terms)) {
				group.archive_terms.forEach(function (termId) {
					const termName = $('<div>').text(termNames[termId] || 'Term #' + termId).html();
					const termHtml = `<span class="term-pill" data-id="${termId}">
						${termName}
						<a href="#" class="remove-term">&times;</a>
						<input type="hidden" name="faq_groups[${gIndex}][archive_terms][]" value="${termId}">
					</span>`;
					selectedContainer.append(termHtml);
				});
			}

			const faqs = group.faqs || [];
			const $faqContainer = $group.find('.fbs-archive-faq-items');
			$.each(faqs, function (faqIndex, faq) {
				let faqHtml = faqTemplate
					.replace(/_GROUP_INDEX_/g, gIndex)
					.replace(/_FAQ_INDEX_/g, faqIndex);

				const $faq = $(faqHtml);
				$faq.find('input[name$="[question]"]').val(faq.question);
				$faq.find('textarea[name$="[answer]"]').val(faq.answer);
				$faqContainer.append($faq);
			});

			$('#faq-groups-container').append($group);
		});

		$('form').on('submit', function(e) {
			var hasError = false;
			$('.fbs-faq-archive-group').each(function() {
				var $group = $(this);
				var archiveType = $group.find('select.archive-type').val();
				var selectedTerms = $group.find('.selected-terms input[type="hidden"]');
				if (archiveType && selectedTerms.length === 0) {
					hasError = true;
					if ($group.find('.fbs-term-error').length === 0) {
						$group.find('.selected-terms').after('<div class="fbs-term-error">Please select at least one term.</div>');
					}
				} else {
					$group.find('.fbs-term-error').remove();
				}
			});
			if (hasError) {
				e.preventDefault();
				$('html, body').animate({
					scrollTop: $('.fbs-term-error').first().offset().top - 100
				}, 300);
			}
		});
	});
	</script>
<?php } ?>
