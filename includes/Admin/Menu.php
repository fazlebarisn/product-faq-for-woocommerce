<?php

namespace Woo\Faq\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Menu {

	public function __construct() {
		// Add plugin menu pages in dashboard.
		add_action( 'admin_menu', [ $this, 'adminMenu' ] );

		// Add plugin action links & row meta links.
		add_filter( 'plugin_action_links_' . WOO_FAQ_BASENAME, [ $this, 'settingsLink' ] );
		add_filter( 'plugin_row_meta', [ $this, 'pluginRowMeta' ], 10, 2 );

		// Register custom settings.
		add_action( 'admin_init', [ $this, 'wooFaqSettings' ] );

		// Enqueue settings page assets.
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAssets' ] );
	}

	/**
	 * Enqueue assets for the settings pages.
	 *
	 * @since 1.1.9
	 * @param string $hook Current admin screen hook.
	 * @return void
	 */
	public function enqueueAssets( $hook = '' ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		if ( ! in_array( $current_page, [ 'woo_sfaq', 'woo_afaq', 'woo_pfaq', 'woo_author_faq' ], true ) ) {
			return;
		}

		wp_enqueue_style( 'woo-faq-admin-settings', WOO_FAQ_URL . '/assets/css/admin-settings.css', [], WOO_FAQ_VERSION );
		wp_enqueue_script( 'woo-faq-admin-settings', WOO_FAQ_URL . '/assets/js/admin-settings.js', [ 'jquery' ], WOO_FAQ_VERSION, true );
		wp_localize_script(
			'woo-faq-admin-settings',
			'faqAjax',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'faq_nonce' ),
			]
		);
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
	}

	/**
	 * Add Settings & Upgrade to Pro links on the Plugins screen.
	 *
	 * @param array<int, string> $links Existing action links.
	 * @return array<int, string>
	 */
	public function settingsLink( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=woo_sfaq' ) ) . '">' . esc_html__( 'Settings', 'product-faq-for-woocommerce' ) . '</a>';

		if ( ! in_array( 'woo-product-faq-pro/product-faq-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) ), true ) ) {
			$pro_url  = woo_faq_utm_url( 'plugins/', 'pro-upgrade', 'plugins-action-link', 'product-faq' );
			$pro_link = '<a href="' . esc_url( $pro_url ) . '" target="_blank" rel="noopener noreferrer" style="color: #667eea; font-weight: 600;">' . esc_html__( '🚀 Upgrade to Pro', 'product-faq-for-woocommerce' ) . '</a>';
			array_push( $links, $settings_link, $pro_link );
		} else {
			array_push( $links, $settings_link );
		}

		return $links;
	}

	/**
	 * Add helpful Docs/Support and WooCommerce Audit links to plugin row meta.
	 *
	 * @param array<int, string> $links Existing row meta links.
	 * @param string             $file  Plugin basename.
	 * @return array<int, string>
	 */
	public function pluginRowMeta( $links, $file ) {
		if ( WOO_FAQ_BASENAME !== $file ) {
			return $links;
		}

		$docs_url  = woo_faq_utm_url( 'hire-us/', 'plugin-support', 'plugin-row-meta' );
		$audit_url = woo_faq_utm_url( 'speed-security/', 'agency-services', 'plugin-row-meta' );

		$links[] = '<a href="' . esc_url( $docs_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Support & Custom Dev', 'product-faq-for-woocommerce' ) . '</a>';
		$links[] = '<a href="' . esc_url( $audit_url ) . '" target="_blank" rel="noopener noreferrer" style="color:#059669;font-weight:600;">' . esc_html__( '⚡ Free WooCommerce Speed Audit', 'product-faq-for-woocommerce' ) . '</a>';

		return $links;
	}

	/**
	 * Add menu pages in WordPress dashboard.
	 *
	 * @return void
	 */
	public function adminMenu() {
		add_menu_page( __( 'Product FAQ', 'product-faq-for-woocommerce' ), __( 'Product FAQ', 'product-faq-for-woocommerce' ), 'manage_options', 'woo_sfaq', [ $this, 'adminPage' ], 'dashicons-editor-help', 56 );
		add_submenu_page( 'woo_sfaq', __( 'Bulk FAQ Management', 'product-faq-for-woocommerce' ), __( 'Bulk FAQ', 'product-faq-for-woocommerce' ), 'manage_options', 'woo_afaq', [ $this, 'productAtchivePage' ] );
		add_submenu_page( 'woo_sfaq', __( 'Browse Our Plugins', 'product-faq-for-woocommerce' ), __( 'Our Plugins', 'product-faq-for-woocommerce' ), 'manage_options', 'woo_pfaq', [ $this, 'ourPluginsPage' ] );
		add_submenu_page( 'woo_sfaq', __( 'Meet Fazle Bari & FBS Fusion', 'product-faq-for-woocommerce' ), __( 'About The Author', 'product-faq-for-woocommerce' ), 'manage_options', 'woo_author_faq', [ $this, 'pluginAuthorPage' ] );
	}

	/**
	 * Include settings dashboard page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function adminPage() {
		$menu_instance = $this;
		require_once WOO_FAQ_PATH . '/pages/dashboard.php';
	}

	public function productAtchivePage() {
		require_once WOO_FAQ_PATH . '/pages/product-archive.php';
	}

	public function ourPluginsPage() {
		require_once WOO_FAQ_PATH . '/pages/our-plugins.php';
	}

	public function pluginAuthorPage() {
		require_once WOO_FAQ_PATH . '/pages/plugin-author.php';
	}

	/**
	 * Register custom settings for plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function wooFaqSettings() {
		register_setting( 'woofaq-settings-group', 'product_faq', [ $this, 'sanitizeTextField' ] );
		register_setting( 'woofaq-settings-group', 'product_faq_position', [ $this, 'sanitizeTextField' ] );
		register_setting( 'woofaq-settings-group', 'faq_heading', [ $this, 'sanitizeTextField' ] );
		register_setting( 'woofaq-settings-group', 'woo_faq_show_count_badge', [ $this, 'sanitizeTextField' ] );
		register_setting( 'woofaq-settings-group', 'woo_faq_open_first', [ $this, 'sanitizeTextField' ] );
		register_setting( 'woofaq-settings-group', 'faq_heading_color', [ $this, 'sanitizeTextField' ] );
		register_setting( 'woofaq-settings-group', 'faq_question_color', [ $this, 'sanitizeTextField' ] );
		register_setting( 'woofaq-settings-group', 'faq_ans_color', [ $this, 'sanitizeTextField' ] );
		register_setting( 'woofaq-settings-group', 'faq_heading_font_size', [ $this, 'sanitizeTextField' ] );
		register_setting( 'woofaq-settings-group', 'faq_question_font_size', [ $this, 'sanitizeTextField' ] );
		register_setting( 'woofaq-settings-group', 'faq_ans_font_size', [ $this, 'sanitizeTextField' ] );
		register_setting( 'woofaq-settings-group', 'woo_faq_enable_schema', [ $this, 'sanitizeTextField' ] );
		register_setting( 'woofaq-settings-group', 'woo_faq_ai_tone', [ $this, 'sanitizeTextField' ] );
	}

	/**
	 * Callback function for settings section.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function productFaqOptions() {
		echo esc_html__( 'From here you can change all settings.', 'product-faq-for-woocommerce' );
	}

	/**
	 * Callback function for style settings section.
	 *
	 * @return void
	 */
	public function productFaqStyle() {
		echo esc_html__( 'From this section you can change all style for your FAQ HTML.', 'product-faq-for-woocommerce' );
	}

	/**
	 * Sanitize setting text input.
	 *
	 * @since 1.0.0
	 * @param mixed $input Raw input value.
	 * @return string
	 */
	public function sanitizeTextField( $input ) {
		return sanitize_text_field( wp_unslash( (string) $input ) );
	}

	/**
	 * Get option data from database.
	 *
	 * @since 1.0.0
	 * @param string $key Option key.
	 * @return mixed
	 */
	public function faq_option_data( $key ) {
		return get_option( $key );
	}

	/**
	 * Display enable/disable input field.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ProductFaq() {
		$product_faq = $this->faq_option_data( 'product_faq' );
		$product_faq = ( false !== $product_faq && '' !== $product_faq ) ? $product_faq : 'enable';
		?>
		<select name="product_faq" id="product_faq">
			<option value="disable" <?php selected( $product_faq, 'disable' ); ?>><?php esc_html_e( 'Disable', 'product-faq-for-woocommerce' ); ?></option>
			<option value="enable" <?php selected( $product_faq, 'enable' ); ?>><?php esc_html_e( 'Enable', 'product-faq-for-woocommerce' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Display FAQ position field.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function faqPosition() {
		$product_faq_position = get_option( 'product_faq_position' );
		$faq_position         = ( false !== $product_faq_position && '' !== $product_faq_position ) ? $product_faq_position : 'after_single_product';
		?>
		<select name="product_faq_position" id="product_faq_position">
			<option value="after_cart_button" <?php selected( $faq_position, 'after_cart_button' ); ?>><?php esc_html_e( 'After Cart Button', 'product-faq-for-woocommerce' ); ?></option>
			<option value="after_meta" <?php selected( $faq_position, 'after_meta' ); ?>><?php esc_html_e( 'After Meta', 'product-faq-for-woocommerce' ); ?></option>
			<option value="after_summary" <?php selected( $faq_position, 'after_summary' ); ?>><?php esc_html_e( 'After Summary', 'product-faq-for-woocommerce' ); ?></option>
			<option value="after_single_product" <?php selected( $faq_position, 'after_single_product' ); ?>><?php esc_html_e( 'After Single Product', 'product-faq-for-woocommerce' ); ?></option>
			<option value="product_tab" <?php selected( $faq_position, 'product_tab' ); ?>><?php esc_html_e( 'Product Tab', 'product-faq-for-woocommerce' ); ?></option>
			<option value="shortcode" <?php selected( $faq_position, 'shortcode' ); ?>><?php esc_html_e( 'Shortcode / Gutenberg Block Only ([woo_product_faq])', 'product-faq-for-woocommerce' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Tip: Insert the "Product FAQ Accordion" Gutenberg block (type /faq in the block editor) or use [woo_product_faq], [woo_product_faq id="123"], or [woo_product_faq category="slug"].', 'product-faq-for-woocommerce' ); ?></p>
		<?php
	}

	/**
	 * Display heading input field.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function Heading() {
		$faq_heading = get_option( 'faq_heading' );
		?>
		<input type="text" name="faq_heading" value="<?php echo esc_attr( $faq_heading ); ?>" placeholder="<?php esc_attr_e( 'Frequently Asked Questions', 'product-faq-for-woocommerce' ); ?>" />
		<?php
	}

	/**
	 * Display live FAQ count badge toggle on Product Tab (`FAQs (5)`).
	 *
	 * @since 1.6.0
	 * @return void
	 */
	public function ShowCountBadge() {
		$show_badge = get_option( 'woo_faq_show_count_badge', 'enable' );
		?>
		<select name="woo_faq_show_count_badge" id="woo_faq_show_count_badge">
			<option value="enable" <?php selected( $show_badge, 'enable' ); ?>><?php esc_html_e( 'Enable — Show live FAQ count badge (Product Tab, Section Heading & Admin Tab)', 'product-faq-for-woocommerce' ); ?></option>
			<option value="disable" <?php selected( $show_badge, 'disable' ); ?>><?php esc_html_e( 'Disable', 'product-faq-for-woocommerce' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Displays a live question counter pill badge on the WooCommerce Product Tab, FAQ section heading, and Admin Product Data tab.', 'product-faq-for-woocommerce' ); ?></p>
		<?php
	}

	/**
	 * Display toggle to automatically expand the first FAQ item on load.
	 *
	 * @since 1.6.0
	 * @return void
	 */
	public function OpenFirstFaq() {
		$open_first = get_option( 'woo_faq_open_first', 'enable' );
		?>
		<select name="woo_faq_open_first" id="woo_faq_open_first">
			<option value="enable" <?php selected( $open_first, 'enable' ); ?>><?php esc_html_e( 'Enable — Expand first FAQ automatically (Recommended for Conversions)', 'product-faq-for-woocommerce' ); ?></option>
			<option value="disable" <?php selected( $open_first, 'disable' ); ?>><?php esc_html_e( 'Disable — Keep all FAQs collapsed initially', 'product-faq-for-woocommerce' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Reveals your top objection-handling answer immediately on page load without requiring an extra click.', 'product-faq-for-woocommerce' ); ?></p>
		<?php
	}

	/**
	 * Display heading color input field.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function HeadingColor() {
		$faq_heading_color = get_option( 'faq_heading_color' );
		?>
		<input type="text" class="color-field" name="faq_heading_color" value="<?php echo esc_attr( $faq_heading_color ); ?>" />
		<?php
	}

	/**
	 * Display question color input field.
	 *
	 * @since 1.1.4
	 * @return void
	 */
	public function QuestionColor() {
		$faq_question_color = get_option( 'faq_question_color' );
		?>
		<input type="text" class="color-field" name="faq_question_color" value="<?php echo esc_attr( $faq_question_color ); ?>" />
		<?php
	}

	/**
	 * Display answer color input field.
	 *
	 * @since 1.1.4
	 * @return void
	 */
	public function AnswerColor() {
		$faq_ans_color = get_option( 'faq_ans_color' );
		?>
		<input type="text" class="color-field" name="faq_ans_color" value="<?php echo esc_attr( $faq_ans_color ); ?>" />
		<?php
	}

	/**
	 * Display heading font size input field.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function HeadingFontSize() {
		$faq_heading_font_size = get_option( 'faq_heading_font_size' );
		?>
		<input type="text" name="faq_heading_font_size" value="<?php echo esc_attr( $faq_heading_font_size ); ?>" placeholder="Example: 28px" />
		<?php
	}

	/**
	 * Display question font size input field.
	 *
	 * @since 1.1.4
	 * @return void
	 */
	public function QuestionFontSize() {
		$faq_question_font_size = get_option( 'faq_question_font_size' );
		?>
		<input type="text" name="faq_question_font_size" value="<?php echo esc_attr( $faq_question_font_size ); ?>" placeholder="Example: 18px" />
		<?php
	}

	/**
	 * Display answer font size input field.
	 *
	 * @since 1.1.4
	 * @return void
	 */
	public function AnswerFontSize() {
		$faq_ans_font_size = get_option( 'faq_ans_font_size' );
		?>
		<input type="text" name="faq_ans_font_size" value="<?php echo esc_attr( $faq_ans_font_size ); ?>" placeholder="Example: 16px" />
		<?php
	}

	/**
	 * Display Google Schema JSON-LD toggle.
	 *
	 * @return void
	 */
	public function EnableSchema() {
		$enable_schema = get_option( 'woo_faq_enable_schema', 'enable' );
		?>
		<select name="woo_faq_enable_schema">
			<option value="enable" <?php selected( $enable_schema, 'enable' ); ?>><?php esc_html_e( 'Enable (Recommended for Google SEO)', 'product-faq-for-woocommerce' ); ?></option>
			<option value="disable" <?php selected( $enable_schema, 'disable' ); ?>><?php esc_html_e( 'Disable', 'product-faq-for-woocommerce' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Automatically generates Schema.org FAQPage structured data (JSON-LD) for rich results in Google Search.', 'product-faq-for-woocommerce' ); ?></p>
		<?php
	}

	/**
	 * Display WordPress Default AI Connection (Settings -> Connectors) Status Card.
	 *
	 * @return void
	 */
	public function AiConnectorStatus() {
		$status         = AiEngine::getConnectorsStatus();
		$is_ready       = ! empty( $status['is_ready'] );
		$connectors_url = $status['connectors_url'];
		$wp_ai_url      = $status['wp_ai_url'];
		$providers      = $status['providers'] ?? [];
		?>
		<div style="background: #ffffff; border: 1px solid <?php echo $is_ready ? '#a7f3d0' : '#fde68a'; ?>; border-radius: 10px; padding: 18px 20px; max-width: 620px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
			<div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 12px;">
				<div style="display: flex; align-items: center; gap: 10px;">
					<span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background: <?php echo $is_ready ? '#d1fae5' : '#fef3c7'; ?>; font-size: 16px;">
						<?php echo $is_ready ? '✅' : '⚠️'; ?>
					</span>
					<div>
						<div style="font-weight: 600; font-size: 14px; color: #1e293b;">
							<?php
							if ( $is_ready ) {
								esc_html_e( 'Connected via WordPress AI Connectors', 'product-faq-for-woocommerce' );
							} else {
								esc_html_e( 'No Active AI Connector Configured', 'product-faq-for-woocommerce' );
							}
							?>
						</div>
						<div style="font-size: 12px; color: #64748b; margin-top: 2px;">
							<?php esc_html_e( 'Uses WordPress native AI Client (wp_ai_client_prompt) & Settings ➔ Connectors.', 'product-faq-for-woocommerce' ); ?>
						</div>
					</div>
				</div>
				<span style="display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; background: <?php echo $is_ready ? '#ecfdf5' : '#fffbeb'; ?>; color: <?php echo $is_ready ? '#047857' : '#b45309'; ?>; border: 1px solid <?php echo $is_ready ? '#6ee7b7' : '#fcd34d'; ?>;">
					<?php echo $is_ready ? esc_html__( 'Ready', 'product-faq-for-woocommerce' ) : esc_html__( 'Setup Needed', 'product-faq-for-woocommerce' ); ?>
				</span>
			</div>

			<?php if ( ! empty( $providers ) ) : ?>
				<div style="display: flex; flex-wrap: wrap; gap: 8px; margin: 12px 0 16px 0; padding-top: 12px; border-top: 1px solid #f1f5f9;">
					<?php foreach ( $providers as $prov ) : ?>
						<div style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 6px; font-size: 12px; background: <?php echo ! empty( $prov['is_connected'] ) ? '#f0fdf4' : '#f8fafc'; ?>; border: 1px solid <?php echo ! empty( $prov['is_connected'] ) ? '#bbf7d0' : '#e2e8f0'; ?>; color: #334155;">
							<span style="width: 7px; height: 7px; border-radius: 50%; background: <?php echo ! empty( $prov['is_connected'] ) ? '#10b981' : '#94a3b8'; ?>;"></span>
							<strong><?php echo esc_html( $prov['name'] ); ?>:</strong>
							<span>
								<?php
								if ( ! empty( $prov['is_connected'] ) ) {
									esc_html_e( 'Connected', 'product-faq-for-woocommerce' );
								} elseif ( ! empty( $prov['has_key'] ) && empty( $prov['plugin_active'] ) ) {
									esc_html_e( 'Provider Plugin Inactive', 'product-faq-for-woocommerce' );
								} else {
									esc_html_e( 'Not Connected', 'product-faq-for-woocommerce' );
								}
								?>
							</span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
				<a href="<?php echo esc_url( $connectors_url ); ?>" class="button button-primary" style="display: inline-flex; align-items: center; gap: 6px;">
					<span>⚙️</span> <?php esc_html_e( 'Manage WordPress Connectors', 'product-faq-for-woocommerce' ); ?>
				</a>
				<button type="button" class="button button-secondary" id="woo-faq-test-ai-key">
					<?php esc_html_e( 'Test AI Connection', 'product-faq-for-woocommerce' ); ?>
				</button>
				<?php if ( ! empty( $wp_ai_url ) ) : ?>
					<a href="<?php echo esc_url( $wp_ai_url ); ?>" class="button button-link" style="text-decoration: none;">
						<?php esc_html_e( 'WordPress AI Settings ➔', 'product-faq-for-woocommerce' ); ?>
					</a>
				<?php endif; ?>
			</div>

			<p class="description" style="margin-top: 8px; margin-bottom: 0;">
				<span id="woo-faq-ai-test-result" style="font-weight: 600;"></span>
			</p>
		</div>
		<?php
	}

	/**
	 * Display AI Tone selection.
	 *
	 * @return void
	 */
	public function AiTone() {
		$tone = get_option( 'woo_faq_ai_tone', 'sales' );
		?>
		<select name="woo_faq_ai_tone">
			<option value="sales" <?php selected( $tone, 'sales' ); ?>><?php esc_html_e( 'Persuasive & Sales-Focused (Objection Buster)', 'product-faq-for-woocommerce' ); ?></option>
			<option value="friendly" <?php selected( $tone, 'friendly' ); ?>><?php esc_html_e( 'Warm, Helpful & Friendly', 'product-faq-for-woocommerce' ); ?></option>
			<option value="concise" <?php selected( $tone, 'concise' ); ?>><?php esc_html_e( 'Concise & Direct (Brief Answers)', 'product-faq-for-woocommerce' ); ?></option>
			<option value="technical" <?php selected( $tone, 'technical' ); ?>><?php esc_html_e( 'Technical, Detailed & Professional', 'product-faq-for-woocommerce' ); ?></option>
		</select>
		<?php
	}
}
