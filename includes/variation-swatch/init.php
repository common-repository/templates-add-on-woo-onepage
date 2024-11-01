<?php 

/**
 * @package woo-onepage-templates
 */

defined( 'ABSPATH' ) or die('Do not access directly !');

include dirname(__FILE__) . '/class/class-variation-swatches.php';
include dirname(__FILE__) . '/functions.php';

$woot_swatch = woot_swatch();
$woot_swatch->frontend();
// $woot_swatch->admin();
require_once ACL_WOOT_PATH . 'includes/variation-swatch/class/class-admin.php';
Woot_WCVariation_Swatches_Admin::instance();