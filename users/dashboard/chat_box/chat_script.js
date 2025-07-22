document.addEventListener('DOMContentLoaded', () => {
    const groupList = document.querySelector('.group-list');
    const chatMessagesContainer = document.getElementById('chat-messages-container');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const groupIdInput = document.getElementById('group-id-input');
    const currentGroupName = document.getElementById('current-group-name');
    const chatInputContainer = document.getElementById('chat-input-container');
    const noGroupPlaceholder = document.querySelector('.no-group-selected');
    
    const uploadFileBtn = document.getElementById('upload-file-btn');
    const noteFileInput = document.getElementById('note-file-input');
    // ADDED: Reference to the new report button
    const reportBtn = document.getElementById('report-group-btn');

    let currentGroupId = null;
    let messageFetchInterval = null;

    const fetchMessages = async (groupId) => {
        try {
            const response = await fetch(`get_messages.php?group_id=${groupId}`);
            const messages = await response.json();

            if (messages.error) {
                chatMessagesContainer.innerHTML = `<p class="error-message">${messages.error}</p>`;
                return;
            }

            const newMessagesHash = JSON.stringify(messages);
            if (chatMessagesContainer.dataset.messagesHash === newMessagesHash) {
                return; 
            }
            chatMessagesContainer.dataset.messagesHash = newMessagesHash;

            chatMessagesContainer.innerHTML = ''; 
            messages.forEach(msg => {
                const messageBubble = document.createElement('div');
                messageBubble.classList.add('message-bubble');

                const messageType = parseInt(msg.user_id) === currentUserId ? 'sent' : 'received';
                messageBubble.classList.add(messageType);

                const senderName = messageType === 'received' ? `<p class="message-sender">${escapeHTML(msg.user_name)}</p>` : '';
                
                messageBubble.innerHTML = `
                    ${senderName}
                    <p class="message-content">${msg.content}</p> 
                    <p class="message-timestamp">${formatTimestamp(msg.time_stamp)}</p>
                `;
                chatMessagesContainer.appendChild(messageBubble);
            });

            chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight;

        } catch (error) {
            console.error('Error fetching messages:', error);
            chatMessagesContainer.innerHTML = `<p class="error-message">Could not load messages.</p>`;
        }
    };

    groupList.addEventListener('click', (e) => {
        const groupItem = e.target.closest('.group-item');
        if (!groupItem) return;

        document.querySelectorAll('.group-item').forEach(item => item.classList.remove('active'));
        groupItem.classList.add('active');

        currentGroupId = groupItem.dataset.groupId;
        const groupName = groupItem.dataset.groupName;

        currentGroupName.textContent = groupName;
        groupIdInput.value = currentGroupId;
        if (noGroupPlaceholder) noGroupPlaceholder.style.display = 'none';
        chatInputContainer.style.display = 'flex';
        chatMessagesContainer.innerHTML = '<p>Loading messages...</p>';

        // MODIFIED: Show the report button and set its link
        if(reportBtn) {
            reportBtn.style.display = 'inline-flex';
            reportBtn.href = `report/report.php?group_id=${currentGroupId}`;
        }

        if (messageFetchInterval) {
            clearInterval(messageFetchInterval);
        }

        fetchMessages(currentGroupId);
        messageFetchInterval = setInterval(() => fetchMessages(currentGroupId), 3000);
    });

    messageForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!currentGroupId || !messageInput.value.trim()) return;

        const formData = new FormData(messageForm);

        try {
            const response = await fetch('send_message.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                messageInput.value = '';
                await fetchMessages(currentGroupId);
            } else {
                alert('Error sending message: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert('A network error occurred while sending the message.');
        }
    });

    uploadFileBtn.addEventListener('click', () => {
        if (!currentGroupId) {
            alert("Please select a group first.");
            return;
        }
        noteFileInput.click();
    });

    noteFileInput.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('note_file', file);
        formData.append('group_id', currentGroupId);

        try {
            const response = await fetch('upload_file.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                await fetchMessages(currentGroupId);
            } else {
                alert('Error uploading file: ' + (result.error || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error uploading file:', error);
            alert('A network error occurred during the file upload.');
        }
        
        e.target.value = ''; 
    });

    const formatTimestamp = (timestamp) => {
        const date = new Date(timestamp);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    };
    
    const escapeHTML = (str) => {
        if (str === null || str === undefined) return '';
        const p = document.createElement('p');
        p.appendChild(document.createTextNode(str));
        return p.innerHTML;
    };
});
