<?php
/**
 * Plugin Name:			OceanWP Extension Boilerplate
 * Plugin URI:			https://github.com/oceanwp/oceanwp-extension-boilerplate
 * Description:			A boilerplate plugin for creating OceanWP extensions.
 * Version:				1.0.0
 * Author:				OceanWP
 * Author URI:			https://oceanwp.org/
 * Requires at least:	4.0.0
 * Tested up to:		4.7
 *
 * Text Domain: oceanwp-extension-boilerplate
 * Domain Path: /languages/
 *
 * @package OceanWP_Extension_Boilerplate
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the main instance of OceanWP_Extension_Boilerplate to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object OceanWP_Extension_Boilerplate
 */
function OceanWP_Extension_Boilerplate() {
	return OceanWP_Extension_Boilerplate::instance();
} // End OceanWP_Extension_Boilerplate()

OceanWP_Extension_Boilerplate();

/**
 * Main OceanWP_Extension_Boilerplate Class
 *
 * @class OceanWP_Extension_Boilerplate
 * @version	1.0.0
 * @since 1.0.0
 * @package	OceanWP_Extension_Boilerplate
 */
final class OceanWP_Extension_Boilerplate {
	/**
	 * OceanWP_Extension_Boilerplate The single instance of OceanWP_Extension_Boilerplate.
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

	// Customizer preview
	private $enable_postMessage  = true;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct() {
		$this->token 			= 'oceanwp-extension-boilerplate';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.0';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'oeb_load_plugin_textdomain' ) );

		add_action( 'init', array( $this, 'oeb_setup' ) );
	}

	/**
	 * Main OceanWP_Extension_Boilerplate Instance
	 *
	 * Ensures only one instance of OceanWP_Extension_Boilerplate is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see OceanWP_Extension_Boilerplate()
	 * @return Main OceanWP_Extension_Boilerplate instance
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
	public function oeb_load_plugin_textdomain() {
		load_plugin_textdomain( 'oceanwp-extension-boilerplate', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
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
	 * Setup all the things.
	 * Only executes if OceanWP or a child theme using OceanWP as a parent is active and the extension specific filter returns true.
	 * @return void
	 */
	public function oeb_setup() {
		$theme = wp_get_theme();

		if ( 'OceanWP' == $theme->name || 'oceanwp' == $theme->template ) {
			add_action( 'customize_preview_init', array( $this, 'oeb_customize_preview_init' ) );
			add_action( 'customize_register', array( $this, 'oeb_customize_register' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'oeb_style' ), 999 );
			add_filter( 'ocean_head_css', array( $this, 'oeb_head_css' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'oeb_install_oceanwp_notice' ) );
		}
	}

	/**
	 * OceanWP install
	 * If the user activates the plugin while having a different parent theme active, prompt them to install OceanWP.
	 * @since   1.0.0
	 * @return  void
	 */
	public function oeb_install_oceanwp_notice() {
		echo '<div class="notice is-dismissible updated">
				<p>' . esc_html__( 'OceanWP Extension Boilerplate requires that you use OceanWP as your parent theme.', 'oceanwp-extension-boilerplate' ) . ' <a href="https://oceanwp.org/">' . esc_html__( 'Install OceanWP now', 'oceanwp-extension-boilerplate' ) . '</a></p>
			</div>';
	}

	/**
	 * Loads js file for customizer preview
	 */
	public function oeb_customize_preview_init() {
		if ( $this->enable_postMessage ) {
			wp_enqueue_script( 'oeb-customize-preview',
				plugins_url( '/assets/js/customizer.min.js', __FILE__ ),
				array( 'customize-preview' ),
				OCEANWP_THEME_VERSION,
				true
			);
		}
	}

	/**
	 * Customizer Controls and settings
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function oeb_customize_register( $wp_customize ) {

		/**
	     * Add a new section
	     */
        $wp_customize->add_section( 'oeb_section' , array(
		    'title'      	=> esc_html__( 'OceanWP Extension Boilerplate', 'oceanwp-extension-boilerplate' ),
		    'description'   => esc_html__( 'Add your description for this section.', 'oceanwp-extension-boilerplate' ),
		    'priority'   	=> 210,
		) );

