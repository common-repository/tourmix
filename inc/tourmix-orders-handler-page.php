<?php
/**
 * This is tha layout of the TOURMIX page where the user can handle the orders
 * 
 * @since      1.0.0
 * @package    Tourmix
 * @subpackage Tourmix/includes
 * @author     Tourmix <info@tourmix.delivery>
 */

defined( 'ABSPATH' ) || exit;

/*-----Update visibility settings and page size-----*/
TourmixDatabaseHandler::updateVisibilitySettings();

/*-----Constants-----*/
define("TOURMIX_PAGE_SIZE", TourmixDatabaseHandler::getPageSize());
define("TOURMIX_CURRENT_PAGE", (isset($_GET['paged']) ? sanitize_text_field($_GET['paged']) : 1));
define("TOURMIX_STATUS_FILTER", (isset($_GET['status']) ? sanitize_text_field($_GET['status']) : null));

/*-----Variables-----*/
$tourmixDatabaseHandler     = new TourmixDatabaseHandler();
$tourmixApiHandler          = new TourmixApiHandler();
$parcelLabelsTableHandler 	= new TourmixParcelLabelsTableHandler();
$tourmixOrdersTableHandler  = new TourmixOrdersTableHandler(TOURMIX_PAGE_SIZE, TOURMIX_STATUS_FILTER);

$tourmix_order_ids          = $tourmixOrdersTableHandler->getOrderIds(TOURMIX_CURRENT_PAGE, TOURMIX_STATUS_FILTER);


include plugin_dir_path( __FILE__ ) . "../page-parts/header.php";


if(isset($_POST['api_token'])) {
    $apiToken = str_replace(" ", "", sanitize_text_field($_POST['api_token']));

    if(TourmixApiHandler::isTokenValid($apiToken)) {
        TourmixDatabaseHandler::setApiToken($apiToken);
        TourmixApiHandler::registerEnpointUrl($apiToken);
    }
}


/*-----If we have the api token-----*/
if(TourmixDatabaseHandler::isSetApiToke()) {

    /*-----If we have any order-----*/
    if($tourmix_order_ids != null && count($tourmix_order_ids) > 0) {
        include plugin_dir_path( __FILE__ ) . "../page-parts/show-orders.php";
    } else {
        include plugin_dir_path( __FILE__ ) . "../page-parts/no-orders.php";
    }
} else {
    include plugin_dir_path( __FILE__ ) . "../page-parts/api-token-form.php";
}

?>

