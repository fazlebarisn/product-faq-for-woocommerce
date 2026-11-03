<?php

namespace Woo\Faq\Admin;

defined( 'ABSPATH' ) || die( 'Nice Try!' );

class AiEngine {

	public function __construct() {
		add_action( 'wp_ajax_woo_faq_test_ai_connection', [ $this, 'testAiConnection' ] );
		add_action( 'wp_ajax_woo_faq_generate_ai_faqs', [ $this, 'generateAiFaqs' ] );
	}

	/**
	 * Check if WordPress native AI Client (wp_ai_client_prompt) is available and ready for text generation.
	 *
	 * @return bool
	 */
	public static function isAiReady() {
		if ( ! function_exists( 'wp_ai_client_prompt' ) ) {
			return false;
		}

		try {
			$builder = wp_ai_client_prompt( 'Test' );
			if ( function_exists( 'WordPress\\AI\\get_preferred_models_for_text_generation' ) ) {
				$models = \WordPress\AI\get_preferred_models_for_text_generation();
				if ( ! empty( $models ) ) {
					$builder = $builder->using_model_preference( ...$models );
				}
			}
			return (bool) $builder->is_supported_for_text_generation();
		} catch ( \Throwable $e ) {
			return false;
		}
	}

	/**
	 * Retrieve status of all registered WordPress AI Provider Connectors (Settings -> Connectors).
	 *
	 * @return array<string, mixed>
	 */
	public static function getConnectorsStatus() {
		$providers   = [];
		$has_any_key = false;

		if ( function_exists( 'wp_get_connectors' ) ) {
			$all_connectors = (array) wp_get_connectors();
			foreach ( $all_connectors as $id => $data ) {
				if ( ! is_array( $data ) || ( $data['type'] ?? '' ) !== 'ai_provider' ) {
					continue;
				}

				$auth          = $data['authentication'] ?? [];
				$setting_name  = $auth['setting_name'] ?? '';
				$constant_name = $auth['constant_name'] ?? '';
				$env_var_name  = $auth['env_var_name'] ?? '';

				$has_key = false;
				if ( ! empty( $env_var_name ) && false !== getenv( $env_var_name ) && '' !== getenv( $env_var_name ) ) {
					$has_key = true;
				} elseif ( ! empty( $constant_name ) && defined( $constant_name ) && '' !== constant( $constant_name ) ) {
					$has_key = true;
				} elseif ( ! empty( $setting_name ) && '' !== get_option( $setting_name, '' ) ) {
					$has_key = true;
				}

				$plugin_active = true;
				if ( isset( $data['plugin']['is_active'] ) && is_callable( $data['plugin']['is_active'] ) ) {
					try {
						$plugin_active = (bool) call_user_func( $data['plugin']['is_active'] );
					} catch ( \Throwable $e ) {
						$plugin_active = false;
					}
				}

				$is_connected = $has_key && $plugin_active;
				if ( $is_connected ) {
					$has_any_key = true;
				}

				$providers[ $id ] = [
					'id'            => $id,
					'name'          => $data['name'] ?? ucfirst( (string) $id ),
					'description'   => $data['description'] ?? '',
					'has_key'       => $has_key,
					'plugin_active' => $plugin_active,
					'is_connected'  => $is_connected,
				];
			}
		}

		$is_ready = self::isAiReady();

		return [
			'is_ready'       => $is_ready,
			'has_any_key'    => $has_any_key || $is_ready,
			'connectors_url' => admin_url( 'options-connectors.php' ),
			'wp_ai_url'      => defined( 'WPAI_VERSION' ) ? admin_url( 'options-general.php?page=ai-wp-admin' ) : '',
			'providers'      => $providers,
		];
	}

