// JavaScript for dashboard functionalities
document.addEventListener('DOMContentLoaded', function() {
    // --- Logic for Create Group Form on study_group.php ---
    const toggleButton = document.getElementById('toggle-create-group-form');
    const createGroupCard = document.getElementById('create-group-form-card');
    const createGroupForm = document.getElementById('group-creation-form');

    if (toggleButton && createGroupCard && createGroupForm) {
        const initialButtonText = toggleButton.innerHTML;
        const hasMessage = document.querySelector('.message');
        if (hasMessage) {
            createGroupCard.style.display = 'none';
            toggleButton.innerHTML = initialButtonText;
            toggleButton.classList.remove('request-creation-active');
            createGroupForm.reset();
        }
        toggleButton.addEventListener('click', function() {
            if (createGroupCard.style.display === 'none' || createGroupCard.style.display === '') {
                createGroupCard.style.display = 'block';
                this.innerHTML = '<i class="fas fa-paper-plane"></i> Request Creation';
                this.classList.add('request-creation-active');
            } else {
                createGroupForm.submit();
            }
        });
        const triggerFromEmptyLink = document.getElementById('trigger-create-group-from-empty');
        if (triggerFromEmptyLink) {
            triggerFromEmptyLink.addEventListener('click', function(event) {
                event.preventDefault();
                toggleButton.click();
            });
        }
    }

    // --- Logic for Profile Picture Upload on profile_page.php ---
    const triggerPicUpload = document.getElementById('trigger-pic-upload');
    const profilePicInput = document.getElementById('profile_pic_input');
    const profilePicForm = document.getElementById('profile-pic-form');

    if (triggerPicUpload && profilePicInput && profilePicForm) {
        triggerPicUpload.addEventListener('click', () => profilePicInput.click());
        profilePicInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                profilePicForm.submit();
            }
        });
    }

    // --- Reusable Notification Function ---
    function showNotification(message, type = 'success') {
        const container = document.getElementById('notification-container');
        if (!container) return;
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        container.appendChild(notification);
        setTimeout(() => notification.remove(), 4000);
    }

    // --- Reusable HTML Escape Function ---
    function escapeHTML(str) {
        if (!str) return '';
        return str.replace(/[&<>"']/g, match => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[match]));
    }

    // --- Event Delegation for Dynamic Content ---
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.addEventListener('click', function(event) {
            const joinButton = event.target.closest('.join-btn');
            if (joinButton) {
                handleGroupAction('join_group.php', joinButton, 'Successfully joined the group!', 'join');
                return;
            }
            
            // MODIFIED: Added event listener for the leave button
            const leaveButton = event.target.closest('.leave-btn');
            if (leaveButton) {
                // A confirmation dialog is good practice for destructive actions,
                // but implementing a custom modal is beyond this scope.
                // Proceeding directly as requested.
                handleGroupAction('leave_group.php', leaveButton, 'Successfully left the group!', 'leave');
                return;
            }
        });
    }

    // MODIFIED: Updated handleGroupAction to include 'leave' logic
    function handleGroupAction(url, button, successMessage, action) {
        const groupId = button.dataset.groupId;
        
        const formData = new FormData();
        formData.append('group_id', groupId);

        fetch(url, { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || successMessage, 'success');
                if (action === 'join') {
                    // Replace 'Join' button with 'Joined' message
                    button.outerHTML = '<span class="joined-message">Joined</span>';
                } else if (action === 'leave') {
                    // If leaving was successful, remove the entire group card from the UI
                    const card = button.closest('.card');
                    if (card) {
                        card.style.transition = 'opacity 0.5s ease';
                        card.style.opacity = '0';
                        setTimeout(() => card.remove(), 500);
                    }
                }
            } else {
                // Show an error notification if the action failed
                showNotification(data.message || 'An error occurred.', 'error');
            }
        })
        .catch(error => {
            console.error(`Error with action ${action}:`, error);
            showNotification('A network error occurred. Please try again.', 'error');
        });
    }


    // --- Logic for Group Search on user_panel.php ---
    const searchInput = document.getElementById('group-search-input');
    const searchButton = document.getElementById('group-search-btn');
    const searchResultsContainer = document.getElementById('search-results-container');
    const searchResultsContent = document.getElementById('search-results-content');

    if (searchInput && searchButton && searchResultsContainer) {
        const performSearch = () => {
            const searchTerm = searchInput.value.trim();
            if (searchTerm.length === 0) {
                searchResultsContainer.style.display = 'none';
                return;
            }
            fetch(`search_groups.php?search=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => displaySearchResults(data))
                .catch(error => {
                    console.error('Error fetching search results:', error);
                    searchResultsContent.innerHTML = '<p class="no-data">Error searching for groups.</p>';
                    searchResultsContainer.style.display = 'block';
                });
        };

        const displaySearchResults = (groups) => {
            searchResultsContent.innerHTML = '';
            if (groups.length === 0) {
                searchResultsContent.innerHTML = '<p class="no-data">No groups found matching your search.</p>';
            } else {
                groups.forEach(group => {
                    const groupElement = document.createElement('div');
                    groupElement.classList.add('group-item');
                    const actionButton = group.is_member > 0
                        ? '<span class="joined-message">Joined</span>'
                        : `<button class="join-btn" data-group-id="${group.group_id}">Join Group</button>`;
                    groupElement.innerHTML = `
                        <div class="group-details">
                            <h4>${escapeHTML(group.group_name)}</h4>
                            <p>${escapeHTML(group.description) || 'No description.'} (${group.member_count} Members)</p>
                        </div>
                        ${actionButton}`;
                    searchResultsContent.appendChild(groupElement);
                });
            }
            searchResultsContainer.style.display = 'block';
        };

        searchButton.addEventListener('click', performSearch);
        searchInput.addEventListener('keyup', (event) => {
            if (event.key === 'Enter') performSearch();
        });

        // The main event listener on .main-content already handles clicks in search results
    }
});
