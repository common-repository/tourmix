<?php

/**
 * Handles the communication with the tourmix api
 *
 * @since      1.1.3
 * @package    Tourmix
 * @subpackage Tourmix/includes
 * @author     Tourmix <info@tourmix.delivery>
 */

class TourmixApiHandler {
    public const TOURMIX_API_URL     = "https://tourmix.delivery/api/"; //TX_POINTS, ADD_PARCELS, GET_PARCES_STATUS, REGISTER_ENDPOINT, SHIPPING_LABEL, ALLOWED_ZIPS
    //public const TOURMIX_API_URL        = "https://test.tourmix.delivery/api/"; //Test server

    public const TX_POINTS              = "tx_points?api_token=";               //<api_token> - GET
    public const ADD_MULTIPLE_PARCELS   = "post_multiple_parcels?api_token=";   //<api_token> - POST
    public const GET_PARCES_STATUS      = "parcels/";                           //<outer_id_type>/<outer_id>/status?api_token=<api_token>
    public const REGISTER_ENDPOINT      = "outer_url?api_token=";               //
    public const SHIPPING_LABEL         = "shipping_label/";                    //

    public const API_ERROR_MESSAGE      = "Unauthenticated.";

    public const CREATED        = 'Létrehozva';
    public const AVAILABLE      = 'Mixerre vár';
    public const RESERVED       = 'Mixer lefoglalta';
    public const ATMIXER        = 'Mixernél';
    public const PENDING        = 'Időpont egyeztetés';
    public const DELIVERED      = 'Kézbesítve';
    public const UNSUCCESSFUL   = 'Sikertelen kézbesítés';
    public const FORCHECK       = 'Ellenőrzésre vár';
    public const AT_PUDO        = 'Csomagponton';
    public const DISPATCHED     = 'Feladva';
    public const UNKNOWN        = 'Ismeretlen státusz';
        
    public const MISSING        = 'Rendelés nem található';

    public function __construct() {}

    /**
     * Checks that the given token is valid or not
     * 
     * @param string $api_token
     * @return boolean
     */
    public static function isTokenValid ($api_token) {
        $url = TourmixApiHandler::TOURMIX_API_URL . TourmixApiHandler::TX_POINTS . $api_token;
        $object = json_decode(TourmixApiHandler::createCurlRequest($url));

        if(isset($object->message) && $object->message == TourmixApiHandler::API_ERROR_MESSAGE) {
            return false;
        }

        return true;
    }

    /**
     * Request the status for the specified parcels from the api
     * 
     * @param string $api_token
     * @param array $orderIDs
     * @return array - containes all the information for a parcel
     */
    public static function parcelStatusApiRequest ($api_token, $accessKeys) {
        $url = TourmixApiHandler::TOURMIX_API_URL . TourmixApiHandler::GET_PARCES_STATUS;
        $url .= implode(',', $accessKeys);
        $url .= '/status?api_token=' . $api_token;

        return json_decode(TourmixApiHandler::createCurlRequest($url));
    }

    /**
     * Requesting the pdf file url what is containing the given parcel label
     */
    public static function parcelShippingLabelApiRequest ($api_token, $parcelNumbers) {
        $url = TourmixApiHandler::TOURMIX_API_URL . TourmixApiHandler::SHIPPING_LABEL;
        $url .= implode(',', $parcelNumbers);
        $url .= '?api_token=' . $api_token;

        return json_decode(TourmixApiHandler::createCurlRequest($url));
    }

    /**
     * Get the status for the specified parcels from the api
     * 
     * @param string $api_token
     * @param array $orderIDs
     * @return array - associative array [woocommerce_order_id] = status . Example [12] = AVAILABLE
     */
    public function getParcelStatuses ($api_token, $accessKeys) {
        $object = $this->parcelStatusApiRequest($api_token, $accessKeys);
        $statusArray = array();

        foreach ($object->parcels as $object) {
            $statusArray += [
                $object->access_key => $object->statuses[0]->status
            ];
        }

        $statusArray = $this->parcelStatusIsMissing($statusArray, $accessKeys);

        return $statusArray;
    }

    /**
     * Checks that in the given $statusArray is containing every orderID.
     * If not the order is missing.
     * 
     * @param array $statusArray
     * @param array $orderIDs
     * 
     * @return array containes the statuses
     */
    private function parcelStatusIsMissing ($statusArray, $orderIDs) {
        foreach ($orderIDs as $id) {
            if(!array_key_exists($id, $statusArray)) {
                $statusArray += [
                    $id => 'MISSING'
                ];
            }
        }

        return $statusArray;
    }

    /**
     * Get the string for the parcel status for humans
     * 
     * @return text
     */
    public function getStringForParcelStatus ($parcelStatus) {
        if( !isset( $parcelStatus ) ) {
            $parcelStatus = "MISSING";
        }

        return constant( "TourmixApiHandler::" . $parcelStatus );
    }

