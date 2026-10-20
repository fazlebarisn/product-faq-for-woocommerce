<?php

namespace Woo\Faq\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FaqHtml{

    function __construct()
    {
        $product_faq = esc_attr( get_option('product_faq') );
        $product_faq_position = esc_attr( get_option('product_faq_position') );

        if('disable'== $product_faq) return;

        // Register universal shortcode [woo_product_faq] for Page Builders (Elementor, Divi, Bricks, Gutenberg)
        add_shortcode( 'woo_product_faq', [ $this, 'renderFaqShortcode' ] );
        
        if( 'after_cart_button' == $product_faq_position ){
            add_action( 'woocommerce_after_add_to_cart_form', [ $this, 'rendeFaqHtml'] );
        }elseif( 'after_meta' == $product_faq_position ){
            add_action( 'woocommerce_product_meta_end', [ $this, 'rendeFaqHtml'] );
        }elseif( 'after_summary' == $product_faq_position ){
            add_action( 'woocommerce_after_single_product_summary', [ $this, 'rendeFaqHtml'] );
        }elseif( 'after_single_product' == $product_faq_position ){
            add_action( 'woocommerce_after_single_product', [ $this, 'rendeFaqHtml'] );
        }elseif( 'product_tab' == $product_faq_position ){
            add_filter( 'woocommerce_product_tabs', [ $this, 'addProductFaqTab' ] );
        }
    }

    /**
     * Resolve FAQs for a product according to hierarchy:
     * 1. If product has FAQs, use product FAQs (ignoring category & subcategory)
     * 2. If Sub category and Main category have FAQs, use Sub category FAQs
     * 3. If Main category has FAQs (and Sub category has none), use Main category FAQs
     * 4. Fallback to other matching taxonomy FAQs (e.g. tags)
     *
     * @param int $product_id
     * @return array Array with 'source' and 'faqs'
     */
    public function get_product_resolved_faqs( $product_id ) {
        // 1. Check Product-Specific FAQs (Highest Priority)
        $product_faqs = get_post_meta( $product_id, 'faq', true );
        $valid_product_faqs = [];

        if ( ! empty( $product_faqs['question'] ) && is_array( $product_faqs['question'] ) ) {
            foreach ( $product_faqs['question'] as $k => $q ) {
                $q = trim( (string) $q );
                $a = trim( (string) ( $product_faqs['answer'][ $k ] ?? '' ) );
                if ( '' !== $q && '' !== $a ) {
                    $valid_product_faqs[] = [
                        'question' => $q,
                        'answer'   => $a,
                    ];
                }
            }
        }

        // Rule 3: If product has FAQ, show product FAQ and ignore main and sub category
        if ( ! empty( $valid_product_faqs ) ) {
            return [
                'source' => 'product',
                'faqs'   => $valid_product_faqs,
            ];
        }

        // 2. Resolve Category & Subcategory Bulk FAQs
        $global_groups = get_option( 'woo_afaq_global_groups', [] );
        if ( empty( $global_groups ) || ! is_array( $global_groups ) ) {
            return [ 'source' => 'none', 'faqs' => [] ];
        }

        // Get product categories
        $cat_terms = wp_get_post_terms( $product_id, 'product_cat' );
        if ( is_wp_error( $cat_terms ) ) {
            $cat_terms = [];
        }

        // Separate sub-categories and main categories + ancestors
        $sub_cat_ids = [];
        $main_cat_ids = [];
        $all_cat_ancestors = [];

        foreach ( $cat_terms as $term ) {
            if ( $term->parent > 0 ) {
                $sub_cat_ids[] = (int) $term->term_id;
                // Fetch all parent/ancestor IDs of this subcategory
                $ancestors = get_ancestors( $term->term_id, 'product_cat', 'taxonomy' );
                if ( ! empty( $ancestors ) ) {
                    $all_cat_ancestors = array_merge( $all_cat_ancestors, array_map( 'intval', $ancestors ) );
                }
            } else {
                $main_cat_ids[] = (int) $term->term_id;
            }
        }

        $all_parent_cat_ids = array_unique( array_merge( $main_cat_ids, $all_cat_ancestors ) );
        $sub_cat_ids = array_unique( $sub_cat_ids );

        // Other taxonomy terms (e.g. product_tag)
        $all_other_term_ids = [];
        $taxonomies = get_object_taxonomies( 'product' );
        foreach ( $taxonomies as $tax ) {
            if ( 'product_cat' === $tax ) {
                continue;
            }
            $terms = wp_get_post_terms( $product_id, $tax, [ 'fields' => 'ids' ] );
            if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
                $all_other_term_ids = array_merge( $all_other_term_ids, array_map( 'intval', $terms ) );
            }
        }

