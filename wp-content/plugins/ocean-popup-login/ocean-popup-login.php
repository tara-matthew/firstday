<?php
/**
 * Plugin Name:			Ocean Popup Login
 * Plugin URI:			https://oceanwp.org/extension/ocean-popup-login/
 * Description:			A plugin to add a popup login, register and lost password form where you want.
 * Version:				1.0.6
 * Author:				OceanWP
 * Author URI:			https://oceanwp.org/
 * Requires at least:	4.5.0
 * Tested up to:		5.0
 *
 * Text Domain: ocean-popup-login
 * Domain Path: /languages/
 *
 * @package Ocean_Popup_Login
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the main instance of Ocean_Popup_Login to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Ocean_Popup_Login
 */
function Ocean_Popup_Login() {
	return Ocean_Popup_Login::instance();
} // End Ocean_Popup_Login()

Ocean_Popup_Login();

/**
 * Main Ocean_Popup_Login Class
 *
 * @class Ocean_Popup_Login
 * @version	1.0.0
 * @since 1.0.0
 * @package	Ocean_Popup_Login
 */
final class Ocean_Popup_Login {
	/**
	 * Ocean_Popup_Login The single instance of Ocean_Popup_Login.
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
	public function __construct() {
		$this->token 			= 'ocean-popup-login';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.6';

		define( 'OPL_URL', $this->plugin_url );
		define( 'OPL_PATH', $this->plugin_path );

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_filter( 'ocean_register_tm_strings', array( $this, 'register_tm_strings' ) );

		add_action( 'init', array( $this, 'setup' ) );
		add_action( 'init', array( $this, 'updater' ), 1 );

		add_shortcode( 'oceanwp_popup_login', array( $this, 'popup_shortcode' ) );
	}

	/**
	 * Initialize License Updater.
	 * Load Updater initialize.
	 * @return void
	 */
	public function updater() {

		// Plugin Updater Code
		if( class_exists( 'OceanWP_Plugin_Updater' ) ) {
			$license	= new OceanWP_Plugin_Updater( __FILE__, 'Popup Login', $this->version, 'OceanWP' );
		}
	}

	/**
	 * Main Ocean_Popup_Login Instance
	 *
	 * Ensures only one instance of Ocean_Popup_Login is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Ocean_Popup_Login()
	 * @return Main Ocean_Popup_Login instance
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
		load_plugin_textdomain( 'ocean-popup-login', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
	 * Register translation strings
	 */
	public static function register_tm_strings( $strings ) {

		$strings['opl_popup_login_text_title']   = esc_html__( 'Log in', 'ocean-popup-login' );
		$strings['opl_popup_login_text_content'] = esc_html__( 'Become a part of our community!', 'ocean-popup-login' );
		$strings['opl_popup_register_text_title']   = esc_html__( 'Log in', 'ocean-popup-login' );
		$strings['opl_popup_register_text_content'] = esc_html__( 'Become a part of our community!', 'ocean-popup-login' );
		$strings['opl_popup_lost_password_text_title']   = esc_html__( 'Log in', 'ocean-popup-login' );
		$strings['opl_popup_lost_password_text_content'] = esc_html__( 'Become a part of our community!', 'ocean-popup-login' );

		return $strings;

	}

