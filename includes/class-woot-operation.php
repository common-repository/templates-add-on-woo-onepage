<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class ACL_WOOT_Operation {

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct () {
        add_filter( 'acl_woosc_search_form_param', array($this,'search_form_callback') );
        add_filter( 'acl_woosc_products_param', array($this,'products_param_callback') );
    }
    /**
     * Overriding the search form based on the setting templates.
     * @access  public
     * @since   1.0.0
     * @param $settings
     * @return mixed
     */
    public function search_form_callback($param) {
        if($param['template']!='1' && $param['template']!='2') {
            $param['template_url'] = __DIR__ . '/../templates/';
            $param['plugin_path'] =  ACL_WOOT_PATH ;
        }
        return $param;
    } // End search_form_callback ()

    /**
     * Overriding the search form based on the setting templates.
     * @access  public
     * @since   1.0.0
     * @param $settings
     * @return mixed
     */
    public function products_param_callback ($param) {
        if($param['template']!='1' && $param['template']!='2') {
            $param['template_url'] = __DIR__ . '/../templates/';
            $param['plugin_url'] =  ACL_WOOT_URL ;
            $param['plugin_path'] =  ACL_WOOT_PATH ;
        }
        return $param;
    } // End products_param_callback ()

}
new ACL_WOOT_Operation();
