<?php

namespace Woo\Faq\Admin;

defined('ABSPATH') or die('Nice Try!');

class AiEngine
{
    public function __construct()
    {
        add_action('wp_ajax_woo_faq_test_ai_connection', [$this, 'testAiConnection']);
        add_action('wp_ajax_woo_faq_generate_ai_faqs', [$this, 'generateAiFaqs']);
    }

    /**
     * Check if WordPress native AI Client (wp_ai_client_prompt) is available and ready for text generation.
     *
     * @return bool
     */
    public static function isAiReady()
    {
        if (!function_exists('wp_ai_client_prompt')) {
            return false;
        }

        try {
            $builder = wp_ai_client_prompt('Test');
            if (function_exists('WordPress\\AI\\get_preferred_models_for_text_generation')) {
                $models = \WordPress\AI\get_preferred_models_for_text_generation();
                if (!empty($models)) {
                    $builder = $builder->using_model_preference(...$models);
                }
            }
            return (bool) $builder->is_supported_for_text_generation();
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Retrieve status of all registered WordPress AI Provider Connectors (Settings -> Connectors).
     *
     * @return array
     */
    public static function getConnectorsStatus()
    {
        $providers = [];
        $has_any_key = false;

        if (function_exists('wp_get_connectors')) {
            $all_connectors = (array) wp_get_connectors();
            foreach ($all_connectors as $id => $data) {
                if (!is_array($data) || ($data['type'] ?? '') !== 'ai_provider') {
                    continue;
                }

                $auth = $data['authentication'] ?? [];
                $setting_name  = $auth['setting_name'] ?? '';
                $constant_name = $auth['constant_name'] ?? '';
                $env_var_name  = $auth['env_var_name'] ?? '';

                $has_key = false;
                if (!empty($env_var_name) && getenv($env_var_name) !== false && getenv($env_var_name) !== '') {
                    $has_key = true;
                } elseif (!empty($constant_name) && defined($constant_name) && constant($constant_name) !== '') {
                    $has_key = true;
                } elseif (!empty($setting_name) && get_option($setting_name, '') !== '') {
                    $has_key = true;
                }

                $plugin_active = true;
                if (isset($data['plugin']['is_active']) && is_callable($data['plugin']['is_active'])) {
                    try {
                        $plugin_active = (bool) call_user_func($data['plugin']['is_active']);
                    } catch (\Throwable $e) {
                        $plugin_active = false;
                    }
                }

                $is_connected = $has_key && $plugin_active;
                if ($is_connected) {
                    $has_any_key = true;
                }

                $providers[$id] = [
                    'id'            => $id,
                    'name'          => $data['name'] ?? ucfirst($id),
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
            'connectors_url' => admin_url('options-connectors.php'),
            'wp_ai_url'      => defined('WPAI_VERSION') ? admin_url('options-general.php?page=ai-wp-admin') : '',
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
    public static function generateTextWithWpAi($prompt, $system_instructions = '', $temperature = 0.7)
    {
        if (!function_exists('wp_ai_client_prompt')) {
            return new \WP_Error(
                'wp_ai_unavailable',
                __('WordPress AI Client API (wp_ai_client_prompt) is not available. Please use WordPress 7.0+ or activate the official WordPress AI plugin.', 'product-faq-for-woocommerce')
            );
        }

        try {
            $builder = wp_ai_client_prompt($prompt);

            if (function_exists('WordPress\\AI\\get_preferred_models_for_text_generation')) {
                $models = \WordPress\AI\get_preferred_models_for_text_generation();
                if (!empty($models)) {
                    $builder = $builder->using_model_preference(...$models);
                }
            }

            if (!empty($system_instructions)) {
                $builder = $builder->using_system_instruction($system_instructions);
            }

            $builder = $builder->using_temperature((float) $temperature);

            if (!$builder->is_supported_for_text_generation()) {
                return new \WP_Error(
                    'wp_ai_not_configured',
                    __('No connected AI provider found in WordPress Settings -> Connectors.', 'product-faq-for-woocommerce')
                );
            }

            return $builder->generate_text();
        } catch (\Throwable $e) {
            return new \WP_Error('wp_ai_exception', $e->getMessage());
        }
    }

    /**
     * Test connection to the WordPress Default AI Client (Settings -> Connectors)
     */
    public function testAiConnection()
    {
        check_ajax_referer('faq_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => __('Permission denied.', 'product-faq-for-woocommerce')]);
        }

        if (!self::isAiReady()) {
            wp_send_json_error([
                'message' => __('No active AI provider is connected in WordPress. Please go to Settings -> Connectors to connect Google Gemini, OpenAI, or Anthropic.', 'product-faq-for-woocommerce')
            ]);
        }

        $result = self::generateTextWithWpAi(
            'Reply with the single word: OK',
            'You are a connection verification assistant.',
            0.1
        );

        if (is_wp_error($result)) {
            wp_send_json_error(['message' => $result->get_error_message()]);
        }

        wp_send_json_success([
            'message' => __('WordPress Default AI Connection verified successfully! AI FAQ Assistant is ready.', 'product-faq-for-woocommerce')
        ]);
    }

    /**
     * Generate FAQs using WordPress Default AI Connection based on product context
     */
    public function generateAiFaqs()
    {
        check_ajax_referer('faq_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['message' => __('Permission denied.', 'product-faq-for-woocommerce')]);
        }

        $product_title = isset($_POST['product_title']) ? sanitize_text_field(wp_unslash($_POST['product_title'])) : '';
        $product_desc  = isset($_POST['product_desc']) ? sanitize_textarea_field(wp_unslash($_POST['product_desc'])) : '';
        $count         = isset($_POST['count']) ? intval(wp_unslash($_POST['count'])) : 3;
        $count         = max(1, min($count, 3)); // Free version capped at 3
        $tone          = isset($_POST['tone']) ? sanitize_text_field(wp_unslash($_POST['tone'])) : get_option('woo_faq_ai_tone', 'sales');

        // If WordPress AI Client is not configured yet, provide contextual smart templates as demo
        if (!self::isAiReady()) {
            $demo_faqs = $this->generateFallbackFaqs($product_title, $product_desc, $count);
            wp_send_json_success([
                'faqs'    => $demo_faqs,
                'is_demo' => true,
                'message' => __('Generated using built-in Smart Assistant. Connect an AI provider in WordPress Settings -> Connectors for live AI generation.', 'product-faq-for-woocommerce')
            ]);
        }

        $system_instructions  = "You are an expert WooCommerce eCommerce conversion copywriter. Create high-converting, realistic customer Frequently Asked Questions (FAQs) and answers for the following product.\n";
        $system_instructions .= "Tone: " . esc_html($tone) . " (address shopper objections such as delivery, returns, sizing/compatibility, quality, usage).\n";
        $system_instructions .= "Return ONLY valid JSON matching this exact structure: {\"faqs\": [{\"question\": \"...\", \"answer\": \"...\"}]}.\n";
        $system_instructions .= "Number of FAQs to generate: " . $count . ".";

        $product_context = "Product Name: " . $product_title . "\n";
        if (!empty($product_desc)) {
            $product_context .= "Product Description: " . wp_strip_all_tags($product_desc) . "\n";
        }

        $raw_text = self::generateTextWithWpAi($product_context, $system_instructions, 0.7);

        if (is_wp_error($raw_text)) {
            wp_send_json_error(['message' => $raw_text->get_error_message()]);
        }

        $faqs = $this->extractFaqsFromRawText((string) $raw_text, $count);

        wp_send_json_success([
            'faqs'    => $faqs,
            'is_demo' => false
        ]);
    }

    /**
     * Fallback parser if JSON structure is slightly off
     */
    private function extractFaqsFromRawText($text, $count)
    {
        $clean = trim($text);
        $clean = preg_replace('/^```(?:json)?\s*/i', '', $clean);
        $clean = preg_replace('/\s*```$/', '', $clean);
        $clean = trim($clean);

        // Try direct decode
        $decoded = json_decode($clean, true);

        // If direct decode failed, try regex match for JSON object or array
        if (!is_array($decoded) && preg_match('/\{[\s\S]*\}/', $clean, $matches)) {
            $decoded = json_decode($matches[0], true);
        }

        if (is_array($decoded)) {
            if (isset($decoded['faqs']) && is_array($decoded['faqs'])) {
                return $decoded['faqs'];
            }
            if (isset($decoded[0]['question'])) {
                return $decoded;
            }
        }

        return $this->generateFallbackFaqs('Product', '', $count);
    }

    /**
     * Smart fallback generator when no AI connector is configured
     */
    private function generateFallbackFaqs($title, $desc, $count)
    {
        $pname = !empty($title) ? $title : __('this product', 'product-faq-for-woocommerce');
        
        $presets = [
            [
                /* translators: %s: Product title or product name */
                'question' => sprintf(__('What is the delivery and shipping timeframe for %s?', 'product-faq-for-woocommerce'), $pname),
                'answer'   => __('Orders are processed within 1-2 business days. Standard delivery typically takes 3-5 business days with full online tracking.', 'product-faq-for-woocommerce')
            ],
            [
                /* translators: %s: Product title or product name */
                'question' => sprintf(__('What is the return and refund policy for %s?', 'product-faq-for-woocommerce'), $pname),
                'answer'   => __('We offer a 30-day hassle-free return guarantee. If you are not completely satisfied, contact our support team for an exchange or full refund.', 'product-faq-for-woocommerce')
            ],
            [
                /* translators: %s: Product title or product name */
                'question' => sprintf(__('Is %s covered under warranty?', 'product-faq-for-woocommerce'), $pname),
                'answer'   => __('Yes! All our products come with a 1-year manufacturer warranty covering any defects in materials or craftsmanship.', 'product-faq-for-woocommerce')
            ]
        ];

        return array_slice($presets, 0, $count);
    }
}
