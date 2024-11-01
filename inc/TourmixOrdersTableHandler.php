<?php

/**
 * Handles the tourmix_orders table at the database
 *
 * @since      1.1.0
 * @package    Tourmix
 * @subpackage Tourmix/includes
 * @author     Tourmix <info@tourmix.delivery>
 */

class TourmixOrdersTableHandler {

    private const TOURMIX_SHIPPING_ID = "tourmix_delivery_shipping";

    private $table_name = 'tourmix_orders';
    private $pagesCount;    //The count of the pages depending on the the current status filter and the page size
    private $pageSize;      //The maximum  count of orders to be shown in one page

    //URL parameters
    private const PARAM_STATUS = "status";  //The status parameter, this will be put to the url
    private const PARAM_PAGED = "paged";    //The paged parameter, this will be put to the url

    //Tourmix order status
    public const PENDING = "pending";       //The order is currently waiting to be sent to the tourmix api
    public const SHIPPING = "shipping";     //The order is in the tourmix api and something happening with it ( more details in TourmixApiHandler )
    public const COMPLETED = "completed";   //The order is delivered to the customer

    //woocommerce order status
    public const WC_TOBESENT = "wc-tourmix-tobesent";  //The order is currently waiting to be sent to the tourmix api ( this is a custom status for the woocommerce orders )
    public const WC_SHIPPING = "wc-tourmix-shipping";  //The order is sent ot the tourmix api ( this is a custom status for the woocommerce orders )
    public const WC_COMPLETED = "completed";           //The order is delivered to the customer

    //table column names
    public const COLUMN_ID              = "id";
    public const COLUMN_ORDER_ID        = "order_id";
    public const COLUMN_ORDER_STATUS    = "order_status";
    public const COLUMN_DISPATCH_ADDR   = "dispatch_addr";
    public const COLUMN_PARCEL_NUMBER   = "parcel_number";
    public const COLUMN_ACCESS_KEY      = "access_key";
    public const COLUMN_CREATED_AT      = "created_at";
    public const COLUMN_UPDATED_AT      = "updated_at";

    public function __construct($pageSize = 20, $statusFilter = null) {
        global $wpdb;
        $this->table_name = $wpdb->prefix . $this->table_name;
        add_action('woocommerce_thankyou', array($this, 'orderPlacedHook'), 10, 1);

        $this->pageSize = $pageSize;
        $this->pagesCount = $this->getPageCount($pageSize, $statusFilter);
    }

