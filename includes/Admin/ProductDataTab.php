<?php

namespace Woo\Faq\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ProductDataTab {

	public function __construct() {
		add_filter( 'woocommerce_product_data_tabs', [ $this, 'faq_product_edit_tab' ] );
		add_filter( 'woocommerce_product_data_panels', [ $this, 'faq_product_tab_options' ] );
		add_action( 'faq_woocommerce_product_options', [ $this, 'faq_add_field_in_panel' ] );
		add_action( 'woocommerce_process_product_meta', [ $this, 'faq_save_field_data' ] );
	}

	/**
	 * Add FAQ tab in WooCommerce Product Data meta box.
	 *
	 * @since 1.0.0
	 * @param array<string, mixed> $product_data_tab Existing product data tabs.
	 * @return array<string, mixed>
	 */
	public function faq_product_edit_tab( $product_data_tab ) {
		$faq_tab['frequently_asked_questions'] = [
			'label'  => __( 'FAQ', 'product-faq-for-woocommerce' ),
			'target' => 'frequently_asked_questions',
			'class'  => [ 'hide_if_downloadable', 'hide_if_grouped' ],
		];

		$position = 3;
		$tabs     = array_slice( $product_data_tab, 0, $position, true );
		$tabs     = array_merge( $tabs, $faq_tab );
		$tabs     = array_merge( $tabs, array_slice( $product_data_tab, $position, null, true ) );

		return $tabs;
	}

