<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
//install
//register_activation_hook( ACL_WOOT_PLUGIN_FILE, 'acl_woot_install');
function acl_woot_install() {
    global $wp_version;
    if ( !class_exists('WooCommerce') ){
        wp_die('WooCommerce is required');
    }
    if ( version_compare( PHP_VERSION, ACL_WOOT_PHP_VERSION, '<' ) ) {
        wp_die('Minimum PHP Version required: ' . ACL_WOOT_PHP_VERSION );
    }
    if ( version_compare( $wp_version, ACL_WOOT_WP_VERSION, '<' ) ) {
        wp_die('Minimum Wordpress Version required: ' . ACL_WOOT_WP_VERSION );
    }
    //add_action('plugins_loaded', 'acl_woot_inactive_notice');
    //acl_woot_inactive_notice();
}
/**
 * Called when WooCommerce & Woo OnePage Checkout Shop is inactive to display an inactive notice.
 *
 * @since 1.0
 */
add_action('plugins_loaded', 'acl_woot_inactive_notice');
function acl_woot_inactive_notice() {
    if ( current_user_can( 'activate_plugins' ) ) :
        if ( !class_exists('WooCommerce') ) :
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            deactivate_plugins('woo-onepage-templates/woo-onepage-templates.php');
            ?>
            <div id="message" class="error">
                <p><?php printf( __( '%sWoo OnePage Checkout Shop Templates Add-on is inactive.%s The %sWooCommerce plugin%s must be active for Woo OnePage Checkout Shop Templates Add-on to work. Please %sinstall & activate WooCommerce%s', 'woo-one-page-templates' ), '<strong>', '</strong>', '<a href="http://wordpress.org/plugins/woocommerce/">', '</a>', '<a href="' . admin_url() . '/plugin-install.php?tab=plugin-information&plugin=woocommerce">', '&nbsp;&raquo;</a>' ); ?></p>
            </div>
        <?php elseif ( !class_exists('ACL_Woo_Onepage_Plugin')) :
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            deactivate_plugins('woo-onepage-templates/woo-onepage-templates.php');
            ?>
            <div id="message" class="error">
                <p><?php printf( __( '%sWoo OnePage Checkout Shop Templates Add-on is inactive.%s The %sWoo OnePage Checkout Shop plugin%s must be active for Woo OnePage Checkout Shop Templates Add-on to work. Please %sinstall & activate Woo OnePage Checkout Shop%s', 'woo-one-page-templates' ), '<strong>', '</strong>', '<a href="http://wordpress.org/plugins/woo-onepage/">', '</a>', '<a href="' . admin_url() . '/plugin-install.php?tab=plugin-information&plugin=woo-onepage">', '&nbsp;&raquo;</a>' ); ?></p>
            </div>
        <?php endif; ?>
    <?php endif;
}
?>