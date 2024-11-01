/**
 * The engine of the invoice numbers dialog. This class is only for the dialog window. 
 * It handles all the things for the visual of the dialog.
 * 
 * @since      1.1.2
 * @package    Tourmix
 * @subpackage Tourmix/javascripts
 * @author     Tourmix <info@tourmix.delivery>
 */

class InvoiceNumbersDialog {
    DIALOG_ANIM_TIME_MS = 200;

    dialog;                 // The dialog html element
    closeBt;                // The close button of the dialog
    ordersTableBody;        // The body of the cod ordes table, this will show the orders
    sendButton;             // Send the orders to the Tourmix API
    errorMessage;           // The error message html element

    sendEventCallback = (object) => {};

    /**
     * Initialize all the things for the dialog window
     */
    constructor () {
        this.initVariables();

        if(this.allVariablesDefined()) {
            this.initActions();
        } else {
            console.error("InvoiceNumbersDialog error, variables are not defined");
        }
    }

    /**
     * Init all the variables
     */
    initVariables () {
        this.dialog             = document.getElementById("tourmix-invoice-number-dialog-container");
        this.closeBt            = document.getElementById("tourmix-invoice-number-dialog-close-bt");
        this.ordersTableBody    = document.getElementById("cod-orders-table-body");
        this.sendButton         = document.getElementById("cod-order-submit");
        this.errorMessage       = document.getElementById("invoice-number-error-msg");
    }

    /**
     * Init all the actions
     */
    initActions () {
        this.setDialogCloseBtClick();
        this.setSendButtonClick();
    }

    /**
     * Returns true if all the variables are defined
     */
    allVariablesDefined () {
        if(
            !this.isDefined(this.dialog)
            || !this.isDefined(this.closeBt)
            || !this.isDefined(this.ordersTableBody)
            || !this.isDefined(this.sendButton)
        ) {
            return false;
        }

        return true;
    }

    isDefined (variable) {
        return variable != undefined;
    }

    /**
     * This will add an action listener to the dialog close button
     */
    setDialogCloseBtClick () {
        this.closeBt.addEventListener("click", (e) => {
            this.closeDialog();
        });
    }

    /**
     * Sets up a click event for the final transfer button which is appears in the dialog.
     * This will call the saveEventCallback and gives all the information
     */
    setSendButtonClick () {
        this.sendButton.addEventListener("click", () => {
            if(this.checkRequiredFields()) {
                this.sendEventCallback(this.collectInvoiceNumbers());
                this.closeDialog();
            }
        });
    }

    /**
     * We can create an event listener for this class to handle the send event of the dialog.
     * The callback furnction what we define should have one parameter which is an object
     * 
     * @param function callback - a callback function
     */
    addSendEventListener (callback) {
        this.sendEventCallback = callback;
    }

    /**
     * Open (show) the dialog
     * ordersData: 
     * [
     *  {   
     *      orderId: 1,
     *      payment: 'cod'
     *      recipientName: 'name',
     *      price: '1000 Ft',
     *  }
     * ]
     */
    openDialog (ordersData) {
        if(this.allVariablesDefined()) {
            this.dialog.style = "";

            this.showCODOrders(ordersData);

            setTimeout(() => {
                this.dialog.children[0].style = "scale: 1;";
            }, 10);
        }
    }

    /**
     * Close (hide) the dialog
     */
    closeDialog () {
        this.dialog.children[0].style = "scale: 0;";

        setTimeout(() => {
            this.dialog.style = "display: none;";
        }, this.DIALOG_ANIM_TIME_MS);
    }

    /**
     * This function renders the given orders on the dialog
     * @param {*} ordersData 
     */
    showCODOrders(ordersData) {
        let html = "";

        ordersData.forEach(order => {
            html += 
            `<tr>
                <td>
                    #${order.orderId} ${order.recipientName}
                </td>
                <td>
                    ${order.price}
                </td>
                <td>
                    <input type="text" class="invoice-number-inputs" data-order-id="${order.orderId}" placeholder="Számla száma">
                </td>
            </tr>
            `;
        });
        
        this.ordersTableBody.innerHTML = html;
        this.hideErrorMessage();
    }

    /**
     * This function collects all the invoice numbers.
     * @returns associative array - conatins the invoice numbers 
     * {
     *  'order id': 'invoice number'
     * }
     */
    collectInvoiceNumbers() {
        const invoiceInputs = document.querySelectorAll('.invoice-number-inputs');
        let invoiceNumbers = {};

        invoiceInputs.forEach(input => {
            invoiceNumbers[input.dataset.orderId] = input.value.trim();
        });

        return invoiceNumbers;
    }
    
    /**
     * This function checkes that all the invoice numbers are given.
     * @returns 
     */
    checkRequiredFields () {
        return true; // Invoice numbers are currently not required.

        let invoiceNumbers = this.collectInvoiceNumbers();

        for(let key in invoiceNumbers) {
            if (invoiceNumbers[key] === '') {
                this.showErrorMessage();
                return false;
            }
        }

        return true;
    }

    showErrorMessage() {
        this.errorMessage.style = "";
    }

    hideErrorMessage() {
        this.errorMessage.style = "display: none;";
    }
}