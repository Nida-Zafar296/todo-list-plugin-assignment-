<?php
class Todolist_Plugin_Public {
    public function __construct() {
        add_shortcode('todolist', array($this, 'todolist_shortcode'));
        add_shortcode('login_form', array($this, 'todolist_login_form_shortcode'));
        add_shortcode('register_form', array($this, 'todolist_register_form_shortcode'));
        add_action('wp_ajax_todolist_add_todo_task', array($this, 'handle_ajax_requests'));
        add_action('wp_ajax_nopriv_todolist_add_todo_task', array($this, 'handle_ajax_requests'));
        add_action('wp_ajax_todolist_fetch_todo_tasks', array($this, 'handle_ajax_requests'));
        add_action('wp_ajax_nopriv_todolist_fetch_todo_tasks', array($this, 'handle_ajax_requests'));
        add_action('wp_ajax_todolist_update_todo_task', array($this, 'handle_ajax_requests'));
        add_action('wp_ajax_nopriv_todolist_update_todo_task', array($this, 'handle_ajax_requests'));
        add_action('wp_ajax_todolist_delete_todo_task', array($this, 'handle_ajax_requests'));
        add_action('wp_ajax_nopriv_todolist_delete_todo_task', array($this, 'handle_ajax_requests'));
      
    }

