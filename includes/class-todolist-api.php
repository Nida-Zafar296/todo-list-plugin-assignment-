<?php
use \Firebase\JWT\JWT;

class Rest_API {

    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
        
    }
    
    public function register_routes() {
        // Register route to fetch user's to-do list (GET method)
        register_rest_route('todo-list/v1', '/user/(?P<user_id>\d+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_user_todo_list'),
        ));

        register_rest_route('todo-list/v1', '/add-task', array(
            'methods' => 'POST',
            'callback' => array($this, 'add_todo_task'),
            'permission_callback' => function () {
                return is_user_logged_in(); // Ensure user is logged in
            }
        ));
        
        
        
        // Register route to update a task (POST method) with JWT authentication
        register_rest_route('todo-list/v1', '/tasks/update', array(
            'methods' => 'POST',
            'callback' => array($this, 'update_task_callback'),  // Correct way to reference the method within the class
            'permission_callback' => function () {
                return is_user_logged_in();  // Set this accordingly if you're using authentication
            }
        ));
        
        
        
    }

    // Function to get the user's to-do list (GET method)
    public function get_user_todo_list(WP_REST_Request $request) {
        $user_id = $request->get_param('user_id'); 
        $user_info = get_userdata($user_id);

        if ($user_info) {
            $user_name = $user_info->display_name;
            $user_tasks = get_user_meta($user_id, 'todolist_tasks', true);
            $tasks = is_array($user_tasks) ? $user_tasks : [];
            $response = [
                'user_name' => $user_name,
                'tasks' => $tasks
            ];

            return new WP_REST_Response($response, 200); 
        } else {
            return new WP_REST_Response(['error' => 'User not found'], 404);
        }
    }

   
    // Function to add a to-do task (POST method)
public function add_todo_task(WP_REST_Request $request) {
    $user_id = intval($request->get_param('user_id'));
    $task = sanitize_text_field($request->get_param('task'));
    $status = sanitize_text_field($request->get_param('status'));
    
    // Check if task is empty
    if (empty($task)) {
        return new WP_REST_Response(['message' => 'Task parameter should not be empty.'], 400); // Use 400 for bad request
    }
    
    // Retrieve existing tasks for the user
    $existing_tasks = get_user_meta($user_id, 'todolist_tasks', true);
    
    // Initialize an empty array if there are no tasks
    if (!$existing_tasks || !is_array($existing_tasks)) {
        $existing_tasks = [];
    }
    
    // Generate a unique task ID
    $task_id = count($existing_tasks) ? max(array_column($existing_tasks, 'id')) + 1 : 1;
    
    // Append the new task to the task list
    $existing_tasks[] = array(
        'id'     => $task_id,
        'task'   => $task,
        'status' => $status
    );
    
    // Update the user meta with the new task list
    if (update_user_meta($user_id, 'todolist_tasks', $existing_tasks)) {
        return new WP_REST_Response(['message' => 'Task added successfully.', 'tasks' => $existing_tasks], 200);
    } else {
        return new WP_REST_Response(['message' => 'Failed to add task. Please try again.'], 500);
    }
}

   // Function to update a to-do task (POST method)
public function update_task_callback(WP_REST_Request $request) {
    // Retrieve data from the request
    $user_id = intval($request->get_param('user_id'));
    $task_id = intval($request->get_param('task_id'));
    $task = sanitize_text_field($request->get_param('task'));
    $status = sanitize_text_field($request->get_param('status'));

    // Check if required parameters are provided
    if (!$user_id || !$task_id || empty($task) || empty($status)) {
        return new WP_Error('missing_data', 'Required fields are missing', array('status' => 400));
    }

    // Retrieve existing tasks from the user's meta
    $existing_tasks = get_user_meta($user_id, 'todolist_tasks', true);

    if (!is_array($existing_tasks)) {
        return new WP_Error('no_tasks', 'No tasks found for this user', array('status' => 404));
    }

    // Find the task by ID and update it
    foreach ($existing_tasks as &$existing_task) {
        if ($existing_task['id'] == $task_id) {
            $existing_task['task'] = $task;
            $existing_task['status'] = $status;
            break;
        }
    }

    // Update the tasks in the usermeta table
    if (update_user_meta($user_id, 'todolist_tasks', $existing_tasks)) {
        return new WP_REST_Response(['message' => 'Task updated successfully.', 'tasks' => $existing_tasks], 200);
    } else {
        return new WP_Error('update_failed', 'Task update failed', array('status' => 500));
    }
}



    // JWT authentication check
    private function jwt_auth_check() {
        $auth_header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$auth_header) {
            return false;
        }
    
        list($token) = sscanf($auth_header, 'Bearer %s');
        if (!$token) {
            return false;
        }
    
        try {
            $decoded = JWT::decode($token, JWT_AUTH_SECRET_KEY, array('HS256'));
            return $decoded ? true : false;
        } catch (Exception $e) {
            return false;
        }
    }
    
}