	/**
	 * Setup all the things.
	 * Only executes if OceanWP or a child theme using OceanWP as a parent is active and the extension specific filter returns true.
	 * @return void
	 */
	public function setup() {
		$theme = wp_get_theme();

		if ( 'OceanWP' == $theme->name || 'oceanwp' == $theme->template ) {
			add_action( 'customize_register', array( $this, 'customizer_options' ) );
			add_action( 'wp_nav_menu_items', array( $this, 'login_link' ), 9, 2 );
			add_action( 'wp_footer', array( $this, 'login_form' ) );
			add_action( 'wp_ajax_nopriv_opl_login_member', array( $this, 'login_member' ) );
			add_action( 'wp_ajax_nopriv_opl_register_member', array( $this, 'register_member' ) );
			add_action( 'wp_ajax_nopriv_opl_reset_password', array( $this, 'reset_password' ) );
			add_action( 'ocean_primary_backgrounds', array( $this, 'primary_background' ) );
			add_action( 'ocean_hover_primary_backgrounds', array( $this, 'hover_primary_background' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 999 );
			add_filter( 'ocean_localize_array', array( $this, 'localize_array' ) );
			add_filter( 'ocean_head_css', array( $this, 'head_css' ) );

			// If WordPress Social Login enabled
			add_action( 'opl_wp_social_login', 'wsl_action_wordpress_social_login' );
			add_action( 'opl_wp_social_register', 'wsl_action_wordpress_social_login' );
		}
	}

	/**
	 * Customizer options
	 *
	 * @since 1.0.0
	 */
	public static function customizer_options( $wp_customize ) {

		// Helpers functions
		require_once( OPL_PATH .'includes/customizer-helpers.php' );

		/**
		 * Section
		 */
		$wp_customize->add_section( 'opl_popup_login', array(
			'title' 			=> esc_html__( 'Popup Login', 'ocean-popup-login' ),
			'priority' 			=> 210,
		) );

		/**
		 * Popup Position
		 */
		$wp_customize->add_setting( 'opl_popup_login_position', array(
			'transport'           	=> 'postMessage',
			'default'           	=> 'menu',
			'sanitize_callback' 	=> 'oceanwp_sanitize_select',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Buttonset_Control( $wp_customize, 'opl_popup_login_position', array(
			'label'	   				=> esc_html__( 'Position', 'ocean-popup-login' ),
			'description'	   		=> sprintf( esc_html__( 'If you choose Manual, check this %1$sdocumentation%2$s.', 'ocean-popup-login' ), '<a href="http://docs.oceanwp.org/article/499-how-to-show-popup-login-link" target="_blank">', '</a>' ),
			'section'  				=> 'opl_popup_login',
			'settings' 				=> 'opl_popup_login_position',
			'priority' 				=> 10,
			'choices' 				=> array(
				'menu' 		=> esc_html__( 'Menu', 'ocean-popup-login' ),
				'manual' 	=> esc_html__( 'Manual', 'ocean-popup-login' ),
			),
		) ) );

		/**
		 * Logged in
		 */
		$wp_customize->add_setting( 'opl_popup_login_logged_in', array(
			'default'           => 'logout',
			'sanitize_callback' => array( $this, 'opl_popup_login_sanitize_logged_in' ),
		) );

		$wp_customize->add_control( 'opl_popup_login_logged_in', array(
			'label'             => esc_html__( 'Logged In', 'ocean-popup-login' ),
			'description'       => esc_html__( 'Display this when user logged in.', 'ocean-popup-login' ),
			'section'           => 'opl_popup_login',
			'priority'          => 10,
			'type'              => 'radio',
			'choices'           => array(
				'nothing'  => esc_html__( 'Display nothing', 'ocean-popup-login' ),
				'logout'   => esc_html__( 'Logout link', 'ocean-popup-login' ),
				'custom'   => esc_html__( 'Custom', 'ocean-popup-login' )
			)
		) );

		/**
		 * Custom text
		 */
		$wp_customize->add_setting( 'opl_popup_login_logged_in_custom', array(
			'default'           => '',
			'transport'			=> 'postMessage',
			'sanitize_callback' => 'wp_kses_post',
		) );

		$wp_customize->add_control( 'opl_popup_login_logged_in_custom', array(
			'label'             => esc_html__( 'Custom Text', 'ocean-popup-login' ),
			'section'           => 'opl_popup_login',
			'priority'          => 10,
			'type'              => 'textarea',
			'active_callback'   => 'opl_popup_login_cac_has_custom_text',
		) );

		/**
		 * Login text
		 */
		$wp_customize->add_setting( 'opl_popup_login_text', array(
			'default'           => esc_html__( 'Sign in / Join', 'ocean-popup-login' ),
			'transport'			=> 'postMessage',
			'sanitize_callback' => 'wp_kses_post',
		) );

		$wp_customize->add_control( 'opl_popup_login_text', array(
			'label'             => esc_html__( 'Login Text', 'ocean-popup-login' ),
			'section'           => 'opl_popup_login',
			'priority'          => 10,
			'type'              => 'text',
		) );

		/**
		 * Logout text
		 */
		$wp_customize->add_setting( 'opl_popup_logout_text', array(
			'default'           => esc_html__( 'Logout', 'ocean-popup-login' ),
			'sanitize_callback' => 'wp_kses_post',
		) );

		$wp_customize->add_control( 'opl_popup_logout_text', array(
			'label'             => esc_html__( 'Logout Text', 'ocean-popup-login' ),
			'section'           => 'opl_popup_login',
			'priority'          => 10,
			'type'              => 'text',
		) );

		/**
		 * Logout url
		 */
		$wp_customize->add_setting( 'opl_popup_logout_url', array(
			'transport'			=> 'postMessage',
			'sanitize_callback' => 'wp_kses_post',
		) );

		$wp_customize->add_control( 'opl_popup_logout_url', array(
			'label'             => esc_html__( 'Logout URL', 'ocean-popup-login' ),
			'section'           => 'opl_popup_login',
			'priority'          => 10,
			'type'              => 'text',
		) );

		/**
		 * Login Form Heading
		 */
		$wp_customize->add_setting( 'opl_popup_login_heading', array(
			'sanitize_callback' 	=> 'wp_kses',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'opl_popup_login_heading', array(
			'label'    	=> esc_html__( 'Login Form', 'ocean-popup-login' ),
			'section'  	=> 'opl_popup_login',
			'priority' 	=> 10,
		) ) );

		/**
		 * Title Text
		 */
		$wp_customize->add_setting( 'opl_popup_login_text_title', array(
			'default'           	=> esc_html__( 'Log in', 'ocean-popup-login' ),
			'transport'				=> 'postMessage',
			'sanitize_callback' 	=> 'wp_kses_post',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'opl_popup_login_text_title', array(
			'label'	   				=> esc_html__( 'Title', 'ocean-popup-login' ),
			'type' 					=> 'text',
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Description Text
		 */
		$wp_customize->add_setting( 'opl_popup_login_text_content', array(
			'default'           	=> esc_html__( 'Become a part of our community!', 'ocean-popup-login' ),
			'transport'				=> 'postMessage',
			'sanitize_callback' 	=> 'wp_kses_post',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'opl_popup_login_text_content', array(
			'label'	   				=> esc_html__( 'Content', 'ocean-popup-login' ),
			'type' 					=> 'textarea',
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Redirect After Login
		 */
		$wp_customize->add_setting( 'opl_popup_login_redirect', array(
			'transport'				=> 'postMessage',
			'sanitize_callback' 	=> 'wp_kses_post',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'opl_popup_login_redirect', array(
			'label'	   				=> esc_html__( 'Redirect After Login', 'ocean-popup-login' ),
			'description'	   		=> esc_html__( 'Add your URL here if you want to redirect after login.', 'ocean-popup-login' ),
			'type' 					=> 'text',
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Register Form Heading
		 */
		$wp_customize->add_setting( 'opl_popup_register_heading', array(
			'sanitize_callback' 	=> 'wp_kses',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'opl_popup_register_heading', array(
			'label'    	=> esc_html__( 'Register Form', 'ocean-popup-login' ),
			'section'  	=> 'opl_popup_login',
			'priority' 	=> 10,
		) ) );

		/**
		 * Title Text
		 */
		$wp_customize->add_setting( 'opl_popup_register_text_title', array(
			'default'           	=> esc_html__( 'Create an account', 'ocean-popup-login' ),
			'transport'				=> 'postMessage',
			'sanitize_callback' 	=> 'wp_kses_post',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'opl_popup_register_text_title', array(
			'label'	   				=> esc_html__( 'Title', 'ocean-popup-login' ),
			'type' 					=> 'text',
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Description Text
		 */
		$wp_customize->add_setting( 'opl_popup_register_text_content', array(
			'default'           	=> esc_html__( 'Welcome! Register for an account', 'ocean-popup-login' ),
			'transport'				=> 'postMessage',
			'sanitize_callback' 	=> 'wp_kses_post',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'opl_popup_register_text_content', array(
			'label'	   				=> esc_html__( 'Content', 'ocean-popup-login' ),
			'type' 					=> 'textarea',
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Redirect After Registration
		 */
		$wp_customize->add_setting( 'opl_popup_register_redirect', array(
			'transport'				=> 'postMessage',
			'sanitize_callback' 	=> 'wp_kses_post',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'opl_popup_register_redirect', array(
			'label'	   				=> esc_html__( 'Redirect After Registration', 'ocean-popup-login' ),
			'description'	   		=> esc_html__( 'Add your URL here if you want to redirect after registration.', 'ocean-popup-login' ),
			'type' 					=> 'text',
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Lost Password Form Heading
		 */
		$wp_customize->add_setting( 'opl_popup_lost_password_heading', array(
			'sanitize_callback' 	=> 'wp_kses',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'opl_popup_lost_password_heading', array(
			'label'    	=> esc_html__( 'Lost Password Form', 'ocean-popup-login' ),
			'section'  	=> 'opl_popup_login',
			'priority' 	=> 10,
		) ) );

		/**
		 * Title Text
		 */
		$wp_customize->add_setting( 'opl_popup_lost_password_text_title', array(
			'default'           	=> esc_html__( 'Reset password', 'ocean-popup-login' ),
			'transport'				=> 'postMessage',
			'sanitize_callback' 	=> 'wp_kses_post',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'opl_popup_lost_password_text_title', array(
			'label'	   				=> esc_html__( 'Title', 'ocean-popup-login' ),
			'type' 					=> 'text',
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Description Text
		 */
		$wp_customize->add_setting( 'opl_popup_lost_password_text_content', array(
			'default'           	=> esc_html__( 'Recover your password', 'ocean-popup-login' ),
			'transport'				=> 'postMessage',
			'sanitize_callback' 	=> 'wp_kses_post',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'opl_popup_lost_password_text_content', array(
			'label'	   				=> esc_html__( 'Content', 'ocean-popup-login' ),
			'type' 					=> 'textarea',
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Styling Heading
		 */
		$wp_customize->add_setting( 'opl_popup_login_styling_heading', array(
			'sanitize_callback' 	=> 'wp_kses',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'opl_popup_login_styling_heading', array(
			'label'    	=> esc_html__( 'Styling', 'ocean-popup-login' ),
			'section'  	=> 'opl_popup_login',
			'priority' 	=> 10,
		) ) );

		/**
		 * Popup Width
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_width', array(
			'transport'				=> 'postMessage',
			'default'           	=> '500',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_width_tablet', array(
			'transport'				=> 'postMessage',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
		) );

		$wp_customize->add_setting( 'opl_popup_login_style_width_mobile', array(
			'transport'				=> 'postMessage',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Slider_Control( $wp_customize, 'opl_popup_login_style_width', array(
			'label'	   				=> esc_html__( 'Width (px)', 'oceanwp' ),
			'section'  				=> 'opl_popup_login',
			'settings' => array(
	            'desktop' 	=> 'opl_popup_login_style_width',
	            'tablet' 	=> 'opl_popup_login_style_width_tablet',
	            'mobile' 	=> 'opl_popup_login_style_width_mobile',
		    ),
			'priority' 				=> 10,
		    'input_attrs' 			=> array(
		        'min'   => 10,
		        'max'   => 5000,
		        'step'  => 1,
		    ),
		) ) );

		/**
		 * Popup Padding
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_top_padding', array(
			'transport'				=> 'postMessage',
			'default'           	=> '30',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_right_padding', array(
			'transport'				=> 'postMessage',
			'default'           	=> '100',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_bottom_padding', array(
			'transport'				=> 'postMessage',
			'default'           	=> '30',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_left_padding', array(
			'transport'				=> 'postMessage',
			'default'           	=> '100',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number',
		) );

		$wp_customize->add_setting( 'opl_popup_login_style_tablet_top_padding', array(
			'transport'				=> 'postMessage',
			'default'           	=> '30',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_tablet_right_padding', array(
			'transport'				=> 'postMessage',
			'default'           	=> '100',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_tablet_bottom_padding', array(
			'transport'				=> 'postMessage',
			'default'           	=> '30',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_tablet_left_padding', array(
			'transport'				=> 'postMessage',
			'default'           	=> '100',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number',
		) );

		$wp_customize->add_setting( 'opl_popup_login_style_mobile_top_padding', array(
			'transport'				=> 'postMessage',
			'default'           	=> '30',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_mobile_right_padding', array(
			'transport'				=> 'postMessage',
			'default'           	=> '50',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_mobile_bottom_padding', array(
			'transport'				=> 'postMessage',
			'default'           	=> '30',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_mobile_left_padding', array(
			'transport'				=> 'postMessage',
			'default'           	=> '50',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Dimensions_Control( $wp_customize, 'opl_popup_login_style_padding_dimensions', array(
			'label'	   				=> esc_html__( 'Padding (px)', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',				
			'settings'   => array(
	            'desktop_top' 		=> 'opl_popup_login_style_top_padding',
	            'desktop_right' 	=> 'opl_popup_login_style_right_padding',
	            'desktop_bottom' 	=> 'opl_popup_login_style_bottom_padding',
	            'desktop_left' 		=> 'opl_popup_login_style_left_padding',
	            'tablet_top' 		=> 'opl_popup_login_style_tablet_top_padding',
	            'tablet_right' 		=> 'opl_popup_login_style_tablet_right_padding',
	            'tablet_bottom' 	=> 'opl_popup_login_style_tablet_bottom_padding',
	            'tablet_left' 		=> 'opl_popup_login_style_tablet_left_padding',
	            'mobile_top' 		=> 'opl_popup_login_style_mobile_top_padding',
	            'mobile_right' 		=> 'opl_popup_login_style_mobile_right_padding',
	            'mobile_bottom' 	=> 'opl_popup_login_style_mobile_bottom_padding',
	            'mobile_left' 		=> 'opl_popup_login_style_mobile_left_padding',
			),
			'priority' 				=> 10,
		    'input_attrs' 			=> array(
		        'min'   => 0,
		        'step'  => 1,
		    ),
		) ) );

		/**
		 * Popup Border Radius
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_top_radius', array(
			'transport'				=> 'postMessage',
			'default'           	=> '3',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_right_radius', array(
			'transport'				=> 'postMessage',
			'default'           	=> '3',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_bottom_radius', array(
			'transport'				=> 'postMessage',
			'default'           	=> '3',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_left_radius', array(
			'transport'				=> 'postMessage',
			'default'           	=> '3',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number',
		) );

		$wp_customize->add_setting( 'opl_popup_login_style_tablet_top_radius', array(
			'transport'				=> 'postMessage',
			'default'           	=> '3',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_tablet_right_radius', array(
			'transport'				=> 'postMessage',
			'default'           	=> '3',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_tablet_bottom_radius', array(
			'transport'				=> 'postMessage',
			'default'           	=> '3',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_tablet_left_radius', array(
			'transport'				=> 'postMessage',
			'default'           	=> '3',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
		) );

		$wp_customize->add_setting( 'opl_popup_login_style_mobile_top_radius', array(
			'transport'				=> 'postMessage',
			'default'           	=> '3',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_mobile_right_radius', array(
			'transport'				=> 'postMessage',
			'default'           	=> '3',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_mobile_bottom_radius', array(
			'transport'				=> 'postMessage',
			'default'           	=> '3',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
		) );
		$wp_customize->add_setting( 'opl_popup_login_style_mobile_left_radius', array(
			'transport'				=> 'postMessage',
			'default'           	=> '3',
			'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Dimensions_Control( $wp_customize, 'opl_popup_login_style_radius_dimensions', array(
			'label'	   				=> esc_html__( 'Border Radius (px)', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',				
			'settings'   => array(
	            'desktop_top' 		=> 'opl_popup_login_style_top_radius',
	            'desktop_right' 	=> 'opl_popup_login_style_right_radius',
	            'desktop_bottom' 	=> 'opl_popup_login_style_bottom_radius',
	            'desktop_left' 		=> 'opl_popup_login_style_left_radius',
	            'tablet_top' 		=> 'opl_popup_login_style_tablet_top_radius',
	            'tablet_right' 		=> 'opl_popup_login_style_tablet_right_radius',
	            'tablet_bottom' 	=> 'opl_popup_login_style_tablet_bottom_radius',
	            'tablet_left' 		=> 'opl_popup_login_style_tablet_left_radius',
	            'mobile_top' 		=> 'opl_popup_login_style_mobile_top_radius',
	            'mobile_right' 		=> 'opl_popup_login_style_mobile_right_radius',
	            'mobile_bottom' 	=> 'opl_popup_login_style_mobile_bottom_radius',
	            'mobile_left' 		=> 'opl_popup_login_style_mobile_left_radius',
			),
			'priority' 				=> 10,
		    'input_attrs' 			=> array(
		        'min'   => 0,
		        'step'  => 1,
		    ),
		) ) );

		/**
		 * Background image
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_bg', array(
			'transport'				=> 'postMessage',
			'default'           	=> '',
			'sanitize_callback' 	=> 'absint',
		) );

		$wp_customize->add_control( new WP_Customize_Cropped_Image_Control( $wp_customize, 'opl_popup_login_style_bg', array(
			'label'             => esc_html__( 'Background Image', 'ocean-popup-login' ),
			'section'           => 'opl_popup_login',
			'priority'          => 10,
			'flex_width'        => true,
			'flex_height'       => true,
			'width'             => 500,
			'height'            => 410
		) ) );

		/**
		 * Popup Background
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_bg_color', array(
			'transport'				=> 'postMessage',
			'default'           	=> '#ffffff',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_bg_color', array(
			'label'	   				=> esc_html__( 'Background Color', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Title Color
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_title_color', array(
			'transport'				=> 'postMessage',
			'default'           	=> '#333333',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_title_color', array(
			'label'	   				=> esc_html__( 'Title Color', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Content Color
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_content_color', array(
			'transport'				=> 'postMessage',
			'default'           	=> '#777777',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_content_color', array(
			'label'	   				=> esc_html__( 'Content Color', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Input Color
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_input_color', array(
			'transport'				=> 'postMessage',
			'default'           	=> '#757575',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_input_color', array(
			'label'	   				=> esc_html__( 'Input Color', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Input Border Color
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_input_border_color', array(
			'transport'				=> 'postMessage',
			'default'           	=> '#dddddd',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_input_border_color', array(
			'label'	   				=> esc_html__( 'Input Border Color', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Input Border Focus Color
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_input_border_focus_color', array(
			'transport'				=> 'postMessage',
			'default'           	=> '',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_input_border_focus_color', array(
			'label'	   				=> esc_html__( 'Input Border Focus Color', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Remember Me Color
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_remember_color', array(
			'transport'				=> 'postMessage',
			'default'           	=> '#040404',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_remember_color', array(
			'label'	   				=> esc_html__( 'Remember Me Color', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Button Background
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_button_bg_color', array(
			'transport'				=> 'postMessage',
			'default'           	=> '',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_button_bg_color', array(
			'label'	   				=> esc_html__( 'Button Background Color', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Button Background Hover
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_button_bg_color_hover', array(
			'transport'				=> 'postMessage',
			'default'           	=> '',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_button_bg_color_hover', array(
			'label'	   				=> esc_html__( 'Button Background Color Hover', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Button Color
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_button_color', array(
			'transport'				=> 'postMessage',
			'default'           	=> '#ffffff',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_button_color', array(
			'label'	   				=> esc_html__( 'Button Color', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Forgot Password Color
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_forgot_color', array(
			'transport'				=> 'postMessage',
			'default'           	=> '',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_forgot_color', array(
			'label'	   				=> esc_html__( 'Forgot Password Color', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Bottom Content Background Color
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_bottom_bg_color', array(
			'transport'				=> 'postMessage',
			'default'           	=> '#f6f6f6',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_bottom_bg_color', array(
			'label'	   				=> esc_html__( 'Bottom Background Color', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Bottom Content Color
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_bottom_color', array(
			'transport'				=> 'postMessage',
			'default'           	=> '#000000',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_bottom_color', array(
			'label'	   				=> esc_html__( 'Bottom Color', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Bottom Button Background Color
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_bottom_button_bg_color', array(
			'transport'				=> 'postMessage',
			'default'           	=> '#ffffff',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_bottom_button_bg_color', array(
			'label'	   				=> esc_html__( 'Bottom Button Background Color', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Bottom Button Color
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_bottom_button_color', array(
			'transport'				=> 'postMessage',
			'default'           	=> '#1f1f1f',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_bottom_button_color', array(
			'label'	   				=> esc_html__( 'Bottom Button Color', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Bottom Button Background Color: hover
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_bottom_button_hover_bg_color', array(
			'transport'				=> 'postMessage',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_bottom_button_hover_bg_color', array(
			'label'	   				=> esc_html__( 'Bottom Button Background Color: hover', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Popup Bottom Button Color: hover
		 */
		$wp_customize->add_setting( 'opl_popup_login_style_bottom_button_hover_color', array(
			'transport'				=> 'postMessage',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_style_bottom_button_hover_color', array(
			'label'	   				=> esc_html__( 'Bottom Button Color: hover', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Privacy Policy Heading
		 */
		$wp_customize->add_setting( 'opl_popup_login_privacy_heading', array(
			'sanitize_callback' 	=> 'wp_kses',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'opl_popup_login_privacy_heading', array(
			'label'    	=> esc_html__( 'Privacy Policy', 'ocean-popup-login' ),
			'description' => esc_html__( 'You need to select your Privacy Policy page in Settings > Privacy.', 'ocean-popup-login' ),
			'section'  	=> 'opl_popup_login',
			'priority' 	=> 10,
		) ) );

		/**
		 * Privacy Policy Color
		 */
		$wp_customize->add_setting( 'opl_popup_login_privacy_color', array(
			'transport'				=> 'postMessage',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_privacy_color', array(
			'label'	   				=> esc_html__( 'Color', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

		/**
		 * Privacy Policy Color: Hover
		 */
		$wp_customize->add_setting( 'opl_popup_login_privacy_hover_color', array(
			'transport'				=> 'postMessage',
			'sanitize_callback' 	=> 'oceanwp_sanitize_color',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'opl_popup_login_privacy_hover_color', array(
			'label'	   				=> esc_html__( 'Color: Hover', 'ocean-popup-login' ),
			'section'  				=> 'opl_popup_login',
			'priority' 				=> 10,
		) ) );

	}

	/**
	 * The popup link.
	 *
	 * @since 1.0.0
	 */
	public function login_link( $items, $args ) {

		// Only used on main menu
		if ( 'main_menu' != $args->theme_location
			|| 'menu' != get_theme_mod( 'opl_popup_login_position', 'menu' ) ) {
			return $items;
		}

		// Get permalink on any page
		$logout_url = get_theme_mod( 'opl_popup_logout_url' );
		if ( '' != $logout_url ) {
			$permalink = $logout_url;
		} else {
			if ( is_tax() ) {
			    $permalink = get_term_link( get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			} elseif ( is_post_type_archive() ) {
			    $permalink = get_post_type_archive_link( get_query_var('post_type') );
			} elseif ( is_home() ) {
			    $permalink = get_permalink( get_option( 'page_for_posts' ) );
			} else {
			    $permalink = get_permalink();
			}
		}

		// Customizer data
		$type = get_theme_mod( 'opl_popup_login_logged_in', 'logout' );
		$login_text = get_theme_mod( 'opl_popup_login_text' );
		$login_text = $login_text ? $login_text: esc_html__( 'Sign in / Join', 'ocean-popup-login' );
		$logout_text = get_theme_mod( 'opl_popup_logout_text' );
		$logout_text = $logout_text ? $logout_text: esc_html__( 'Logout', 'ocean-popup-login' );

		// Add login item to menu
		$items .= '<li class="opl-login-li">';
			if ( ! is_user_logged_in() ) {
				$items .= '<a href="#opl-login-form" class="opl-link">'. $login_text .'</a>';
			} else {
				if ( $type == 'logout' ) {
					$items .= '<a href="'. wp_logout_url( $permalink ) .'" class="opl-logout-link">'. $logout_text .'</a>';
				} elseif ( $type == 'custom' ) {
					$items .= '<span class="opl-logout-link">' . esc_html( get_theme_mod( 'opl_popup_login_logged_in_custom' ) ) . '</span>';
				} else {
					$items .= false;
				}
			}
		$items .= '</li>';
		
		// Return nav $items
		return $items;

	}

	/**
	 * The popup link shortcode.
	 *
	 * @since 1.0.0
	 */
	public function login_link_shortcode( $items, $login_text, $logout_text ) {

		// Get permalink on any page
		$logout_url = get_theme_mod( 'opl_popup_logout_url' );
		if ( '' != $logout_url ) {
			$permalink = $logout_url;
		} else {
			if ( is_tax() ) {
			    $permalink = get_term_link( get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			} elseif ( is_post_type_archive() ) {
			    $permalink = get_post_type_archive_link( get_query_var('post_type') );
			} elseif ( is_home() ) {
			    $permalink = get_permalink( get_option( 'page_for_posts' ) );
			} else {
			    $permalink = get_permalink();
			}
		}

		// Vars
		$type = get_theme_mod( 'opl_popup_login_logged_in', 'logout' );
		$login_text = $login_text ? $login_text: esc_html__( 'Sign in / Join', 'ocean-popup-login' );
		$logout_text = $logout_text ? $logout_text: esc_html__( 'Logout', 'ocean-popup-login' );

		// Login link
		if ( ! is_user_logged_in() ) {
			$items .= '<a href="#opl-login-form" class="opl-link">'. $login_text .'</a>';
		} else {
			if ( $type == 'logout' ) {
				$items .= '<a href="'. wp_logout_url( $permalink ) .'" class="opl-logout-link">'. $logout_text .'</a>';
			} elseif ( $type == 'custom' ) {
				$items .= '<span class="opl-logout-link">' . esc_html( get_theme_mod( 'opl_popup_login_logged_in_custom' ) ) . '</span>';
			} else {
				$items .= false;
			}
		}
		
		// Return nav $items
		return $items;

	}

	/**
	 * Gets the popup template part.
	 *
	 * @since 1.0.0
	 */
	public function login_form() {

		$file 		= $this->plugin_path . 'template/popup.php';
		$theme_file = get_stylesheet_directory() . '/templates/extra/popup.php';

		if ( file_exists( $theme_file ) ) {
			$file = $theme_file;
		}

		if ( file_exists( $file ) ) {
			include $file;
		}

	}

	/**
	 * Login.
	 *
	 * @since 1.0.0
	 */
	public function login_member() {

		// Get variables
		$user_login		= $_POST['opl_user_login'];	
		$user_pass		= $_POST['opl_user_pass'];
		$user_remember	= $_POST['opl_user_remember'];


		// Check CSRF token
		if ( ! check_ajax_referer( 'opl-login-nonce', 'login-security', false ) ) {
			echo json_encode( array( 'error' => true, 'message' => '<div class="alert alert-danger">'. esc_html__( 'Session token has expired, please reload the page and try again', 'ocean-popup-login' ) .'</div>' ) );
		}
	 	
	 	// Check if input variables are empty
	 	else if ( empty( $user_login ) || empty( $user_pass ) ) {
			echo json_encode( array( 'error' => true, 'message' => '<div class="alert alert-danger">'. esc_html__( 'Please fill all form fields', 'ocean-popup-login' ) .'</div>' ) );
	 	}

	 	// Now we can insert this account
	 	else {

	 		$user = wp_signon( array( 'user_login' => $user_login, 'user_password' => $user_pass, 'remember' => $user_remember ), is_ssl() );

		    if ( is_wp_error( $user) ) {
				echo json_encode( array( 'error' => true, 'message' => '<div class="alert alert-danger">'. esc_html__( 'ERROR: Username or password incorrect!', 'ocean-popup-login' ) .'</div>' ) );
			} else {
				echo json_encode( array( 'error' => false, 'message' => '<div class="alert alert-success">'. esc_html__( 'Login successful, reloading page...', 'ocean-popup-login' ) .'</div>' ) );
			}
	 	}

	 	die();

	}

	/**
	 * Register.
	 *
	 * @since 1.0.0
	 */
	public function register_member() {

		// Get variables
		$user_login	= $_POST['opl_register_login'];	
		$user_email	= $_POST['opl_register_email'];
		$user_pass	= $_POST['opl_register_pass'];
		$user_pass2	= $_POST['opl_register_pass2'];
		$user_data	= '';
		
		// Check CSRF token
		if ( ! check_ajax_referer( 'opl-login-nonce', 'register-security', false ) ) {
			echo json_encode( array( 'error' => true, 'message' => '<div class="alert alert-danger">'. esc_html__( 'Session token has expired, please reload the page and try again', 'ocean-popup-login' ) .'</div>' ) );
			die();
		}
	 	
	 	// Check if the username is empty
	 	else if ( empty( $user_login ) ) {
			echo json_encode( array( 'error' => true, 'message' => '<div class="alert alert-danger">'. esc_html__( 'Invalid username', 'ocean-popup-login' ) .'</div>' ) );
			die();
		}

		// Check if the username exist
	 	else if ( username_exists( $user_login ) ) {
			echo json_encode( array( 'error' => true, 'message' => '<div class="alert alert-danger">'. esc_html__( 'Username already taken', 'ocean-popup-login' ) .'</div>' ) );
			die();
		}

		// Check if the username is valid
	 	else if ( ! validate_username( $user_login ) ) {
			echo json_encode( array( 'error' => true, 'message' => '<div class="alert alert-danger">'. esc_html__( 'Invalid username', 'ocean-popup-login' ) .'</div>' ) );
			die();
		}

		// Check if email is empty
	 	else if ( empty( $user_email ) || ! is_email( $user_email ) ) {
			echo json_encode( array( 'error' => true, 'message' => '<div class="alert alert-danger">'. esc_html__( 'Invalid email', 'ocean-popup-login' ) .'</div>' ) );
			die();
		}

		// Check if the eamil exist
	 	else if ( email_exists( $user_email ) ) {
			echo json_encode( array( 'error' => true, 'message' => '<div class="alert alert-danger">'. esc_html__( 'Email address already taken', 'ocean-popup-login' ) .'</div>' ) );
			die();
		}

		// Check if password is empty
	 	else if ( empty( $user_pass ) ) {
			echo json_encode( array( 'error' => true, 'message' => '<div class="alert alert-danger">'. esc_html__( 'Please enter a password', 'ocean-popup-login' ) .'</div>' ) );
			die();
		}

		// Check if passwords match
	 	else if ( ( ! empty( $user_pass ) && empty( $user_pass2 ) ) || ( $user_pass !== $user_pass2 ) ) {
			echo json_encode( array( 'error' => true, 'message' => '<div class="alert alert-danger">'. esc_html__( 'Passwords do not match', 'ocean-popup-login' ) .'</div>' ) );
			die();
		}

		// User args
		$user_args = apply_filters( 'opl_insert_user_args', array(
			'user_login' 		=> $user_login,
			'user_email' 		=> $user_email,
			'user_pass' 		=> $user_pass,
			'user_registered' 	=> date( 'Y-m-d H:i:s' ),
			'role' 				=> get_option( 'default_role' )
		), $user_data );

		// Insert new user
		$user_id = wp_insert_user( $user_args );

		if ( is_wp_error( $user_id ) ) {
			echo json_encode( array( 'error' => true, 'message' => '<div class="alert alert-success">'. esc_html__( 'Error on user creation.', 'ocean-popup-login' ) .'</p>' ) );
		} else {

			// Login new user
			self::opl_log_user_in( $user_id, $user_login, $user_pass );

			echo json_encode( array( 'error' => false, 'message' => '<div class="alert alert-success">'. esc_html__( 'Registration complete, reloading page...', 'ocean-popup-login' ) .'</p>' ) );

		}

	 	die();

	}

	/**
	 * Log User In
	 *
	 * @since 1.0.0
	*/
	public function opl_log_user_in( $user_id, $user_login, $user_pass, $remember = false ) {
		if ( $user_id < 1 ) {
			return;
		}

		wp_set_auth_cookie( $user_id, $remember );
		wp_set_current_user( $user_id, $user_login );
		do_action( 'wp_login', $user_login, get_userdata( $user_id ) );
		do_action( 'opl_log_user_in', $user_id, $user_login, $user_pass );
	}

	/**
	 * Reset password.
	 *
	 * @since 1.0.0
	 */
	public function reset_password() {

		// Get variables
		$username_or_email = $_POST['opl_user_or_email'];

		// Check CSRF token
		if ( ! check_ajax_referer( 'opl-login-nonce', 'password-security', false ) ) {
			echo json_encode( array( 'error' => true, 'message' => '<div class="alert alert-danger">'. esc_html__( 'Session token has expired, please reload the page and try again', 'ocean-popup-login' ) .'</div>' ) );
		}		

	 	// Check if input variables are empty
	 	else if ( empty( $username_or_email ) ){
			echo json_encode( array('error' => true, 'message' => '<div class="alert alert-danger">'. esc_html__( 'Please fill all form fields', 'ocean-popup-login' ) .'</div>' ) );
	 	} else {

			$username = is_email( $username_or_email ) ? sanitize_email( $username_or_email ) : sanitize_user( $username_or_email );

			$user_forgotten = self::lost_password_retrieve( $username );
			
			if ( is_wp_error( $user_forgotten ) ) {
			
				$lostpass_error_messages = $user_forgotten->errors;

				$display_errors = '<div class="alert alert-warning">';
					foreach( $lostpass_error_messages as $error ) {
						$display_errors .= '<p>'.$error[0].'</p>';
					}
				$display_errors .= '</div>';
				
				echo json_encode( array( 'error' => true, 'message' => $display_errors ) );
			} else {
				echo json_encode( array( 'error' => false, 'message' => '<p class="alert alert-success">'. esc_html__( 'Password Reset. Please check your email.', 'ocean-popup-login' ) .'</p>' ) );
			}
	 	}

	 	die();

	}

	/**
	 * Reset password.
	 *
	 * @since 1.0.0
	 */
	public function lost_password_retrieve( $user_data ) {

		global $wpdb, $current_site, $wp_hasher;

		$errors = new WP_Error();

		if ( empty( $user_data ) ) {
			$errors->add( 'empty_username', esc_html__( 'Please enter a username or e-mail address.', 'ocean-popup-login' ) );
		} else if ( strpos( $user_data, '@' ) ) {
			$user_data = get_user_by( 'email', trim( $user_data ) );
			if ( empty( $user_data ) ) {
				$errors->add( 'invalid_email', esc_html__( 'There is no user registered with that email address.', 'ocean-popup-login' ) );
			}
		} else {
			$login = trim( $user_data );
			$user_data = get_user_by( 'login', $login );
		}

		if ( $errors->get_error_code() ) {
			return $errors;
		}

		if ( ! $user_data ) {
			$errors->add( 'invalidcombo', esc_html__('Invalid username or e-mail.', 'ocean-popup-login' ) );
			return $errors;
		}

		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;

		do_action( 'retrieve_password', $user_login );

		$allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

		if( ! $allow ) {
			return new WP_Error( 'no_password_reset', esc_html__( 'Password reset is not allowed for this user', 'ocean-popup-login' ) );
		} elseif ( is_wp_error( $allow ) ){
			return $allow;
		}

		$key = wp_generate_password( 20, false );

		do_action( 'retrieve_password_key', $user_login, $key );

		if ( empty( $wp_hasher ) ) {
			require_once ABSPATH .'wp-includes/class-phpass.php';
			$wp_hasher = new PasswordHash( 8, true );
		}

		$hashed = $wp_hasher->HashPassword( $key );

		$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );
		
		$message = esc_html__( 'Someone requested that the password be reset for the following account:', 'ocean-popup-login' ) . "\r\n\r\n";
		$message .= network_home_url( '/' ) . "\r\n\r\n";
		$message .= sprintf( esc_html__( 'Username: %s', 'ocean-popup-login' ), $user_login ) . "\r\n\r\n";
		$message .= esc_html__( 'If this was a mistake, just ignore this email and nothing will happen.', 'ocean-popup-login' ) . "\r\n\r\n";
		$message .= esc_html__( 'To reset your password, visit the following address:', 'ocean-popup-login' ) . "\r\n\r\n";
		$message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n\r\n";
		
		if ( is_multisite() ) {
			$blogname = $GLOBALS['current_site']->site_name;
		} else {
			$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		}

		$title   = sprintf( esc_html__( '[%s] Password Reset', 'ocean-popup-login' ), $blogname );
		$title   = apply_filters( 'retrieve_password_title', $title );
		$message = apply_filters( 'retrieve_password_message', $message, $key );

		if ( $message && ! wp_mail( $user_email, $title, $message ) ) {
			$errors->add( 'noemail', esc_html__( 'The e-mail could not be sent. Possible reason: your host may have disabled the mail() function.', 'ocean-popup-login' ) );

			return $errors;

			wp_die();
		}

		return true;

	}

	/**
	 * Sanitize the logged in options value.
	 *
	 * @since 1.0.0
	 */
	public function opl_popup_login_sanitize_logged_in( $type ) {
		if ( ! in_array( $type, array( 'nothing', 'logout', 'custom' ) ) ) {
			$type = 'logout';
		}
		return $type;
	}

	/**
	 * Primary background.
	 *
	 * @since 1.0.0
	 */
	public function primary_background( $backgrounds ) {

		$backgrounds[] = '#opl-login-form .opl-button';
		$backgrounds[] = '#opl-login-form .input-wrap .opl-focus-line';

		return $backgrounds;

	}

	/**
	 * Primary hover background.
	 *
	 * @since 1.0.0
	 */
	public function hover_primary_background( $hover ) {

		$hover[] = '#opl-login-form .opl-button:active';
		$hover[] = '#opl-login-form .opl-button:hover';

		return $hover;

	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 1.0.0
	 */
	public function scripts() {

		// Return if logged in
		if ( is_user_logged_in() ) {
			return;
		}

		// Load main stylesheet
		wp_enqueue_style( 'opl-style', plugins_url( '/assets/css/style.min.css', __FILE__ ) );
		
		// Load custom js methods.
		wp_enqueue_script( 'opl-js-script', plugins_url( '/assets/js/main.min.js', __FILE__ ), array( 'jquery' ), null, true );

	}

	/**
	 * Localize array.
	 *
	 * @since 1.0.0
	 */
	public function localize_array( $array ) {

		// if is not logged in
		if ( ! is_user_logged_in() ) {
			$array['loggedIn'] 	= is_user_logged_in();
			$array['ajaxURL'] 	= admin_url( 'admin-ajax.php' );
		}

		return $array;
	}

	/**
	 * Registers the function as a shortcode
	 */
	public function popup_shortcode( $atts, $items ) {

		// Extract attributes
		extract( shortcode_atts( array(
			'login_text' => esc_html__( 'Sign in / Join', 'ocean-popup-login' ),
			'logout_text' => esc_html__( 'Logout', 'ocean-popup-login' ),
		), $atts ) );

		return $this->login_link_shortcode( $items, $login_text, $logout_text );
	}

	/**
	 * Get CSS
	 */
	public function head_css( $output ) {
		$popup_width							= get_theme_mod( 'opl_popup_login_style_width', '500' );
		$popup_width_tablet						= get_theme_mod( 'opl_popup_login_style_width_tablet' );
		$popup_width_mobile						= get_theme_mod( 'opl_popup_login_style_width_mobile' );
		$top_padding 							= get_theme_mod( 'opl_popup_login_style_top_padding', '30' );
		$right_padding 							= get_theme_mod( 'opl_popup_login_style_right_padding', '100' );
		$bottom_padding 						= get_theme_mod( 'opl_popup_login_style_bottom_padding', '30' );
		$left_padding 							= get_theme_mod( 'opl_popup_login_style_left_padding', '100' );
		$tablet_top_padding 					= get_theme_mod( 'opl_popup_login_style_tablet_top_padding', '30' );
		$tablet_right_padding 					= get_theme_mod( 'opl_popup_login_style_tablet_right_padding', '100' );
		$tablet_bottom_padding 					= get_theme_mod( 'opl_popup_login_style_tablet_bottom_padding', '30' );
		$tablet_left_padding 					= get_theme_mod( 'opl_popup_login_style_tablet_left_padding', '100' );
		$mobile_top_padding 					= get_theme_mod( 'opl_popup_login_style_mobile_top_padding', '30' );
		$mobile_right_padding 					= get_theme_mod( 'opl_popup_login_style_mobile_right_padding', '50' );
		$mobile_bottom_padding 					= get_theme_mod( 'opl_popup_login_style_mobile_bottom_padding', '30' );
		$mobile_left_padding 					= get_theme_mod( 'opl_popup_login_style_mobile_left_padding', '50' );
		$top_radius 							= get_theme_mod( 'opl_popup_login_style_top_radius', '3' );
		$right_radius 							= get_theme_mod( 'opl_popup_login_style_right_radius', '3' );
		$bottom_radius 							= get_theme_mod( 'opl_popup_login_style_bottom_radius', '3' );
		$left_radius 							= get_theme_mod( 'opl_popup_login_style_left_radius', '3' );
		$tablet_top_radius 						= get_theme_mod( 'opl_popup_login_style_tablet_top_radius', '3' );
		$tablet_right_radius 					= get_theme_mod( 'opl_popup_login_style_tablet_right_radius', '3' );
		$tablet_bottom_radius 					= get_theme_mod( 'opl_popup_login_style_tablet_bottom_radius', '3' );
		$tablet_left_radius 					= get_theme_mod( 'opl_popup_login_style_tablet_left_radius', '3' );
		$mobile_top_radius 						= get_theme_mod( 'opl_popup_login_style_mobile_top_radius', '3' );
		$mobile_right_radius 					= get_theme_mod( 'opl_popup_login_style_mobile_right_radius', '3' );
		$mobile_bottom_radius 					= get_theme_mod( 'opl_popup_login_style_mobile_bottom_radius', '3' );
		$mobile_left_radius 					= get_theme_mod( 'opl_popup_login_style_mobile_left_radius', '3' );
		$popup_bg_img 							= get_theme_mod( 'opl_popup_login_style_bg' );
		$popup_bg 								= get_theme_mod( 'opl_popup_login_style_bg_color', '#ffffff' );
		$popup_title_color 						= get_theme_mod( 'opl_popup_login_style_title_color', '#333333' );
		$popup_content_color 					= get_theme_mod( 'opl_popup_login_style_content_color', '#777777' );
		$popup_input_color 						= get_theme_mod( 'opl_popup_login_style_input_color', '#757575' );
		$popup_input_border_color 				= get_theme_mod( 'opl_popup_login_style_input_border_color', '#dddddd' );
		$popup_input_border_focus_color 		= get_theme_mod( 'opl_popup_login_style_input_border_focus_color' );
		$popup_input_remember_color 			= get_theme_mod( 'opl_popup_login_style_remember_color', '#040404' );
		$popup_input_button_bg_color 			= get_theme_mod( 'opl_popup_login_style_button_bg_color' );
		$popup_input_button_bg_color_hover 		= get_theme_mod( 'opl_popup_login_style_button_bg_color_hover' );
		$popup_input_button_color 				= get_theme_mod( 'opl_popup_login_style_button_color', '#ffffff' );
		$popup_forgot_color 					= get_theme_mod( 'opl_popup_login_style_forgot_color' );
		$bottom_bg_color 						= get_theme_mod( 'opl_popup_login_style_bottom_bg_color', '#f6f6f6' );
		$bottom_color 							= get_theme_mod( 'opl_popup_login_style_bottom_color', '#000000' );
		$bottom_button_bg_color 				= get_theme_mod( 'opl_popup_login_style_bottom_button_bg_color', '#ffffff' );
		$bottom_button_color 					= get_theme_mod( 'opl_popup_login_style_bottom_button_color', '#1f1f1f' );
		$bottom_button_hover_bg_color 			= get_theme_mod( 'opl_popup_login_style_bottom_button_hover_bg_color' );
		$bottom_button_hover_color 				= get_theme_mod( 'opl_popup_login_style_bottom_button_hover_color' );
		$privacy_color 							= get_theme_mod( 'opl_popup_login_privacy_color' );
		$privacy_hover_color 					= get_theme_mod( 'opl_popup_login_privacy_hover_color' );

		// Set up empty variables.
		$css = '';
		$padding_css = '';
		$tablet_padding_css = '';
		$mobile_padding_css = '';
		$radius_css = '';
		$tablet_radius_css = '';
		$mobile_radius_css = '';

		// Popup width
		if ( ! empty( $popup_width ) && '500' != $popup_width ) {
			$css .= '#opl-login-form .opl-popup-block{width:'. $popup_width .'px;}';
		}

		// Popup width tablet
		if ( ! empty( $popup_width_tablet ) ) {
			$css .= '@media (max-width: 768px){#opl-login-form .opl-popup-block{width:'. $popup_width_tablet .'px;}}';
		}

		// Popup width mobile
		if ( ! empty( $popup_width_mobile ) ) {
			$css .= '@media (max-width: 480px){#opl-login-form .opl-popup-block{width:'. $popup_width_mobile .'px;}}';
		}

		// Popup top padding
		if ( ! empty( $top_padding ) && '30' != $top_padding ) {
			$padding_css .= 'padding-top:'. $top_padding .'px;';
		}

		// Popup right padding
		if ( ! empty( $right_padding ) && '100' != $right_padding ) {
			$padding_css .= 'padding-right:'. $right_padding .'px;';
		}

		// Popup bottom padding
		if ( ! empty( $bottom_padding ) && '30' != $bottom_padding ) {
			$padding_css .= 'padding-bottom:'. $bottom_padding .'px;';
		}

		// Popup left padding
		if ( ! empty( $left_padding ) && '100' != $left_padding ) {
			$padding_css .= 'padding-left:'. $left_padding .'px;';
		}

		// Popup padding css
		if ( ! empty( $top_padding ) && '30' != $top_padding
			|| ! empty( $right_padding ) && '100' != $right_padding
			|| ! empty( $bottom_padding ) && '30' != $bottom_padding
			|| ! empty( $left_padding ) && '100' != $left_padding ) {
			$css .= '#opl-login-form .opl-popup-block{'. $padding_css .'}';
		}

		// Tablet popup top padding
		if ( ! empty( $tablet_top_padding ) && '30' != $tablet_top_padding ) {
			$tablet_padding_css .= 'padding-top:'. $tablet_top_padding .'px;';
		}

		// Tablet popup right padding
		if ( ! empty( $tablet_right_padding ) && '100' != $tablet_right_padding ) {
			$tablet_padding_css .= 'padding-right:'. $tablet_right_padding .'px;';
		}

		// Tablet popup bottom padding
		if ( ! empty( $tablet_bottom_padding ) && '30' != $tablet_bottom_padding ) {
			$tablet_padding_css .= 'padding-bottom:'. $tablet_bottom_padding .'px;';
		}

		// Tablet popup left padding
		if ( ! empty( $tablet_left_padding ) && '100' != $tablet_left_padding ) {
			$tablet_padding_css .= 'padding-left:'. $tablet_left_padding .'px;';
		}

		// Tablet popup padding css
		if ( ! empty( $tablet_top_padding ) && '30' != $tablet_top_padding
			|| ! empty( $tablet_right_padding ) && '100' != $tablet_right_padding
			|| ! empty( $tablet_bottom_padding ) && '30' != $tablet_bottom_padding
			|| ! empty( $tablet_left_padding ) && '100' != $tablet_left_padding ) {
			$css .= '@media (max-width: 768px){#opl-login-form .opl-popup-block{'. $tablet_padding_css .'}}';
		}

		// Mobile popup top padding
		if ( ! empty( $mobile_top_padding ) && '30' != $mobile_top_padding ) {
			$mobile_padding_css .= 'padding-top:'. $mobile_top_padding .'px;';
		}

		// Mobile popup right padding
		if ( ! empty( $mobile_right_padding ) && '50' != $mobile_right_padding ) {
			$mobile_padding_css .= 'padding-right:'. $mobile_right_padding .'px;';
		}

		// Mobile popup bottom padding
		if ( ! empty( $mobile_bottom_padding ) && '30' != $mobile_bottom_padding ) {
			$mobile_padding_css .= 'padding-bottom:'. $mobile_bottom_padding .'px;';
		}

		// Mobile popup left padding
		if ( ! empty( $mobile_left_padding ) && '50' != $mobile_left_padding ) {
			$mobile_padding_css .= 'padding-left:'. $mobile_left_padding .'px;';
		}

		// Mobile popup padding css
		if ( ! empty( $mobile_top_padding ) && '30' != $mobile_top_padding
			|| ! empty( $mobile_right_padding ) && '50' != $mobile_right_padding
			|| ! empty( $mobile_bottom_padding ) && '30' != $mobile_bottom_padding
			|| ! empty( $mobile_left_padding ) && '50' != $mobile_left_padding ) {
			$css .= '@media (max-width: 480px){#opl-login-form .opl-popup-block{'. $mobile_padding_css .'}}';
		}

		// Popup top border radius
		if ( ! empty( $top_radius ) && '3' != $top_radius ) {
			$radius_css .= 'border-top-left-radius:'. $top_radius .'px;';
		}

		// Popup right border radius
		if ( ! empty( $right_radius ) && '3' != $right_radius ) {
			$radius_css .= 'border-top-right-radius:'. $right_radius .'px;';
		}

		// Popup bottom border radius
		if ( ! empty( $bottom_radius ) && '3' != $bottom_radius ) {
			$radius_css .= 'border-bottom-right-radius:'. $bottom_radius .'px;';
		}

		// Popup left border radius
		if ( ! empty( $left_radius ) && '3' != $left_radius ) {
			$radius_css .= 'border-bottom-left-radius:'. $left_radius .'px;';
		}

		// Popup border radius css
		if ( ! empty( $top_radius ) && '3' != $top_radius
			|| ! empty( $right_radius ) && '3' != $right_radius
			|| ! empty( $bottom_radius ) && '3' != $bottom_radius
			|| ! empty( $left_radius ) && '3' != $left_radius ) {
			$css .= '#opl-login-form .opl-popup-block{'. $radius_css .'}';
		}

		// Tablet popup top border radius
		if ( ! empty( $tablet_top_radius ) && '3' != $tablet_top_radius ) {
			$tablet_radius_css .= 'border-top-left-radius:'. $tablet_top_radius .'px;';
		}

		// Tablet popup right border radius
		if ( ! empty( $tablet_right_radius ) && '3' != $tablet_right_radius ) {
			$tablet_radius_css .= 'border-top-right-radius:'. $tablet_right_radius .'px;';
		}

		// Tablet popup bottom border radius
		if ( ! empty( $tablet_bottom_radius ) && '3' != $tablet_bottom_radius ) {
			$tablet_radius_css .= 'border-bottom-right-radius:'. $tablet_bottom_radius .'px;';
		}

		// Tablet popup left border radius
		if ( ! empty( $tablet_left_radius ) && '3' != $tablet_left_radius ) {
			$tablet_radius_css .= 'border-bottom-left-radius:'. $tablet_left_radius .'px;';
		}

		// Tablet popup border radius css
		if ( ! empty( $tablet_top_radius ) && '3' != $tablet_top_radius
			|| ! empty( $tablet_right_radius ) && '3' != $tablet_right_radius
			|| ! empty( $tablet_bottom_radius ) && '3' != $tablet_bottom_radius
			|| ! empty( $tablet_left_radius ) && '3' != $tablet_left_radius ) {
			$css .= '@media (max-width: 768px){#opl-login-form .opl-popup-block{'. $tablet_radius_css .'}}';
		}

		// Mobile popup top border radius
		if ( ! empty( $mobile_top_radius ) && '3' != $mobile_top_radius ) {
			$mobile_radius_css .= 'border-top-left-radius:'. $mobile_top_radius .'px;';
		}

		// Mobile popup right border radius
		if ( ! empty( $mobile_right_radius ) && '3' != $mobile_right_radius ) {
			$mobile_radius_css .= 'border-top-right-radius:'. $mobile_right_radius .'px;';
		}

		// Mobile popup bottom border radius
		if ( ! empty( $mobile_bottom_radius ) && '3' != $mobile_bottom_radius ) {
			$mobile_radius_css .= 'border-bottom-right-radius:'. $mobile_bottom_radius .'px;';
		}

		// Mobile popup left border radius
		if ( ! empty( $mobile_left_radius ) && '3' != $mobile_left_radius ) {
			$mobile_radius_css .= 'border-bottom-left-radius:'. $mobile_left_radius .'px;';
		}

		// Mobile popup border radius css
		if ( ! empty( $mobile_top_radius ) && '3' != $mobile_top_radius
			|| ! empty( $mobile_right_radius ) && '3' != $mobile_right_radius
			|| ! empty( $mobile_bottom_radius ) && '3' != $mobile_bottom_radius
			|| ! empty( $mobile_left_radius ) && '3' != $mobile_left_radius ) {
			$css .= '@media (max-width: 480px){#woo-popup-wrap #woo-popup-inner{'. $mobile_radius_css .'}}';
		}

		// Popup background image
		if ( ! empty( $popup_bg_img ) ) {
			$img = wp_get_attachment_image_src( absint( $popup_bg_img ), 'full' );
			$css .= '#opl-login-form .has-background-image{background-image: url(' . esc_url( $img[0] ) . ');}';
		}

		// Popup background color
		if ( ! empty( $popup_bg ) && '#ffffff' != $popup_bg ) {
			$css .= '#opl-login-form .opl-popup-block{background-color:'. $popup_bg .';}';
		}

		// Popup title color
		if ( ! empty( $popup_title_color ) && '#333333' != $popup_title_color ) {
			$css .= '#opl-login-form .opl-title{color:'. $popup_title_color .';}';
		}

		// Popup content color
		if ( ! empty( $popup_content_color ) && '#777777' != $popup_content_color ) {
			$css .= '#opl-login-form .opl-intro{color:'. $popup_content_color .';}';
		}

		// Popup input color
		if ( ! empty( $popup_input_color ) && '#757575' != $popup_input_color ) {
			$css .= '#opl-login-form .input-wrap .opl-label{color:'. $popup_input_color .';}';
		}

		// Popup input border color
		if ( ! empty( $popup_input_border_color ) && '#dddddd' != $popup_input_border_color ) {
			$css .= '#opl-login-form .input-wrap .opl-line{background-color:'. $popup_input_border_color .';}';
		}

		// Popup input border color
		if ( ! empty( $popup_input_border_focus_color ) ) {
			$css .= '#opl-login-form .input-wrap .opl-focus-line{background-color:'. $popup_input_border_focus_color .';}';
		}

		// Popup remember me color
		if ( ! empty( $popup_input_remember_color ) && '#040404' != $popup_input_remember_color ) {
			$css .= '#opl-login-form .input-wrap.opl-remember label{color:'. $popup_input_remember_color .';}';
		}

		// Popup button background color
		if ( ! empty( $popup_input_button_bg_color ) ) {
			$css .= '#opl-login-form .opl-button{background-color:'. $popup_input_button_bg_color .';}';
		}

		// Popup button background color hover
		if ( ! empty( $popup_input_button_bg_color_hover ) ) {
			$css .= '#opl-login-form .opl-button:hover{background-color:'. $popup_input_button_bg_color_hover .';}';
		}

		// Popup button color
		if ( ! empty( $popup_input_button_color ) && '#ffffff' != $popup_input_button_color ) {
			$css .= '#opl-login-form .opl-button{color:'. $popup_input_button_color .';}';
		}

		// Popup forgot password color
		if ( ! empty( $popup_forgot_color ) ) {
			$css .= '#opl-login-form .opl-text a{color:'. $popup_forgot_color .';}';
		}

		// Popup bottom background color
		if ( ! empty( $bottom_bg_color ) && '#f6f6f6' != $bottom_bg_color ) {
			$css .= '#opl-login-form .opl-bottom{background-color:'. $bottom_bg_color .';}';
		}

		// Popup bottom color
		if ( ! empty( $bottom_color ) && '#000000' != $bottom_color ) {
			$css .= '#opl-login-form .opl-bottom .text{color:'. $bottom_color .';}';
		}

		// Popup bottom button background color
		if ( ! empty( $bottom_button_bg_color ) && '#ffffff' != $bottom_button_bg_color ) {
			$css .= '#opl-login-form .opl-bottom .opl-btn{background-color:'. $bottom_button_bg_color .';}';
		}

		// Popup bottom button color
		if ( ! empty( $bottom_button_color ) && '#1f1f1f' != $bottom_button_color ) {
			$css .= '#opl-login-form .opl-bottom .opl-btn{color:'. $bottom_button_color .';}';
		}

		// Popup bottom button hover background color
		if ( ! empty( $bottom_button_hover_bg_color ) ) {
			$css .= '#opl-login-form .opl-bottom .opl-btn:hover{background-color:'. $bottom_button_hover_bg_color .';}';
		}

		// Popup bottom button hover color
		if ( ! empty( $bottom_button_hover_color ) ) {
			$css .= '#opl-login-form .opl-bottom .opl-btn:hover{color:'. $bottom_button_hover_color .';}';
		}

		// Popup bottom privacy color
		if ( ! empty( $privacy_color ) ) {
			$css .= '#opl-login-form .opl-privacy a{color:'. $privacy_color .';}';
		}

		// Popup bottom privacy hover color
		if ( ! empty( $privacy_hover_color ) ) {
			$css .= '#opl-login-form .opl-privacy a:hover{color:'. $privacy_hover_color .';}';
		}

		// Return CSS
		if ( ! empty( $css ) ) {
			$output .= '/* Login Popup CSS */'. $css;
		}

		// Return output css
		return $output;
	}

} // End Class