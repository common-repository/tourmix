<?php
/**
 * This shows the orders.
 * This file is only inserted if we have orders
 * 
 * @since      1.1.0
 * @package    Tourmix
 * @subpackage Tourmix/page-parts
 * @author     Tourmix <info@tourmix.delivery>
 */

defined( 'ABSPATH' ) || exit;


include plugin_dir_path( __FILE__ ) . "settings.php";
include plugin_dir_path( __FILE__ ) . "orders-table.php";
include plugin_dir_path( __FILE__ ) . "invoice-numbers-dialog.php";
include plugin_dir_path( __FILE__ ) . "information-dialog.php";
include plugin_dir_path( __FILE__ ) . "parcel-label-dialog.php";
include plugin_dir_path( __FILE__ ) . "download-dialog.php";