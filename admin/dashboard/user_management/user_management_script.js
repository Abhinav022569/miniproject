document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('user-search-input');
    const userTableBody = document.getElementById('user-table-body');
    const tableRows = userTableBody.getElementsByTagName('tr');

    // Ensure the search input exists before adding an event listener
    if (searchInput) {
        searchInput.addEventListener('keyup', () => {
            const searchTerm = searchInput.value.toLowerCase();

            // Loop through all table rows to filter them
            for (let i = 0; i < tableRows.length; i++) {
                const row = tableRows[i];
                const cells = row.getElementsByTagName('td');
                let match = false;

                // Loop through all cells in the current row
                for (let j = 0; j < cells.length - 1; j++) { // -1 to exclude the actions column
                    if (cells[j]) {
                        const cellText = cells[j].textContent || cells[j].innerText;
                        if (cellText.toLowerCase().indexOf(searchTerm) > -1) {
                            match = true;
                            break; // Exit the inner loop if a match is found
                        }
                    }
                }

                // Show or hide the row based on whether a match was found
                if (match) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }
});
