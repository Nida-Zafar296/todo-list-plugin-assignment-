<?php
// File: includes/external/class-todolist-cron-jobs.php

// Activation hook to schedule the cron event
register_activation_hook(__FILE__, 'todo_list_schedule_task_reminder');

// Register scheduler
function register_scheduler() {
    $scheduler = new Scheduler();
    $scheduler->todo_list_send_task_reminder_email();
}

// Cron job for pending tasks reminder
add_action('todo_list_task_reminder', 'register_scheduler');

// Schedule the task reminder event
function todo_list_schedule_task_reminder() {
    if (!wp_next_scheduled('todo_list_task_reminder')) {
        wp_schedule_event(time(), 'daily', 'todo_list_task_reminder');
    }
}

// Deactivation hook to clear the scheduled cron event
register_deactivation_hook(__FILE__, 'todo_list_clear_task_reminder');

// Clear the task reminder event
function todo_list_clear_task_reminder() {
    wp_clear_scheduled_hook('todo_list_task_reminder');
}
