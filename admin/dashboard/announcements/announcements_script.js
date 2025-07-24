document.addEventListener('DOMContentLoaded', () => {
    // --- Toggle visibility of the creation form ---
    const createBtn = document.getElementById('create-announcement-btn');
    const createCard = document.getElementById('create-announcement-card');

    if (createBtn && createCard) {
        // Hide the form by default if it's not needed for displaying errors
        const hasMessages = createCard.querySelector('.message');
        if (!hasMessages) {
            createCard.style.display = 'none';
        }

        createBtn.addEventListener('click', () => {
            // Toggle the display of the form card
            const isHidden = createCard.style.display === 'none';
            createCard.style.display = isHidden ? 'block' : 'none';
        });
    }

    // --- Expand/collapse announcement content ---
    const announcementList = document.querySelector('.announcements-list');

    if (announcementList) {
        announcementList.addEventListener('click', (event) => {
            // Find the title row that was clicked
            const titleRow = event.target.closest('.announcement-title-row');
            if (!titleRow) return;

            // Find the content associated with that title
            const content = titleRow.nextElementSibling;
            if (content && content.classList.contains('announcement-content')) {
                // Toggle the display of the content
                const isContentVisible = content.style.display === 'block';
                content.style.display = isContentVisible ? 'none' : 'block';
            }
        });
    }
});
