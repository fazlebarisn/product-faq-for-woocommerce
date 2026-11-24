<?php

namespace Woo\Faq\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Enqueue {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	/**
	 * Register frontend assets globally and conditionally enqueue them only on relevant
	 * WooCommerce / FAQ pages (while allowing shortcodes and blocks to enqueue on demand).
	 *
	 * @return void
	 */
	public function enqueue() {
		$front_css_ver = file_exists( WOO_FAQ_PATH . '/assets/css/woo-faq.css' ) ? WOO_FAQ_VERSION . '.' . filemtime( WOO_FAQ_PATH . '/assets/css/woo-faq.css' ) : WOO_FAQ_VERSION;
		$front_js_ver  = file_exists( WOO_FAQ_PATH . '/assets/js/woo-faq.js' ) ? WOO_FAQ_VERSION . '.' . filemtime( WOO_FAQ_PATH . '/assets/js/woo-faq.js' ) : WOO_FAQ_VERSION;

		wp_register_style( 'faqstyle', WOO_FAQ_URL . '/assets/css/woo-faq.css', [], $front_css_ver, 'all' );
		wp_register_script( 'faqscript', WOO_FAQ_URL . '/assets/js/woo-faq.js', [ 'jquery' ], $front_js_ver, true );

		if ( 'disable' === get_option( 'product_faq', 'enable' ) ) {
			return;
		}

		$should_enqueue = false;

		if ( is_singular( 'product' ) || ( function_exists( 'is_product' ) && is_product() ) ) {
			$should_enqueue = true;
		} elseif ( function_exists( 'is_shop' ) && ( is_shop() || ( function_exists( 'is_product_taxonomy' ) && is_product_taxonomy() ) ) ) {
			$is_pro_active = in_array( 'woo-product-faq-pro/product-faq-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins', [] ) ), true );
			if ( $is_pro_active ) {
				$should_enqueue = true;
			}
		} else {
			global $post;
			if ( is_object( $post ) && isset( $post->post_content ) ) {
				if ( has_shortcode( (string) $post->post_content, 'woo_product_faq' ) || ( function_exists( 'has_block' ) && has_block( 'woo-faq/product-faq', $post ) ) ) {
					$should_enqueue = true;
				}
			}
		}

		if ( apply_filters( 'woo_faq_should_enqueue_frontend_assets', $should_enqueue ) ) {
			wp_enqueue_style( 'faqstyle' );
			wp_enqueue_script( 'faqscript' );
		}
	}
}
