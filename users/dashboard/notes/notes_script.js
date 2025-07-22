document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('notes-search-input');
    const groupFilter = document.getElementById('group-filter-select');

    const filterNotes = () => {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedGroup = groupFilter.value;

        const studyGroupSections = document.querySelectorAll('.study-group-section');

        studyGroupSections.forEach(section => {
            const groupName = section.querySelector('.study-group-header').textContent.replace('Study Group: ', '').trim();
            const noteItems = section.querySelectorAll('.note-item-link');
            let visibleNotesInGroup = 0;

            // First, filter notes within the group based on the search term
            noteItems.forEach(note => {
                const noteTitle = note.querySelector('.note-title').textContent.toLowerCase();
                if (noteTitle.includes(searchTerm)) {
                    note.style.display = 'block';
                    visibleNotesInGroup++;
                } else {
                    note.style.display = 'none';
                }
            });

            // Now, decide if the entire group section should be visible
            const groupMatchesFilter = selectedGroup === 'all' || selectedGroup === groupName;

            if (groupMatchesFilter && visibleNotesInGroup > 0) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    };

    if (searchInput) {
        searchInput.addEventListener('keyup', filterNotes);
    }

    if (groupFilter) {
        groupFilter.addEventListener('change', filterNotes);
    }
});