    public static function todolist_login_form_shortcode() {
        if (is_user_logged_in() && !current_user_can('manage_options')) {
            wp_redirect(home_url('index.php/to-do-list/')); 
            exit;
        }
        ob_start();
        ?>
        <div class="container">
            <div class="form-container">
                <h2>Login Now</h2>
                <div id="loginMessage" class="message"></div>
                <form id="loginForm">
                    <div class="input-group">
                        <label for="loginEmail">Email:</label>
                        <input type="email" id="loginEmail" name="email" required>
                    </div>
                    <div class="input-group">
                        <label for="loginPassword">Password:</label>
                        <input type="password" id="loginPassword" name="password" required>
                    </div>
                    <button type="submit" class="button" title="Click to submit">Login</button>
                </form>
               
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function todolist_register_form_shortcode() {
        if (is_user_logged_in() && !current_user_can('administrator')) {
            wp_redirect(home_url('index.php/to-do-list/'));
            exit;
        }
        
        ob_start();
        ?>
        <div class="container">
            <div class="form-container">
                <h2>Register Now</h2>
                <div id="registerMessage" class="message"></div>
                <form id="registerForm">
                    <div class="input-group">
                        <label for="registerEmail">Email:</label>
                        <input type="email" id="registerEmail" name="email" required>
                    </div>
                    <div class="input-group">
                        <label for="registerPassword">Password:</label>
                        <input type="password" id="registerPassword" name="password" required>
                    </div>
                    <div class="input-group">
                        <label for="registerConfirmPassword">Confirm Password:</label>
                        <input type="password" id="registerConfirmPassword" name="confirm_password" required>
                    </div>
                    <button type="submit" class="button" title="Click to submit">Register</button>
                </form>
                <div id="registerMessage"></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function todolist_shortcode() {
        if (!is_user_logged_in()) {
            wp_redirect(home_url('/todo-list')); 
            exit;
        }

        ob_start();
        ?>
        <div class="container">
            <div class="form-container">
                <h2>Your To-Do List</h2>
                <div id="message" style="display:none;"></div>
                <form id="todoForm">
                    <div class="input-group">
                        <label for="todoItem">New Task:</label>
                        <input type="text" id="todoItem" name="todoItem" required>
                    </div>
                    <button type="submit" class="button" title="Click to add task">Add Task</button>
                </form>
                <ul id="todoItemsList" class="todo-list"></ul>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function handle_ajax_requests() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'todolist_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed.'));
            wp_die(); 
        }
    
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'todolist_login_user':
                    $this->login_user();
                    break;
                case 'todolist_register_user':
                    $this->register_user();
                    break;
                case 'todolist_add_todo_task':
                    $this->add_todo_task();
                    break;
                case 'todolist_fetch_todo_tasks':
                    $this->fetch_todo_tasks();
                    break;
                case 'todolist_update_todo_task':
                    $this->update_todo_task();
                    break;
                case 'todolist_delete_todo_task':
                    $this->delete_todo_task();
                    break;
                default:
                    wp_send_json_error(array('message' => 'Invalid action.'));
            }
        } else {
            wp_send_json_error(array('message' => 'Action not specified.'));
        }
        wp_die(); 
    }

    private function login_user() {
        $email = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);
        
        $user = get_user_by('email', $email);
        if ($user && wp_check_password($password, $user->user_pass, $user->ID)) {
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID);
            $redirect_url = home_url('/todo-list'); 
            wp_send_json_success(array('message' => 'Login successful. Redirecting...', 'redirect_url' => $redirect_url));
        } else {
            wp_send_json_error(array('message' => 'Invalid email or password. Please try again.'));
        }
    }

    private function register_user() {
        $email = sanitize_email($_POST['email']);
        $password = sanitize_text_field($_POST['password']);
        $confirm_password = sanitize_text_field($_POST['confirm_password']);
    
        if ($password !== $confirm_password) {
            wp_send_json_error(array('message' => 'Passwords do not match. Please check and try again.'));
            return;
        }
    
        if (!email_exists($email)) {
            $user_id = wp_create_user($email, $password, $email);
            if (!is_wp_error($user_id)) {
                wp_send_json_success(array('message' => 'Registration successful. You can now log in.'));
            } else {
                wp_send_json_error(array('message' => 'Registration failed. Please try again.'));
            }
        } else {
            wp_send_json_error(array('message' => 'Email already exists. Please use a different email.'));
        }
    }

    private function add_todo_task() {
        $task = sanitize_text_field($_POST['todoItem']);
    
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'You need to be logged in to add tasks.'));
            return;
        }
    
        $user_id = get_current_user_id();
        $tasks = get_user_meta($user_id, 'todolist_tasks', true);
    
        if (!is_array($tasks)) {
            $tasks = array();
        }
    
        // Generate a unique task ID
        $task_id = count($tasks) ? max(array_column($tasks, 'id')) + 1 : 1;
    
        $tasks[] = array(
            'id' => $task_id,
            'task' => $task,
            'status' => 'pending'
        );
    
        $result = update_user_meta($user_id, 'todolist_tasks', $tasks);
    
        if ($result === false) {
            wp_send_json_error(array('message' => 'Failed to add task. Please try again.'));
        } else {
            wp_send_json_success(array('message' => 'Task added successfully.'));
        }
    }
    
    

    private function fetch_todo_tasks() {
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'You need to be logged in to view tasks.'));
            return;
        }
    
        $user_id = get_current_user_id();
        $tasks = get_user_meta($user_id, 'todolist_tasks', true);
    
        if ($tasks === false || !is_array($tasks)) {
            wp_send_json_error(array('message' => 'Failed to fetch tasks. Please try again.'));
        } else {
            wp_send_json_success($tasks);
        }
    }
    
    private function update_todo_task() {
        $task_id = intval($_POST['task_id']);
        $task = sanitize_text_field($_POST['task']);
        $status = sanitize_text_field($_POST['status']);
    
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'You need to be logged in to update tasks.'));
            return;
        }
    
        $user_id = get_current_user_id();
        $tasks = get_user_meta($user_id, 'todolist_tasks', true);
    
        if (!is_array($tasks)) {
            wp_send_json_error(array('message' => 'No tasks found.'));
            return;
        }
    
        $task_found = false;
        foreach ($tasks as &$existing_task) {
            if ($existing_task['id'] === $task_id) {
                $existing_task['task'] = $task;
                $existing_task['status'] = $status;
                $task_found = true;
                break;
            }
        }
    
        if (!$task_found) {
            wp_send_json_error(array('message' => 'Task not found.'));
            return;
        }
    
        $result = update_user_meta($user_id, 'todolist_tasks', $tasks);
    
        if ($result === false) {
            wp_send_json_error(array('message' => 'Failed to update task. Please try again.'));
        } else {
            wp_send_json_success(array('message' => 'Task updated successfully.'));
        }
    }
    

    private function delete_todo_task() {
        $task_id = intval($_POST['task_id']);
    
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => 'You need to be logged in to delete tasks.'));
            return;
        }
    
        $user_id = get_current_user_id();
        $tasks = get_user_meta($user_id, 'todolist_tasks', true);
    
        if (!is_array($tasks)) {
            wp_send_json_error(array('message' => 'No tasks found.'));
            return;
        }
    
        $task_found = false;
        foreach ($tasks as $index => $existing_task) {
            if ($existing_task['id'] === $task_id) {
                unset($tasks[$index]);
                $tasks = array_values($tasks); // Reindex array
                $task_found = true;
                break;
            }
        }
    
        if (!$task_found) {
            wp_send_json_error(array('message' => 'Task not found.'));
            return;
        }
    
        $result = update_user_meta($user_id, 'todolist_tasks', $tasks);
    
        if ($result === false) {
            wp_send_json_error(array('message' => 'Failed to delete task. Please try again.'));
        } else {
            wp_send_json_success(array('message' => 'Task deleted successfully.'));
        }
    }
    
}