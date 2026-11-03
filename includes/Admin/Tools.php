<?php

namespace Woo\Faq\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1-Click JSON Export & Import (Backup / Migration) for Settings & Bulk FAQ Groups.
 *
 * @since 1.6.0
 */
class Tools {

	/**
	 * Allowed plugin option keys for export and import.
	 *
	 * @var array<int, string>
	 */
	const EXPORTABLE_OPTIONS = [
		'product_faq',
		'product_faq_position',
		'faq_heading',
		'woo_faq_show_count_badge',
		'woo_faq_open_first',
		'woo_faq_enable_schema',
		'faq_heading_color',
		'faq_question_color',
		'faq_ans_color',
		'faq_heading_font_size',
		'faq_question_font_size',
		'faq_ans_font_size',
		'woo_faq_ai_tone',
	];

	/**
	 * Register hooks.
	 */
	public function __construct() {
		add_action( 'admin_post_woo_faq_export_json', [ $this, 'handleExportJson' ] );
		add_action( 'wp_ajax_woo_faq_import_json', [ $this, 'handleAjaxImportJson' ] );
	}

	/**
	 * Build export payload array for settings + bulk FAQ groups.
	 *
	 * @return array<string, mixed>
	 */
	public static function buildExportPayload() {
		$settings = [];
		foreach ( self::EXPORTABLE_OPTIONS as $option_key ) {
			$val = get_option( $option_key, null );
			if ( null !== $val && false !== $val ) {
				$settings[ $option_key ] = (string) $val;
			}
		}

		$bulk_groups = get_option( 'woo_afaq_global_groups', [] );
		if ( ! is_array( $bulk_groups ) ) {
			$bulk_groups = [];
		}

		return [
			'plugin'      => 'product-faq-for-woocommerce',
			'version'     => defined( 'WOO_FAQ_VERSION' ) ? WOO_FAQ_VERSION : '1.6.0',
			'exported_at' => gmdate( 'c' ),
			'site_url'    => home_url( '/' ),
			'settings'    => $settings,
			'bulk_groups' => array_values( $bulk_groups ),
		];
	}

	/**
	 * Stream a downloadable JSON backup file via admin-post.php.
	 *
	 * @return void
	 */
	public function handleExportJson() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to export plugin settings.', 'product-faq-for-woocommerce' ) );
		}

		check_admin_referer( 'woo_faq_export_nonce', 'woo_faq_nonce' );

		$payload  = self::buildExportPayload();
		$filename = 'woo-product-faq-backup-' . gmdate( 'Y-m-d' ) . '.json';

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

		echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		exit;
	}

	/**
	 * Validate, sanitize, and import a JSON backup payload.
	 *
	 * @param array<string, mixed> $decoded Decoded JSON array.
	 * @return array{settings_count:int, groups_count:int}|\WP_Error
	 */
	public static function processImportPayload( $decoded ) {
		if ( ! is_array( $decoded ) ) {
			return new \WP_Error(
				'invalid_json',
				__( 'Invalid backup file. Please upload a valid JSON export created by Product FAQ for WooCommerce.', 'product-faq-for-woocommerce' )
			);
		}

		if ( ! isset( $decoded['settings'] ) && ! isset( $decoded['bulk_groups'] ) ) {
			return new \WP_Error(
				'missing_keys',
				__( 'This JSON file does not contain any Product FAQ settings or Bulk FAQ groups.', 'product-faq-for-woocommerce' )
			);
		}

		$settings_imported = 0;
		if ( isset( $decoded['settings'] ) && is_array( $decoded['settings'] ) ) {
			foreach ( self::EXPORTABLE_OPTIONS as $option_key ) {
				if ( array_key_exists( $option_key, $decoded['settings'] ) && is_scalar( $decoded['settings'][ $option_key ] ) ) {
					$clean_val = sanitize_text_field( (string) $decoded['settings'][ $option_key ] );
					update_option( $option_key, $clean_val );
					++$settings_imported;
				}
			}
		}

		$groups_imported = 0;
		if ( isset( $decoded['bulk_groups'] ) && is_array( $decoded['bulk_groups'] ) ) {
			$sanitized_groups = [];
			foreach ( $decoded['bulk_groups'] as $group ) {
				if ( ! is_array( $group ) || empty( $group['archive_type'] ) || empty( $group['archive_terms'] ) ) {
					continue;
				}

				$archive_type  = sanitize_text_field( (string) $group['archive_type'] );
				$archive_terms = array_values( array_filter( array_map( 'absint', (array) $group['archive_terms'] ) ) );

				if ( empty( $archive_type ) || empty( $archive_terms ) ) {
					continue;
				}

				$faqs = [];
				if ( ! empty( $group['faqs'] ) && is_array( $group['faqs'] ) ) {
					foreach ( $group['faqs'] as $faq ) {
						if ( ! is_array( $faq ) ) {
							continue;
						}
						$question = sanitize_text_field( (string) ( $faq['question'] ?? '' ) );
						$answer   = wp_kses_post( (string) ( $faq['answer'] ?? '' ) );

						if ( '' !== $question && '' !== $answer ) {
							$faqs[] = [
								'question' => $question,
								'answer'   => $answer,
							];
						}
					}
				}

				$sanitized_groups[] = [
					'archive_type'  => $archive_type,
					'archive_terms' => $archive_terms,
					'faqs'          => $faqs,
				];
				++$groups_imported;
			}

			update_option( 'woo_afaq_global_groups', $sanitized_groups );
		}

		return [
			'settings_count' => $settings_imported,
			'groups_count'   => $groups_imported,
		];
	}

	/**
	 * Handle AJAX 1-click JSON import from Settings -> Backup & Tools.
	 *
	 * @return void
	 */
	public function handleAjaxImportJson() {
		check_ajax_referer( 'faq_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'product-faq-for-woocommerce' ) ] );
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON decoded first, then strictly sanitized field-by-field in processImportPayload().
		$raw_json = isset( $_POST['json_payload'] ) ? wp_unslash( $_POST['json_payload'] ) : '';
		if ( empty( $raw_json ) || strlen( $raw_json ) > 2 * 1024 * 1024 ) {
			wp_send_json_error( [ 'message' => __( 'Backup payload is empty or exceeds the 2MB limit.', 'product-faq-for-woocommerce' ) ] );
		}

		$decoded = json_decode( $raw_json, true );
		$result  = self::processImportPayload( $decoded );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( [ 'message' => $result->get_error_message() ] );
		}

		wp_send_json_success(
			[
				'message' => sprintf(
					/* translators: 1: number of settings imported, 2: number of bulk FAQ groups imported */
					__( 'Import complete! Restored %1$d settings and %2$d Bulk FAQ group(s). Reloading settings...', 'product-faq-for-woocommerce' ),
					(int) $result['settings_count'],
					(int) $result['groups_count']
				),
			]
		);
	}
}
