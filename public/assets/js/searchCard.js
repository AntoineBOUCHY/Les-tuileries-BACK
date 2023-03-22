const searchCard = {
    baseURI: window.location.origin + '/admin/card/',
    searchBar: [],
    bodyTable: [],
    init: function() {
        // initialise values
        searchCard.searchBar = document.querySelector("input[type=search]");
        searchCard.bodyTable = document.querySelector('tbody');

        // Add listener for
        searchCard.searchBar.addEventListener('keyup', searchCard.callApi);
        searchCard.callApi();
    },
    createRow: function(cardId, reference, receiver, amount, boughtAt, status) {
        // Create a td element
        const rowElement = document.createElement('tr');
    
        const referenceElement = document.createElement('td');
        // Create a link element
        const linkReference = searchCard.createLink('#' + reference, cardId);
        // Add the link in the td
        referenceElement.appendChild(linkReference);
        
        const receiverElement = searchCard.createTdElement(receiver);
        const amountElement = searchCard.createTdElement(amount + "€");
        const boughtAtElement = searchCard.createTdElement(boughtAt);
    
        const statusElement = document.createElement('td');
        const linkStatus = searchCard.createLink(status, cardId);
        // Add a personalize class for bootstrap css style
        linkStatus.classList.add('badge', 'rounded-pill');
        if(status === "expiré"){
            linkStatus.classList.add('text-bg-danger');
        } else if(status === "active") {
            linkStatus.classList.add('text-bg-success');
        } else {
            linkStatus.classList.add('text-bg-secondary');
        }
        statusElement.appendChild(linkStatus)
    
        // Add all td element in the row
        rowElement.appendChild(referenceElement);
        rowElement.appendChild(receiverElement);
        rowElement.appendChild(amountElement);
        rowElement.appendChild(boughtAtElement);
        rowElement.appendChild(statusElement);
    
        return rowElement;
    },
    /**
     * Create a link
     * @param {string} content The content of the link
     * @param {string} cardId  The id of the card
     * @returns {HTMLAnchorElement}
     */
    createLink: function(content, cardId) {
        // Create a link element
        const linkReference = document.createElement('a');
        // Add a content
        linkReference.textContent = content;
        // Add a href
        linkReference.href = searchCard.baseURI + cardId;

        return linkReference;
    },
    /**
     * Create td element
     * @param {string} content The content of the td element
     * @returns {HTMLTableCellElement}
     */
    createTdElement: function(content) {
        // Create a td element
        const tdElement = document.createElement('td');
        // Add a content
        tdElement.textContent = content;

        return tdElement;
    },
    /**
     * Remove all td elements
     */
    emptyTbody: function() {
        // Get all td elements
        const rows = document.querySelectorAll('td');
        for(let row of rows){
            row.remove();
        }
    },
    /**
     * Get the status of the card
     * @param {Date} limitedDate    The expiration date
     * @param {Date} today          Today
     * @param {Date|null} usedDate  The date of use of the card
     * @returns {string}
     */
    getStatus: function(limitedDate, today, usedDate) {
        let status = "";
        // if the deadline has passed
        if(usedDate !== null){
            status = 'désactivé';
            // if the card has been used
        } else if(limitedDate < today){
            status = 'expiré';
        } else {
            status = 'active';
        }

        return status;
    },
    /**
     * Call the endpoint to obtain the required cards
     */
    callApi: function() {
        fetch( searchCard.baseURI + 'search',{
            method: "POST",
            headers: {
                'content-type': 'application/json'
            },
            body:JSON.stringify(searchCard.searchBar.value)
        })
        .then((response) => response.json())
        .then((data) => {
            // Remove all td elements
            searchCard.emptyTbody();
            const today = new Date();
            for(let card of data.cards){
                const boughtAt = new Date(card.bought_at);
                const limitedDate = new Date(card.limited_date);
                // Get the status of the card
                const status = searchCard.getStatus(limitedDate, today, card.used_at);
                // Create a row
                const newRowElement = searchCard.createRow(card.id, card.reference, card.receiver, card.amount, boughtAt.toLocaleDateString(), status);
                // Add the row to the tbody element
                searchCard.bodyTable.appendChild(newRowElement); 
            }
        })
    }
}

window.addEventListener('DOMContentLoaded', searchCard.init);