        $sub_cat_faqs = [];
        $main_cat_faqs = [];
        $other_faqs = [];

        foreach ( $global_groups as $group ) {
            $archive_type  = $group['archive_type'] ?? '';
            $archive_terms = array_map( 'intval', (array) ( $group['archive_terms'] ?? [] ) );
            $group_faqs    = $group['faqs'] ?? [];

            $clean_group_faqs = [];
            if ( is_array( $group_faqs ) ) {
                foreach ( $group_faqs as $gf ) {
                    $g_q = trim( (string) ( $gf['question'] ?? '' ) );
                    $g_a = trim( (string) ( $gf['answer'] ?? '' ) );
                    if ( '' !== $g_q && '' !== $g_a ) {
                        $clean_group_faqs[] = [ 'question' => $g_q, 'answer' => $g_a ];
                    }
                }
            }

            if ( empty( $clean_group_faqs ) ) {
                continue;
            }

            if ( 'product_cat' === $archive_type ) {
                // Check if matches sub-category
                $matched_sub = array_intersect( $sub_cat_ids, $archive_terms );
                if ( ! empty( $matched_sub ) ) {
                    $sub_cat_faqs = array_merge( $sub_cat_faqs, $clean_group_faqs );
                }

                // Check if matches main / parent category
                $matched_main = array_intersect( $all_parent_cat_ids, $archive_terms );
                if ( ! empty( $matched_main ) ) {
                    $main_cat_faqs = array_merge( $main_cat_faqs, $clean_group_faqs );
                }
            } else {
                $matched_other = array_intersect( $all_other_term_ids, $archive_terms );
                if ( ! empty( $matched_other ) ) {
                    $other_faqs = array_merge( $other_faqs, $clean_group_faqs );
                }
            }
        }

        // Rule 2: If Sub category has FAQ, show sub category FAQ
        if ( ! empty( $sub_cat_faqs ) ) {
            return [
                'source' => 'sub_category',
                'faqs'   => $sub_cat_faqs,
            ];
        }

        // Rule 1: If main category has FAQ, show main category FAQ
        if ( ! empty( $main_cat_faqs ) ) {
            return [
                'source' => 'main_category',
                'faqs'   => $main_cat_faqs,
            ];
        }

        // Fallback: Other taxonomy (tag/custom) FAQs
        if ( ! empty( $other_faqs ) ) {
            return [
                'source' => 'taxonomy',
                'faqs'   => $other_faqs,
            ];
        }

