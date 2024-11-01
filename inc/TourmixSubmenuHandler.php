<?php

/**
 * Creates a sub menu page to woocommerce where uses can see orders where the delivery option was Tourmix
 *
 * @since      1.0.0
 * @package    Tourmix
 * @subpackage Tourmix/includes
 * @author     Tourmix <info@tourmix.delivery>
 */

 class TourmixSubmenuHandler {
    public function __construct() {
        add_action('admin_menu', array($this, 'orders_handler_page'));
    }

    /**
     * If the users is on our page we show this content for them
     */
    public function orders_handler_page_content() {
        include plugin_dir_path( __FILE__ ) . 'tourmix-orders-handler-page.php';
    }

    /**
     * Creates the submenu element to woocommerce
     */
    public function orders_handler_page() {
        add_submenu_page('woocommerce', 'TOURMIX', 'TOURMIX ' . $this->create_awaiting_counter(), 'manage_options', 'tourmix-orders-handler', array($this, 'orders_handler_page_content'));
    }

    /**
     * Creates the little notification next to the TOURMIX submenu item which contains the pending orders count
     * 
     * @return HTML
     */
    private function create_awaiting_counter () {
        $database = new TourmixDatabaseHandler();

        if( $database->getApiToken() != false ) {
            $handler = new TourmixOrdersTableHandler();
            $count = $handler->getOrdersCount($handler::PENDING);

            if($count > 0) {
                return '<span class="awaiting-mod">' . $count . '</span>';
            }

            return '';
        }
    }
 }