	/**
	 * Generate text using WordPress Core AI Client (wp_ai_client_prompt).
	 *
	 * @param string $prompt              User prompt content.
	 * @param string $system_instructions Optional system instruction.
	 * @param float  $temperature         Generation temperature.
	 * @return string|\WP_Error
	 */
	public static function generateTextWithWpAi( $prompt, $system_instructions = '', $temperature = 0.7 ) {
		if ( ! function_exists( 'wp_ai_client_prompt' ) ) {
			return new \WP_Error(
				'wp_ai_unavailable',
				__( 'WordPress AI Client API (wp_ai_client_prompt) is not available. Please use WordPress 7.0+ or activate the official WordPress AI plugin.', 'product-faq-for-woocommerce' )
			);
		}

		try {
			$builder = wp_ai_client_prompt( $prompt );

			if ( function_exists( 'WordPress\\AI\\get_preferred_models_for_text_generation' ) ) {
				$models = \WordPress\AI\get_preferred_models_for_text_generation();
				if ( ! empty( $models ) ) {
					$builder = $builder->using_model_preference( ...$models );
				}
			}

			if ( ! empty( $system_instructions ) ) {
				$builder = $builder->using_system_instruction( $system_instructions );
			}

			$builder = $builder->using_temperature( (float) $temperature );

			if ( ! $builder->is_supported_for_text_generation() ) {
				return new \WP_Error(
					'wp_ai_not_configured',
					__( 'No connected AI provider found in WordPress Settings -> Connectors.', 'product-faq-for-woocommerce' )
				);
			}

			return $builder->generate_text();
		} catch ( \Throwable $e ) {
			return new \WP_Error( 'wp_ai_exception', $e->getMessage() );
		}
	}

	/**
	 * Test connection to the WordPress Default AI Client (Settings -> Connectors).
	 *
	 * @return void
	 */
	public function testAiConnection() {
		check_ajax_referer( 'faq_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'product-faq-for-woocommerce' ) ] );
		}

		if ( ! self::isAiReady() ) {
			wp_send_json_error(
				[
					'message' => __( 'No active AI provider is connected in WordPress. Please go to Settings -> Connectors to connect Google Gemini, OpenAI, or Anthropic.', 'product-faq-for-woocommerce' ),
				]
			);
		}

		$result = self::generateTextWithWpAi(
			'Reply with the single word: OK',
			'You are a connection verification assistant.',
			0.1
		);

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( [ 'message' => $result->get_error_message() ] );
		}

