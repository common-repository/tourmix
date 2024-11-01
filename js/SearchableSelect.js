/**
 * The engine of the searchable selects
 * 
 * @since      1.0.0
 * @package    Tourmix
 * @subpackage Tourmix/javascripts
 * @author     Tourmix <info@tourmix.delivery>
 */

class SearchableSelect {
    dataArray;
    container;
    searchbar;
    resultsHolder;
    destInput;
    arrayUsedField;
    arraySiblingField;
    siblingInput;
    arrayDisplayedField;

    /**
     * The constructor
     */
    constructor (dataArray, arrayUsedField, arraySiblingField, arrayDisplayedField, siblingInput, destInput, containerID, searchbarID, resultsHolderID) {
        this.initVariables(dataArray, arrayUsedField, arraySiblingField, arrayDisplayedField, siblingInput, destInput, containerID, searchbarID, resultsHolderID);

        if(this.allVariablesDefined())
            this.initActions();
        
        this.showResults(dataArray);
    }

    /**
     * Init all the variables
     */
    initVariables (dataArray, arrayUsedField, arraySiblingField, arrayDisplayedField, siblingInput, destInput, containerID, searchbarID, resultsHolderID) {
        this.dataArray              = dataArray;
        this.arrayUsedField         = arrayUsedField;
        this.destInput              = destInput;
        this.arraySiblingField      = arraySiblingField;
        this.siblingInput           = siblingInput;
        this.arrayDisplayedField    = arrayDisplayedField;
        this.container              = document.getElementById(containerID);
        this.searchbar              = document.getElementById(searchbarID);
        this.resultsHolder          = document.getElementById(resultsHolderID);
    }

    /**
     * Init all the actions
     */
    initActions () {
        this.setWindowClickEvent();
        this.setDestInputClickEvent();
        this.setSearchbarInputEvent();
    }

    /**
     * Returns true if all the variables are defined
     */
    allVariablesDefined () {
        if(
               !this.isDefined(this.container)
            || !this.isDefined(this.searchbar)
            || !this.isDefined(this.resultsHolder)
        ) {
            return false;
        }

        return true;
    }

    isDefined (variable) {
        return variable != undefined;
    }

    setDestInputClickEvent () {
        this.destInput.addEventListener("click", (e) => {
            this.handleDestInputEvent();
        });
    }

    setWindowClickEvent () {
        window.addEventListener("click", (event) => {
            if (event.target === this.container || this.container.contains(event.target) || event.target === this.destInput) {
                event.preventDefault();
            } else {
                this.close();
            }
        });
    }

    open () {
        this.searchbar.focus();
        this.container.style.display = "block";

        setTimeout(() => {
            this.resultsHolder.style.height = "195px";
            this.searchbar.focus();
        }, 1);
    }

    close () {
        this.resultsHolder.style.height = "0px";

        setTimeout(() => {
            this.container.style.display = "none";
            this.searchbar.value = "";
            this.showResults(this.dataArray);
        }, 200);
    }

    handleDestInputEvent () {
        if(this.container.style.display == "block") {
            this.close();
        } else {
            this.open();
        }
    }

    showResults (resultArray) {
        this.resultsHolder.innerHTML = "";

        resultArray.forEach(result => {
            this.resultsHolder.appendChild(this.createResultDiv(result));
        });
    }

    createResultDiv (result) {
        let div = document.createElement("div");
        div.setAttribute("class", "searchable-select-result");
        div.setAttribute("data-sibling-value", result[this.arraySiblingField]);
        div.setAttribute("data-value", result[this.arrayUsedField]);
        div.innerHTML = result[this.arrayDisplayedField];

        this.setResultClickEvent(div);

        return div;
    }

    setResultClickEvent (element) {
        element.addEventListener("click", (e) => {
            this.destInput.value = element.dataset.value;
            this.siblingInput.value = element.dataset.siblingValue;
            this.close();
        })
    }

    setSearchbarInputEvent () {
        this.searchbar.addEventListener("input", (e) => {
            this.showResults(
                this.filterResults(this.searchbar.value)
            );
        });
    }

    filterResults (filterString) {
        return this.dataArray.filter((item) => {
            return (item[this.arrayUsedField] + "").toUpperCase().includes(filterString.toUpperCase());
        });
    }
}