    /**
     * Calculates the give order weight
     * 
     * @param WC_Order $wc_order
     * @param number $default
     * @return number
     */
    public function getOrderWeight ($wc_order, $default) {
        $total_weight = 0;

        foreach ($wc_order->get_items() as $item) {
            $product = $item->get_product();
            $weight = ($product->get_weight() == "" ? 0 : $product->get_weight());
            $quantity = $item->get_quantity();
            $total_weight += ($weight * $quantity);
        }

        $total_weight = round($total_weight);

        return ($total_weight == 0 ? $default : $total_weight);
    }

    /**
     * This function send all the parcels to the tourmix api server
     * 
     * @param string $apiToken
     * @param array $orderIDs
     * @param assoc_array $invoiceArray
     */
    public function addParcelsToApi ($apiToken, $orderIDs, $invoiceArray) {
        $parcelsData = $this->generateParcelsData( $orderIDs, $invoiceArray );

        $handler = new TourmixOrdersTableHandler();
        $handler->changeOrdersStatus( $orderIDs, $handler::SHIPPING );

        $responseData = $this->sendMultipleParcelsDataToApi(
            $apiToken,  
            $parcelsData->getSetProperties()
        );

        $accessKeys = $this->fetchAccessKeysFromResponse( $orderIDs, $responseData );

        $this->updateOrdersData(
            $handler, 
            $orderIDs, 
            $accessKeys
        );

        $labelHandler = new TourmixParcelLabelsTableHandler();
        $labelHandler->saveLink( $responseData->label_url, $accessKeys );


        return $responseData;
    }

    /**
     * This function can be used to generate the parcels data by the give orderIDs and invoiceNumbers.
     * The generated object can be sent to the Tourmix API.
     */
    public function generateParcelsData ($orderIDs, $invoiceNumbers) {

        $parcels = new TourmixParcelsObject();

        foreach ( $orderIDs as $id ) {
            $order = new WC_Order( $id );
            $parcel = new TourmixParcelObject();


            /* ---------- Start location ---------- */
            $start_location = new TourmixLocationObject();

            $street_info = explode( ' ', get_option( 'woocommerce_store_address' ) );
            $street = '';

            for($i = 0; $i<count($street_info)-1; $i++) {
                $street .= $street_info[$i] . ' ';
            }

            $start_location->zip        = $this->getValue( get_option( 'woocommerce_store_postcode' ), "1133" );
            $start_location->city       = $this->getValue( get_option( 'woocommerce_store_city' ) );
            $start_location->street     = $this->getValue( $street );
            $start_location->number     = $this->getValue( $street_info[count($street_info)-1] );
            $start_location->other      = $this->getValue( get_option( 'woocommerce_store_address_2' ), NULL );
            

            /* ---------- End location ---------- */
            $end_location = new TourmixLocationObject();

            $street_info = explode( ' ', $order->get_billing_address_1() );

            $street = '';

            for($i = 0; $i<count($street_info)-1; $i++) {
                $street .= $street_info[$i] . ' ';
            }

            $end_location->zip        = $this->getValue( $order->get_billing_postcode(), "1133" );
            $end_location->city       = $this->getValue( $order->get_billing_city() );
            $end_location->street     = $this->getValue( $street );
            $end_location->number     = $this->getValue( $street_info[count($street_info)-1] );
            $end_location->other      = $this->getValue( $order->get_billing_address_2(), NULL );


            /* ---------- Recipient ---------- */
            $recipient = new TourmixRecipientObject();

            $recipient->name    = $this->getValue( $order->get_formatted_billing_full_name() );
            $recipient->phone   = $this->getValue( $order->get_billing_phone() );
            $recipient->email   = $this->getValue( $order->get_billing_email() );


            /* ---------- Parcel ---------- */
            $parcel->recipient = $recipient;
            $parcel->start_location = $start_location;
            $parcel->end_location = $end_location;
            $parcel->weight = $this->getOrderWeight($order, 1);
            $parcel->size = "2x2x2";
            $parcel->outer_id = $id;
            $parcel->outer_id_type = "WOOCOMMERCE";
            

            /* ---------- Invoice ---------- */
            if( isset( $invoiceNumbers[ $id ] ) ) {
                $parcel->cod = $order->get_total();
                $parcel->invoice_number = $invoiceNumbers[ $id ];
            }
            

            /* ---------- Append the new parcel data to the parcels ---------- */
            array_push($parcels->parcels, $parcel);
        }

        return $parcels;
    }

    /**
     * This function fetch the access keys from the add multiple parcels response.
     * 
     * @param Objetc responseData - the parsed object of the response.
     */
    private function fetchAccessKeysFromResponse($orderIDs, $responseData) {
        $accessKeys = [];

        for( $i = 0; $i < count($orderIDs); $i++ ) {
            $accessKeys[ $orderIDs[ $i ] ] = $responseData->parcels[ $i ]->access_key;
        }

        return $accessKeys;
    }

