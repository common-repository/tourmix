<?php

/**
 * Handles the database usage of the plugin but not all
 * TourmixOrdersTableHandler class is more specific because its handle the orders table
 *
 * @since      1.0.0
 * @package    Tourmix
 * @subpackage Tourmix/includes
 * @author     Tourmix <info@tourmix.delivery>
 */

class TourmixDatabaseHandler {
    public const ORDER_DATE         = "tourmix_visible_order_date";
    public const DISPATCH_ADDRESS   = "tourmix_visible_dispatch_address";
    public const SHIPPING_ADDRESS   = "tourmix_visible_shipping_address";
    public const ORDER_TOTAL        = "tourmix_visible_order_total";
    public const PAGE_SIZE          = "tourmix_visible_page_size";
    public const DOWNLOAD_DIALOG    = "tourmix_visible_download_dialog";

    public function __construct() {}

    /**
     * Store the data inn the database
     */
    public function storeShippingSettings ($shippingSettingsData) {
        update_option( 'tourmix_saved_zip_code',            $shippingSettingsData->zipCode );
        update_option( 'tourmix_saved_city',                $shippingSettingsData->city );
        update_option( 'tourmix_saved_address',             $shippingSettingsData->address );
        update_option( 'tourmix_saved_street_number',       $shippingSettingsData->streetnumber );
        update_option( 'tourmix_saved_address_other',       $shippingSettingsData->addressOther );
        update_option( 'tourmix_saved_chosen_txpoint',      $shippingSettingsData->chosenTXPoint );
        update_option( 'tourmix_saved_chosen_txpoint_id',   $shippingSettingsData->chosenTXPointID );
    }

    /**
     * Create the options on the worldpress database to store data next time.
     * We have to do it only the first time when we enable the plugin
     */
    private function registerOptionsToTheDatabase () {
        add_option('tourmix_saved_zip_code',           '', '', 'yes');
        add_option('tourmix_saved_city',               '', '', 'yes');
        add_option('tourmix_saved_address',            '', '', 'yes');
        add_option('tourmix_saved_street_number',      '', '', 'yes');
        add_option('tourmix_saved_address_other',      '', '', 'yes');
        add_option('tourmix_saved_chosen_txpoint',     '', '', 'yes');
        add_option('tourmix_saved_chosen_txpoint_id',  '', '', 'yes');

        /*-----Visibility settings options-----*/
        add_option($this::ORDER_DATE,          '1', '', 'yes');
        add_option($this::DISPATCH_ADDRESS,    '1', '', 'yes');
        add_option($this::SHIPPING_ADDRESS,    '1', '', 'yes');
        add_option($this::ORDER_TOTAL,         '1', '', 'yes');
        add_option($this::PAGE_SIZE,           '20', '', 'yes');
        add_option($this::DOWNLOAD_DIALOG,     '1', '', 'yes');
    }

    /**
     * Get the api token from the database
     * 
     * @return string api token
     */
    public static function getApiToken () {
        return get_option( "tourmix_api_token" );
    }

    /**
     * Returns the the api token isset
     */
    public static function isSetApiToke () {
        return ( get_option( "tourmix_api_token" ) == false ? false : true );
    }

    /**
     * Save the api token in the database
     */
    public static function setApiToken ($apiToken) {
        add_option( 'tourmix_api_token', $apiToken, '', 'yes' );
    }

    /**
     * Save the default shipping setting in the detabase.
     * The default means that when we enable the plugin that time the stored address information by woocommerce.
     */
    public function saveDefaultShippingSettings () {
        $this->registerOptionsToTheDatabase();
    }

    /**
     * Delete options from the database
     */
    public function deleteOptions () {
        delete_option('tourmix_saved_zip_code');
        delete_option('tourmix_saved_city');
        delete_option('tourmix_saved_address');
        delete_option('tourmix_saved_street_number');
        delete_option('tourmix_saved_address_other');
        delete_option('tourmix_saved_chosen_txpoint');
        delete_option('tourmix_saved_chosen_txpoint_id');
        delete_option('tourmix_api_token' );

        /*-----Visibility settings options-----*/
        delete_option($this::ORDER_DATE);
        delete_option($this::DISPATCH_ADDRESS);
        delete_option($this::SHIPPING_ADDRESS);
        delete_option($this::ORDER_TOTAL);
        delete_option($this::PAGE_SIZE);
        delete_option($this::DOWNLOAD_DIALOG);
    }

    /**
     * Updates the visibility settings
     */
    public static function updateVisibilitySettings () {
        if(isset($_GET["update_tourmix_visibility_settings"])) {
            TourmixDatabaseHandler::updateOptionIfIsset(TourmixDatabaseHandler::ORDER_DATE);
            TourmixDatabaseHandler::updateOptionIfIsset(TourmixDatabaseHandler::DISPATCH_ADDRESS);
            TourmixDatabaseHandler::updateOptionIfIsset(TourmixDatabaseHandler::SHIPPING_ADDRESS);
            TourmixDatabaseHandler::updateOptionIfIsset(TourmixDatabaseHandler::ORDER_TOTAL);
            TourmixDatabaseHandler::updateOptionIfIsset(TourmixDatabaseHandler::PAGE_SIZE);
            TourmixDatabaseHandler::updateOptionIfIsset(TourmixDatabaseHandler::DOWNLOAD_DIALOG);
        }
    }

    public static function updateOptionIfIsset ($name) {
        update_option($name, TourmixDatabaseHandler::getValueIfIsset($name));
    }

    public static function getValueIfIsset ($name) {
        $default = "0";

        if($name == TourmixDatabaseHandler::PAGE_SIZE) {
            $default = "20";
        }

        return (isset($_GET[$name]) ? sanitize_text_field($_GET[$name]) : $default);
    }

    /**
     * Returns the stored page size
     */
    public static function getPageSize () {
        return get_option(TourmixDatabaseHandler::PAGE_SIZE);
    }

    /**
     * Returns the the given visibility settings is true or false
     * 
     * @param string $name - the name of the visibility setting (ORDER_DATE, DISPATCH_ADDRESS ...)
     */
    public static function isVisible($name) {
        return get_option($name) == "1";
    }
}