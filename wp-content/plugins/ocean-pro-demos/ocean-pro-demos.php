<?php
/**
 * Plugin Name:			Ocean Pro Demos
 * Description:			Import the OceanWP pro demos, widgets and customizer settings with one click.
 * Version:				1.1.0
 * Author:				OceanWP
 * Author URI:			https://oceanwp.org/
 * Requires at least:	4.0.0
 * Tested up to:		5.1
 *
 * Text Domain: ocean-pro-demos
 * Domain Path: /languages/
 *
 * @package Ocean_Pro_Demos
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the main instance of Ocean_Pro_Demos to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Ocean_Pro_Demos
 */
function Ocean_Pro_Demos() {
	return Ocean_Pro_Demos::instance();
} // End Ocean_Pro_Demos()

Ocean_Pro_Demos();

/**
 * Main Ocean_Pro_Demos Class
 *
 * @class Ocean_Pro_Demos
 * @version	1.0.0
 * @since 1.0.0
 * @package	Ocean_Pro_Demos
 */
final class Ocean_Pro_Demos {
	/**
	 * Ocean_Pro_Demos The single instance of Ocean_Pro_Demos.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct( $widget_areas = array() ) {
		$this->token 			= 'ocean-pro-demos';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.1.0';

		define( 'OPD_PATH', $this->plugin_path );
		define( 'OPD_URL', $this->plugin_url );

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'updater' ), 1 );

		// Add pro demos in the demos page
		add_filter( 'owp_demos_data', array( $this, 'get_pro_demos' ) );
	}

	/**
	 * Initialize License Updater.
	 * Load Updater initialize.
	 * @return void
	 */
	public function updater() {

		// Plugin Updater Code
		if ( class_exists( 'OceanWP_Plugin_Updater' ) ) {
			$license	= new OceanWP_Plugin_Updater( __FILE__, 'Pro Demos', $this->version, 'OceanWP' );
		}
	}