		/**
		 * Checkbox Control
		 */
		$wp_customize->add_setting( 'oeb_checkbox_control', array(
			'transport' 			=> 'postMessage',
			'default'           	=> true,
			'sanitize_callback' 	=> 'absint',
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Buttonset_Control( $wp_customize, 'oeb_checkbox_control', array(
			'label'	   				=> esc_html__( 'Checkbox Control', 'oceanwp-extension-boilerplate' ),
			'type' 					=> 'checkbox',
			'section'  				=> 'oeb_section',
			'settings' 				=> 'oeb_checkbox_control',
			'priority' 				=> 10,
		) ) );

		/**
		 * Buttonset Control
		 */
		$wp_customize->add_setting( 'oeb_buttonset_control', array(
			'transport' 			=> 'postMessage',
			'default'           	=> 'option1',
			'sanitize_callback' 	=> false,
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Buttonset_Control( $wp_customize, 'oeb_buttonset_control', array(
			'label'	   				=> esc_html__( 'Buttonset Control', 'oceanwp-extension-boilerplate' ),
			'section'  				=> 'oeb_section',
			'settings' 				=> 'oeb_buttonset_control',
			'priority' 				=> 10,
			'choices' 				=> array(
				'option1'  			=> esc_html__( 'Option 1', 'oceanwp-extension-boilerplate' ),
				'option2' 			=> esc_html__( 'Option 2', 'oceanwp-extension-boilerplate' ),
			),
		) ) );

		/**
		 * Color Control
		 */
		$wp_customize->add_setting( 'oeb_color_control', array(
			'transport' 			=> 'postMessage',
			'default'           	=> '#fff',
			'sanitize_callback' 	=> false,
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'oeb_color_control', array(
			'label'	   				=> esc_html__( 'Color Control', 'oceanwp-extension-boilerplate' ),
			'section'  				=> 'oeb_section',
			'settings' 				=> 'oeb_color_control',
			'priority' 				=> 10,
		) ) );

		/**
		 * Heading Control
		 */
		$wp_customize->add_setting( 'oeb_heading_control', array(
			'sanitize_callback' 	=> false,
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'oeb_heading_control', array(
			'label'	   				=> esc_html__( 'Heading Control', 'oceanwp-extension-boilerplate' ),
			'section'  				=> 'oeb_section',
			'settings' 				=> 'oeb_heading_control',
			'priority' 				=> 10,
		) ) );

		/**
		 * Range Control
		 */
		$wp_customize->add_setting( 'oeb_range_control', array(
			'transport' 			=> 'postMessage',
			'default'           	=> '50',
			'sanitize_callback' 	=> false,
		) );

		$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'oeb_range_control', array(
			'label'	   				=> esc_html__( 'Range Control', 'oceanwp' ),
			'section'  				=> 'oeb_section',
			'settings' 				=> 'oeb_range_control',
			'priority' 				=> 10,
		    'input_attrs' 			=> array(
		        'min'   => 0,
		        'max'   => 300,
		        'step'  => 1,
		    ),
		) ) );
	}

	/**
	 * Enqueue style.
	 * @since   1.0.0
	 * @return  void
	 */
	public function oeb_style() {
		
		// Load main stylesheet
		wp_enqueue_style( 'oeb-styles', plugins_url( '/assets/css/style.min.css', __FILE__ ) );
		
	}

	/**
	 * Add css in head tag.
	 */
	public function oeb_head_css( $output ) {

		// Global vars
		$color_control = get_theme_mod( 'oeb_color_control', '#fff' );
		$range_control = get_theme_mod( 'oeb_range_control', '50' );

		// Define css var
		$css = '';

		// Add css
		if ( ! empty( $color_control ) && '#fff' != $color_control ) {
			$css .= 'body{background-color:'. $color_control .';}';
		}

		// Add css
		if ( ! empty( $range_control ) && '50' != $range_control ) {
			$css .= '#main #content-wrap{padding:'. $range_control .'px 0;}';
		}
			
		// Return CSS
		if ( ! empty( $css ) ) {
			$output .= '/* OceanWP Extension Boilerplate CSS */'. $css;
		}

		// Return output css
		return $output;

	}

} // End Class