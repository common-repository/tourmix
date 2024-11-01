<?php
/**
 * This shows a dialog window where we can choose a pdf parcel label to be downloaded
 * 
 * @since      1.0.0
 * @package    Tourmix
 * @subpackage Tourmix/page-parts
 * @author     Tourmix <info@tourmix.delivery>
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="tourmix-dialog-container" id="tourmix-download-dialog" style="display: none;">
    <div class="tourmix-dialog" style="scale: 1;">
        <span class="tourmix-dialog-close-button" id="tourmix-download-dialog-close-bt">✕</span>

        <div class="tourmix-dialog-message">
            <h2>Az Ön által feladott rendelésekhez tartozó cimkék:</h2>

            <p>
                Az alábbi listában tudja kiválasztani és letölteni a cimkéket tartalmazó pdf állományokat.
            </p>

            <p>
                Egy pdf állomány egyszerre több rendelést is tartalmazhat.
            </p>
        </div>

        <fieldset class="tourmix-dialog-inputs-frame">
            <legend>Kattintson a letölteni kívánt pdf állományra</legend>

            <div class="tourmix-dialog-search-bar">
                <input type="text" id="tourmix-download-dialog-search" placeholder="Kezdjen el gépelni">
            </div>

            <div class="tourmix-dialog-data-container" id="tourmix-download-dialog-results"></div>
        </fieldset>

    </div>
</div>

<script>
    const shippingLabelsArray = JSON.parse('<?php echo wp_slash($parcelLabelsTableHandler->getLinksAndNamesArray()); ?>');
</script>