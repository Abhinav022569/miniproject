document.addEventListener('DOMContentLoaded', () => {
    // --- Profile Picture Selection Logic ---
    const picOptions = document.querySelectorAll('.profile-pic-option');
    const picPathInput = document.getElementById('profile-pic-path-input');
    let groupsJoined = 0; // Keep track of joined groups

    picOptions.forEach(option => {
        option.addEventListener('click', () => {
            // Remove 'selected' class from all other options
            picOptions.forEach(opt => opt.classList.remove('selected'));
            // Add 'selected' class to the clicked option
            option.classList.add('selected');
            // Update the hidden input with the path of the selected image
            picPathInput.value = option.dataset.pic;
        });
    });

    // --- Group Joining Logic ---
    const joinButtons = document.querySelectorAll('.join-btn');

    joinButtons.forEach(button => {
        button.addEventListener('click', () => {
            const groupId = button.dataset.groupId;
            
            // Prevent multiple clicks while processing
            if (button.disabled) return;
            button.disabled = true;

            // CORRECTED FILE PATH: The path now correctly points to the join_group.php script.
            fetch('../dashboard/join_group.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `group_id=${groupId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button style and text on successful join
                    button.textContent = 'Joined';
                    button.classList.add('joined');
                    groupsJoined++; // Increment the counter
                } else {
                    // If joining fails, show an alert and re-enable the button
                    alert(data.message || 'Failed to join group.');
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error joining group:', error);
                alert('An error occurred. Please try again.');
                button.disabled = false;
            });
        });
    });
    
    // --- Group Search/Filter Logic ---
    const searchInput = document.getElementById('group-search');
    const groupItems = document.querySelectorAll('.group-item');

    searchInput.addEventListener('keyup', () => {
        const searchTerm = searchInput.value.toLowerCase().trim();

        groupItems.forEach(item => {
            const groupName = item.dataset.groupName;
            // Show or hide group items based on the search term
            if (groupName.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // --- Form Submission Validation ---
    const setupForm = document.getElementById('setup-form');
    setupForm.addEventListener('submit', (e) => {
        // Check if a profile picture has been selected before submitting
        if (!picPathInput.value) {
            e.preventDefault(); // Stop form submission
            alert('Please choose a profile picture before continuing.');
            return; // Stop further checks
        }
        // NEW: Check if at least one group has been joined
        if (groupsJoined === 0) {
            e.preventDefault();
            alert('Please join at least one study group to get started.');
        }
    });
});
