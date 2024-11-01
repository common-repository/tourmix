/**
 * The engine of the settings toggling system
 * 
 * @since      1.0.0
 * @package    Tourmix
 * @subpackage Tourmix/javascripts
 * @author     Tourmix <info@tourmix.delivery>
 */

class SettingsToggler {
    POINT_UP = "up-pointing-triangle";
    POINT_DOWN = "down-pointing-triangle";
    HEIGHT = "300px";
    
    toggleButton;   //The toggle button
    togglePanel;    //The toggle panel
    isPanelUp;      //Is the toggle panel up;

    /**
     * Brings to the life the settings toggler button
     * 
     * @param string buttonID - the id of the toggler button
     * @param string panelID - the id of the panel what is would be toggled
     */
    constructor (buttonID, panelID, height) {
        this.initVariables(buttonID, panelID, height);
        this.initActions();
    }

    /**
     * Init all the variables
     */
    initVariables (buttonID, panelID, height) {
        this.toggleButton   = document.getElementById(buttonID);
        this.togglePanel    = document.getElementById(panelID);
        this.HEIGHT         = height;
        this.isPanelUp      = true;
    }

    /**
     * Init all the actions
     */
    initActions () {
        this.setToggleButtonClickEvent();
    }

    /**
     * Sets up an event handling for the toggler button
     */
    setToggleButtonClickEvent () {
        this.toggleButton.addEventListener("click", () => {
            this.handleToggleRequest();
        });
    }

    /**
     * Handles toggling requests
     */
    handleToggleRequest () {
        if(this.isPanelUp) {
            this.toggleDown();
            this.isPanelUp = false;
        } else {
            this.toggleUp();
            this.isPanelUp = true;
        }
    }

    /**
     * Toggles up all the things
     */
    toggleUp () {
        this.toggleButton.classList.remove(this.POINT_UP);
        this.toggleButton.classList.add(this.POINT_DOWN);
        
        this.togglePanel.style = "height: 0;";
    }

    /**
     * Toggles down all the things
     */
    toggleDown () {
        this.toggleButton.classList.remove(this.POINT_DOWN);
        this.toggleButton.classList.add(this.POINT_UP);

        this.togglePanel.style = "height: " + this.HEIGHT + ";";
    }
}