		wp_send_json_success(
			[
				'message' => __( 'WordPress Default AI Connection verified successfully! AI FAQ Assistant is ready.', 'product-faq-for-woocommerce' ),
			]
		);
	}

	/**
	 * Build a rich WooCommerce specification context string from WC_Product metadata
	 * (Price, Stock Status, Weight & Dimensions, Short Description, Categories, Tags, and Attributes).
	 *
	 * @since 1.6.0
	 * @param int    $product_id   WooCommerce product ID.
	 * @param string $editor_title Live product title from editor.
	 * @param string $editor_desc  Live product description from editor.
	 * @return string
	 */
	public static function buildProductSpecContext( $product_id, $editor_title = '', $editor_desc = '' ) {
		$lines = [];
		$title = trim( (string) $editor_title );
		$desc  = trim( wp_strip_all_tags( (string) $editor_desc ) );

		$product = ( $product_id > 0 && function_exists( 'wc_get_product' ) ) ? wc_get_product( $product_id ) : false;

		if ( $product && is_object( $product ) ) {
			if ( '' === $title && method_exists( $product, 'get_name' ) ) {
				$title = (string) $product->get_name();
			}

			if ( '' !== $title ) {
				$lines[] = 'Product Name: ' . $title;
			}

			if ( method_exists( $product, 'get_type' ) ) {
				$lines[] = 'Product Type: ' . $product->get_type();
			}

			if ( method_exists( $product, 'get_sku' ) && '' !== (string) $product->get_sku() ) {
				$lines[] = 'SKU: ' . $product->get_sku();
			}

			// Price & currency details.
			if ( method_exists( $product, 'get_price' ) && '' !== (string) $product->get_price() ) {
				$currency  = function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'USD';
				$price_str = $product->get_price() . ' ' . $currency;
				if ( method_exists( $product, 'is_on_sale' ) && $product->is_on_sale() && method_exists( $product, 'get_regular_price' ) ) {
					$price_str .= ' (On Sale; Regular Price: ' . $product->get_regular_price() . ' ' . $currency . ')';
				}
				$lines[] = 'Price: ' . $price_str;
			}

			// Stock availability.
			if ( method_exists( $product, 'get_stock_status' ) ) {
				$stock_map = [
					'instock'     => 'In Stock',
					'outofstock'  => 'Out of Stock',
					'onbackorder' => 'Available on Backorder',
				];
				$st        = (string) $product->get_stock_status();
				$lines[]   = 'Stock Status: ' . ( $stock_map[ $st ] ?? $st );
			}

			// Physical weight & dimensions.
			if ( method_exists( $product, 'has_weight' ) && $product->has_weight() ) {
				$weight  = function_exists( 'wc_format_weight' ) ? wc_format_weight( $product->get_weight() ) : $product->get_weight();
				$lines[] = 'Weight: ' . wp_strip_all_tags( (string) $weight );
			}

			if ( method_exists( $product, 'has_dimensions' ) && $product->has_dimensions() ) {
				$dims    = function_exists( 'wc_format_dimensions' ) ? wc_format_dimensions( $product->get_dimensions( false ) ) : '';
				$dims    = trim( wp_strip_all_tags( (string) $dims ) );
				if ( '' !== $dims && 'N/A' !== $dims ) {
					$lines[] = 'Dimensions: ' . $dims;
				}
			}

			// Product categories & tags.
			$cats = wp_get_post_terms( $product_id, 'product_cat', [ 'fields' => 'names' ] );
			if ( ! is_wp_error( $cats ) && ! empty( $cats ) ) {
				$lines[] = 'Categories: ' . implode( ', ', $cats );
			}

			$tags = wp_get_post_terms( $product_id, 'product_tag', [ 'fields' => 'names' ] );
			if ( ! is_wp_error( $tags ) && ! empty( $tags ) ) {
				$lines[] = 'Tags: ' . implode( ', ', $tags );
			}

			// Product attributes (e.g. Material, Size, Color, Compatibility).
			if ( method_exists( $product, 'get_attributes' ) ) {
				$attributes = $product->get_attributes();
				$attr_pairs = [];
				if ( is_array( $attributes ) ) {
					foreach ( $attributes as $attr_key => $attribute ) {
						if ( is_object( $attribute ) && method_exists( $attribute, 'get_name' ) ) {
							$attr_name  = $attribute->get_name();
							$attr_label = function_exists( 'wc_attribute_label' ) ? wc_attribute_label( $attr_name, $product ) : $attr_name;
							$values     = [];

							if ( method_exists( $attribute, 'is_taxonomy' ) && $attribute->is_taxonomy() && function_exists( 'wc_get_product_terms' ) ) {
								$values = wc_get_product_terms( $product_id, $attr_name, [ 'fields' => 'names' ] );
							} elseif ( method_exists( $attribute, 'get_options' ) ) {
								$values = (array) $attribute->get_options();
							}

							$values = array_filter( array_map( 'strval', (array) $values ) );
							if ( ! empty( $values ) ) {
								$attr_pairs[] = $attr_label . ': ' . implode( ', ', $values );
							}
						}
					}
				}

				if ( ! empty( $attr_pairs ) ) {
					$lines[] = 'Product Attributes / Specifications: ' . implode( ' | ', $attr_pairs );
				}
			}

			// Short description.
			if ( method_exists( $product, 'get_short_description' ) ) {
				$short_desc = trim( wp_strip_all_tags( (string) $product->get_short_description() ) );
				if ( '' !== $short_desc ) {
					$lines[] = 'Short Summary: ' . $short_desc;
				}
			}

			if ( '' === $desc && method_exists( $product, 'get_description' ) ) {
				$desc = trim( wp_strip_all_tags( (string) $product->get_description() ) );
			}
		} else {
			if ( '' !== $title ) {
				$lines[] = 'Product Name: ' . $title;
			}
		}

		if ( '' !== $desc ) {
			$lines[] = 'Detailed Description: ' . $desc;
		}

		return implode( "\n", $lines );
	}

	/**
	 * Generate FAQs using WordPress Default AI Connection enriched with WooCommerce product specs.
	 *
	 * @return void
	 */
	public function generateAiFaqs() {
		check_ajax_referer( 'faq_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'product-faq-for-woocommerce' ) ] );
		}

		$product_id    = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;
		$product_title = isset( $_POST['product_title'] ) ? sanitize_text_field( wp_unslash( $_POST['product_title'] ) ) : '';
		$product_desc  = isset( $_POST['product_desc'] ) ? sanitize_textarea_field( wp_unslash( $_POST['product_desc'] ) ) : '';
		$count         = isset( $_POST['count'] ) ? intval( wp_unslash( $_POST['count'] ) ) : 3;
		$max_ai_count  = (int) apply_filters( 'woo_faq_ai_max_count', 3 );
		$count         = max( 1, min( $count, max( 1, $max_ai_count ) ) );
		$tone          = isset( $_POST['tone'] ) ? sanitize_text_field( wp_unslash( $_POST['tone'] ) ) : get_option( 'woo_faq_ai_tone', 'sales' );

		$product_context = self::buildProductSpecContext( $product_id, $product_title, $product_desc );

		// If WordPress AI Client is not configured yet, provide contextual smart templates as demo.
		if ( ! self::isAiReady() ) {
			$demo_faqs = $this->generateFallbackFaqs( $product_title, $product_desc, $count );
			wp_send_json_success(
				[
					'faqs'    => $demo_faqs,
					'is_demo' => true,
					'message' => __( 'Generated using built-in Smart Assistant. Connect an AI provider in WordPress Settings -> Connectors for live spec-aware AI generation.', 'product-faq-for-woocommerce' ),
				]
			);
		}

		$system_instructions  = "You are an expert WooCommerce eCommerce conversion copywriter. Create high-converting, accurate customer Frequently Asked Questions (FAQs) and answers using the exact WooCommerce product specifications provided.\n";
		$system_instructions .= 'Tone: ' . esc_html( $tone ) . " (incorporate real attributes, dimensions, materials, compatibility, pricing/value, and address buyer objections naturally).\n";
		$system_instructions .= "Do not invent specs that contradict the provided product data.\n";
		$system_instructions .= "Return ONLY valid JSON matching this exact structure: {\"faqs\": [{\"question\": \"...\", \"answer\": \"...\"}]}.\n";
		$system_instructions .= 'Number of FAQs to generate: ' . $count . '.';

		$raw_text = self::generateTextWithWpAi( $product_context, $system_instructions, 0.7 );

		if ( is_wp_error( $raw_text ) ) {
			wp_send_json_error( [ 'message' => $raw_text->get_error_message() ] );
		}

		$faqs = $this->extractFaqsFromRawText( (string) $raw_text, $count );

		wp_send_json_success(
			[
				'faqs'    => $faqs,
				'is_demo' => false,
			]
		);
	}

	/**
	 * Fallback parser if JSON structure includes markdown fences or extra text.
	 *
	 * @param string $text  Raw AI response text.
	 * @param int    $count Desired count.
	 * @return array<int, array<string, string>>
	 */
	private function extractFaqsFromRawText( $text, $count ) {
		$clean = trim( $text );
		$clean = preg_replace( '/^```(?:json)?\s*/i', '', $clean );
		$clean = preg_replace( '/\s*```$/', '', $clean );
		$clean = trim( (string) $clean );

		$decoded = json_decode( $clean, true );

		if ( ! is_array( $decoded ) && preg_match( '/\{[\s\S]*\}/', $clean, $matches ) ) {
			$decoded = json_decode( $matches[0], true );
		}

		if ( is_array( $decoded ) ) {
			if ( isset( $decoded['faqs'] ) && is_array( $decoded['faqs'] ) ) {
				return $decoded['faqs'];
			}
			if ( isset( $decoded[0]['question'] ) ) {
				return $decoded;
			}
		}

		return $this->generateFallbackFaqs( 'Product', '', $count );
	}

	/**
	 * Smart fallback generator when no AI connector is configured.
	 *
	 * @param string $title Product title.
	 * @param string $desc  Product description.
	 * @param int    $count Count of FAQs.
	 * @return array<int, array<string, string>>
	 */
	private function generateFallbackFaqs( $title, $desc, $count ) {
		$pname = ! empty( $title ) ? $title : __( 'this product', 'product-faq-for-woocommerce' );

		$presets = [
			[
				/* translators: %s: Product title or product name */
				'question' => sprintf( __( 'What is the delivery and shipping timeframe for %s?', 'product-faq-for-woocommerce' ), $pname ),
				'answer'   => __( 'Orders are processed within 1-2 business days. Standard delivery typically takes 3-5 business days with full online tracking.', 'product-faq-for-woocommerce' ),
			],
			[
				/* translators: %s: Product title or product name */
				'question' => sprintf( __( 'What is the return and refund policy for %s?', 'product-faq-for-woocommerce' ), $pname ),
				'answer'   => __( 'We offer a 30-day hassle-free return guarantee. If you are not completely satisfied, contact our support team for an exchange or full refund.', 'product-faq-for-woocommerce' ),
			],
			[
				/* translators: %s: Product title or product name */
				'question' => sprintf( __( 'Is %s covered under warranty?', 'product-faq-for-woocommerce' ), $pname ),
				'answer'   => __( 'Yes! All our products come with a 1-year manufacturer warranty covering any defects in materials or craftsmanship.', 'product-faq-for-woocommerce' ),
			],
		];

		return array_slice( $presets, 0, $count );
	}
}
