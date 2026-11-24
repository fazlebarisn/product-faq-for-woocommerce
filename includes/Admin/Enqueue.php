<?php

namespace Woo\Faq\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Enqueue {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	/**
	 * Enqueue admin scripts & styles strictly on WooCommerce Product screens and Product FAQ pages.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public function enqueue( $hook = '' ) {
		if ( ! $this->isRelevantAdminScreen( (string) $hook ) ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-autocomplete' );

		$admin_css_ver = file_exists( WOO_FAQ_PATH . '/assets/css/woo-admin-faq.css' ) ? WOO_FAQ_VERSION . '.' . filemtime( WOO_FAQ_PATH . '/assets/css/woo-admin-faq.css' ) : WOO_FAQ_VERSION;
		$admin_js_ver  = file_exists( WOO_FAQ_PATH . '/assets/js/woo-admin-faq.js' ) ? WOO_FAQ_VERSION . '.' . filemtime( WOO_FAQ_PATH . '/assets/js/woo-admin-faq.js' ) : WOO_FAQ_VERSION;

		wp_enqueue_style( 'faq-admin-style', WOO_FAQ_URL . '/assets/css/woo-admin-faq.css', [], $admin_css_ver, 'all' );
		wp_enqueue_script( 'faq-admin-script', WOO_FAQ_URL . '/assets/js/woo-admin-faq.js', [ 'jquery', 'jquery-ui-autocomplete' ], $admin_js_ver, true );

		wp_localize_script(
			'faq-admin-script',
			'faqAjax',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'faq_nonce' ),
			]
		);

		$is_pro_plugin_active = in_array( 'woo-product-faq-pro/product-faq-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) ), true );

		$is_license_active = false;
		if ( $is_pro_plugin_active && function_exists( 'faq_pro_is_license_active' ) ) {
			$is_license_active = faq_pro_is_license_active();
		}

		$is_pro = $is_pro_plugin_active && $is_license_active;

		wp_localize_script(
			'faq-admin-script',
			'wooFaqPro',
			[
				'is_pro'           => $is_pro,
				'show_count_badge' => ( 'disable' !== get_option( 'woo_faq_show_count_badge', 'enable' ) ),
				'tab_label'        => __( 'FAQ', 'product-faq-for-woocommerce' ),
				'upgrade_url'      => woo_faq_utm_url( 'plugins/', 'pro-upgrade', 'limit-reached-button', 'product-faq' ),
			]
		);
	}

	/**
	 * Determine whether the current wp-admin screen needs Product FAQ assets.
	 *
	 * @param string $hook Current admin hook suffix.
	 * @return bool
	 */
	private function isRelevantAdminScreen( $hook ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only page slug check for asset scoping.
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		if ( '' !== $page && ( 0 === strpos( $page, 'woo_' ) || false !== strpos( $page, 'faq' ) ) ) {
			return true;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen ) {
			if ( 'product' === $screen->post_type || in_array( $screen->id, [ 'product', 'edit-product' ], true ) ) {
				return true;
			}
			if ( false !== strpos( (string) $screen->id, 'woo_' ) || false !== strpos( (string) $screen->id, 'faq' ) ) {
				return true;
			}
		}

		if ( in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only post type check.
			$post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only post ID check.
			$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;
			if ( 'product' === $post_type || ( $post_id > 0 && 'product' === get_post_type( $post_id ) ) ) {
				return true;
			}
		}

		return false;
	}
}
