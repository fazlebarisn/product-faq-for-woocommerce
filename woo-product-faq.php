<?php
/**
 * Plugin Name:          Product FAQ for WooCommerce — Tab, Accordion & AI FAQ Generator
 * Requires Plugins:     woocommerce
 * Plugin URI:           https://fbsfusion.com/plugins/
 * Description:          Boost WooCommerce conversions and Google Rich Results with responsive Product FAQ tabs, Gutenberg blocks, rich HTML answers, FAQPage JSON-LD schema, and spec-aware AI FAQ generation.
 * Version:              1.7.0
 * Author:               Fazle Bari
 * Author URI:           https://fbsfusion.com/
 * Requires PHP:         7.3
 * Tested up to:         7.1
 * WC requires at least: 5.0
 * WC tested up to:      11.1.0
 * License:              GPL v2 or later
 * License URI:          https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:          product-faq-for-woocommerce
 * Domain Path:          /i18n/languages/
 *
 * @package              woofaq
 */

defined( 'ABSPATH' ) || die( 'Nice Try!' );

// Include helper functions.
if ( file_exists( __DIR__ . '/functions.php' ) ) {
	require_once __DIR__ . '/functions.php';
}

// Include Composer PSR-4 autoloader.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

/**
 * Main plugin bootstrap class.
 */
final class WooFaq {

	/**
	 * Plugin version.
	 */
	const version = '1.7.0';

	/**
	 * Class constructor.
	 */
	private function __construct() {
		$this->defineConstants();

		add_action( 'before_woocommerce_init', [ $this, 'product_faq_hpos' ] );

		register_activation_hook( __FILE__, [ $this, 'activate' ] );

		add_action( 'plugins_loaded', [ $this, 'maybeMigrateLegacyDefaults' ], 5 );
		add_action( 'plugins_loaded', [ $this, 'initPlugin' ] );
	}

	/**
	 * Preserve legacy visual defaults (`disable` for count badge & auto-open first FAQ on pre-1.6.0 stores,
	 * and `after_single_product` position on pre-1.7.0 stores that never saved settings) so existing stores
	 * never experience unexpected layout shifts upon updating.
	 *
	 * @since 1.6.2
	 * @return void
	 */
	public function maybeMigrateLegacyDefaults() {
		$v162_done = ( '1' === get_option( 'woo_faq_v162_migrated' ) );
		$v170_done = ( '1' === get_option( 'woo_faq_v170_migrated' ) );

		if ( $v162_done && $v170_done ) {
			return;
		}

		$installed = get_option( 'woo_faq_installed', false );
		$prev_ver  = (string) get_option( 'woo_faq_version', '' );

		if ( ! $v162_done ) {
			$is_pre_160_store = ( false !== $installed && ( '' === $prev_ver || version_compare( $prev_ver, '1.6.0', '<' ) ) );

			if ( $is_pre_160_store ) {
				if ( false === get_option( 'woo_faq_open_first', false ) ) {
					update_option( 'woo_faq_open_first', 'disable' );
				}
				if ( false === get_option( 'woo_faq_show_count_badge', false ) ) {
					update_option( 'woo_faq_show_count_badge', 'disable' );
				}
			}

			update_option( 'woo_faq_v162_migrated', '1', false );
		}

		if ( ! $v170_done ) {
			$is_pre_170_store = ( false !== $installed && ( '' === $prev_ver || version_compare( $prev_ver, '1.7.0', '<' ) ) );

			if ( $is_pre_170_store && false === get_option( 'product_faq_position', false ) ) {
				update_option( 'product_faq_position', 'after_single_product' );
			}

			update_option( 'woo_faq_version', WOO_FAQ_VERSION );
			update_option( 'woo_faq_v170_migrated', '1', false );
		}
	}

	/**
	 * Declare compatibility with WooCommerce High-Performance Order Storage (HPOS)
	 * and Cart/Checkout Blocks.
	 *
	 * @since 1.1.2
	 * @author Fazle Bari
	 */
	public function product_faq_hpos() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
		}
	}

	/**
	 * Initialize singleton instance.
	 *
	 * @return \WooFaq
	 */
	public static function init() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Define plugin constants.
	 *
	 * @return void
	 */
	public function defineConstants() {
		define( 'WOO_FAQ_VERSION', self::version );
		define( 'WOO_FAQ_FILE', __FILE__ );
		define( 'WOO_FAQ_PATH', __DIR__ );
		define( 'WOO_FAQ_URL', plugins_url( '', WOO_FAQ_FILE ) );
		define( 'WOO_FAQ_ASSETS', WOO_FAQ_URL . '/assets' );
		define( 'WOO_FAQ_BASENAME', plugin_basename( __FILE__ ) );
	}

	/**
	 * Initialize plugin modules.
	 *
	 * @return void
	 */
	public function initPlugin() {
		// Register Gutenberg block in all contexts (Editor, REST API ServerSideRender, and Frontend).
		if ( class_exists( '\Woo\Faq\Blocks' ) ) {
			new \Woo\Faq\Blocks();
		}

		if ( is_admin() ) {
			new \Woo\Faq\Admin();
		} else {
			new \Woo\Faq\Frontend();
		}
	}

	/**
	 * Handle plugin activation metadata.
	 *
	 * @return void
	 */
	public function activate() {
		$installed = get_option( 'woo_faq_installed' );
		if ( ! $installed ) {
			update_option( 'woo_faq_installed', time() );
			if ( false === get_option( 'product_faq_position', false ) ) {
				update_option( 'product_faq_position', 'product_tab' );
			}
			update_option( 'woo_faq_v162_migrated', '1', false );
			update_option( 'woo_faq_v170_migrated', '1', false );
		}

		update_option( 'woo_faq_version', WOO_FAQ_VERSION );
	}
}

/**
 * Initialize the main plugin instance.
 *
 * @return \WooFaq
 */
function wooFaq() {
	return WooFaq::init();
}

// Kick off the plugin.
wooFaq();
