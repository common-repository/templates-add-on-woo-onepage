<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class ACL_WOOT_Admin {

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct () {
        add_filter( 'acl_woosc_settings_fields', array($this,'setting_option_callback') );
    }
    /**
     * Adding option to Woosc plugin admin settings.
     * @access  public
     * @since   1.0.0
     * @param $settings
     * @return mixed
     */
	public function setting_option_callback ($settings) {
        $settings['woosc_ui']['fields'][0]['options']=array(
            '1'=>'template 01',
            '2'=>'template 02',
            '3'=>'template 03',
            '4'=>'template 04',
        );
        return $settings;
	} // End setting_option_callback ()

}
//Exporting admin settings to Woosc plugin
if ( is_admin() && isset($_GET['page']) && ($_GET['page']=="woocommerce-one-page-checkout-shop") ) {
    new ACL_WOOT_Admin();
}