    /**
     * The scheme of the tourmix_orders table
     */
    public function createTable () {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $this->table_name (
            id              INT NOT NULL AUTO_INCREMENT,
            order_id        INT NOT NULL,
            order_status    VARCHAR(20) NOT NULL,
            dispatch_addr   VARCHAR(250) NOT NULL DEFAULT '',
            parcel_number   VARCHAR(10) NOT NULL DEFAULT '',
            access_key      VARCHAR(10) NOT NULL DEFAULT '',
            created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY     (id),
            UNIQUE KEY      (order_id)
            ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * When an order is placed we store it in the database
     * 
     * @param WC_Order $wc_order - woocommerce order object
     */
    private function saveOrderData ( $wc_order ) {
        global $wpdb; 
        
        $wpdb->insert ( 
            $this->table_name, 
            array(
                'order_id'              => $wc_order->get_id(),
                'order_status'          => self::PENDING,
                'dispatch_addr'         => "",
                'parcel_number'         => "",
                'access_key'            => "",
                'created_at'            => current_time('mysql'),
                'updated_at'            => current_time('mysql')
            )
        );
    }

    /**
     * Delete the tourmix orders table from the wordPress database
     */
    public function deleteTable () {
        global $wpdb;
        $wpdb->query( "DROP TABLE IF EXISTS " . $this->table_name );
    }

    /**
     * We have a woocommerce hook for this function and when an order is placed woocommerce will call this for us.
     * This function will call the 'saveOrderData' function to save the data
     * 
     * @param number $order_id - the order id of the order wich was placed
     */
    public function orderPlacedHook ( $order_id ) {
        $order = wc_get_order( $order_id );

        $shipping_method = @array_shift($order->get_shipping_methods());
        $shipping_method_id = $shipping_method['method_id'];

        if($shipping_method_id == $this::TOURMIX_SHIPPING_ID) {
            if($order->get_status() != "on-hold") {
                $order->update_status( $this::WC_TOBESENT );
            }
            $this->saveOrderData( $order );
        }
    }

    /**
     * Retrieve the count of the orders for the specified order status.
     * If the status unset retrieve the count of all orders.
     * 
     * @param string $order_status - the status of the orders
     * @return int
     */
    public function getOrdersCount ($order_status = NULL) {
        global $wpdb; 

        $filter = "";
        if( $order_status != null )
            $filter = $wpdb->prepare( 'WHERE order_status LIKE "%s"', $order_status );

        $query = $wpdb->prepare(
            "SELECT COUNT(id) AS 'count' 
            FROM $this->table_name 
            $filter"
        );
        $results = $wpdb->get_results($query, OBJECT);
        return $results[0]->count;
    }

    /**
     * Get the order ids form the database
     */
    public function getOrderIds ($page = 1, $statusFilter = null) {
        global $wpdb;
        
        $size = $this->pageSize;

        $filter = "";
        if( $statusFilter != null )
            $filter = $wpdb->prepare( 'WHERE order_status LIKE "%s"', $statusFilter );

        $query = $wpdb->prepare(
            "SELECT order_id AS id
            FROM $this->table_name
            $filter
            ORDER BY order_id DESC
            LIMIT %d, %d",
            ( $page - 1 ) * $size,
            $size
        );

        $results = $wpdb->get_results( $query, OBJECT );
        return $this->createArrayFromIdsObject( $results );
    }

    /**
     * Creates an array from the given object which contains the order ids
     */
    private function createArrayFromIdsObject ($order_ids) {
        $ids = array();

        foreach ($order_ids as $id) {
            array_push($ids, $id->id);
        }

        return $ids;
    }
    
    /**
     * Change the status of the specified order
     */
    public function changeOrdersStatus ($ids_array, $status) {
        $this->changeWCOrdersStatus( $ids_array, $status );

        $this->updateOrderData([
            $this::COLUMN_ID => $ids_array,
            $this::COLUMN_ORDER_STATUS => $status
        ]);
    }

    /**
     * Change the status of the specified orders by the access keys
     */
    public function changeOrdersStatusByAccessKeys ($accessKeys, $status) {
        $order_ids = $this->getOrderIdsForAccessKeys( $accessKeys );

        $this->changeWCOrdersStatus( $order_ids, $status );

        $this->updateOrderData([
            $this::COLUMN_ID => $order_ids,
            $this::COLUMN_ORDER_STATUS => $status
        ]);
    }

    /**
     * Some data of the order can be updated via this method
     * 
     * @param array $data assoc array table column names
     * [
     *    TourmixOrdersTableHandler::COLUMN_ID => 47,
     *    TourmixOrdersTableHandler::COLUMN_DISPATCH_ADDR => "almafa utca 12",
     *    TourmixOrdersTableHandler::COLUMN_PARCEL_NUMBER => "AAA-111"
     *    TourmixOrdersTableHandler::COLUMN_ACCESS_KEY => "AAA-111"
     *    ...
     * ]
     */
    public function updateOrderData ($data) {
        global $wpdb;

        $columnString = null;
        foreach ($data as $key => $value) {
            if($key != $this::COLUMN_ID) {
                if($columnString == null) {
                    $columnString = $wpdb->prepare("$key = '%s'", $value);
                } else {
                    $columnString .= $wpdb->prepare(", $key = '%s'", $value);
                }
            }
        }

        $ids_array = is_array($data[$this::COLUMN_ID]) ? $data[$this::COLUMN_ID] : [$data[$this::COLUMN_ID]];

        $sql = $wpdb->prepare(
            "UPDATE $this->table_name
            SET $columnString
            WHERE order_id IN (" . implode(', ', $ids_array) . ")",
        );

        $wpdb->query($sql);
    }

    /**
     * Gets the order status for the specified orders
     * 
     * @param array $ids_array
     * @return assocArray
     */
    public function getOrdersStatus ($ids_array) {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT order_status AS status, order_id AS id
            FROM $this->table_name
            WHERE order_id IN (" . implode(', ', $ids_array) . ")"
        );

        return $this->generateAssocArrayForStatuses( 
            $wpdb->get_results($query, OBJECT)
        );
    }

    public function getOrderIdsForAccessKeys ($accessKeys) {
        global $wpdb;

        $placeholders = implode(', ', array_fill(0, count($accessKeys), '%s'));

        $query = $wpdb->prepare(
            "SELECT order_id, access_key
            FROM $this->table_name
            WHERE access_key IN ($placeholders)",
            $accessKeys
        );

        $objects = $wpdb->get_results($query, OBJECT);

        $orderIds = [];
        foreach ($objects as $obj) {
            $orderIds[$obj->access_key] = $obj->order_id;
        }

        return $orderIds;
    }

    public function getAccessKeysForOrderIds ($order_ids) {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT order_id, access_key
            FROM $this->table_name
            WHERE order_id IN (" . implode(', ', $order_ids) . ")"
        );

        $objects = $wpdb->get_results($query, OBJECT);

        $accessKeys = [];
        foreach ($objects as $obj) {
            $accessKeys[$obj->order_id] = $obj->access_key;
        }

        return $accessKeys;
    }

