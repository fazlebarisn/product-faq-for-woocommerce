<?php

namespace Woo\Faq;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Native Gutenberg Block registration for Product FAQ Accordion.
 *
 * @since 1.6.0
 */
class Blocks {

	/**
	 * Register hooks.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'registerBlocks' ] );
	}

	/**
	 * Register block assets and server-rendered block type.
	 *
	 * @return void
	 */
	public function registerBlocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		wp_register_style(
			'woo-faq-block-style',
			WOO_FAQ_URL . '/assets/css/woo-faq.css',
			[],
			WOO_FAQ_VERSION
		);

		wp_register_script(
			'woo-faq-block-editor',
			WOO_FAQ_URL . '/assets/js/faq-block.js',
			[
				'wp-blocks',
				'wp-element',
				'wp-block-editor',
				'wp-components',
				'wp-server-side-render',
				'wp-i18n',
			],
			WOO_FAQ_VERSION,
			true
		);

		register_block_type(
			'woo-faq/product-faq',
			[
				'api_version'     => 2,
				'editor_script'   => 'woo-faq-block-editor',
				'style'           => 'woo-faq-block-style',
				'attributes'      => [
					'productId' => [
						'type'    => 'string',
						'default' => '',
					],
					'category'  => [
						'type'    => 'string',
						'default' => '',
					],
					'title'     => [
						'type'    => 'string',
						'default' => '',
					],
				],
				'render_callback' => [ $this, 'renderBlock' ],
			]
		);
	}

	/**
	 * Server-side render callback for the woo-faq/product-faq block.
	 *
	 * @param array $attributes Block attributes.
	 * @return string
	 */
	public function renderBlock( $attributes = [] ) {
		if ( 'disable' === get_option( 'product_faq', 'enable' ) ) {
			if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
				return '<div style="padding:16px 20px;border:1px dashed #cbd5e1;border-radius:10px;background:#f8fafc;color:#475569;font-size:14px;">'
					. esc_html__( 'Product FAQ is currently disabled in Product FAQ → Settings.', 'product-faq-for-woocommerce' )
					. '</div>';
			}
			return '';
		}

		// Ensure frontend script & style are loaded whenever the block renders on the frontend.
		if ( ! is_admin() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			wp_enqueue_style( 'faqstyle', WOO_FAQ_URL . '/assets/css/woo-faq.css', [], WOO_FAQ_VERSION, 'all' );
			wp_enqueue_script( 'faqscript', WOO_FAQ_URL . '/assets/js/woo-faq.js', [ 'jquery' ], WOO_FAQ_VERSION, true );
		}

		$product_id = isset( $attributes['productId'] ) ? absint( $attributes['productId'] ) : 0;
		$category   = isset( $attributes['category'] ) ? sanitize_text_field( $attributes['category'] ) : '';
		$title      = isset( $attributes['title'] ) ? sanitize_text_field( $attributes['title'] ) : '';

		$renderer = new Frontend\FaqHtml( false );
		$output   = $renderer->renderFaqShortcode(
			[
				'id'       => $product_id,
				'category' => $category,
				'title'    => $title,
			]
		);

		if ( '' === trim( (string) $output ) && defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return '<div style="padding:18px 20px;border:1px dashed #94a3b8;border-radius:10px;background:#f8fafc;color:#334155;font-size:14px;line-height:1.5;">'
				. '<strong>' . esc_html__( 'Product FAQ Accordion Block', 'product-faq-for-woocommerce' ) . '</strong><br>'
				. esc_html__( 'No FAQs matched the current preview context yet. On a Single Product template, this block automatically renders that product\'s FAQs; on standard pages, it displays your Bulk & Product FAQs (or set a specific Product ID / Category Slug in the block sidebar).', 'product-faq-for-woocommerce' )
				. '</div>';
		}

		return (string) $output;
	}
}
