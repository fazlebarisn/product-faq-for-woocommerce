<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build a tracked FBS Fusion URL with clean UTM parameters.
 *
 * @param string $path     Relative path on fbsfusion.com (e.g. 'plugins/', 'hire-us/', 'speed-security/').
 * @param string $campaign UTM campaign slug (e.g. 'pro-upgrade', 'agency-services').
 * @param string $content  UTM content placement identifier.
 * @param string $anchor   Optional URL hash fragment without '#'.
 * @return string
 */
function woo_faq_utm_url( $path = 'plugins/', $campaign = 'pro-upgrade', $content = 'dashboard', $anchor = '' ) {
	$clean_path = ltrim( (string) $path, '/' );
	$base_url   = 'https://fbsfusion.com/' . $clean_path;

	$url = add_query_arg(
		[
			'utm_source'   => 'product-faq-for-woocommerce',
			'utm_medium'   => 'wp-admin',
			'utm_campaign' => sanitize_key( $campaign ),
			'utm_content'  => sanitize_key( $content ),
		],
		$base_url
	);

	if ( ! empty( $anchor ) ) {
		$url .= '#' . ltrim( sanitize_text_field( $anchor ), '#' );
	}

	return $url;
}

/**
 * Search WooCommerce product taxonomy terms via AJAX.
 *
 * @author Fazle Bari
 */
add_action(
	'wp_ajax_faq_term_search',
	function () {
		check_ajax_referer( 'faq_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json( [] );
		}

		$term     = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';
		$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) : '';

		$allowed_taxonomies = apply_filters(
			'woo_faq_archive_taxonomies',
			[
				'product_cat' => __( 'Category', 'product-faq-for-woocommerce' ),
				'product_tag' => __( 'Tag', 'product-faq-for-woocommerce' ),
			]
		);

		if ( ! array_key_exists( $taxonomy, $allowed_taxonomies ) ) {
			wp_send_json( [] );
		}

		$results = get_terms(
			[
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'name__like' => $term,
				'number'     => 10,
			]
		);

		if ( is_wp_error( $results ) || ! is_array( $results ) ) {
			wp_send_json( [] );
		}

		$formatted = array_map(
			function ( $term_obj ) {
				return [
					'label' => $term_obj->name,
					'value' => $term_obj->term_id,
				];
			},
			$results
		);

		wp_send_json( $formatted );
	}
);

/**
 * Handle dismissing review request notice.
 *
 * @author Fazle Bari
 */
add_action(
	'wp_ajax_woo_faq_dismiss_review_notice',
	function () {
		check_ajax_referer( 'woo_faq_dismiss_review_notice', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		$user_id = get_current_user_id();
		if ( $user_id ) {
			update_user_meta( $user_id, 'woo_faq_review_notice_dismissed', true );
		}

		wp_send_json_success();
	}
);
