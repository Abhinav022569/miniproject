// JavaScript for dashboard functionalities, including create group form toggle.

document.addEventListener('DOMContentLoaded', function() {
    // --- Logic for Create Group Form on study_group.php ---
    const toggleButton = document.getElementById('toggle-create-group-form');
    const createGroupCard = document.getElementById('create-group-form-card');
    const createGroupForm = document.getElementById('group-creation-form');

    // Check if these elements exist on the current page before adding listeners
    if (toggleButton && createGroupCard && createGroupForm) {
        const initialButtonText = toggleButton.innerHTML; // Store initial text

        // Check if there's a message (success/error) on page load
        const hasMessage = document.querySelector('.message');
        if (hasMessage) {
            // If there's a message, hide the form and revert button text
            createGroupCard.style.display = 'none'; // Hide the form
            toggleButton.innerHTML = initialButtonText; // Revert button text
            toggleButton.classList.remove('request-creation-active'); // Remove active class
            createGroupForm.reset(); // Clear form fields
            // Removed: hasMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        toggleButton.addEventListener('click', function() {
            if (createGroupCard.style.display === 'none' || createGroupCard.style.display === '') {
                // Form is hidden or not yet displayed, so show it
                createGroupCard.style.display = 'block';
                this.innerHTML = '<i class="fas fa-paper-plane"></i> Request Creation';
                this.classList.add('request-creation-active');
                // Removed: createGroupCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                // Form is visible, so trigger form submission
                createGroupForm.submit();
            }
        });

        // Optional: Trigger form display from "Create or join one!" link if no groups found
        const triggerFromEmptyLink = document.getElementById('trigger-create-group-from-empty');
        if (triggerFromEmptyLink) {
            triggerFromEmptyLink.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default link behavior
                toggleButton.click(); // Simulate click on the main toggle button
            });
        }
    }

    // --- Logic for Profile Picture Upload on profile_page.php ---
    const triggerPicUpload = document.getElementById('trigger-pic-upload');
    const profilePicInput = document.getElementById('profile_pic_input');
    const profilePicForm = document.getElementById('profile-pic-form');

    if (triggerPicUpload && profilePicInput && profilePicForm) {
        triggerPicUpload.addEventListener('click', function() {
            profilePicInput.click();
        });

        profilePicInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                profilePicForm.submit();
            }
        });
    }
});
