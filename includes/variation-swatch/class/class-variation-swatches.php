<?php 

/**
 * @package woo-onepage-templates
 */

defined( 'ABSPATH' ) or die('Do not access directly !');

/**
 * Main Class for the Woo Onepage Variation Swatches Plugin
 */
final class Woot_WCVariation_Swatches {
	/**
	 * The single class instance
	 *
	 * @var Woot_WCVariation_Swatches
	 */
	protected static $instance = null;

	/**
	 * Extra attribute types
	 *
	 * @var array
	 */
	public $types = array();

	/**
	 * Main instance
	 *
	 * @return Woot_WCVariation_Swatches
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->types = array(
			'label' => esc_html__( 'Label', 'woot' ),
		);

		$this->includes();
		$this->init_hooks();
	}


	/**
	 * Include the required core files utilized in admin and on the frontend.
	 */
	public function includes() {
		require_once ACL_WOOT_PATH . 'includes/variation-swatch/class/class-frontend.php';

		if ( is_admin() ) {
			require_once ACL_WOOT_PATH . 'includes/variation-swatch/class/class-admin.php';
		}
	}

	/**
	 * Initialize hooks
	 */
	public function init_hooks() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		add_filter( 'product_attributes_type_selector', array( $this, 'add_attribute_types' ) );

		if ( is_admin() ) {
			add_action( 'init', array( 'Woot_WCVariation_Swatches_Admin', 'instance' ) );
		}

		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			add_action( 'init', array( 'Woot_WCVariation_Swatches_Frontend', 'instance' ) );
		}
	}

	/**
	 * Add extra attribute types
	 * Add Color, Image and Label type
	 *
	 * @param array $types
	 *
	 * @return array
	 */
	public function add_attribute_types( $types ) {
		$types = array_merge( $types, $this->types );

		return $types;
	}

	/**
	 * Get attribute's properties data
	 *
	 * @param string $taxonomy
	 *
	 * @return object
	 */
	public function get_tax_attribute( $taxonomy ) {
		global $wpdb;

		$attr = substr( $taxonomy, 3 );
		$attr = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attr'" );

		return $attr;
	}

	/**
	 * Instance of admin
	 *
	 * @return object
	 */
	public function admin() {
		return Woot_WCVariation_Swatches_Admin::instance();
	}

	/**
	 * Instance of frontend
	 *
	 * @return object
	 */
	public function frontend() {
		return Woot_WCVariation_Swatches_Frontend::instance();
	}
}

if ( ! function_exists( 'woot_swatch' ) ) {
	/**
	 * Main Instance for the Super Product Variation Swatches Plugin
	 *
	 * @return Woot_WCVariation_Swatches
	 */
	function  woot_swatch() {
		return Woot_WCVariation_Swatches::instance();
	}
}