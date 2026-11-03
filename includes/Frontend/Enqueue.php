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
		wp_enqueue_style( 'faqstyle', WOO_FAQ_URL . '/assets/css/woo-faq.css', [], WOO_FAQ_VERSION, 'all' );
		wp_enqueue_script( 'faqscript', WOO_FAQ_URL . '/assets/js/woo-faq.js', [ 'jquery' ], WOO_FAQ_VERSION, true );
	}
}
