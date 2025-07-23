document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('approved-group-search');
    
    // Check if the search input element exists on the page
    if (searchInput) {
        // Add an event listener for the 'keyup' event, which fires every time a key is released
        searchInput.addEventListener('keyup', () => {
            // Get the search term and convert it to lower case for case-insensitive matching
            const filter = searchInput.value.toLowerCase();
            
            // Get all the approved group cards
            const cards = document.querySelectorAll('.approved-group-card');
            
            // Loop through each card to show or hide it based on the search term
            cards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const description = card.querySelector('p').textContent.toLowerCase();
                
                // If the card's title or description includes the filter text, show it
                if (title.includes(filter) || description.includes(filter)) {
                    card.style.display = ''; // Setting display to '' reverts it to the default (e.g., 'flex')
                } else {
                    card.style.display = 'none'; // Hide the card if it doesn't match
                }
            });
        });
    }
});
