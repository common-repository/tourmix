<?php
/**
 * This shows a little dialog about what happens if the user choose the tx pont option
 * 
 * @since      1.0.0
 * @package    Tourmix
 * @subpackage Tourmix/page-parts
 * @author     Tourmix <info@tourmix.delivery>
 */

defined( 'ABSPATH' ) || exit;

?>

<div class="tourmix-dialog-container" id="information-dialog-txpont" style="display: none;">
    <div class="tourmix-dialog information-dialog" style="scale: 0;">
        <div class="tourmix-dialog-message information-dialog-message">
            Amennyiben Ön végül a <b>TOURMIX csomagpontot</b> választja feladási helyként, fontos tisztában lennie azzal, 
            hogy a kiválasztott TOURMIX csomagpontra <b>el kell juttatnia a csomagot</b>. Ugyanis a Mixer a megadott feladási pontra fog menni a csomagjáért.
        </div>

        <div class="tourmix-dialog-actions-holder information-dialog-actions">
            <button type="button" class="dialog-button" id="information-dialog-understand-btn">
                Megértettem
            </button>

            <button type="button" class="dialog-button" id="information-dialog-dont-show-btn">
                Ne mutassa többet
            </button>
        </div>
    </div>
</div>