/**
 * This is the script file of the parcel-label-diallog. 
 * Which shows up when the user dispatch a new order.
 * 
 * @since      1.1.0
 * @package    Tourmix
 * @subpackage Tourmix/javascripts
 * @author     Tourmix <info@tourmix.delivery>
 */

class ParcelLabelDialog {

    labelDialog;            // The info dialog
    labelDownload;          // The understand button
    closeBt;                // The close button

    ANIMATION_TIME = 200;   // How long the open/close animation should be in milliseconds

    /**
     * Initialize all the things for the dialog window
     */
    constructor () {
        this.initVariables();

        if(this.allVariablesDefined())
            this.initActions();
    }

    /**
     * Initialize the variables for the dialog window
     */
    initVariables() {
        this.labelDialog    = document.getElementById("parcel-label-container-id");
        this.labelDownload  = document.getElementById("label-download");
        this.closeBt        = document.getElementById("tourmix-parcel-label-dialog-close-bt");
    }

    /**
     * Initialize the actions for the dialog window
     */
    initActions() {
        this.labelDownloadClick();
        this.setDialogCloseBtClick();
    }

    /**
     * Returns true if all the variables are defined
     */
    allVariablesDefined () {
        if(
            !this.isDefined(this.labelDownload)
            || !this.isDefined(this.labelDialog)
            || !this.isDefined(this.closeBt)
        ) {
            return false;
        }

        return true;
    }

    isDefined (variable) {
        return variable != undefined;
    }

    /**
     * Adds an event listener for the understand button
     */
    labelDownloadClick() {
        this.labelDownload.addEventListener ("click", (e) => {
            this.closeDialog();
            this.setLastLinkDownloaded();
        });
    }

    /**
     * This will add an action listener to the dialog close button
     */
    setDialogCloseBtClick () {
        this.closeBt.addEventListener("click", (e) => {
            this.closeDialog();
            this.setLastLinkDownloaded();
        });
    }

    /**
     * Close the dialog
     */
    closeDialog() {
        this.labelDialog.children[0].style = "scale: 0;";

        setTimeout (() => {
            this.labelDialog.style = "display: none";
        }, this.ANIMATION_TIME);
    }

    /**
     * Open the dialog
     */
    openDialog() {
        this.labelDialog.style = "";

        setTimeout (() => {
            this.labelDialog.children[0].style = "scale: 1;";
        }, 10);
    }

    /**
     * Creates an ajax request to set the last link to downloaded
     */
    setLastLinkDownloaded () {
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl, //ajaxurl is a worldpress super global
            data: {
                action:         'tourmixChangeLastLinkToDownloaded',
            },
            success: function(response) {
                console.log(response);
            },
            error: function(response) {
                console.log(response.responseText);
            }
        });
    }
}