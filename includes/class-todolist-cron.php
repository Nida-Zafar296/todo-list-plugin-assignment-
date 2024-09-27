<?php
class Scheduler
{
    function todo_list_send_task_reminder_email()
    {
        // Get all users
        $users = get_users(); 
 
        foreach ($users as $user) {
            $user_id = $user->ID;
            $tasks = get_user_meta($user_id, 'todolist_tasks', true);
 
            if ($tasks) {
                // Filter tasks with 'pending' status
                $pending_tasks = array_filter($tasks, function ($task) {
                    return $task['status'] === 'pending';
                });
 
                if (!empty($pending_tasks)) {
                    // Build the task list for the email body
                    $task_list = "";
                    foreach ($pending_tasks as $task) {
                        $task_list .= sprintf(
                            "- %s\n",
                            esc_html($task['task'])
                        );
                    }
 
                    $task_count = count($pending_tasks);
                    $subject = __('Your Pending Tasks Reminder', 'todolist_tasks');
                    $message = sprintf(
                        __("Dear %s,\n\nWe hope this message finds you well. This is a reminder that you have %d pending task%s:\n\n%s\n\nPlease take some time to review and complete these tasks as soon as possible. If you need any assistance, feel free to contact us.\n\nThank you for your attention!\n\nBest regards,\nThe To-Do List Team", 'todolist_tasks'),
                        $user->display_name,
                        $task_count,
                        ($task_count > 1 ? 's' : ''), 
                        $task_list
                    );
 
                    // Attempt to send the email
                    $email_sent = wp_mail($user->user_email, $subject, $message);
 
                    // Log the email status
                    if ($email_sent) {
                        $status_message = sprintf(
                            "Reminder email sent successfully to %s (%s)",
                            $user->display_name,
                            $user->user_email
                        );
                    } else {
                        $status_message = sprintf(
                            "Failed to send reminder email to %s (%s)",
                            $user->display_name,
                            $user->user_email
                        );
                    }

                    // Use error_log to log email status (can be viewed in debug.log if WP_DEBUG is enabled)
                    error_log($status_message);

                    // Optionally, store the email status in a transient for checking later via WP Control
                    set_transient('todolist_email_status_' . $user_id, $status_message, 60 * 60); // Store status for 1 hour
                }
            }
        }
    }
}