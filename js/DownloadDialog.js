/**
 * The engine of the download dialog
 * 
 * @since      1.0.0
 * @package    Tourmix
 * @subpackage Tourmix/javascripts
 * @author     Tourmix <info@tourmix.delivery>
 */

class DownloadDialog {

    dialog;         //The dialog window
    search;         //The search input field (More ore less a filter)
    results;        //The search results
    closeBtn;       //The close button of the dialog
    openBtn;        //The open button of the dialog

    ANIMATION_TIME = 200;

    RESULTS_ARRAY;

    items;          //The search result item ()

    /**
     * Initialize all the things for the dialog window
     */
    constructor (openButtonID) {
        this.initVariables(openButtonID);

        if(this.allVariablesDefined())
            this.initActions();
    }

    /**
     * Init all the variables
     */
    initVariables (openButtonID) {
        this.dialog     = document.getElementById("tourmix-download-dialog");
        this.search     = document.getElementById("tourmix-download-dialog-search");
        this.results    = document.getElementById("tourmix-download-dialog-results");
        this.openBtn    = document.getElementById(openButtonID);
        this.closeBtn   = document.getElementById("tourmix-download-dialog-close-bt");

        if(typeof shippingLabelsArray !== 'undefined')
            this.RESULTS_ARRAY = shippingLabelsArray;
    }

    /**
     * Init all the actions
     */
    initActions () {
        this.setDialogCloseBtClick();
        this.setDialogOpenBtClick();
        this.setSearchInputEvent();
        this.showResults(this.RESULTS_ARRAY);
    }

    /**
     * Returns true if all the variables are defined
     */
    allVariablesDefined () {
        if(
            !this.isDefined(this.dialog)
            || !this.isDefined(this.search)
            || !this.isDefined(this.results)
            || !this.isDefined(this.closeBtn)
            || !this.isDefined(this.openBtn)
            || !this.isDefined(this.RESULTS_ARRAY)
        ) {
            return false;
        }

        return true;
    }

    isDefined (variable) {
        return variable != undefined;
    }

    /**
     * Set up an event listener for the dialog close button
     */
    setDialogCloseBtClick () {
        this.closeBtn.addEventListener("click", () => {
            this.closeDialog();
        });
    }

    /**
     * Set up an event listener for the dialog open button
     */
    setDialogOpenBtClick () {
        this.openBtn.addEventListener("click", () => {
            this.openDialog();
        });
    }

    /**
     * Open (show) the dialog
     */
    openDialog () {
        this.dialog.style = "";

        setTimeout(() => {
            this.dialog.children[0].style = "scale: 1;";
        }, 10);
    }

    /**
     * Close (hide) the dialog
     */
    closeDialog () {
        this.dialog.children[0].style = "scale: 0;";

        setTimeout(() => {
        this.dialog.style = "display: none;";
        }, this.ANIMATION_TIME);
    }

    /**
     * Shows the resuts from the specified array
     * The array should contain objects and an object should have a name attribute
     * 
     * @param array resultsArray 
     */
    showResults (resultsArray) {
        this.results.innerHTML = "";

        resultsArray.forEach( obj => {
            this.results.appendChild( this.createDownloadLink( obj ) );
        });
    }

    /**
     * This create the link html element which would be put to the dialog
     * 
     * @param object obj - same as the resultArray but this only contains one object
     * @returns html element
     */
    createDownloadLink (obj) {
        let link = document.createElement("a");
        link.setAttribute("href", obj.url);
        link.setAttribute("class", "tourmix-dialog-data");
        link.setAttribute("target", "_blank");
        link.innerHTML = obj.name;

        this.setDownloadLinkClickEvent(link);

        return link;
    }

    /**
     * Sets up a click event listener for the created download link
     * @param htmlElement link 
     */
    setDownloadLinkClickEvent (link) {
        link.addEventListener("click", () => {
            this.closeDialog();
        })
    }

    /**
     * Filters the RESULTS_ARRAY by the given string
     * 
     * @param string filterString
     * @returns the filtered array
     */
    filterResults (filterString) {
        return this.RESULTS_ARRAY.filter((item) => {
            return item.name.toUpperCase().includes(filterString.toUpperCase());
        });
    }

    /**
     * Sets up an event handling for the search bar
     */
    setSearchInputEvent () {
        this.search.addEventListener("input", (e) => {
            this.showResults(
                this.filterResults(this.search.value)
            );
        });
    }
}