	/**
	 * Main Ocean_Pro_Demos Instance
	 *
	 * Ensures only one instance of Ocean_Pro_Demos is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Ocean_Pro_Demos()
	 * @return Main Ocean_Pro_Demos instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'ocean-pro-demos', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Installation.
	 * Runs on activation. Logs the version number and assigns a notice message to a WordPress option.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
	}

	/**
	 * Log the plugin version number.
	 * @access  private
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		// Log the version number.
		update_option( $this->token . '-version', $this->version );
	}

	/**
	 * Get pro demos.
	 * 
	 * @since   1.0.0
	 */
	public static function get_pro_demos( $data ) {

		// Demos url
		$url = 'https://demos.oceanwp.org/';

		$data['bakery'] = array(
			'categories'        => array( 'eCommerce' ),
			'xml_file'     		=> $url . 'bakery/sample-data.xml',
			'theme_settings' 	=> $url . 'bakery/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'bakery/widgets.wie',
			'form_file'  		=> $url . 'bakery/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '7',
			'elementor_width'  	=> '1140',
			'is_shop'  			=> true,
			'woo_image_size'  	=> '600',
			'woo_thumb_size' 	=> '300',
			'woo_crop_width'  	=> '1',
			'woo_crop_height' 	=> '1',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-product-sharing',
						'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
						'name'  	=> 'Ocean Product Sharing',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
					array(
						'slug'  	=> 'woocommerce',
						'init'  	=> 'woocommerce/woocommerce.php',
						'name'  	=> 'WooCommerce',
					),
					array(
						'slug'  	=> 'smart-slider-3',
						'init'  	=> 'smart-slider-3/smart-slider-3.php',
						'name'  	=> 'Smart Slider 3',
					),
					array(
						'slug'  	=> 'woo-gutenberg-products-block',
						'init'  	=> 'woo-gutenberg-products-block/woocommerce-gutenberg-products-block.php',
						'name'  	=> 'WooCommerce Blocks',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-woo-popup',
						'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
						'name' 		=> 'Ocean Woo Popup',
					),
					array(
						'slug' 		=> 'ocean-popup-login',
						'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
						'name' 		=> 'Ocean Popup Login',
					),
				),
			),
		);

		$data['corporate'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'corporate/sample-data.xml',
			'theme_settings' 	=> $url . 'corporate/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'corporate/widgets.wie',
			'form_file'  		=> $url . 'corporate/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '7',
			'elementor_width'  	=> '1140',
			'is_shop'  			=> true,
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-portfolio',
						'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
						'name' 		=> 'Ocean Portfolio',
					),
					array(
						'slug' 		=> 'ocean-popup-login',
						'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
						'name' 		=> 'Ocean Popup Login',
					),
				),
			),
		);

		$data['destination'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'destination/sample-data.xml',
			'theme_settings' 	=> $url . 'destination/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'destination/widgets.wie',
			'form_file'  		=> $url . 'destination/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '7',
			'elementor_width'  	=> '1140',
			'is_shop'  			=> true,
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['lauren'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'lauren/sample-data.xml',
			'theme_settings' 	=> $url . 'lauren/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'lauren/widgets.wie',
			'form_file'  		=> $url . 'lauren/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '7',
			'elementor_width'  	=> '1140',
			'is_shop'  			=> true,
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-portfolio',
						'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
						'name' 		=> 'Ocean Portfolio',
					),
				),
			),
		);

		$data['onestore'] = array(
			'categories'        => array( 'eCommerce' ),
			'xml_file'     		=> $url . 'onestore/sample-data.xml',
			'theme_settings' 	=> $url . 'onestore/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'onestore/widgets.wie',
			'form_file'  		=> $url . 'onestore/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '7',
			'elementor_width'  	=> '1140',
			'is_shop'  			=> true,
			'woo_image_size'  	=> '600',
			'woo_thumb_size' 	=> '300',
			'woo_crop_width'  	=> '1',
			'woo_crop_height' 	=> '1',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-product-sharing',
						'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
						'name'  	=> 'Ocean Product Sharing',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'ocean-posts-slider',
						'init'  	=> 'ocean-posts-slider/ocean-posts-slider.php',
						'name'  	=> 'Ocean Posts Slider',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
					array(
						'slug'  	=> 'woocommerce',
						'init'  	=> 'woocommerce/woocommerce.php',
						'name'  	=> 'WooCommerce',
					),
					array(
						'slug'  	=> 'ti-woocommerce-wishlist',
						'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
						'name'  	=> 'WooCommerce Wishlist',
					),
					array(
						'slug'  	=> 'smart-slider-3',
						'init'  	=> 'smart-slider-3/smart-slider-3.php',
						'name'  	=> 'Smart Slider 3',
					),
					array(
						'slug'  	=> 'woo-gutenberg-products-block',
						'init'  	=> 'woo-gutenberg-products-block/woocommerce-gutenberg-products-block.php',
						'name'  	=> 'WooCommerce Blocks',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-hooks',
						'init'  	=> 'ocean-hooks/ocean-hooks.php',
						'name' 		=> 'Ocean Hooks',
					),
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-woo-popup',
						'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
						'name' 		=> 'Ocean Woo Popup',
					),
					array(
						'slug' 		=> 'ocean-popup-login',
						'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
						'name' 		=> 'Ocean Popup Login',
					),
				),
			),
		);

		$data['outfits'] = array(
			'categories'        => array( 'eCommerce' ),
			'xml_file'     		=> $url . 'outfits/sample-data.xml',
			'theme_settings' 	=> $url . 'outfits/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'outfits/widgets.wie',
			'form_file'  		=> $url . 'outfits/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '7',
			'elementor_width'  	=> '1140',
			'is_shop'  			=> true,
			'woo_image_size'  	=> '600',
			'woo_thumb_size' 	=> '300',
			'woo_crop_width'  	=> '1',
			'woo_crop_height' 	=> '1',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-product-sharing',
						'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
						'name'  	=> 'Ocean Product Sharing',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'ocean-posts-slider',
						'init'  	=> 'ocean-posts-slider/ocean-posts-slider.php',
						'name'  	=> 'Ocean Posts Slider',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
					array(
						'slug'  	=> 'woocommerce',
						'init'  	=> 'woocommerce/woocommerce.php',
						'name'  	=> 'WooCommerce',
					),
					array(
						'slug'  	=> 'ti-woocommerce-wishlist',
						'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
						'name'  	=> 'WooCommerce Wishlist',
					),
					array(
						'slug'  	=> 'smart-slider-3',
						'init'  	=> 'smart-slider-3/smart-slider-3.php',
						'name'  	=> 'Smart Slider 3',
					),
					array(
						'slug'  	=> 'woo-gutenberg-products-block',
						'init'  	=> 'woo-gutenberg-products-block/woocommerce-gutenberg-products-block.php',
						'name'  	=> 'WooCommerce Blocks',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-hooks',
						'init'  	=> 'ocean-hooks/ocean-hooks.php',
						'name' 		=> 'Ocean Hooks',
					),
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-footer-callout',
						'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
						'name' 		=> 'Ocean Footer Callout',
					),
					array(
						'slug' 		=> 'ocean-woo-popup',
						'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
						'name' 		=> 'Ocean Woo Popup',
					),
					array(
						'slug' 		=> 'ocean-popup-login',
						'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
						'name' 		=> 'Ocean Popup Login',
					),
				),
			),
		);

		$data['simply'] = array(
			'categories'        => array( 'Blog' ),
			'xml_file'     		=> $url . 'simply/sample-data.xml',
			'theme_settings' 	=> $url . 'simply/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'simply/widgets.wie',
			'form_file'  		=> $url . 'simply/form.json',
			'blog_title'  		=> 'Home',
			'posts_to_show'  	=> '7',
			'elementor_width'  	=> '1140',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug'  	=> 'ocean-portfolio',
						'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
						'name'  	=> 'Ocean Portfolio',
					),
				),
			),
		);

		$data['studio'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'studio/sample-data.xml',
			'theme_settings' 	=> $url . 'studio/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'studio/widgets.wie',
			'form_file'  		=> $url . 'studio/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '7',
			'elementor_width'  	=> '1140',
			'is_shop'  			=> true,
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-portfolio',
						'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
						'name' 		=> 'Ocean Portfolio',
					),
				),
			),
		);

		$data['tech'] = array(
			'categories'        => array( 'Blog' ),
			'xml_file'     		=> $url . 'tech/sample-data.xml',
			'theme_settings' 	=> $url . 'tech/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'tech/widgets.wie',
			'form_file'  		=> $url . 'tech/form.json',
			'blog_title'  		=> 'Home',
			'posts_to_show'  	=> '10',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-modal-window',
						'init'  	=> 'ocean-modal-window/ocean-modal-window.php',
						'name'  	=> 'Ocean Modal Window',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'ocean-posts-slider',
						'init'  	=> 'ocean-posts-slider/ocean-posts-slider.php',
						'name'  	=> 'Ocean Posts Slider',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug'  	=> 'ocean-popup-login',
						'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
						'name'  	=> 'Ocean Popup Login',
					),
				),
			),
		);

		$data['simpleblog'] = array(
			'categories'        => array( 'Blog' ),
			'xml_file'     		=> $url . 'simpleblog/sample-data.xml',
			'theme_settings' 	=> $url . 'simpleblog/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'simpleblog/widgets.wie',
			'form_file'  		=> $url . 'simpleblog/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '7',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'ocean-posts-slider',
						'init'  	=> 'ocean-posts-slider/ocean-posts-slider.php',
						'name'  	=> 'Ocean Posts Slider',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['agency'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'agency/sample-data.xml',
			'theme_settings' 	=> $url . 'agency/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'agency/widgets.wie',
			'form_file'  		=> $url . 'agency/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '7',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-portfolio',
						'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
						'name' 		=> 'Ocean Portfolio',
					),
				),
			),
		);

		$data['barber'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'barber/sample-data.xml',
			'theme_settings' 	=> $url . 'barber/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'barber/widgets.wie',
			'form_file'  		=> $url . 'barber/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['bright'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'bright/sample-data.xml',
			'theme_settings' 	=> $url . 'bright/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'bright/widgets.wie',
			'form_file'  		=> $url . 'bright/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '6',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-portfolio',
						'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
						'name' 		=> 'Ocean Portfolio',
					),
				),
			),
		);

		$data['charity'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'charity/sample-data.xml',
			'theme_settings' 	=> $url . 'charity/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'charity/widgets.wie',
			'form_file'  		=> $url . 'charity/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '9',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'give',
						'init'  	=> 'give/give.php',
						'name'  	=> 'Give - Donation Plugin',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['computer'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'computer/sample-data.xml',
			'theme_settings' 	=> $url . 'computer/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'computer/widgets.wie',
			'form_file'  		=> $url . 'computer/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['construction'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'construction/sample-data.xml',
			'theme_settings' 	=> $url . 'construction/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'construction/widgets.wie',
			'form_file'  		=> $url . 'construction/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-portfolio',
						'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
						'name' 		=> 'Ocean Portfolio',
					),
					array(
						'slug' 		=> 'ocean-sticky-footer',
						'init'  	=> 'ocean-sticky-footer/ocean-sticky-footer.php',
						'name' 		=> 'Ocean Sticky Footer',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['coffee'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'coffee/sample-data.xml',
			'theme_settings' 	=> $url . 'coffee/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'coffee/widgets.wie',
			'form_file'  		=> $url . 'coffee/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-instagram',
						'init'  	=> 'ocean-instagram/ocean-instagram.php',
						'name' 		=> 'Ocean Instagram',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['design'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'design/sample-data.xml',
			'theme_settings' 	=> $url . 'design/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'design/widgets.wie',
			'form_file'  		=> $url . 'design/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '7',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-portfolio',
						'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
						'name' 		=> 'Ocean Portfolio',
					),
				),
			),
		);

		$data['fitness'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'fitness/sample-data.xml',
			'theme_settings' 	=> $url . 'fitness/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'fitness/widgets.wie',
			'form_file'  		=> $url . 'fitness/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '7',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['florist'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'florist/sample-data.xml',
			'theme_settings' 	=> $url . 'florist/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'florist/widgets.wie',
			'form_file'  		=> $url . 'florist/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '4',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-portfolio',
						'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
						'name' 		=> 'Ocean Portfolio',
					),
				),
			),
		);

		$data['freelance'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'freelance/sample-data.xml',
			'theme_settings' 	=> $url . 'freelance/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'freelance/widgets.wie',
			'form_file'  		=> $url . 'freelance/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['hairdresser'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'hairdresser/sample-data.xml',
			'theme_settings' 	=> $url . 'hairdresser/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'hairdresser/widgets.wie',
			'form_file'  		=> $url . 'hairdresser/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['hosting'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'hosting/sample-data.xml',
			'theme_settings' 	=> $url . 'hosting/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'hosting/widgets.wie',
			'form_file'  		=> $url . 'hosting/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-modal-window',
						'init'  	=> 'ocean-modal-window/ocean-modal-window.php',
						'name'  	=> 'Ocean Modal Window',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['interior'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'interior/sample-data.xml',
			'theme_settings' 	=> $url . 'interior/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'interior/widgets.wie',
			'form_file'  		=> $url . 'interior/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['inspire'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'inspire/sample-data.xml',
			'theme_settings' 	=> $url . 'inspire/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'inspire/widgets.wie',
			'form_file'  		=> $url . 'inspire/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '7',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-portfolio',
						'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
						'name' 		=> 'Ocean Portfolio',
					),
				),
			),
		);

		$data['learn'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'learn/sample-data.xml',
			'theme_settings' 	=> $url . 'learn/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'learn/widgets.wie',
			'form_file'  		=> $url . 'learn/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['nails'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'nails/sample-data.xml',
			'theme_settings' 	=> $url . 'nails/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'nails/widgets.wie',
			'form_file'  		=> $url . 'nails/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-footer-callout',
						'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
						'name' 		=> 'Ocean Footer Callout',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['medical'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'medical/sample-data.xml',
			'theme_settings' 	=> $url . 'medical/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'medical/widgets.wie',
			'form_file'  		=> $url . 'medical/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-footer-callout',
						'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
						'name' 		=> 'Ocean Footer Callout',
					),
					array(
						'slug' 		=> 'ocean-side-panel',
						'init'  	=> 'ocean-side-panel/ocean-side-panel.php',
						'name' 		=> 'Ocean Side Panel',
					),
				),
			),
		);

		$data['music'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'music/sample-data.xml',
			'theme_settings' 	=> $url . 'music/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'music/widgets.wie',
			'form_file'  		=> $url . 'music/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['photo'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'photo/sample-data.xml',
			'theme_settings' 	=> $url . 'photo/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'photo/widgets.wie',
			'form_file'  		=> $url . 'photo/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '10',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['photography'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'photography/sample-data.xml',
			'theme_settings' 	=> $url . 'photography/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'photography/widgets.wie',
			'form_file'  		=> $url . 'photography/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '6',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-portfolio',
						'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
						'name' 		=> 'Ocean Portfolio',
					),
				),
			),
		);

		$data['pizza'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'pizza/sample-data.xml',
			'theme_settings' 	=> $url . 'pizza/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'pizza/widgets.wie',
			'form_file'  		=> $url . 'pizza/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '6',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['scuba'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'scuba/sample-data.xml',
			'theme_settings' 	=> $url . 'scuba/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'scuba/widgets.wie',
			'form_file'  		=> $url . 'scuba/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '9',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-full-screen',
						'init'  	=> 'ocean-full-screen/ocean-full-screen.php',
						'name' 		=> 'Ocean Full Screen',
					),
				),
			),
		);

		$data['skate'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'skate/sample-data.xml',
			'theme_settings' 	=> $url . 'skate/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'skate/widgets.wie',
			'form_file'  		=> $url . 'skate/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '6',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['surfing'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'surfing/sample-data.xml',
			'theme_settings' 	=> $url . 'surfing/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'surfing/widgets.wie',
			'form_file'  		=> $url . 'surfing/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['veggie'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'veggie/sample-data.xml',
			'theme_settings' 	=> $url . 'veggie/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'veggie/widgets.wie',
			'form_file'  		=> $url . 'veggie/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['wedding'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'wedding/sample-data.xml',
			'theme_settings' 	=> $url . 'wedding/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'wedding/widgets.wie',
			'form_file'  		=> $url . 'wedding/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '5',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-popup-login',
						'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
						'name' 		=> 'Ocean Popup Login',
					),
					array(
						'slug' 		=> 'ocean-portfolio',
						'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
						'name' 		=> 'Ocean Portfolio',
					),
				),
			),
		);

		$data['consulting'] = array(
			'categories'        => array( 'One Page' ),
			'xml_file'     		=> $url . 'consulting/sample-data.xml',
			'theme_settings' 	=> $url . 'consulting/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'consulting/widgets.wie',
			'form_file'  		=> $url . 'consulting/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['spa'] = array(
			'categories'        => array( 'One Page' ),
			'xml_file'     		=> $url . 'spa/sample-data.xml',
			'theme_settings' 	=> $url . 'spa/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'spa/widgets.wie',
			'form_file'  		=> $url . 'spa/form.json',
			'home_title'  		=> 'Home',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['restaurant'] = array(
			'categories'        => array( 'One Page' ),
			'xml_file'     		=> $url . 'restaurant/sample-data.xml',
			'theme_settings' 	=> $url . 'restaurant/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'restaurant/widgets.wie',
			'form_file'  		=> $url . 'restaurant/form.json',
			'home_title'  		=> 'Home',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
				),
			),
		);

		$data['chocolate'] = array(
			'categories'        => array( 'One Page' ),
			'xml_file'     		=> $url . 'chocolate/sample-data.xml',
			'theme_settings' 	=> $url . 'chocolate/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'chocolate/widgets.wie',
			'form_file'  		=> $url . 'chocolate/form.json',
			'home_title'  		=> 'Home',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-sticky-footer',
						'init'  	=> 'ocean-sticky-footer/ocean-sticky-footer.php',
						'name' 		=> 'Ocean Sticky Footer',
					),
				),
			),
		);

		$data['hotel'] = array(
			'categories'        => array( 'One Page' ),
			'xml_file'     		=> $url . 'hotel/sample-data.xml',
			'theme_settings' 	=> $url . 'hotel/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'hotel/widgets.wie',
			'form_file'  		=> $url . 'hotel/form.json',
			'home_title'  		=> 'Home',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['makeup'] = array(
			'categories'        => array( 'One Page' ),
			'xml_file'     		=> $url . 'makeup/sample-data.xml',
			'theme_settings' 	=> $url . 'makeup/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'makeup/widgets.wie',
			'form_file'  		=> $url . 'makeup/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['portfolio'] = array(
			'categories'        => array( 'One Page' ),
			'xml_file'     		=> $url . 'portfolio/sample-data.xml',
			'theme_settings' 	=> $url . 'portfolio/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'portfolio/widgets.wie',
			'form_file'  		=> $url . 'portfolio/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '10',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-portfolio',
						'init'  	=> 'ocean-portfolio/ocean-portfolio.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-side-panel',
						'init'  	=> 'ocean-side-panel/ocean-side-panel.php',
						'name' 		=> 'Ocean Side Panel',
					),
				),
			),
		);

		$data['skyscraper'] = array(
			'categories'        => array( 'One Page' ),
			'xml_file'     		=> $url . 'skyscraper/sample-data.xml',
			'theme_settings' 	=> $url . 'skyscraper/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'skyscraper/widgets.wie',
			'form_file'  		=> $url . 'skyscraper/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '6',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['book'] = array(
			'categories'        => array( 'eCommerce' ),
			'xml_file'     		=> $url . 'book/sample-data.xml',
			'theme_settings' 	=> $url . 'book/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'book/widgets.wie',
			'form_file'  		=> $url . 'book/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '4',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
					array(
						'slug'  	=> 'easy-digital-downloads',
						'init'  	=> 'easy-digital-downloads/easy-digital-downloads.php',
						'name'  	=> 'Easy Digital Downloads',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-popup-login',
						'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
						'name' 		=> 'Ocean Popup Login',
					),
					array(
						'slug' 		=> 'ocean-woo-popup',
						'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
						'name' 		=> 'Ocean Woo Popup',
					),
				),
			),
		);

		$data['cycle'] = array(
			'categories'        => array( 'eCommerce' ),
			'xml_file'     		=> $url . 'cycle/sample-data.xml',
			'theme_settings' 	=> $url . 'cycle/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'cycle/widgets.wie',
			'form_file'  		=> $url . 'cycle/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1260',
			'is_shop'  			=> true,
			'woo_image_size'  	=> '600',
			'woo_thumb_size' 	=> '300',
			'woo_crop_width'  	=> '1',
			'woo_crop_height' 	=> '1',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-product-sharing',
						'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
						'name'  	=> 'Ocean Product Sharing',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
					array(
						'slug'  	=> 'woocommerce',
						'init'  	=> 'woocommerce/woocommerce.php',
						'name'  	=> 'WooCommerce',
					),
					array(
						'slug'  	=> 'smart-slider-3',
						'init'  	=> 'smart-slider-3/smart-slider-3.php',
						'name'  	=> 'Smart Slider 3',
					),
					array(
						'slug'  	=> 'woo-gutenberg-products-block',
						'init'  	=> 'woo-gutenberg-products-block/woocommerce-gutenberg-products-block.php',
						'name'  	=> 'WooCommerce Blocks',
					),
					array(
						'slug'  	=> 'ti-woocommerce-wishlist',
						'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
						'name'  	=> 'WooCommerce Wishlist',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-woo-popup',
						'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
						'name' 		=> 'Ocean Woo Popup',
					),
				),
			),
		);

		$data['school'] = array(
			'categories'        => array( 'Corporate' ),
			'xml_file'     		=> $url . 'school/sample-data.xml',
			'theme_settings' 	=> $url . 'school/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'school/widgets.wie',
			'form_file'  		=> $url . 'school/form.json',
			'home_title'  		=> 'Home',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'lifterlms',
						'init'  	=> 'lifterlms/lifterlms.php',
						'name'  	=> 'LifterLMS',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['streetfood'] = array(
			'categories'        => array( 'eCommerce' ),
			'xml_file'     		=> $url . 'streetfood/sample-data.xml',
			'theme_settings' 	=> $url . 'streetfood/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'streetfood/widgets.wie',
			'form_file'  		=> $url . 'streetfood/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '7',
			'elementor_width'  	=> '1260',
			'is_shop'  			=> true,
			'woo_image_size'  	=> '600',
			'woo_thumb_size' 	=> '300',
			'woo_crop_width'  	=> '1',
			'woo_crop_height' 	=> '1',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-product-sharing',
						'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
						'name'  	=> 'Ocean Product Sharing',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
					array(
						'slug'  	=> 'woocommerce',
						'init'  	=> 'woocommerce/woocommerce.php',
						'name'  	=> 'WooCommerce',
					),
					array(
						'slug'  	=> 'woo-gutenberg-products-block',
						'init'  	=> 'woo-gutenberg-products-block/woocommerce-gutenberg-products-block.php',
						'name'  	=> 'WooCommerce Blocks',
					),
					array(
						'slug'  	=> 'ti-woocommerce-wishlist',
						'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
						'name'  	=> 'WooCommerce Wishlist',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-popup-login',
						'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
						'name' 		=> 'Ocean Popup Login',
					),
					array(
						'slug' 		=> 'ocean-woo-popup',
						'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
						'name' 		=> 'Ocean Woo Popup',
					),
				),
			),
		);

		$data['jewelry'] = array(
			'categories'        => array( 'eCommerce' ),
			'xml_file'     		=> $url . 'jewelry/sample-data.xml',
			'theme_settings' 	=> $url . 'jewelry/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'jewelry/widgets.wie',
			'form_file'  		=> $url . 'jewelry/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1260',
			'is_shop'  			=> true,
			'woo_image_size'  	=> '600',
			'woo_thumb_size' 	=> '300',
			'woo_crop_width'  	=> '1',
			'woo_crop_height' 	=> '1',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-product-sharing',
						'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
						'name'  	=> 'Ocean Product Sharing',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
					array(
						'slug'  	=> 'woocommerce',
						'init'  	=> 'woocommerce/woocommerce.php',
						'name'  	=> 'WooCommerce',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-footer-callout',
						'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
						'name' 		=> 'Ocean Footer Callout',
					),
					array(
						'slug' 		=> 'ocean-woo-popup',
						'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
						'name' 		=> 'Ocean Woo Popup',
					),
				),
			),
		);

		$data['shoes'] = array(
			'categories'        => array( 'eCommerce' ),
			'xml_file'     		=> $url . 'shoes/sample-data.xml',
			'theme_settings' 	=> $url . 'shoes/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'shoes/widgets.wie',
			'form_file'  		=> $url . 'shoes/form.json',
			'home_title'  		=> 'Home',
			'elementor_width'  	=> '1320',
			'is_shop'  			=> true,
			'woo_image_size'  	=> '600',
			'woo_thumb_size' 	=> '316',
			'woo_crop_width'  	=> '4',
			'woo_crop_height' 	=> '5',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-product-sharing',
						'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
						'name'  	=> 'Ocean Product Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
					array(
						'slug'  	=> 'woocommerce',
						'init'  	=> 'woocommerce/woocommerce.php',
						'name'  	=> 'WooCommerce',
					),
					array(
						'slug'  	=> 'ti-woocommerce-wishlist',
						'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
						'name'  	=> 'WooCommerce Wishlist',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-footer-callout',
						'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
						'name' 		=> 'Ocean Footer Callout',
					),
					array(
						'slug' 		=> 'ocean-popup-login',
						'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
						'name' 		=> 'Ocean Popup Login',
					),
				),
			),
		);

		$data['flowers'] = array(
			'categories'        => array( 'eCommerce' ),
			'xml_file'     		=> $url . 'flowers/sample-data.xml',
			'theme_settings' 	=> $url . 'flowers/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'flowers/widgets.wie',
			'form_file'  		=> $url . 'flowers/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'is_shop'  			=> true,
			'woo_image_size'  	=> '478',
			'woo_thumb_size' 	=> '294',
			'woo_crop_width'  	=> '4',
			'woo_crop_height' 	=> '5',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-product-sharing',
						'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
						'name'  	=> 'Ocean Product Sharing',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
					array(
						'slug'  	=> 'woocommerce',
						'init'  	=> 'woocommerce/woocommerce.php',
						'name'  	=> 'WooCommerce',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['garden'] = array(
			'categories'        => array( 'eCommerce' ),
			'xml_file'     		=> $url . 'garden/sample-data.xml',
			'theme_settings' 	=> $url . 'garden/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'garden/widgets.wie',
			'form_file'  		=> $url . 'garden/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '3',
			'elementor_width'  	=> '1220',
			'is_shop'  			=> true,
			'woo_image_size'  	=> '441',
			'woo_thumb_size' 	=> '270',
			'woo_crop_width'  	=> '4',
			'woo_crop_height' 	=> '5',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-product-sharing',
						'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
						'name'  	=> 'Ocean Product Sharing',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
					array(
						'slug'  	=> 'woocommerce',
						'init'  	=> 'woocommerce/woocommerce.php',
						'name'  	=> 'WooCommerce',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-footer-callout',
						'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
						'name' 		=> 'Ocean Footer Callout',
					),
				),
			),
		);

		$data['service'] = array(
			'categories'        => array( 'eCommerce' ),
			'xml_file'     		=> $url . 'service/sample-data.xml',
			'theme_settings' 	=> $url . 'service/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'service/widgets.wie',
			'form_file'  		=> $url . 'service/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '9',
			'elementor_width'  	=> '1220',
			'is_shop'  			=> true,
			'woo_image_size'  	=> '441',
			'woo_thumb_size' 	=> '270',
			'woo_crop_width'  	=> '4',
			'woo_crop_height' 	=> '5',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-product-sharing',
						'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
						'name'  	=> 'Ocean Product Sharing',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
					array(
						'slug'  	=> 'woocommerce',
						'init'  	=> 'woocommerce/woocommerce.php',
						'name'  	=> 'WooCommerce',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-popup-login',
						'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
						'name' 		=> 'Ocean Popup Login',
					),
					array(
						'slug' 		=> 'ocean-footer-callout',
						'init'  	=> 'ocean-footer-callout/ocean-footer-callout.php',
						'name' 		=> 'Ocean Footer Callout',
					),
				),
			),
		);

		$data['style'] = array(
			'categories'        => array( 'eCommerce' ),
			'xml_file'     		=> $url . 'style/sample-data.xml',
			'theme_settings' 	=> $url . 'style/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'style/widgets.wie',
			'form_file'  		=> $url . 'style/form.json',
			'home_title'  		=> 'Home',
			'blog_title'  		=> 'Blog',
			'posts_to_show'  	=> '7',
			'elementor_width'  	=> '1220',
			'is_shop'  			=> true,
			'woo_image_size'  	=> '441',
			'woo_thumb_size' 	=> '270',
			'woo_crop_width'  	=> '4',
			'woo_crop_height' 	=> '5',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'ocean-product-sharing',
						'init'  	=> 'ocean-product-sharing/ocean-product-sharing.php',
						'name'  	=> 'Ocean Product Sharing',
					),
					array(
						'slug'  	=> 'ocean-social-sharing',
						'init'  	=> 'ocean-social-sharing/ocean-social-sharing.php',
						'name'  	=> 'Ocean Social Sharing',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
					array(
						'slug'  	=> 'woocommerce',
						'init'  	=> 'woocommerce/woocommerce.php',
						'name'  	=> 'WooCommerce',
					),
					array(
						'slug'  	=> 'ti-woocommerce-wishlist',
						'init'  	=> 'ti-woocommerce-wishlist/ti-woocommerce-wishlist.php',
						'name'  	=> 'WooCommerce Wishlist',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-sticky-header',
						'init'  	=> 'ocean-sticky-header/ocean-sticky-header.php',
						'name' 		=> 'Ocean Sticky Header',
					),
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
					array(
						'slug' 		=> 'ocean-popup-login',
						'init'  	=> 'ocean-popup-login/ocean-popup-login.php',
						'name' 		=> 'Ocean Popup Login',
					),
					array(
						'slug' 		=> 'ocean-woo-popup',
						'init'  	=> 'ocean-woo-popup/ocean-woo-popup.php',
						'name' 		=> 'Ocean Woo Popup',
					),
				),
			),
		);

		$data['electronic'] = array(
			'categories'        => array( 'Coming Soon' ),
			'xml_file'     		=> $url . 'electronic/sample-data.xml',
			'theme_settings' 	=> $url . 'electronic/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'electronic/widgets.wie',
			'form_file'  		=> $url . 'electronic/form.json',
			'home_title'  		=> 'Home',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['fashion'] = array(
			'categories'        => array( 'Coming Soon' ),
			'xml_file'     		=> $url . 'fashion/sample-data.xml',
			'theme_settings' 	=> $url . 'fashion/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'fashion/widgets.wie',
			'form_file'  		=> $url . 'fashion/form.json',
			'home_title'  		=> 'Home',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['food'] = array(
			'categories'        => array( 'Coming Soon' ),
			'xml_file'     		=> $url . 'food/sample-data.xml',
			'theme_settings' 	=> $url . 'food/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'food/widgets.wie',
			'form_file'  		=> $url . 'food/form.json',
			'home_title'  		=> 'Home',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['gaming'] = array(
			'categories'        => array( 'Coming Soon' ),
			'xml_file'     		=> $url . 'gaming/sample-data.xml',
			'theme_settings' 	=> $url . 'gaming/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'gaming/widgets.wie',
			'form_file'  		=> $url . 'gaming/form.json',
			'home_title'  		=> 'Home',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		$data['pink'] = array(
			'categories'        => array( 'Coming Soon' ),
			'xml_file'     		=> $url . 'pink/sample-data.xml',
			'theme_settings' 	=> $url . 'pink/oceanwp-export.dat',
			'widgets_file'  	=> $url . 'pink/widgets.wie',
			'form_file'  		=> $url . 'pink/form.json',
			'home_title'  		=> 'Home',
			'elementor_width'  	=> '1220',
			'required_plugins'  => array(
				'free' => array(
					array(
						'slug'  	=> 'ocean-extra',
						'init'  	=> 'ocean-extra/ocean-extra.php',
						'name'  	=> 'Ocean Extra',
					),
					array(
						'slug'  	=> 'elementor',
						'init'  	=> 'elementor/elementor.php',
						'name'  	=> 'Elementor',
					),
					array(
						'slug'  	=> 'wpforms-lite',
						'init'  	=> 'wpforms-lite/wpforms.php',
						'name'  	=> 'WPForms',
					),
				),
				'premium' => array(
					array(
						'slug' 		=> 'ocean-elementor-widgets',
						'init'  	=> 'ocean-elementor-widgets/ocean-elementor-widgets.php',
						'name' 		=> 'Ocean Elementor Widgets',
					),
				),
			),
		);

		// Return
		return $data;

	}

} // End Class
