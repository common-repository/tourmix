/**
 * @since      1.0.0
 * @package    Tourmix
 * @subpackage Tourmix/javascripts
 * @author     Tourmix <info@tourmix.delivery>
 */

class InformationDialog {

    txpontInfo;             //The info dialog
    txpontInfoUnderstand;   //The understand button
    txpontInfoDontShow;     //The dont show again button

    ANIMATION_TIME = 200;   //How long the open/close animation should be in milliseconds

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
        this.txpontInfo             = document.getElementById("information-dialog-txpont");
        this.txpontInfoUnderstand   = document.getElementById("information-dialog-understand-btn");
        this.txpontInfoDontShow     = document.getElementById("information-dialog-dont-show-btn");
    }

    /**
     * Initialize the actions for the dialog window
     */
    initActions() {
        this.txpontInfoUnderstandClick();
        this.txpontInfoDontShowClick();
    }

    /**
     * Returns true if all the variables are defined
     */
    allVariablesDefined () {
        if(
            !this.isDefined(this.txpontInfo)
            || !this.isDefined(this.txpontInfoUnderstand)
            || !this.isDefined(this.txpontInfoDontShow)
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
    txpontInfoUnderstandClick() {
        this.txpontInfoUnderstand.addEventListener ("click", (e) => {
            this.closeDialog();
        });
    }

    /**
     * Adds an event listener for the dont show again button
     */
    txpontInfoDontShowClick() {
        this.txpontInfoDontShow.addEventListener ("click", (e) => {
            this.setCookie("infoShow", "1");
            this.closeDialog();
        });
    }

    /**
     * Creates a cookie with the given name and value
     * 
     * @param string cookieName 
     * @param string cookieValue 
     */
    setCookie(cookieName, cookieValue) {
        document.cookie = `${cookieName}=${cookieValue}`;
    }

    /**
     * Returns the value of the cookie
     * 
     * @param string cookieName 
     * @returns stirng - cookie value
     */
    getCookieValue(cookieName) {
        let cookie = document.cookie.replaceAll(" ", "");

        let cookies = [];
        
        for (let a of cookie.split(";")) {
            let tmpArray = a.split("=");
        
            cookies[tmpArray[0]] = tmpArray[1];
        }
        
        return cookies[cookieName];
    }

    /**
     * Close the dialog
     */
    closeDialog() {
        this.txpontInfo.children[0].style = "scale: 0;";

        setTimeout (() => {
            this.txpontInfo.style = "display: none";
        }, this.ANIMATION_TIME);
    }

    /**
     * Open the dialog
     */
    openDialog() {
        let understand = this.getCookieValue("infoShow");

        if(understand == undefined || understand == "0") {
            this.txpontInfo.style = "";

            setTimeout (() => {
                this.txpontInfo.children[0].style = "scale: 1;";
            }, 10);
        }
    }
}