    /**
     * Updates some colummns for the given orders by orderIDs, and the accessKeys.
     * @param array $orderIDs - contains the order ids
     * @param assoc_array $accessKeys - the index is the order id an the value is the access key
     */
    private function updateOrdersData ($handler, $orderIDs, $accessKeys) {
        $address = 
            get_option( 'woocommerce_store_postcode' )  . " " . 
            get_option( 'woocommerce_store_city' ) . ", " . 
            get_option( 'woocommerce_store_address' ) . " " . 
            get_option( 'woocommerce_store_address_2' ) . 
            " <span class='method'>(Saját cím)</span>";

        foreach($orderIDs as $id) {

            $handler->updateOrderData([
                TourmixOrdersTableHandler::COLUMN_ID => $id,
                TourmixOrdersTableHandler::COLUMN_DISPATCH_ADDR => $address,
                TourmixOrdersTableHandler::COLUMN_ACCESS_KEY => $accessKeys[ $id ],
            ]);
        }
    }

    /**
     * Sends all the given parcels to the Tourmix api
     * 
     * @param string $api_token - the parner api token
     * @param TourmixParcelsObject $parcel_data - the data of the parcel
     * @return text - the response
     */
    private function sendMultipleParcelsDataToApi ($api_token, $parcels_data) {
        $url = TourmixApiHandler::TOURMIX_API_URL . TourmixApiHandler::ADD_MULTIPLE_PARCELS . $api_token;
        return json_decode(TourmixApiHandler::createCurlRequest($url, 'POST', $parcels_data));
    }

    /**
     * We can call this function by a specific orderID to update it status
     */
    public function updateOrderStatusForId ($orderId) {
        $ordersHandler  = new TourmixOrdersTableHandler(5);
        $dataHandler    = new TourmixDatabaseHandler();

        $accessKeys = $ordersHandler->getAccessKeysForOrderIds([ $orderId ]);
        
        $status         = $this->getParcelStatuses( $dataHandler->getApiToken(), $accessKeys );

        if( $status[ $orderId ] == 'DELIVERED' ) {
            $ordersHandler->changeOrdersStatus( [ $orderId ], $ordersHandler::COMPLETED );
            
            return 'SUCCESS';
        }

        return 'FAIL';
    }

    public function updateOrderStatusForAccessKey ($accessKey) {
        $ordersHandler  = new TourmixOrdersTableHandler(5);
        $dataHandler    = new TourmixDatabaseHandler();
        
        $status         = $this->getParcelStatuses( $dataHandler->getApiToken(), [ $accessKey ] );

        if( $status[ $accessKey ] == 'DELIVERED' ) {
            $ordersHandler->changeOrdersStatusByAccessKeys( [ $accessKey ], $ordersHandler::COMPLETED );
            
            return 'SUCCESS';
        }

        return 'FAIL';
    }

    /**
     * Sends the wordpress rest api base url of the shop for tourmix
     * 
     * @param string $api_token - the api token
     * @return string the response
     */
    public static function registerEnpointUrl ($api_token) {
        $data = (object) [
            "outer_url"         => rest_url("tourmix-delivery"),
            "outer_url_type"    => "WOOCOMMERCE",
        ];

        $url = TourmixApiHandler::TOURMIX_API_URL . TourmixApiHandler::REGISTER_ENDPOINT . $api_token;

        return TourmixApiHandler::createCurlRequest($url, 'POST', $data);
    }

    /**
     * Creates a curl request for the given url with the given data
     * 
     * @param object $params contains all the information {url, method, data}
     * 
     * @return response - returs back the answer or an "[]" if there was an error
     */
    public static function createCurlRequest($url, $method = "GET", $data = null) {
        $response = null;

        $args = array(
            'timeout'     => '5',
            'redirection' => '5',
            'httpversion' => '1.1',
            'blocking'    => true,
            'headers'     => array('Accept' => 'application/json', 'Content-Type' => 'application/json'),
            'cookies'     => array(),
        );

        if($method == "GET") {
            $response = wp_remote_get($url, $args);
        } else {
            $args += [
                'body' => json_encode($data, JSON_UNESCAPED_UNICODE)
            ]; 

            $response = wp_remote_post($url, $args);
        }


        $body       = wp_remote_retrieve_body($response);
        $http_code  = wp_remote_retrieve_response_code($response);


        if($http_code == 200) {
            return $body;
        }
        
        /**
         * It is soooo important, because if we have some errors we check this.
         */
        return json_encode([
            "message" => TourmixApiHandler::API_ERROR_MESSAGE
        ]);
    }

    public static function appendMsgToDebugLog($msg) {
        //TourmixApiHandler::appendMsgToDebugLog(json_encode($responseData, JSON_PRETTY_PRINT));
        $file_path = 'log.txt';
        $file_handle = fopen($file_path, 'a');
        
        if($file_handle) {
            fwrite($file_handle, $msg);
            fwrite($file_handle, "\n");
            fclose($file_handle);
        }
    }

    /**
     * Return the value or the default string for the give variable
     * 
     * @param string $variable
     * @return string
     */
    private function getValue ( $variable = NULL, $default = "unknown" ) {
        return ( 
            isset($variable) ? 
            sanitize_text_field(
                $variable == "" ? 
                $default : 
                $variable
            ) : 
            $default
        );
    }

}