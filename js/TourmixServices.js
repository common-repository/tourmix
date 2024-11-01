/**
 * @since      1.1.0
 * @package    Tourmix
 * @subpackage Tourmix/javascripts
 * @author     Tourmix <info@tourmix.delivery>
 */

window.addEventListener("load", () => {
    let parcelLabelDialog   = new ParcelLabelDialog();
    let downloadDialog      = new DownloadDialog("open-download-dialog");
    let tourmixServices     = new TourmixServices();
})

class TourmixServices {

    pageHeader;                 //The header section of the TOURMIX admin page
    multiActionbutton;          //This button hadles the multiple actions
    multipleEventHandlingList;  //This is the select element whic contains the possible actions like "Átadás Tourmix-nek"
    orderCheckBoxes;            //The check boxes of the orders
    transferButtons;            //The transfer action button of the orders
    cbSelectAll;                //The select all check box
    loader;                     //The loader container

    invoiceNumbersDialog;       //The invoice numbers dialog object
    
    chosenOrderIds = [];      //The chosen product ids to be transfered

    constructor () {
        this.initVariables();
        this.initActions();
    }

    /**
     * Init all the variables
     */
    initVariables () {
        this.pageHeader                 = document.getElementById("tourmix-delivery-orders-header");
        this.multiActionbutton          = document.getElementById("do-multi-action");
        this.multipleEventHandlingList  = document.getElementById("multiple-event-handling");
        this.orderCheckBoxes            = document.getElementsByClassName('tourmix-order-cb');
        this.transferButtons            = document.getElementsByClassName("tourmix-transfer-button");
        this.cbSelectAll                = document.getElementById('tourmix-cb-select-all');
        this.loader                     = document.getElementById('loader-container');

        this.invoiceNumbersDialog       = new InvoiceNumbersDialog();
    }

    /**
     * Init the actions
     */
    initActions () {
        this.headerScrollHandler();
        this.setTransferButtonClick();
        this.setCheckAllCheckboxes();
        this.setMultiActionButtonClick();
        this.setInvoiceDialogSendEvent();
    }

    /**
     * Sets up an event listener for the dialog and handles it
     */
    setInvoiceDialogSendEvent () {
        this.invoiceNumbersDialog.addSendEventListener( (invoiceNumbers) => {
            console.log(invoiceNumbers);
            this.dispachOorders(this.chosenOrderIds, invoiceNumbers);
        });
    }

    /**
     * The dispatchOrders function handles the dispatch process of orders identified by their IDs. 
     * When dealing with cash on delivery orders, an optional invoiceArray can be provided 
     * where each order ID corresponds to its invoice number.
     * 
     * @param {*} invoiceArray 
     * {
     *  'order_id': invoice_number
     * }
     */
    dispachOorders (orderIds, invoiceArray) {
        if (!Array.isArray(orderIds) || orderIds.length === 0) {
            throw new Error('The orderIds parameter is required and must be a non-empty array.');
        }

        this.showLoader();

        this.generatePostToApiForOrders(orderIds, invoiceArray, ( orderIds, response ) => {
            console.log(response);
            location.reload();
        });
    }

    /**
     * When the user Scrolls down the header section will get a 'scrolled' class attribute, 
     * by this attribute the header will get a fancy shadow
     */
    headerScrollHandler () {
        window.addEventListener("scroll", (e) => {
            const SIZE = 10;

            if(window.scrollY >= SIZE) {
                this.pageHeader.classList.add("scrolled");
            } else {
                this.pageHeader.classList.remove("scrolled");
            }

        });
    }

    /**
     * It handles the multiple events what users do.
     */
    setMultiActionButtonClick () {
        this.multiActionbutton.addEventListener("click", () => {
            if(this.multipleEventHandlingList.value == "transfer-tourmix") {
                this.chosenOrderIds = this.getSelectedItems();
                this.handleOrdersDisppatch(this.chosenOrderIds);
            }
        });
    }

    /**
     * It will give back an array of ids what orders were selected by the user
     * @returns array of order ids
     */
    getSelectedItems () {
        let items = [];

        for (let cb of this.orderCheckBoxes) {
            if(cb.checked) {
                items.push(cb.value);
            }
        }

        return items;
    }

    /**
     * This will add action listener to all the transfer buttons and handle the event
     */
    setTransferButtonClick () {
        for(let bt of this.transferButtons) {
            bt.addEventListener("click", (e) => {
                this.chosenOrderIds = [];
                this.chosenOrderIds.push( e.target.id );

                this.handleOrdersDisppatch(this.chosenOrderIds);
            })
        }
    }

    /**
     * Create an ajax request to tourmixSendOrdersToApi endpoint
     * 
     * @param array orderIds 
     * @param object transferData 
     * @param function callback 
     */
    generatePostToApiForOrders (orderIds, invoiceArray, callback) {
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl, //ajaxurl is a worldpress super global
            data: {
                action:         'tourmixSendOrdersToApi',
                order_ids:      orderIds,
                invoice_array:  invoiceArray
            },
            success: function(response) {
                callback( orderIds, response );
            },
            error: function(response) {
                console.log(response.responseText);
            }
        });
    }

    /**
     * This will add an action listener to the select-all checkbox and handle the event.
     */
    setCheckAllCheckboxes () {
        this.cbSelectAll.addEventListener('change', (e) => {
            this.selectAllCheckbox(this.cbSelectAll.checked);
        });
    }

    /**
     * Set the checked parameter to true or false for all the product check boxes
     * @param check - true, false
     */
    selectAllCheckbox (check) {
        for (let cb of this.orderCheckBoxes) {
            cb.checked = check;
        }
    }

    /**
     * Shows the loader
     */
    showLoader () {
        this.loader.style = "";
    }

    /**
     * This function handles the dispatch process of orders. It opens the invoice dialog if any 'cod' order given.
     * @param {*} orderIds 
     */
    handleOrdersDisppatch(orderIds) {
        if(orderIds.length == 0) {
            return;
        }

        let ordersData = this.collectOrdersDataForIds( this.chosenOrderIds );
        let codOrdersData = this.filterCODOrdersData( ordersData );

        if( codOrdersData.length > 0 ) {
            this.invoiceNumbersDialog.openDialog( codOrdersData );
        } else {
            this.dispachOorders( this.chosenOrderIds ); 
        }
    }

    /**
     * Filters the COD (Cahce on delivery) orders from the give 'ordersData' parameter.
     * For getting the 'ordersData' use the function 'collectOrdersDataForIds'.
     * @param {*} ordersData 
     */
    filterCODOrdersData(ordersData) {
        return ordersData.filter(order => order.payment == "cod");
    }

    /**
     * Collect all the orders data for the given order ids.
     * @param {*} orderIds 
     * @returns 
     */
    collectOrdersDataForIds(orderIds) {
        let ordersData = [];

        console.log(orderIds)
        
        orderIds.forEach((id) => {
            console.log(id);
            let order = document.getElementById(`post-${id}`);
            
            ordersData.push({
                orderId: id,
                payment: order.dataset.payment,
                recipientName: order.dataset.recipientName,
                price: order.dataset.orderPrice
            });
        })

        return ordersData;
    }
}