        return [ 'source' => 'none', 'faqs' => [] ];
    }

    /**
     * Resolve all store/category FAQs when [woo_product_faq] is placed on a general Page (non-product)
     *
     * @param string|int $category Optional category ID or slug filter.
     * @return array Array with 'source' and 'faqs'
     */
    public function get_store_general_faqs( $category = '' ) {
        $collected_faqs = [];
        $seen_questions = [];

        $filter_term_id = 0;
        if ( ! empty( $category ) ) {
            if ( is_numeric( $category ) ) {
                $filter_term_id = intval( $category );
            } else {
                $term_obj = get_term_by( 'slug', sanitize_text_field( $category ), 'product_cat' );
                if ( ! $term_obj ) {
                    $term_obj = get_term_by( 'name', sanitize_text_field( $category ), 'product_cat' );
                }
                if ( $term_obj && ! is_wp_error( $term_obj ) ) {
                    $filter_term_id = (int) $term_obj->term_id;
                }
            }
        }

        // 1. Collect from Bulk / Global FAQ Groups
        $global_groups = get_option( 'woo_afaq_global_groups', [] );
        if ( ! empty( $global_groups ) && is_array( $global_groups ) ) {
            foreach ( $global_groups as $group ) {
                $archive_terms = array_map( 'intval', (array) ( $group['archive_terms'] ?? [] ) );
                if ( $filter_term_id > 0 && ! in_array( $filter_term_id, $archive_terms, true ) ) {
                    continue;
                }

                $group_faqs = $group['faqs'] ?? [];
                if ( is_array( $group_faqs ) ) {
                    foreach ( $group_faqs as $gf ) {
                        $q = trim( (string) ( $gf['question'] ?? '' ) );
                        $a = trim( (string) ( $gf['answer'] ?? '' ) );
                        $q_key = strtolower( $q );
                        if ( '' !== $q && '' !== $a && ! isset( $seen_questions[ $q_key ] ) ) {
                            $seen_questions[ $q_key ] = true;
                            $collected_faqs[] = [
                                'question' => $q,
                                'answer'   => $a,
                            ];
                        }
                    }
                }
            }
        }

        // 2. Also collect from published WooCommerce Products if needed
        $query_args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 50,
            'fields'         => 'ids',
        ];

        if ( $filter_term_id > 0 ) {
            $term_object_ids = get_objects_in_term( $filter_term_id, 'product_cat' );
            if ( empty( $term_object_ids ) || is_wp_error( $term_object_ids ) ) {
                return [
                    'source' => ! empty( $collected_faqs ) ? 'general_shortcode' : 'none',
                    'faqs'   => $collected_faqs,
                ];
            }
            $query_args['include'] = array_map( 'intval', $term_object_ids );
        }

        $product_ids = get_posts( $query_args );
        if ( ! empty( $product_ids ) ) {
            foreach ( $product_ids as $pid ) {
                $p_faqs = get_post_meta( $pid, 'faq', true );
                if ( ! empty( $p_faqs['question'] ) && is_array( $p_faqs['question'] ) ) {
                    foreach ( $p_faqs['question'] as $k => $q ) {
                        $q = trim( (string) $q );
                        $a = trim( (string) ( $p_faqs['answer'][ $k ] ?? '' ) );
                        $q_key = strtolower( $q );
                        if ( '' !== $q && '' !== $a && ! isset( $seen_questions[ $q_key ] ) ) {
                            $seen_questions[ $q_key ] = true;
                            $collected_faqs[] = [
                                'question' => $q,
                                'answer'   => $a,
                            ];
                        }
                    }
                }
            }
        }

        return [
            'source' => ! empty( $collected_faqs ) ? 'general_shortcode' : 'none',
            'faqs'   => $collected_faqs,
        ];
    }

    /**
     * Render FAQ HTML on Single Product Page or via [woo_product_faq] Shortcode
     *
     * @param int|string $custom_product_id Optional custom product ID from shortcode.
     * @param string     $custom_title      Optional custom heading override from shortcode.
     * @param string     $category          Optional category ID or slug from shortcode.
     * @param bool       $is_shortcode      Whether invoked via [woo_product_faq] shortcode.
     */
    public function rendeFaqHtml( $custom_product_id = 0, $custom_title = '', $category = '', $is_shortcode = false ){
        $explicit_product_id = is_numeric( $custom_product_id ) && intval( $custom_product_id ) > 0 ? intval( $custom_product_id ) : 0;
        $product_id          = $explicit_product_id;

        if ( ! $product_id && empty( $category ) ) {
            if ( ! $is_shortcode && ! is_product() ) {
                return;
            }
            $current_id = get_the_ID();
            if ( $current_id && 'product' === get_post_type( $current_id ) ) {
                $product_id = $current_id;
            }
        }

        if ( $product_id > 0 && 'product' === get_post_type( $product_id ) ) {
            $resolved = $this->get_product_resolved_faqs( $product_id );
        } elseif ( $is_shortcode ) {
            // Shortcode placed on a general WordPress Page/Post (like /faq/) or filtered by category
            $resolved   = $this->get_store_general_faqs( $category );
            $product_id = get_the_ID() ? get_the_ID() : 0;
        } else {
            return;
        }

        if ( empty( $resolved['faqs'] ) ) {
            return;
        }

        $faqs = $resolved['faqs'];
        $source = $resolved['source'];

        // Styling
        $faq_heading           = ! empty( $custom_title ) ? esc_attr( $custom_title ) : esc_attr( get_option( 'faq_heading' ) );
        $faq_heading_color     = esc_attr( get_option( 'faq_heading_color' ) );
        $faq_question_color    = esc_attr( get_option( 'faq_question_color' ) );
        $faq_ans_color         = esc_attr( get_option( 'faq_ans_color' ) );
        $faq_heading_font_size = esc_attr( get_option( 'faq_heading_font_size' ) );
        $faq_question_font_size = esc_attr( get_option( 'faq_question_font_size' ) );
        $faq_ans_font_size     = esc_attr( get_option( 'faq_ans_font_size' ) );

        $faq_heading_style  = 'color:' . $faq_heading_color . ';' . 'font-size:' . $faq_heading_font_size;
        $faq_question_style = 'color:' . $faq_question_color . ';' . 'font-size:' . $faq_question_font_size;
        $faq_ans_style      = 'color:' . $faq_ans_color . ';' . 'font-size:' . $faq_ans_font_size;
        ?>
        <div class="container woo-faq-container" data-faq-source="<?php echo esc_attr( $source ); ?>">
            <h2 style="<?php echo esc_attr( $faq_heading_style ); ?>">
                <?php 
                if ( ! empty( $faq_heading ) ) {
                    echo esc_html( $faq_heading );
                } else {
                    echo esc_html__( 'Frequently Asked Questions', 'product-faq-for-woocommerce' );
                }
                ?>
            </h2>
            <?php foreach ( $faqs as $key => $faq ) : 
                $question = $faq['question'] ?? '';
                $answer   = $faq['answer'] ?? '';
            ?>
                <div class="accordion">
                    <div class="accordion-item" data-faq-index="<?php echo esc_attr( $key ); ?>" data-source="<?php echo esc_attr( $source ); ?>">
                        <button type="button" aria-expanded="false">
                            <span class="accordion-title" style="<?php echo esc_attr( $faq_question_style ); ?>">
                                <?php echo esc_html( $question ); ?>
                            </span>
                            <span class="icon" aria-hidden="true"></span>
                        </button>
                        <div class="accordion-content">
                            <p style="<?php echo esc_attr($faq_ans_style); ?>">
                                <?php echo esc_html( $answer ); ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
        $this->renderJsonLdSchema( $faqs, $product_id );
    }

    /**
     * Add custom Product FAQ tab
     *
     * @param array $tabs
     * @return array
     */
    public function addProductFaqTab( $tabs ) {
        if ( ! is_product() ) {
            return $tabs;
        }

        $product_id = get_the_ID();
        $resolved = $this->get_product_resolved_faqs( $product_id );

        if ( empty( $resolved['faqs'] ) ) {
            return $tabs;
        }

        $faq_heading = esc_attr( get_option( 'faq_heading' ) );
        $tab_title = ! empty( $faq_heading ) ? $faq_heading : __( 'FAQs', 'product-faq-for-woocommerce' );

        $tabs['product_faq_tab'] = array(
            'title'    => esc_html( $tab_title ),
            'priority' => 50,
            'callback' => [ $this, 'rendeTabFaqHtml' ]
        );

        return $tabs;
    }

    /**
     * Callback to render tab FAQ content
     */
    public function rendeTabFaqHtml() {
        $this->rendeFaqHtml();
    }

    /**
     * Universal Shortcode [woo_product_faq] for Elementor, Divi, Bricks, Gutenberg, and Custom Templates
     * Usage: [woo_product_faq] or [woo_product_faq id="123" title="Product FAQs"]
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function renderFaqShortcode( $atts = [] ) {
        $atts = shortcode_atts(
            [
                'id'         => 0,
                'product_id' => 0,
                'category'   => '',
                'title'      => '',
            ],
            $atts,
            'woo_product_faq'
        );

        $explicit_product_id = intval( $atts['id'] ) > 0 ? intval( $atts['id'] ) : intval( $atts['product_id'] );

        ob_start();
        $this->rendeFaqHtml(
            $explicit_product_id,
            sanitize_text_field( $atts['title'] ),
            sanitize_text_field( $atts['category'] ),
            true
        );
        return ob_get_clean();
    }

    /**
     * Render Schema.org FAQPage JSON-LD Structured Data for Google Rich Results
     */
    public function renderJsonLdSchema( $faqs, $product_id ) {
        static $rendered_schema = [];

        if ( isset( $rendered_schema[ $product_id ] ) ) {
            return;
        }

        if ( get_option( 'woo_faq_enable_schema', 'enable' ) === 'disable' ) {
            return;
        }

        if ( empty( $faqs ) || ! is_array( $faqs ) ) {
            return;
        }

        $rendered_schema[ $product_id ] = true;

        $schema_items = [];
        foreach ( $faqs as $faq ) {
            $q = $faq['question'] ?? '';
            $a = $faq['answer'] ?? '';
            if ( ! empty( $q ) && ! empty( $a ) ) {
                $schema_items[] = [
                    '@type'          => 'Question',
                    'name'           => wp_strip_all_tags( $q ),
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => wp_strip_all_tags( $a )
                    ]
                ];
            }
        }

        if ( ! empty( $schema_items ) ) {
            $schema_data = [
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => $schema_items
            ];
            echo "\n<!-- Product FAQ for WooCommerce - Google SEO FAQ Schema -->\n";
            echo '<script type="application/ld+json">' . wp_json_encode( $schema_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
        }
    }
}