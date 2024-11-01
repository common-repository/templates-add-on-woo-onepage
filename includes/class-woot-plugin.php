<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class ACL_WOOT_Plugin {

	/**
	 * The single instance of ACL_Woo_Onepage_Plugin.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;
    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version='1.0.0';

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token='acl_woot';

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */

	public function __construct (  ) {
	    //Load requires and constant
        $this->assets_url=ACL_WOOT_URL.'assets';

        $this->define_constants();
        $this->includes();
        // Load frontend JS & CSS
        //add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
       add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
        // Load admin JS & CSS
        if(isset($_GET['page']) && ($_GET['page']=="woo-onepage")){
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );
        }
        // Handle localisation
        $this->load_plugin_textdomain();
        add_action( 'init', array( $this, 'load_localisation' ), 0 );

	} // End __construct ()

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {
        /**
         * install.
         */
        //include_once ACL_WOOT_ABSPATH . 'includes/class-woot-install.php';
        /**
        * admin.
         */
        include_once ('class-woot-admin.php');

        /**
         * operations.
         */
        include_once('class-woot-operation.php');
        
        /**
        * Varition Swatches
        */
        require_once ACL_WOOT_PATH . 'includes/variation-swatch/init.php';
 
        
    }
    /**
     * Define WC Constants.
     */
    private function define_constants() {
        $this->define( 'ACL_WOOT_ABSPATH', dirname( ACL_WOOT_PLUGIN_FILE ) . '/' );
        $this->define( 'ACL_WOOT_PHP_VERSION', '5.4'  );
        $this->define( 'ACL_WOOT_WP_VERSION', '3.0'  );
    }
    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }
	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
	    wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'scss/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {
	    wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . '/js/frontend.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-frontend' );

        wp_localize_script($this->_token . '-frontend', 'woot_ajax_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                )
        );

	} // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_styles ( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_scripts ( $hook = '' ) {
	    wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-admin' );
		wp_localize_script($this->_token . '-admin', 'woot_admin_object',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
            )
        );
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'woocommerce-one-page-checkout-shop', false, ACL_WOOT_ABSPATH . 'lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'woo-one-page-templates';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, ACL_WOOT_ABSPATH . 'lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main Woot Instance
	 *
	 * Ensures only one instance of Woot is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WordPress_Plugin_Template()
	 * @return Main WordPress_Plugin_Template instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()

    /**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

}