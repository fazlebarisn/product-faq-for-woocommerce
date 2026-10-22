<?php

namespace Woo\Faq\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class Menu{

    public function __construct(){
        // add a menu page in dashboard
        add_action( 'admin_menu' , [ $this , 'adminMenu'] );
        // add exta links to the plugin
        add_filter('plugin_action_links_'.WOO_FAQ_BASENAME, [$this, 'settingsLink']);
        // active custom settings
        add_action('admin_init', [$this, 'wooFaqSettings']);

        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    /**
     * Enqueue assets for the settings page
     * @since 1.1.9
     */
    public function enqueueAssets($hook = '') {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $current_page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        if (!in_array($current_page, ['woo_sfaq', 'woo_afaq', 'woo_pfaq', 'woo_author_faq'])) {
            return;
        }

        wp_enqueue_style('woo-faq-admin-settings', WOO_FAQ_URL . '/assets/css/admin-settings.css', [], WOO_FAQ_VERSION);
        wp_enqueue_script('woo-faq-admin-settings', WOO_FAQ_URL . '/assets/js/admin-settings.js', ['jquery'], WOO_FAQ_VERSION, true);
        wp_localize_script('woo-faq-admin-settings', 'faqAjax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('faq_nonce')
        ]);
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }

    /**
     * add settings link 
     *
     * @return void
     */
    public function settingsLink( $links ){
        $settings_link = '<a href="admin.php?page=woo_sfaq">Settings</a>';
        
        // Only show upgrade link if pro plugin is not active
        if ( ! in_array( 'woo-product-faq-pro/product-faq-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            $pro_link = '<a href="https://wpbay.com/product/product-faq-for-woocommerce-pro/" target="_blank" style="color: #667eea; font-weight: 600;">🚀 Upgrade to Pro</a>';
            array_push($links, $settings_link, $pro_link);
        } else {
            array_push($links, $settings_link);
        }
        
        return $links;
    }
    /**
     * Add menu in wordpress dashboard menu
     *
     * @return void
     */
    public function adminMenu(){
        add_menu_page( __('Product Faq' , 'product-faq-for-woocommerce' ) , __('Product FAQ' , 'product-faq-for-woocommerce') , 'manage_options' , 'woo_sfaq' , [$this , 'adminPage'] , 'dashicons-editor-help' , 56 );
        add_submenu_page( 'woo_sfaq' , __('Product Archive' , 'product-faq-for-woocommerce') , __('Bulk FAQ' , 'product-faq-for-woocommerce') , 'manage_options' , 'woo_afaq' , [$this , 'productAtchivePage'] );
        add_submenu_page( 'woo_sfaq' , __('Browse Our Plugins' , 'product-faq-for-woocommerce') , __('Our Plugins' , 'product-faq-for-woocommerce') , 'manage_options' , 'woo_pfaq' , [$this , 'ourPluginsPage'] );
        add_submenu_page( 'woo_sfaq' , __('About The Author' , 'product-faq-for-woocommerce') , __('About The Author' , 'product-faq-for-woocommerce') , 'manage_options' , 'woo_author_faq' , [$this , 'pluginAuthorPage'] );
    }

    /**
     * admin page function
     * include template here
     * @since 1.0.0
     * @return void
     * @author Fazle Bari <fazlebarisn@gmail.com>
     */
    public function adminPage(){
        $menu_instance = $this;
        require_once WOO_FAQ_PATH.'/pages/dashboard.php';
    }
    public function productAtchivePage(){
        require_once WOO_FAQ_PATH.'/pages/product-archive.php';
    }
    public function ourPluginsPage(){
        require_once WOO_FAQ_PATH.'/pages/our-plugins.php';
    }
    public function pluginAuthorPage(){
        require_once WOO_FAQ_PATH.'/pages/plugin-author.php';
    }

    /**
     * Register custom settings for plugin
     * @return void
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     */
    public function wooFaqSettings(){
        // register sections
        register_setting('woofaq-settings-group', 'product_faq', [$this, 'sanitizeTextField']);
        register_setting('woofaq-settings-group', 'product_faq_position', [$this, 'sanitizeTextField']);
        register_setting('woofaq-settings-group', 'faq_heading', [$this, 'sanitizeTextField']);
        register_setting('woofaq-settings-group', 'faq_heading_color', [$this, 'sanitizeTextField']);
        register_setting('woofaq-settings-group', 'faq_question_color', [$this, 'sanitizeTextField']);
        register_setting('woofaq-settings-group', 'faq_ans_color', [$this, 'sanitizeTextField']);
        register_setting('woofaq-settings-group', 'faq_heading_font_size', [$this, 'sanitizeTextField']);
        register_setting('woofaq-settings-group', 'faq_question_font_size', [$this, 'sanitizeTextField']);
        register_setting('woofaq-settings-group', 'faq_ans_font_size', [$this, 'sanitizeTextField']);
        register_setting('woofaq-settings-group', 'woo_faq_enable_schema', [$this, 'sanitizeTextField']);
        register_setting('woofaq-settings-group', 'woo_faq_ai_tone', [$this, 'sanitizeTextField']);
    }

    /**
     * callback function for settings section
     * echo html
     * @return void
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com>
     */
    public function productFaqOptions(){
        echo esc_html__('From here you can change all setiings' , 'product-faq-for-woocommerce');
    }

    /**
     * callback function for style settings section
     * echo html
     * @return void
     * @author Fazle Bari <fazlebarisn@gmail.com> 
     */
    public function productFaqStyle(){
        echo esc_html__('From this section you can change all style for your FAQ html.' , 'product-faq-for-woocommerce');
    }

    /**
     * Sanitize heading data data before input
     * 
     * @return void
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com> 
     */
    public function sanitizeTextField($input){
        $output = sanitize_text_field(wp_unslash($input));
        return $output;
    }

    /**
     * Get option data from database
     * 
     * @return void
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com> 
     */
    public function faq_option_data( $key ){
        $value = get_option($key);
        return $value;
    }

    /**
     * dusplay enable/disable input field
     * 
     * @return void
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com> 
     */
    public function ProductFaq(){

        $product_faq = $this->faq_option_data('product_faq');

        $product_faq = isset($product_faq ) ? $product_faq : 'enable';
        ?>
            <select name="product_faq" id="product_faq">
                <option value="disable" <?php echo esc_attr($product_faq ) && $product_faq == 'disable' ? 'selected' : ''; ?> >Disable</option>
                <option value="enable" <?php echo esc_attr($product_faq ) && $product_faq == 'enable' ? 'selected' : ''; ?> >Enable</option>
            </select>
        <?php
    }

    /**
     * dusplay faq position field
     * 
     * @return void
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com> 
     */
    public function faqPosition(){
        $product_faq_position = get_option('product_faq_position');
        $faq_position = isset($product_faq_position ) ? $product_faq_position : 'after_single_product';
        ?>
            <select name="product_faq_position" id="product_faq_position">
                <option value="after_cart_button" <?php echo esc_attr( $faq_position ) && $faq_position == 'after_cart_button' ? 'selected' : ''; ?> >After Cart Button</option>
                <option value="after_meta" <?php echo esc_attr( $faq_position ) && $faq_position == 'after_meta' ? 'selected' : ''; ?> >After Meta</option>
                <option value="after_summary" <?php echo esc_attr( $faq_position ) && $faq_position == 'after_summary' ? 'selected' : ''; ?> >After Summary</option>
                <option value="after_single_product" <?php echo esc_attr( $faq_position ) && $faq_position == 'after_single_product' ? 'selected' : ''; ?> >After Single Product</option>
                <option value="product_tab" <?php echo esc_attr( $faq_position ) && $faq_position == 'product_tab' ? 'selected' : ''; ?> >Product Tab</option>
                <option value="shortcode" <?php echo esc_attr( $faq_position ) && $faq_position == 'shortcode' ? 'selected' : ''; ?> >Shortcode Only ([woo_product_faq])</option>
            </select>
            <p class="description"><?php esc_html_e( 'Tip: Use [woo_product_faq] on Single Product templates or a general FAQ Page, [woo_product_faq id="123"] for a specific product ID, or [woo_product_faq category="slug"] for a category.', 'product-faq-for-woocommerce' ); ?></p>
        <?php
    }

    /**
     * Display heading input field
     * 
     * @return void
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com> 
     */
    public function Heading(){
        $faq_heading = get_option('faq_heading');
        ?>
            <input type="text" name="faq_heading" value="<?php echo esc_attr( $faq_heading ); ?>" placeholder="Insert Faq Heading" />
        <?php
    }

    /**
     * Display heading color input field
     * 
     * @return void
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com> 
     */
    public function HeadingColor(){
        $faq_heading_color = get_option('faq_heading_color');
        ?>
            <input type="text" class="color-field" name="faq_heading_color" value="<?php echo esc_attr( $faq_heading_color ); ?>" />
        <?php
    }

    /**
     * Display question color input field
     * 
     * @return void
     * @since 1.1.4
     * @author Fazle Bari <fazlebarisn@gmail.com> 
     */
    public function QuestionColor(){
        $faq_question_color = get_option('faq_question_color');
        ?>
            <input type="text" class="color-field" name="faq_question_color" value="<?php echo esc_attr( $faq_question_color ); ?>" />
        <?php
    }
    /**
     * Display answer color input field
     * 
     * @return void
     * @since 1.1.4
     * @author Fazle Bari <fazlebarisn@gmail.com> 
     */
    public function AnswerColor(){
        $faq_ans_color = get_option('faq_ans_color');
        ?>
            <input type="text" class="color-field" name="faq_ans_color" value="<?php echo esc_attr( $faq_ans_color ); ?>" />
        <?php
    }

    /**
     * Display heading font size input field
     * 
     * @return void
     * @since 1.0.0
     * @author Fazle Bari <fazlebarisn@gmail.com> 
     */
    public function HeadingFontSize(){
        $faq_heading_font_size = get_option('faq_heading_font_size');
        ?>
            <input type="text" name="faq_heading_font_size" value="<?php echo esc_attr( $faq_heading_font_size ); ?>" placeholder="Example: 45px" />
        <?php
    }

    /**
     * Display question font size input field
     * 
     * @return void
     * @since 1.1.4
     * @author Fazle Bari <fazlebarisn@gmail.com> 
     */
    public function QuestionFontSize(){
        $faq_question_font_size = get_option('faq_question_font_size');
        ?>
            <input type="text" name="faq_question_font_size" value="<?php echo esc_attr( $faq_question_font_size ); ?>" placeholder="Example: 45px" />
        <?php
    }

    /**
     * Display answer font size input field
     * 
     * @return void
     * @since 1.1.4
     * @author Fazle Bari <fazlebarisn@gmail.com> 
     */
    public function AnswerFontSize(){
        $faq_ans_font_size = get_option('faq_ans_font_size');
        ?>
            <input type="text" name="faq_ans_font_size" value="<?php echo esc_attr( $faq_ans_font_size ); ?>" placeholder="Example: 45px" />
        <?php
    }

    /**
     * Display Google Schema JSON-LD toggle
     */
    public function EnableSchema(){
        $enable_schema = get_option('woo_faq_enable_schema', 'enable');
        ?>
            <select name="woo_faq_enable_schema">
                <option value="enable" <?php selected($enable_schema, 'enable'); ?>><?php esc_html_e('Enable (Recommended for Google SEO)', 'product-faq-for-woocommerce'); ?></option>
                <option value="disable" <?php selected($enable_schema, 'disable'); ?>><?php esc_html_e('Disable', 'product-faq-for-woocommerce'); ?></option>
            </select>
            <p class="description"><?php esc_html_e('Automatically generates Schema.org FAQPage structured data (JSON-LD) for rich results in Google Search.', 'product-faq-for-woocommerce'); ?></p>
        <?php
    }

    /**
     * Display WordPress Default AI Connection (Settings -> Connectors) Status Card
     */
    public function AiConnectorStatus(){
        $status         = AiEngine::getConnectorsStatus();
        $is_ready       = ! empty( $status['is_ready'] );
        $connectors_url = $status['connectors_url'];
        $wp_ai_url      = $status['wp_ai_url'];
        $providers      = $status['providers'] ?? [];
        ?>
        <div style="background: #ffffff; border: 1px solid <?php echo $is_ready ? '#a7f3d0' : '#fde68a'; ?>; border-radius: 10px; padding: 18px 20px; max-width: 620px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background: <?php echo $is_ready ? '#d1fae5' : '#fef3c7'; ?>; font-size: 16px;">
                        <?php echo $is_ready ? '✅' : '⚠️'; ?>
                    </span>
                    <div>
                        <div style="font-weight: 600; font-size: 14px; color: #1e293b;">
                            <?php
                            if ( $is_ready ) {
                                esc_html_e( 'Connected via WordPress AI Connectors', 'product-faq-for-woocommerce' );
                            } else {
                                esc_html_e( 'No Active AI Connector Configured', 'product-faq-for-woocommerce' );
                            }
                            ?>
                        </div>
                        <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                            <?php esc_html_e( 'Uses WordPress native AI Client (wp_ai_client_prompt) & Settings ➔ Connectors.', 'product-faq-for-woocommerce' ); ?>
                        </div>
                    </div>
                </div>
                <span style="display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; background: <?php echo $is_ready ? '#ecfdf5' : '#fffbeb'; ?>; color: <?php echo $is_ready ? '#047857' : '#b45309'; ?>; border: 1px solid <?php echo $is_ready ? '#6ee7b7' : '#fcd34d'; ?>;">
                    <?php echo $is_ready ? esc_html__( 'Ready', 'product-faq-for-woocommerce' ) : esc_html__( 'Setup Needed', 'product-faq-for-woocommerce' ); ?>
                </span>
            </div>

            <?php if ( ! empty( $providers ) ) : ?>
                <div style="display: flex; flex-wrap: wrap; gap: 8px; margin: 12px 0 16px 0; padding-top: 12px; border-top: 1px solid #f1f5f9;">
                    <?php foreach ( $providers as $prov ) : ?>
                        <div style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 6px; font-size: 12px; background: <?php echo ! empty( $prov['is_connected'] ) ? '#f0fdf4' : '#f8fafc'; ?>; border: 1px solid <?php echo ! empty( $prov['is_connected'] ) ? '#bbf7d0' : '#e2e8f0'; ?>; color: #334155;">
                            <span style="width: 7px; height: 7px; border-radius: 50%; background: <?php echo ! empty( $prov['is_connected'] ) ? '#10b981' : '#94a3b8'; ?>;"></span>
                            <strong><?php echo esc_html( $prov['name'] ); ?>:</strong>
                            <span>
                                <?php
                                if ( ! empty( $prov['is_connected'] ) ) {
                                    esc_html_e( 'Connected', 'product-faq-for-woocommerce' );
                                } elseif ( ! empty( $prov['has_key'] ) && empty( $prov['plugin_active'] ) ) {
                                    esc_html_e( 'Provider Plugin Inactive', 'product-faq-for-woocommerce' );
                                } else {
                                    esc_html_e( 'Not Connected', 'product-faq-for-woocommerce' );
                                }
                                ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                <a href="<?php echo esc_url( $connectors_url ); ?>" class="button button-primary" style="display: inline-flex; align-items: center; gap: 6px;">
                    <span>⚙️</span> <?php esc_html_e( 'Manage WordPress Connectors', 'product-faq-for-woocommerce' ); ?>
                </a>
                <button type="button" class="button button-secondary" id="woo-faq-test-ai-key">
                    <?php esc_html_e( 'Test AI Connection', 'product-faq-for-woocommerce' ); ?>
                </button>
                <?php if ( ! empty( $wp_ai_url ) ) : ?>
                    <a href="<?php echo esc_url( $wp_ai_url ); ?>" class="button button-link" style="text-decoration: none;">
                        <?php esc_html_e( 'WordPress AI Settings ➔', 'product-faq-for-woocommerce' ); ?>
                    </a>
                <?php endif; ?>
            </div>

            <p class="description" style="margin-top: 8px; margin-bottom: 0;">
                <span id="woo-faq-ai-test-result" style="font-weight: 600;"></span>
            </p>
        </div>
        <?php
    }

    /**
     * Display AI Tone selection
     */
    public function AiTone(){
        $tone = get_option('woo_faq_ai_tone', 'sales');
        ?>
            <select name="woo_faq_ai_tone">
                <option value="sales" <?php selected($tone, 'sales'); ?>>Persuasive & Sales-Focused (Objection Buster)</option>
                <option value="friendly" <?php selected($tone, 'friendly'); ?>>Warm, Helpful & Friendly</option>
                <option value="concise" <?php selected($tone, 'concise'); ?>>Concise & Direct (Brief Answers)</option>
                <option value="technical" <?php selected($tone, 'technical'); ?>>Technical, Detailed & Professional</option>
            </select>
        <?php
    }
}

