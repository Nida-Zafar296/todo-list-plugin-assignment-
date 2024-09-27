<?php

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
    return;
}

class WP_Todolist_CLI_Command extends WP_CLI_Command {
    
    public function add($args, $assoc_args) {
        list($user_id, $task) = $args;

        if (empty($user_id) || empty($task)) {
            WP_CLI::error('Both user_id and task are required.');
            return;
        }

        $user_id = intval($user_id);
        $task = sanitize_text_field($task);

      
        $tasks = get_user_meta($user_id, 'todolist_tasks', true);
        if (!is_array($tasks)) {
            $tasks = array();
        }

        $tasks[] = array(
            'id' => uniqid(), 
            'task' => $task,
            'status' => 'pending'
        );

        $result = update_user_meta($user_id, 'todolist_tasks', $tasks);

        if ($result) {
            WP_CLI::success("Task '{$task}' added to user {$user_id}'s to-do list.");
        } else {
            WP_CLI::error("Failed to add task.");
        }
    }

    // Fetch tasks for a specific user
public function fetch($args, $assoc_args) {
    list($user_id) = $args;

    if (empty($user_id)) {
        WP_CLI::error('User ID is required.');
        return;
    }

    $user_id = intval($user_id);

    // Fetch tasks from usermeta
    $tasks = get_user_meta($user_id, 'todolist_tasks', true);

    if (!is_array($tasks) || empty($tasks)) {
        WP_CLI::error("No tasks found for user {$user_id}.");
        return;
    }

    WP_CLI::success("Tasks for user {$user_id}:");
    foreach ($tasks as $task) {
        $status = $task['status'] === 'completed' ? '✔' : '✘';
        $status_text = $task['status'] === 'completed' ? 'Completed' : 'Pending';
        WP_CLI::log("Task ID: {$task['id']}, Task: {$task['task']}, Status: {$status} ({$status_text})");
    }
}

    // Update the status of a task for a specific user
    public function update($args, $assoc_args) {
        list($user_id, $task_id, $new_status) = $args;

        if (empty($user_id) || empty($task_id) || empty($new_status)) {
            WP_CLI::error('User ID, task ID, and new status are required.');
            return;
        }

        $user_id = intval($user_id);
        $new_status = sanitize_text_field($new_status);

        // Fetch tasks from usermeta
        $tasks = get_user_meta($user_id, 'todolist_tasks', true);

        if (!is_array($tasks) || empty($tasks)) {
            WP_CLI::error("No tasks found for user {$user_id}.");
            return;
        }

        $updated = false;
        // Loop through tasks and update the status of the matching task
        foreach ($tasks as &$task) {
            if ($task['id'] === $task_id) {
                $task['status'] = $new_status;
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            WP_CLI::error("Task ID '{$task_id}' not found.");
            return;
        }

        // Update usermeta with modified tasks
        $result = update_user_meta($user_id, 'todolist_tasks', $tasks);

        if ($result) {
            WP_CLI::success("Task '{$task_id}' updated to status '{$new_status}' for user {$user_id}.");
        } else {
            WP_CLI::error("Failed to update task.");
        }
    }
}

// Register the command with WP-CLI
WP_CLI::add_command('todolist', 'WP_Todolist_CLI_Command');
