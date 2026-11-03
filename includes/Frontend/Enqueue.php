<?php

namespace Woo\Faq\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Enqueue {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	public function enqueue() {
		$front_css_ver = file_exists( WOO_FAQ_PATH . '/assets/css/woo-faq.css' ) ? WOO_FAQ_VERSION . '.' . filemtime( WOO_FAQ_PATH . '/assets/css/woo-faq.css' ) : WOO_FAQ_VERSION;
		$front_js_ver  = file_exists( WOO_FAQ_PATH . '/assets/js/woo-faq.js' ) ? WOO_FAQ_VERSION . '.' . filemtime( WOO_FAQ_PATH . '/assets/js/woo-faq.js' ) : WOO_FAQ_VERSION;

		wp_enqueue_style( 'faqstyle', WOO_FAQ_URL . '/assets/css/woo-faq.css', [], $front_css_ver, 'all' );
		wp_enqueue_script( 'faqscript', WOO_FAQ_URL . '/assets/js/woo-faq.js', [ 'jquery' ], $front_js_ver, true );
	}
}
