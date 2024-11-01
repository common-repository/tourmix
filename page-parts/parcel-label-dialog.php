<?php
/**
 * This shows a dialog, where the use can download the generated parcel labels.
 * 
 * @since      1.1.1
 * @package    Tourmix
 * @subpackage Tourmix/page-parts
 * @author     Tourmix <info@tourmix.delivery>
 */

 defined('ABSPATH') || exit;

$lastLink = $parcelLabelsTableHandler->getLastLink();

if($lastLink != null && TourmixDatabaseHandler::isVisible(TourmixDatabaseHandler::DOWNLOAD_DIALOG)) {
?>

<div class="tourmix-dialog-container" id="parcel-label-container-id" style="
    <?php 
        echo ($parcelLabelsTableHandler->isLastLinkDownloaded() ? "display: none;" : "display: block;");
    ?>
">
    <div class="tourmix-dialog parcel-label-dialog">
        <span class="tourmix-dialog-close-button" id="tourmix-parcel-label-dialog-close-bt">✕</span>

        <div class="tourmix-dialog-message">
            <h2>Az ön által feladott rendelésekhez tartozó cimkék:</h2>
        </div>

        <iframe src="<?php echo esc_url($lastLink); ?>" title="Parcel label" class="parcel-label-iframe"></iframe>

        <div class="tourmix-dialog-actions-holder">
            <a href="<?php echo esc_url($lastLink); ?>" class="dialog-button" id="label-download" target="_blank" download> Letöltés </a>
        </div>
    </div>
</div>

<?php
}