    /**
     * Get all the information from the tourmix orders table for the specified orders
     * 
     * @param array $ids_array - contains the order ids what we want info about
     * @return assocArray - [ orderID => (object) [column names and values] ] 
     */
    public function getOrderInfos ($ids_array) {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT *
            FROM $this->table_name
            WHERE order_id IN (" . implode(', ', $ids_array) . ")"
        );

        return $this->genOrderInfoAssocArrayByOrderID( 
            $wpdb->get_results($query, OBJECT)
        );
    }

    /**
     * For the specified array of object creates an associative array by the order id
     * 
     * @param array $orderInfos - tourmix orders table result
     * @return assocArray
     */
    private function genOrderInfoAssocArrayByOrderID ($orderInfos) {
        $assocArray = [];

        foreach( $orderInfos as $obj ) {
            $assocArray += [
                $obj->order_id => $obj
            ];
        }

        return $assocArray;
    }



    /**
     * Generates an associative array from the result which contains the statuses and the order ids
     */
    private function generateAssocArrayForStatuses ($statuses) {
        $assocArray = [];

        foreach( $statuses as $obj ) {
            $assocArray += [
                $obj->id => $obj->status
            ];
        }

        return $assocArray;
    }

    /**
     * Creates a pagination button.
     * If it is active it will be a link (a) and if it's not (span)
     * 
     * @param boolean $isActive - is this button active or not
     * @param string $urlString - the current url string
     * @param number $page - the destionation page of this link
     * @param string $text - The text to be shown
     * 
     * @return html - the html element
     */
    private function createPaginationButton ($isActive, $urlString, $page, $text) {
        if($isActive) {
            $urlString .= $this::PARAM_PAGED . "=" . $page;

            return "<a href='$urlString' class='pagination-button active-pagination'>$text</a>";
        }

        return "<span class='pagination-button inactive-pagination'>$text</span>";
    }

    /**
     * Creates the pagination for the orders table
     * 
     * @param number $currentPage - the currently active page
     */
    public function createPagination ($currentPage) {
        $pagesCount = $this->pagesCount;

        if( $pagesCount > 1 ) {
            $urlString      = $this->getCurrentUrlParameterString([$this::PARAM_PAGED]);

            $prevActive = ( $currentPage > 1 );
            $nextActive = ( $currentPage < $pagesCount );

            $prevPage   = ( $currentPage > 1 ? $currentPage - 1 : 1 );
            $nextPage   = ( $currentPage < $pagesCount ? $currentPage + 1 : $pagesCount );

            echo ("
                {$this->createPaginationButton($prevActive, $urlString, 1,              '«')}
                {$this->createPaginationButton($prevActive, $urlString, $prevPage,      '‹')}

                <span class='pagination-page-number'> $currentPage / $pagesCount </span>

                {$this->createPaginationButton($nextActive, $urlString, $nextPage,      '›')}
                {$this->createPaginationButton($nextActive, $urlString, $pagesCount,    '»')}
            ");
        }
    }

    /**
     * Calculates the page count for the specified page size
     * 
     * @param number $pageSize - how many items will be visible in the orders table
     * @return integer
     */
    private function getPageCount ($pageSize, $statusFilter = null) {
        return intval( ( $this->getOrdersCount($statusFilter) / $pageSize ) + 0.9999 );
    }

    /**
     * Change the status of the woocommerce orders
     */
    public function changeWCOrdersStatus ($ids_array, $status) {
        foreach( $ids_array as $id ) {
            $WC_Order = wc_get_order( $id );

            if( $status == $this::PENDING ) {
                $WC_Order->update_status( $this::WC_TOBESENT );
            } else if ( $status == $this::SHIPPING ) {
                $WC_Order->update_status( $this::WC_SHIPPING );
            } else if ( $status == $this::COMPLETED ) {
                $WC_Order->update_status( $this::WC_COMPLETED );
            }
        }
    }

    /**
     * Creates the status filter for the status types like PENDING, SHIPPING, COMPLETED
     * 
     * @param string $currentFilter - the currently set filter
     */
    public function createStatusFilter ($currentFilter) {
        $urlString      = $this->getCurrentUrlParameterString([$this::PARAM_STATUS, $this::PARAM_PAGED]);

        echo ("
            {$this->createStatusFilterLink($currentFilter, $urlString, null,                'Összes',           '')} 
            {$this->createStatusFilterLink($currentFilter, $urlString, $this::PENDING,      'Feladandó',        '|')} 
            {$this->createStatusFilterLink($currentFilter, $urlString, $this::SHIPPING,     'Szállítás alatt',  '|')}
            {$this->createStatusFilterLink($currentFilter, $urlString, $this::COMPLETED,    'Teljesítve',       '|')}
        ");
    }

    /**
     * Creates one status filter link with the given parameters
     * 
     * @param string $currentFilter - the currently set status filter
     * @param string $urlString - the actual url string which contains the nessesary parameters
     * @param string $status - the status for this link
     * @param string $text - the text to be shown by this link
     * @param string $seperator - the seperator between the links
     * 
     * @return string - the created link
     */
    private function createStatusFilterLink ($currentFilter, $urlString, $status, $text, $seperator) {
        $count = $this->getOrdersCount($status);

        if($count > 0) {
            $inactiveClass = ($currentFilter == $status ? "" : "tourmix-orders-count-inactive");
            $urlString .= ($status != null ? $this::PARAM_STATUS . "=" . $status : "");

            return " $seperator <a href='$urlString' class='tourmix-orders-count-title $inactiveClass'>$text ($count)</a>";
        }

        return "";
    }

    /**
     * Gets all the parameters from the current url and creates a string from it to use this next time.
     * If we set up $excludeParams this function will leave this params from the final url string
     * 
     * @param array $excludeParams - contains all the parameters that sould be left out
     * 
     * @return string - the created parameters string
     */
    private function getCurrentUrlParameterString ($excludeParams = null) {
        $urlString = "?";

        foreach ($_GET as $key => $val) {
            if(!in_array($key, $excludeParams)) {
                $urlString .= "$key=$val&";
            }
        }

        return $urlString;
    }
}