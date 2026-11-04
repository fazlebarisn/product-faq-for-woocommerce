<?php
/**
 * Plugin Name:          Product FAQ for WooCommerce — Tab, Accordion & AI FAQ Generator
 * Requires Plugins:     woocommerce
 * Plugin URI:           https://fbsfusion.com/plugins/
 * Description:          Boost WooCommerce conversions and Google Rich Results with responsive Product FAQ tabs, Gutenberg blocks, rich HTML answers, FAQPage JSON-LD schema, and spec-aware AI FAQ generation.
 * Version:              1.6.1
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
	const version = '1.6.1';

	/**
	 * Class constructor.
	 */
	private function __construct() {
		$this->defineConstants();

		add_action( 'before_woocommerce_init', [ $this, 'product_faq_hpos' ] );

		register_activation_hook( __FILE__, [ $this, 'activate' ] );

		add_action( 'plugins_loaded', [ $this, 'initPlugin' ] );
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
