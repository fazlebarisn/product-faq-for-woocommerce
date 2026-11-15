<?php

namespace Woo\Faq\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminNotice {

	public function __construct() {
		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) ), true ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
		}

		// Show review request notice after 7 days ONLY on Product FAQ & WooCommerce product screens.
		add_action( 'admin_notices', [ $this, 'review_request_notice' ] );
		add_action( 'admin_notices', [ $this, 'legacy_ai_connector_notice' ] );

		// Register AJAX dismissal handlers.
		add_action( 'wp_ajax_woo_faq_dismiss_review_notice', [ $this, 'ajax_dismiss_review_notice' ] );
		add_action( 'wp_ajax_woo_faq_dismiss_ai_connector_notice', [ $this, 'ajax_dismiss_ai_connector_notice' ] );
	}

	/**
	 * Persist review notice dismissal for the current administrator.
	 *
	 * @since 1.6.2
	 * @return void
	 */
	public function ajax_dismiss_review_notice() {
		check_ajax_referer( 'woo_faq_dismiss_review_notice', 'nonce' );
		if ( current_user_can( 'manage_options' ) ) {
			update_user_meta( get_current_user_id(), 'woo_faq_review_notice_dismissed', '1' );
		}
		wp_send_json_success();
	}

	/**
	 * Persist legacy AI key -> WordPress Connectors reminder dismissal.
	 *
	 * @since 1.6.2
	 * @return void
	 */
	public function ajax_dismiss_ai_connector_notice() {
		check_ajax_referer( 'woo_faq_dismiss_ai_connector_notice', 'nonce' );
		if ( current_user_can( 'manage_options' ) ) {
			update_user_meta( get_current_user_id(), 'woo_faq_ai_connector_notice_dismissed', '1' );
		}
		wp_send_json_success();
	}

	/**
	 * Friendly one-time notice for stores upgrading from older versions with `woo_faq_ai_api_key`,
	 * guiding them to configure WordPress Settings -> Connectors.
	 *
	 * @since 1.6.2
	 * @return void
	 */
	public function legacy_ai_connector_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$legacy_key = get_option( 'woo_faq_ai_api_key', '' );
		if ( empty( $legacy_key ) ) {
			return;
		}

		if ( get_user_meta( get_current_user_id(), 'woo_faq_ai_connector_notice_dismissed', true ) ) {
			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || ( false === strpos( (string) $screen->id, 'woo_' ) && ! in_array( $screen->id, [ 'product', 'edit-product' ], true ) ) ) {
			return;
		}

		$ai_ready = class_exists( '\Woo\Faq\Admin\AiEngine' ) && method_exists( '\Woo\Faq\Admin\AiEngine', 'isWpAiReady' )
			? \Woo\Faq\Admin\AiEngine::isWpAiReady()
			: false;
		if ( $ai_ready ) {
			return;
		}

		$connectors_url = admin_url( 'options-connectors.php' );
		$settings_url   = admin_url( 'admin.php?page=woo_sfaq#tab-ai' );
		?>
		<div class="notice notice-info is-dismissible woo-faq-ai-migrate-notice" style="border-left-color:#6366f1;">
			<p>
				<strong><?php esc_html_e( 'Product FAQ AI Assistant Upgraded:', 'product-faq-for-woocommerce' ); ?></strong>
				<?php esc_html_e( 'Product FAQ now uses WordPress Native AI Connectors (supporting Google Gemini, OpenAI, and Anthropic Claude with zero token markups). Please connect your preferred AI key under Settings → Connectors to continue generating 1-click AI FAQs.', 'product-faq-for-woocommerce' ); ?>
				<a href="<?php echo esc_url( $connectors_url ); ?>" class="button button-secondary" style="margin-left:8px;vertical-align:middle;"><?php esc_html_e( 'Open Settings → Connectors', 'product-faq-for-woocommerce' ); ?></a>
				<a href="<?php echo esc_url( $settings_url ); ?>" style="margin-left:8px;vertical-align:middle;"><?php esc_html_e( 'AI Setup Guide', 'product-faq-for-woocommerce' ); ?></a>
			</p>
		</div>
		<script>
		jQuery(document).ready(function($) {
			$('.woo-faq-ai-migrate-notice').on('click', '.notice-dismiss', function() {
				$.post(ajaxurl, {
					action: 'woo_faq_dismiss_ai_connector_notice',
					nonce: '<?php echo esc_js( wp_create_nonce( 'woo_faq_dismiss_ai_connector_notice' ) ); ?>'
				});
			});
		});
		</script>
		<?php
	}

	public function admin_notice_missing_main_plugin() {
		$class   = 'notice notice-error';
		$message = __( 'Product FAQ for WooCommerce requires WooCommerce to be activated.', 'product-faq-for-woocommerce' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}

	/**
	 * Show polite, permanently dismissible review request notice after 7 days of installation,
	 * scoped strictly to plugin settings pages and WooCommerce product edit/list screens.
	 *
	 * @since 1.3.2
	 * @return void
	 */
	public function review_request_notice() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Strictly scope notice to Product FAQ screens and WooCommerce product screens (WP.org Guideline #11).
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen ) {
			return;
		}

		$allowed_screens = [
			'toplevel_page_woo_sfaq',
			'product-faq_page_woo_afaq',
			'product-faq_page_woo_pfaq',
			'product-faq_page_woo_author_faq',
			'product',
			'edit-product',
		];

		if ( ! in_array( $screen->id, $allowed_screens, true ) && false === strpos( (string) $screen->id, 'woo_' ) ) {
			return;
		}

		$dismissed = get_user_meta( get_current_user_id(), 'woo_faq_review_notice_dismissed', true );
		if ( $dismissed ) {
			return;
		}

		$installed_time = get_option( 'woo_faq_installed' );
		if ( ! $installed_time || ( time() - (int) $installed_time ) < 7 * DAY_IN_SECONDS ) {
			return;
		}

		?>
		<style>
		.woo-faq-review-notice {
			border-left-color: #10b981 !important;
			padding: 14px 20px !important;
			margin: 15px 20px 15px 0 !important;
			background: #fff !important;
			box-shadow: 0 1px 2px rgba(15, 23, 42, 0.06) !important;
			border-radius: 6px !important;
		}
		.woo-faq-review-notice h3 {
			margin: 0 0 4px 0 !important;
			color: #1f2937 !important;
			font-size: 14px !important;
			font-weight: 600 !important;
			line-height: 1.4 !important;
		}
		.woo-faq-review-notice p {
			margin: 0 !important;
			color: #4b5563 !important;
			font-size: 13px !important;
			line-height: 1.5 !important;
		}
		.woo-faq-review-notice .button-primary {
			background: #10b981 !important;
			border: none !important;
			border-radius: 6px !important;
			padding: 7px 15px !important;
			font-size: 13px !important;
			font-weight: 600 !important;
			color: #fff !important;
			cursor: pointer !important;
			text-decoration: none !important;
			display: inline-block !important;
			height: auto !important;
			line-height: 1.4 !important;
			box-shadow: none !important;
			text-shadow: none !important;
		}
		.woo-faq-review-notice .button-primary:hover {
			background: #059669 !important;
			color: #fff !important;
		}
		</style>
		<div class="notice notice-info is-dismissible woo-faq-review-notice">
			<div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
				<div style="font-size: 1.75rem;">⭐</div>
				<div style="flex: 1; min-width: 240px;">
					<h3><?php esc_html_e( 'Enjoying Product FAQ for WooCommerce?', 'product-faq-for-woocommerce' ); ?></h3>
					<p>
						<?php esc_html_e( 'If Product FAQ is helping you answer shopper questions and grow sales, would you mind leaving a quick 5-star review on WordPress.org? Your feedback helps us keep building free features!', 'product-faq-for-woocommerce' ); ?>
					</p>
				</div>
				<div style="display: flex; gap: 0.75rem; align-items: center;">
					<a href="https://wordpress.org/support/plugin/product-faq-for-woocommerce/reviews/#new-post" target="_blank" rel="noopener noreferrer" class="button button-primary">
						<?php esc_html_e( 'Leave a Review ↗', 'product-faq-for-woocommerce' ); ?>
					</a>
					<a href="#" class="woo-faq-review-dismiss" style="color: #6b7280; text-decoration: none; font-size: 13px; font-weight: 500;">
						<?php esc_html_e( 'Dismiss', 'product-faq-for-woocommerce' ); ?>
					</a>
				</div>
			</div>
		</div>
		<script>
		jQuery(document).ready(function($) {
			function dismissReviewNotice() {
				$.post(ajaxurl, {
					action: 'woo_faq_dismiss_review_notice',
					nonce: '<?php echo esc_js( wp_create_nonce( 'woo_faq_dismiss_review_notice' ) ); ?>'
				});
				$('.woo-faq-review-notice').fadeTo(100, 0, function() {
					$(this).slideUp(100, function() {
						$(this).remove();
					});
				});
			}

			$('.woo-faq-review-notice').on('click', '.notice-dismiss, .woo-faq-review-dismiss', function(e) {
				if ($(this).hasClass('woo-faq-review-dismiss')) {
					e.preventDefault();
				}
				dismissReviewNotice();
			});
		});
		</script>
		<?php
	}
}
