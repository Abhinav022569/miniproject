document.addEventListener('DOMContentLoaded', () => {
    const addTaskForm = document.getElementById('add-task-form');

    // Add a new task
    addTaskForm.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Add task form submitted.'); // Debug: Log form submission

        const formData = new FormData(this);

        fetch('add_task.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Received response from add_task.php:', response); // Debug: Log the response object
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Parsed JSON data:', data); // Debug: Log the parsed data
            if (data.success) {
                location.reload(); 
            } else {
                // Alert with the specific error message from the server
                alert('Error: ' + (data.message || 'Could not add task.'));
            }
        })
        .catch(error => {
            // This will catch network errors or problems with the server response
            console.error('Fetch Error:', error);
            alert('A critical error occurred. Please check the console for details.');
        });
    });

    const taskList = document.querySelector('.task-list');

    // Handle clicks on checkboxes and delete icons
    taskList.addEventListener('click', function(e) {
        const taskItem = e.target.closest('.task-item');
        if (!taskItem) return;

        const taskId = taskItem.dataset.taskId;

        // Handle checkbox click
        if (e.target.closest('.task-checkbox')) {
            const isCompleted = taskItem.classList.contains('completed');
            const newStatus = isCompleted ? 'in progress' : 'done';
            updateTaskStatus(taskId, newStatus);
        }

        // Handle delete click
        if (e.target.closest('.task-delete')) {
            if (confirm('Are you sure you want to delete this task?')) {
                deleteTask(taskId, taskItem);
            }
        }
    });

    function updateTaskStatus(taskId, status) {
        const formData = new FormData();
        formData.append('task_id', taskId);
        formData.append('status', status);

        fetch('update_task.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Could not update task.');
            }
        })
        .catch(error => {
            console.error('Update Task Error:', error);
            alert('An error occurred while updating the task.');
        });
    }

    function deleteTask(taskId, taskElement) {
        const formData = new FormData();
        formData.append('task_id', taskId);

        fetch('delete_task.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                taskElement.remove();
            } else {
                alert('Could not delete task.');
            }
        })
        .catch(error => {
            console.error('Delete Task Error:', error);
            alert('An error occurred while deleting the task.');
        });
    }
});
