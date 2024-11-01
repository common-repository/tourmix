<?php
/**
 * This is the layout of the orders table. This table shows the orders where the shipping method is tourmix delivery.
 * 
 * @since      1.0.0
 * @package    Tourmix
 * @subpackage Tourmix/page-parts
 * @author     Tourmix <info@tourmix.delivery>
 */ 

defined('ABSPATH') || exit;

?>
<div class="visibility-settings-container">
    <div class="visibility-settings" id="visibility-settings-panel">
        <form class="visibility-settings-form" method="GET">
            <fieldset>
                <legend>Oszlopok</legend>

                <label class="visibility-settings-option">
                    <input name="tourmix_visible_order_date" type="checkbox" value="1" <?php echo (TourmixDatabaseHandler::isVisible(TourmixDatabaseHandler::ORDER_DATE) ? "checked" : ""); ?>>Dátum
                </label>

                <label class="visibility-settings-option">
                    <input name="tourmix_visible_dispatch_address" type="checkbox" value="1" <?php echo (TourmixDatabaseHandler::isVisible(TourmixDatabaseHandler::DISPATCH_ADDRESS) ? "checked" : ""); ?>>Feladási cím
                </label>

                <label class="visibility-settings-option">
                    <input name="tourmix_visible_shipping_address" type="checkbox" value="1" <?php echo (TourmixDatabaseHandler::isVisible(TourmixDatabaseHandler::SHIPPING_ADDRESS) ? "checked" : ""); ?>>Szállítási cím
                </label>

                <label class="visibility-settings-option">
                    <input name="tourmix_visible_order_total" type="checkbox" value="1" <?php echo (TourmixDatabaseHandler::isVisible(TourmixDatabaseHandler::ORDER_TOTAL) ? "checked" : ""); ?>>Összeg
                </label>
            </fieldset>

            <fieldset>
                <legend>Lapozás</legend>

                <label class="visibility-settings-option">Elemek száma oldalanként:
                    <input type="number" step="1" min="1" max="1000" class="screen-per-page" name="tourmix_visible_page_size" maxlength="4" value="<?php echo esc_attr(TourmixDatabaseHandler::getPageSize()); ?>">
                </label>
            </fieldset>

            <fieldset>
                <legend>Felugró ablakok</legend>

                <label class="visibility-settings-option">
                    <input name="tourmix_visible_download_dialog" type="checkbox" value="1" <?php echo (TourmixDatabaseHandler::isVisible(TourmixDatabaseHandler::DOWNLOAD_DIALOG) ? "checked" : ""); ?>>Letöltés
                </label>
            </fieldset>

            <input type="hidden" name="page" value="tourmix-orders-handler">
            <input type="hidden" name="update_tourmix_visibility_settings" value="1">
            <input type="submit" class="button button-primary visibility-settings-button" value="Alkalmaz">
        </form>
    </div>

    <button type="button" class="visibility-settings-toggle-button down-pointing-triangle" id="tourmix-visibility-toggle-button">
        Mit lássunk?
    </button>
</div>

<script>
    let toggler = new SettingsToggler(
        "tourmix-visibility-toggle-button",
        "visibility-settings-panel",
        "300px"
    );
</script>