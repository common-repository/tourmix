<?php

/**
 * Handles the ajax calls
 *
 * @since      1.1.0
 * @package    Tourmix
 * @subpackage Tourmix/includes
 * @author     Tourmix <info@tourmix.delivery>
 */

class TourmixAjaxHandler {
    private $tourmixApiHandler;
    private $tourmixDatabaseHandler;

    public function __construct() {
        $this->initVariables();
        $this->addActions();
    }

    public function initVariables () {
        $this->tourmixApiHandler = new TourmixApiHandler();
        $this->tourmixDatabaseHandler = new TourmixDatabaseHandler();
    }

    public function addActions () {
        add_action( 'wp_ajax_tourmixSendOrdersToApi', array($this, 'sendOrdersToApi') );
        add_action( 'wp_ajax_tourmixChangeLastLinkToDownloaded', array($this, 'changeLastLinkToDownloaded') );
    }

    /**
     * Sends the orders to the tourmix appi
     */
    public function sendOrdersToApi () {
        $orderIDs = $this->getOrderIds();
        $invoiceArray = $this->getInvoiceNumbers();

        $response = $this->tourmixApiHandler->addParcelsToApi( $this->tourmixDatabaseHandler->getApiToken(), $orderIDs, $invoiceArray );
    
        wp_send_json_success( $response );
        wp_die();
    }
    
    /**
     * We can create an ajax request to call this function. This function will set the last link to downloaded
     */
    public function changeLastLinkToDownloaded() {
        $handler = new TourmixParcelLabelsTableHandler();
        $handler->setLastLinkDownloaded();
    }

    /**
     * If the post request contains the order IDs sanitize it and returns back.
     */
    private function getOrderIds() {
        $orderIDs = [];

        if(isset($_POST['order_ids'])) {
            foreach ($_POST['order_ids'] as $key => $value) {
                $orderIDs[$key] = sanitize_text_field($value);
            }
        }

        return $orderIDs;
    }

    /**
     * If the post request contains the invoice numbers sanitize it and returns back.
     */
    private function getInvoiceNumbers() {
        $invoiceArray = [];

        if(isset($_POST['invoice_array'])) {
            foreach ($_POST['invoice_array'] as $key => $value) {
                $invoiceArray[$key] = sanitize_text_field($value);
            }
        }

        return $invoiceArray;
    }
}