	/**
	 * Render the question and answer panel inside Product Data.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function faq_product_tab_options() {
		?>
		<div id="frequently_asked_questions" class="panel woocommerce_options_panel">
			<div class="faq-product-toolbar" style="display: flex; gap: 10px; align-items: center; justify-content: space-between; flex-wrap: wrap; padding: 12px 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 10px;">
				<div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
					<button type="button" class="faq-add-question button button-primary">
						<span class="dashicons dashicons-plus-alt2" style="font-size: 16px; line-height: 26px; vertical-align: middle;"></span>
						<?php esc_html_e( 'Add New FAQ', 'product-faq-for-woocommerce' ); ?>
					</button>
					<select id="faq-insert-template-select" style="max-width: 220px; height: 30px; font-size: 13px;">
						<option value=""><?php esc_html_e( '💡 Insert FAQ Template...', 'product-faq-for-woocommerce' ); ?></option>
						<option value="shipping"><?php esc_html_e( '🚚 Shipping & Delivery', 'product-faq-for-woocommerce' ); ?></option>
						<option value="returns"><?php esc_html_e( '🔄 Returns & Refunds', 'product-faq-for-woocommerce' ); ?></option>
						<option value="warranty"><?php esc_html_e( '🛡️ Warranty & Guarantee', 'product-faq-for-woocommerce' ); ?></option>
						<option value="sizing"><?php esc_html_e( '📏 Sizing & Fit Guide', 'product-faq-for-woocommerce' ); ?></option>
						<option value="care"><?php esc_html_e( '🧼 Care & Cleaning', 'product-faq-for-woocommerce' ); ?></option>
					</select>
				</div>
				<div>
					<button type="button" class="button button-secondary faq-ai-modal-open-btn" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: #ffffff; border: none; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.25); font-weight: 500;">
						<span class="dashicons dashicons-superhero" style="font-size: 16px; line-height: 26px; vertical-align: middle; color: #fbbf24;"></span>
						<?php esc_html_e( '✨ Generate with AI', 'product-faq-for-woocommerce' ); ?>
					</button>
				</div>
			</div>

			<p style="margin: 0 12px 12px; font-size: 12px; color: #64748b;">
				<?php
				echo wp_kses(
					__( '💡 <strong>Rich Answer Support:</strong> Answers support clickable links (<code>&lt;a href="..."&gt;</code>), bold/italic text (<code>&lt;strong&gt;</code>, <code>&lt;em&gt;</code>), bullet lists (<code>&lt;ul&gt;&lt;li&gt;</code>), and multi-paragraph line breaks.', 'product-faq-for-woocommerce' ),
					[
						'strong' => [],
						'code'   => [],
					]
				);
				?>
			</p>

			<div class="option-group-wrapper">
				<?php do_action( 'faq_woocommerce_product_options' ); ?>
				<?php
				$product_faq = get_option( 'product_faq', 'enable' );
				if ( 'disable' === $product_faq ) :
					?>
					<p class="faq-note"><span><?php esc_html_e( 'Important note:', 'product-faq-for-woocommerce' ); ?> </span><?php esc_html_e( 'Please enable "Product FAQ" from Product FAQ → Settings.', 'product-faq-for-woocommerce' ); ?></p>
				<?php endif; ?>
			</div>

			<!-- AI Modal Container -->
			<div id="faq-ai-modal-backdrop">
				<div id="faq-ai-modal-box">
					<div class="fbs-ai-modal-header">
						<h3 class="fbs-ai-modal-title">
							<span>✨</span> <?php esc_html_e( 'AI Product FAQ Generator', 'product-faq-for-woocommerce' ); ?>
						</h3>
						<button type="button" id="faq-ai-modal-close" class="fbs-ai-modal-close-btn">&times;</button>
					</div>
					<div class="fbs-ai-modal-body">
						<p class="fbs-ai-modal-intro">
							<?php esc_html_e( 'Generates high-converting, product-specific FAQs using this product’s title, description, price, stock status, weight/dimensions, categories, and attributes.', 'product-faq-for-woocommerce' ); ?>
						</p>

						<div class="fbs-ai-modal-grid">
							<div class="fbs-ai-field-group">
								<label class="fbs-ai-field-label"><?php esc_html_e( 'Tone & Style', 'product-faq-for-woocommerce' ); ?></label>
								<select id="faq-ai-tone-select" class="fbs-ai-select">
									<option value="sales"><?php esc_html_e( 'Persuasive & Sales-Focused', 'product-faq-for-woocommerce' ); ?></option>
									<option value="friendly"><?php esc_html_e( 'Friendly & Casual', 'product-faq-for-woocommerce' ); ?></option>
									<option value="concise"><?php esc_html_e( 'Concise & Direct', 'product-faq-for-woocommerce' ); ?></option>
									<option value="technical"><?php esc_html_e( 'Technical & Professional', 'product-faq-for-woocommerce' ); ?></option>
								</select>
							</div>
							<div class="fbs-ai-field-group is-count">
								<label class="fbs-ai-field-label"><?php esc_html_e( 'Number of FAQs', 'product-faq-for-woocommerce' ); ?></label>
								<select id="faq-ai-count-select" class="fbs-ai-select">
									<option value="3"><?php esc_html_e( '3 FAQs (Free Max)', 'product-faq-for-woocommerce' ); ?></option>
									<option value="2"><?php esc_html_e( '2 FAQs', 'product-faq-for-woocommerce' ); ?></option>
									<option value="1"><?php esc_html_e( '1 FAQ', 'product-faq-for-woocommerce' ); ?></option>
								</select>
							</div>
						</div>

						<div class="fbs-ai-generate-action">
							<button type="button" class="button button-primary fbs-ai-generate-btn" id="faq-ai-generate-submit-btn">
								<span class="dashicons dashicons-update faq-ai-spinner" style="display: none; font-size: 16px; vertical-align: middle; animation: spin 1s infinite linear;"></span>
								<span class="faq-ai-btn-text"><?php esc_html_e( '⚡ Generate FAQs with AI', 'product-faq-for-woocommerce' ); ?></span>
							</button>
						</div>

						<div id="faq-ai-results-wrapper" class="fbs-ai-results-box" style="display: none;">
							<div id="faq-ai-notice" class="fbs-ai-notice-banner" style="display: none;"></div>
							<h4 style="margin: 0 0 12px 0; font-size: 13px; color: #1e293b;"><?php esc_html_e( 'Review & Select FAQs to Insert:', 'product-faq-for-woocommerce' ); ?></h4>
							<div id="faq-ai-items-list" style="display: flex; flex-direction: column; gap: 10px;"></div>
						</div>
					</div>
					<div class="fbs-ai-modal-footer">
						<span class="fbs-ai-footer-brand">
							<span class="dashicons dashicons-superhero" style="font-size: 15px; width: 15px; height: 15px; color: #818cf8;"></span>
							<?php esc_html_e( 'Powered by Product FAQ AI Assistant', 'product-faq-for-woocommerce' ); ?>
						</span>
						<div style="display: flex; gap: 8px;">
							<button type="button" class="fbs-ai-btn-cancel" id="faq-ai-modal-cancel-btn"><?php esc_html_e( 'Cancel', 'product-faq-for-woocommerce' ); ?></button>
							<button type="button" class="fbs-ai-btn-insert" id="faq-ai-insert-all-btn" style="display: none;">
								<span class="dashicons dashicons-yes" style="font-size: 16px; width: 16px; height: 16px; line-height: 16px;"></span>
								<?php esc_html_e( 'Insert into Product', 'product-faq-for-woocommerce' ); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Question and Answer inputs for the current product.
	 *
	 * @return void
	 */
	public function faq_add_field_in_panel() {
		global $post;
		$value = get_post_meta( $post->ID, 'faq', true );

		if ( ! is_array( $value ) || ! isset( $value['question'] ) || ! isset( $value['answer'] ) ) {
			$value = [
				'question' => [ '' ],
				'answer'   => [ '' ],
			];
		}

		$max_count = max( count( $value['question'] ), count( $value['answer'] ), 1 );
		for ( $i = 0; $i < $max_count; $i++ ) {
			if ( ! isset( $value['question'][ $i ] ) ) {
				$value['question'][ $i ] = '';
			}
			if ( ! isset( $value['answer'][ $i ] ) ) {
				$value['answer'][ $i ] = '';
			}
		}

		$args = [];

		foreach ( $value['question'] as $key => $val ) {
			echo '<div class="options_group faq-group" data-index="' . esc_attr( $key ) . '">';

			if ( $key > 0 ) {
				echo '<button type="button" class="faq-remove-question" style="float:right; margin-top:5px; background:#fff; color:#b32d2e; border:1px solid #b32d2e; border-radius:50%; width:24px; height:24px; padding:0; cursor:pointer;">
					<span class="dashicons dashicons-no-alt" style="font-size:12px; line-height:22px;"></span>
				</button>';
			}

			woocommerce_wp_text_input(
				[
					'id'          => 'faq_' . $key,
					'name'        => 'faq[question][' . $key . ']',
					'label'       => 'Question ' . ( $key + 1 ),
					'class'       => 'faq_input faq-question-box',
					'type'        => 'text',
					'desc_tip'    => true,
					'data_type'   => 'text',
					'value'       => $value['question'][ $key ] ?? '',
					'placeholder' => 'Enter your question here...',
					'style'       => 'width: 98%;',
				]
			);

			woocommerce_wp_textarea_input(
				[
					'id'          => 'faq_ans_' . $key,
					'name'        => 'faq[answer][' . $key . ']',
					'label'       => 'Answer ' . ( $key + 1 ),
					'class'       => 'faq_input faq-answer-box',
					'desc_tip'    => true,
					'value'       => $value['answer'][ $key ] ?? '',
					'placeholder' => 'Enter your answer here (HTML links, bold/italic, lists & line breaks supported)...',
					'rows'        => 3,
					'style'       => 'width: 100%;',
				]
			);

			echo '</div>';
		}

		$args = apply_filters( 'faq_field_args', $args );

		foreach ( $args as $arg ) {
			woocommerce_wp_text_input( $arg );
		}
	}

	/**
	 * Save Product FAQ post meta with rich HTML answer support (wp_kses_post).
	 *
	 * @param int $post_id Product post ID.
	 * @return void
	 */
	public function faq_save_field_data( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-post_' . $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['faq'] ) || ! is_array( $_POST['faq'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- wp_unslash() is used here; each question/answer is sanitized individually below.
		$_data         = wp_unslash( $_POST['faq'] );
		$sanitize_data = [];

		foreach ( $_data as $key => $data ) {
			if ( ! is_array( $data ) || ! in_array( $key, [ 'question', 'answer' ], true ) ) {
				continue;
			}

			$sanitize_data[ $key ] = [];
			foreach ( $data as $index => $item ) {
				$raw_item = trim( (string) $item );
				if ( '' !== $raw_item ) {
					$sanitize_data[ $key ][ $index ] = ( 'answer' === $key )
						? wp_kses_post( $raw_item )
						: sanitize_text_field( $raw_item );
				}
			}
		}

		if ( ! empty( $sanitize_data['question'] ) || ! empty( $sanitize_data['answer'] ) ) {
			update_post_meta( $post_id, 'faq', $sanitize_data );
		} else {
			delete_post_meta( $post_id, 'faq' );
		}
	}
}
