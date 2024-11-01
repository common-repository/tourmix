<?php
/**
 * This is the layout of the orders table. This table shows the orders where the shipping method is tourmix delivery.
 * 
 * @since      1.1.0
 * @package    Tourmix
 * @subpackage Tourmix/page-parts
 * @author     Tourmix <info@tourmix.delivery>
 */ 

defined('ABSPATH') || exit;


/* ---------- Internally used variables ---------- */
$accessKeys = $tourmixOrdersTableHandler->getAccessKeysForOrderIds( $tourmix_order_ids );
$orderIds = $tourmixOrdersTableHandler->getOrderIdsForAccessKeys( $accessKeys );

$order_infos = $tourmixOrdersTableHandler->getOrderInfos($tourmix_order_ids);

$api_order_statuses = $tourmixApiHandler->getParcelStatuses( 
    TourmixDatabaseHandler::getApiToken(), 
    $accessKeys
);

/* ---------- Internally used variables END ---------- */
?>

<div class="tourmix-delivery-orders-container">
    <div id="tourmix-orders-count"> 
        <?php
            $tourmixOrdersTableHandler->createStatusFilter(TOURMIX_STATUS_FILTER);
        ?>
    </div>
    
    <div class="handle-multievent">
        <label for="multiple-event-handling" class="screen-reader-text">Csoportos kijelölés művelet</label>
        <select name="action" id="multiple-event-handling">
            <option value="transfer-tourmix">Átadás TOURMIX-nak</option>
        </select>
        <input type="button" id="do-multi-action" class="tourmix-multi-action-button" value="Alkalmaz">

        <input type="button" id="open-download-dialog" class="tourmix-multi-action-button" value="Címkék letöltése">
    </div>

    <table class="wp-list-table widefat fixed striped table-view-list posts tourmix-table">
        <thead>
            <tr>
                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="tourmix-cb-select-all">Összes kijelölése</label><input id="tourmix-cb-select-all" type="checkbox"></td>
                <th id="order_number">Rendelés/Csomagkód</th>

                <?php
                    if(TourmixDatabaseHandler::isVisible(TourmixDatabaseHandler::ORDER_DATE)) {
                        echo '<th id="order_date">Dátum</th>';
                    }

                    if(TourmixDatabaseHandler::isVisible(TourmixDatabaseHandler::DISPATCH_ADDRESS)) {
                        echo '<th id="order_dispatch">Feladási cím</th>';
                    }

                    if(TourmixDatabaseHandler::isVisible(TourmixDatabaseHandler::SHIPPING_ADDRESS)) {
                        echo '<th id="order_shipping">Szállítási cím</th>';
                    }

                    if(TourmixDatabaseHandler::isVisible(TourmixDatabaseHandler::ORDER_TOTAL)) {
                        echo '<th id="order_total">Összeg</th>';
                    }
                ?>

                <th id="wc_actions">Műveletek</th>
            </tr>
        </thead>

        <tbody id="orders-list">
            <?php
                
                foreach ($tourmix_order_ids as $order_id) {
                    $order = new WC_Order($order_id);
                    $status = $order_infos[$order_id]->order_status;
            ?>

            <tr 
                id="post-<?php echo esc_attr($order_id); ?>"
                data-payment="<?php echo esc_attr($order->get_payment_method()); ?>"
                data-recipient-name="<?php echo esc_attr($order->get_formatted_billing_full_name()); ?>"
                data-order-price="<?php echo esc_attr($order->get_formatted_order_total()); ?>"
            >
                <td class="tourmix_td_cb">
                    <?php
                        if($status == TourmixOrdersTableHandler::PENDING) {
                            echo '<input id="cb-select-' . esc_attr($order_id) . '" type="checkbox" class="tourmix-order-cb" value="' . esc_attr($order_id) . '">';
                        }
                    ?>
                </td>

                <td class="tourmix_td_name">
                    <?php
                        if($status == TourmixOrdersTableHandler::PENDING) {
                            echo "<a href='" . esc_url($order->get_edit_order_url()) . "'><strong>#" . esc_attr($order_id) . " " . wp_kses_post($order->get_formatted_billing_full_name()) . "</strong></a>";
                        } else {
                            echo "<strong>" . esc_html($order_infos[$order_id]->access_key) . "</strong>";
                        }
                    ?>
                </td>
                
                <?php
                    if(TourmixDatabaseHandler::isVisible(TourmixDatabaseHandler::ORDER_DATE)) {
                        $formatted_date = $order->get_date_created()->format('Y-m-d H:i:s');
                        echo "<td class='tourmix_td_date'>" . esc_html($formatted_date) . "</td>";
                    }

                    if(TourmixDatabaseHandler::isVisible(TourmixDatabaseHandler::DISPATCH_ADDRESS)) {
                        if($status == TourmixOrdersTableHandler::PENDING) {
                            echo "<td class='tourmix_td_dispatch_address'>A rendelés még nincs feladva!</td>";
                        } else {
                            echo "<td class='tourmix_td_dispatch_address'>" . wp_kses_post($order_infos[$order_id]->dispatch_addr) . "</td>";
                        }
                    }

                    if(TourmixDatabaseHandler::isVisible(TourmixDatabaseHandler::SHIPPING_ADDRESS)) {
                        $name = str_replace( "<br/>", ", ", $order->get_formatted_shipping_address());
                        echo "<td class='tourmix_td_shipping'>" . esc_html($name) . " <span class='method'>(TOURMIX)</span></td>";
                    }

                    if(TourmixDatabaseHandler::isVisible(TourmixDatabaseHandler::ORDER_TOTAL)) {
                        echo "<td class='tourmix_td_total'>" . wp_kses_post($order->get_formatted_order_total()) . "</td>";
                    }
                ?>

                <td class="tourmix_td_bt">
                    <?php
                        if( $status == TourmixOrdersTableHandler::PENDING ) {
                            echo "
                                <a>
                                    <input type='button' class='tourmix-transfer-button'
                                    id='" . esc_attr($order_id) . "'
                                    value='Feladás'>
                                </a>
                            ";

                        } else if ( $status == TourmixOrdersTableHandler::SHIPPING ) {
                            $tourmix_status = $api_order_statuses[ $accessKeys[ $order_id ] ];
                            echo esc_html( $tourmixApiHandler->getStringForParcelStatus( $tourmix_status ) );

                        } else if ( $status == TourmixOrdersTableHandler::COMPLETED ) {
                            echo 'Kézbesítve';
                        }
                    ?>
                </td>
            </tr>

            <?php 
                } 
            ?>
        </tbody>
    </table>

    <div class="handle-pagination">
    <?php
        $tourmixOrdersTableHandler->createPagination(TOURMIX_CURRENT_PAGE);
    ?>
    </div>
</div>

<div id="loader-container" style="display: none;">
    <div class="loader"></div>
</div>