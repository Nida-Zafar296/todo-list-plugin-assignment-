jQuery(document).ready(function($) {
    
    $('#todoForm').submit(function(e) {
        e.preventDefault();
        var task = $('#todoItem').val();

        $.ajax({
            url: todolistAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'todolist_add_todo_task',
                todoItem: task,
                nonce: todolistAjax.nonce 
            },
            success: function(response) {
                console.log('Add Task Response:', response); 
                if (response.success) {
                    $('#todoItem').val(''); 
                    showMessage('Task successfully added!', 'success');
                    loadTodoTasks(); 
                } else {
                    showMessage(response.data.message || 'Failed to add the task.', 'error');
                }
            },
            error: function() {
                console.log('Add Task AJAX Error'); 
                showMessage('An error occurred while adding the task.', 'error');
            }
        });
    });

   
    function loadTodoTasks() {
        $.ajax({
            url: todolistAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'todolist_fetch_todo_tasks',
                nonce: todolistAjax.nonce 
            },
            success: function(response) {
                console.log('Fetch Tasks Response:', response);
                if (response.success) {
                    var tasks = response.data;
                    var tasksList = $('#todoItemsList');
                    tasksList.empty();
    
                    if (Array.isArray(tasks) && tasks.length > 0) {
                        var groupedTasks = tasks.reduce(function(acc, task) {
                            if (!acc[task.status]) {
                                acc[task.status] = [];
                            }
                            acc[task.status].push(task);
                            return acc;
                        }, {});
    
                        for (var status in groupedTasks) {
                            tasksList.append('<li class="status-group">' + status.charAt(0).toUpperCase() + status.slice(1) + '</li>');
                            groupedTasks[status].forEach(function(task) {
                                tasksList.append(
                                    '<li data-id="' + task.id + '">' +
                                    '<span class="task-text">' + task.task + '</span>' +
                                    '<select class="task-status">' +
                                    '<option value="pending" ' + (task.status === 'pending' ? 'selected' : '') + '>Pending</option>' +
                                    '<option value="complete" ' + (task.status === 'complete' ? 'selected' : '') + '>Complete</option>' +
                                    '<option value="in process" ' + (task.status === 'in process' ? 'selected' : '') + '>In Process</option>' +
                                    '</select>' +
                                    '<button class="edit-task">Edit</button>' +
                                    '<button class="delete-task">Delete</button>' +
                                    '</li>'
                                );
                            });
                        }
                    } else {
                        tasksList.append('<li>No tasks found.</li>');
                    }
                } else {
                    showMessage(response.data.message || 'Failed to fetch tasks.', 'error');
                }
            },
            error: function() {
                console.log('Fetch Tasks AJAX Error'); 
                showMessage('An error occurred while fetching tasks.', 'error');
            }
        });
    }
    

    
    $('#todoItemsList').on('change', '.task-status', function() {
        var listItem = $(this).closest('li');
        var taskId = listItem.data('id');
        var newStatus = $(this).val();
        var taskText = listItem.find('.task-text').text();

       

        $.ajax({
            url: todolistAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'todolist_update_todo_task',
                task_id: taskId,
                task: taskText,
                status: newStatus,
                nonce: todolistAjax.nonce 
            },
            success: function(response) {
                console.log('Update Task Response:', response); 
                if (response.success) {
                    showMessage(newStatus === 'delete' ? 'Task successfully deleted!' : 'Task successfully updated!', 'success');
                    if (newStatus === 'delete') {
                        loadTodoTasks(); 
                    }
                } else {
                    showMessage(response.data.message || 'Failed to update the task.', 'error');
                }
            },
            error: function() {
                console.log('Update Task AJAX Error'); 
                showMessage('An error occurred while updating the task.', 'error');
            }
        });
    });


    $('#todoItemsList').on('click', '.edit-task', function() {
        var listItem = $(this).closest('li');
        var taskId = listItem.data('id');
        var taskText = listItem.find('.task-text').text();
        var taskStatus = listItem.find('.task-status').val(); 

        var newTask = prompt('Edit Task:', taskText);
        if (newTask !== null && newTask.trim() !== '') {
            $.ajax({
                url: todolistAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'todolist_update_todo_task',
                    nonce: todolistAjax.nonce,
                    task_id: taskId,
                    task: newTask,
                    status: taskStatus 
                },
                success: function(response) {
                    console.log('Edit Task Response:', response); 
                    if (response.success) {
                        showMessage('Task successfully edit!', 'success');
                        loadTodoTasks(); 
                    } else {
                        showMessage(response.data.message || 'Failed to update the task.', 'error');
                    }
                },
                error: function() {
                    console.log('Edit Task AJAX Error'); 
                    showMessage('An error occurred while updating the task.', 'error');
                }
            });
        }
    });


    $('#todoItemsList').on('click', '.delete-task', function() {
        var listItem = $(this).closest('li');
        var taskId = listItem.data('id');

            $.ajax({
                url: todolistAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'todolist_delete_todo_task',
                    task_id: taskId,
                    nonce: todolistAjax.nonce 
                },
                success: function(response) {
                    console.log('Delete Task Response:', response); 
                    if (response.success) {
                        showMessage('Task successfully deleted!', 'success');
                        loadTodoTasks(); 
                    } else {
                        showMessage(response.data.message || 'Failed to delete the task.', 'error');
                    }
                },
                error: function() {
                    console.log('Delete Task AJAX Error'); 
                    showMessage('An error occurred while deleting the task.', 'error');
                }
            });
        }
    );

    function showMessage(message, type) {
        var messageElement = $('#message');
        messageElement.text(message);
        messageElement.css('color', type === 'success' ? 'green' : 'red');
        messageElement.fadeIn().delay(3000).fadeOut(); 
    }

    
    loadTodoTasks();
});


