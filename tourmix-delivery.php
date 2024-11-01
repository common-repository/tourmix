<?php

/**
 * @link              https://tourmix.delivery/
 * @since             1.1.3
 * @package           TOURMIX
 *
 * @wordpress-plugin
 * Plugin Name:       TOURMIX
 * Plugin URI:        https://tourmix.delivery/
 * Description:       TOURMIX a környezettudatos csomagszállítási alternatíva
 * Version:           1.1.3
 * Author:            TOURMIX Hungary Ltd.
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'TOURMIX_DELIVERY_VERSION', '1.1.3' );

require_once plugin_dir_path( __FILE__ ) . './inc/TourmixDatabaseHandler.php';
require_once plugin_dir_path( __FILE__ ) . './inc/tourmix-objects.php';
require_once plugin_dir_path( __FILE__ ) . './inc/TourmixShippingMethod.php';
require_once plugin_dir_path( __FILE__ ) . './inc/TourmixSubmenuHandler.php';
require_once plugin_dir_path( __FILE__ ) . './inc/TourmixAjaxHandler.php';
require_once plugin_dir_path( __FILE__ ) . './inc/TourmixOrdersTableHandler.php';
require_once plugin_dir_path( __FILE__ ) . './inc/TourmixApiHandler.php';
require_once plugin_dir_path( __FILE__ ) . './inc/TourmixParcelLabelsTableHandler.php';

/**
 * Function is called when the plugin activated
 */
function tourmixActivation () {
	$tourmixOrdersTableHandler 	= new TourmixOrdersTableHandler();
	$parcelLabelsTableHandler 	= new TourmixParcelLabelsTableHandler();
	$tourmixDatabaseHandler 	= new TourmixDatabaseHandler();
	
	$tourmixOrdersTableHandler->createTable();
	$parcelLabelsTableHandler->createTable();
	$tourmixDatabaseHandler->saveDefaultShippingSettings();
}

/**
 * Function is called when the plugin deactivated
 */
function tourmixDeactivation () { }

/**
 * Function is called when the plugin is deleted
 */
function tourmixUninstall () {
	$tourmixOrdersTableHandler = new TourmixOrdersTableHandler();
	$parcelLabelsTableHandler 	= new TourmixParcelLabelsTableHandler();
	$tourmixDatabaseHandler = new TourmixDatabaseHandler();

	$tourmixOrdersTableHandler->deleteTable();
	$parcelLabelsTableHandler->deleteTable();
	$tourmixDatabaseHandler->deleteOptions();
}

/**
 * Create all the objects if woocommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	$tourmixSubmenuHandler = new TourmixSubmenuHandler();
	$tourmixAjaxHandler = new TourmixAjaxHandler();
	$tourmixOrdersTableHandler = new TourmixOrdersTableHandler();
	$tourmixDatabaseHandler = new TourmixDatabaseHandler();

	register_activation_hook( __FILE__, 'tourmixActivation' );
	register_deactivation_hook( __FILE__, 'tourmixDeactivation' );
	register_uninstall_hook( __FILE__, 'tourmixUninstall' );
}

/**
 * Enqueuing the scripts and styles when the TOURMIX page is used
 */
add_action( 'admin_enqueue_scripts', function () {
	global $pagenow;

	// Check if we are on the correct submenu page
	if ( $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == 'tourmix-orders-handler' ) {
		wp_enqueue_style( 'tourmix-orders-handler-page-styles', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), TOURMIX_DELIVERY_VERSION );
		wp_enqueue_style( 'tourmix-popup-dialog-style', plugin_dir_url( __FILE__ ) . 'css/PopupDialog.css', array(), TOURMIX_DELIVERY_VERSION );
		wp_enqueue_style( 'tourmix-transfer-dialog-style', plugin_dir_url( __FILE__ ) . 'css/InvoiceNumbersDialog.css', array(), TOURMIX_DELIVERY_VERSION );
		wp_enqueue_style( 'tourmix-visibility-settings-style', plugin_dir_url( __FILE__ ) . 'css/Visibility.css', array(), TOURMIX_DELIVERY_VERSION );

		wp_enqueue_script( 'tourmix-orders-handler-page-scripts', plugin_dir_url( __FILE__ ) . 'js/TourmixServices.js', array(), TOURMIX_DELIVERY_VERSION );
        wp_enqueue_script( 'tourmix-invoice-numbers-dialog-scripts', plugin_dir_url( __FILE__ ) . 'js/InvoiceNumbersDialog.js', array(), TOURMIX_DELIVERY_VERSION );
		wp_enqueue_script( 'tourmix-information-dialog-scripts', plugin_dir_url( __FILE__ ) . 'js/InformationDialog.js', array(), TOURMIX_DELIVERY_VERSION );
		wp_enqueue_script( 'tourmix-label-dialog-scripts', plugin_dir_url( __FILE__ ) . 'js/ParcelLabelDialog.js', array(), TOURMIX_DELIVERY_VERSION );
		wp_enqueue_script( 'tourmix-download-dialog-scripts', plugin_dir_url( __FILE__ ) . 'js/DownloadDialog.js', array(), TOURMIX_DELIVERY_VERSION );
		wp_enqueue_script( 'tourmix-settings-toggler-scripts', plugin_dir_url( __FILE__ ) . 'js/SettingsToggler.js', array(), TOURMIX_DELIVERY_VERSION );
		wp_enqueue_script( 'tourmix-searchable-select-scripts', plugin_dir_url( __FILE__ ) . 'js/SearchableSelect.js', array(), TOURMIX_DELIVERY_VERSION );
	}

	/**
	 * This style should be always loaded
	 */
	wp_enqueue_style( 'tourmix-woocommerce-overwrite-styles', plugin_dir_url( __FILE__ ) . 'css/wcStyleOverwrite.css' );
} );

/**
 * Register the two custom order status (wc-tourmix-tobesent, wc-tourmix-shipping)
 */
add_action( 'init', function () {
    register_post_status( 'wc-tourmix-tobesent', array(
        'label'                     => 'Feladandó',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Feladandó <span class="count">(%s)</span>', 'Feladandó <span class="count">(%s)</span>' )
    ) );

	register_post_status( 'wc-tourmix-shipping', array(
        'label'                     => 'Szállítás alatt',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Szállítás alatt <span class="count">(%s)</span>', 'Szállítás alatt <span class="count">(%s)</span>' )
    ) );
} );

add_filter( 'wc_order_statuses', function ( $order_statuses ) {
    $order_statuses['wc-tourmix-tobesent'] 	= 'Feladandó';
	$order_statuses['wc-tourmix-shipping'] 	= 'Szállítás alatt'; 

    return $order_statuses;
});

/**
 * Rest API.
 * Torumix server can call this endpoint to notify the shop that an order's status was changed
 * 
 * example: http://localhost/wp-json/tourmix-delivery/status-changed/100
 */
add_action( 'rest_api_init', function () {
	register_rest_route( 'tourmix-delivery', 'status-changed/(?P<id>\d+)', [
        'methods' => 'GET',
        'callback' => 'tourmixUpdateOrderStatusEndpoint',
    ] );
} );

function tourmixUpdateOrderStatusEndpoint($request) {
	$apiHandler = new TourmixApiHandler();

	return [
        'message' => $apiHandler->updateOrderStatusForAccessKey( $request['id'] )
	];
}