<?php
if (!defined('ABSPATH')) {
    exit; 
}

class Todolist_Plugin_Activator {

    /**
     * Function to create the tasks table in the database.
     */
    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'usermeta'; 
        $charset_collate = $wpdb->get_charset_collate();

        // SQL to create the tasks table
        $sql = "CREATE TABLE {$table_name} (
            id INT(11) NOT NULL AUTO_INCREMENT,
            user_id INT(11) NOT NULL,
            task VARCHAR(255) NOT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Load required file to use dbDelta function for creating/updating the table
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Save the plugin version in options for future reference
        add_option('todolist_plugin_db_version', '1.0');
    }

    /**
     * Function to create essential plugin pages for login, register, and to-do list.
     */
    public static function create_pages() {
        // Pages array with page titles and corresponding shortcodes
        $pages = array(
            'Login' => '[todolist_login_form]',
            'Register' => '[todolist_register_form]',
            'To-Do List' => '[todolist_list]'
        );

        // Loop through each page and create it if it doesn't exist
        foreach ($pages as $title => $shortcode) {
            $page = get_page_by_title($title);
            if (!$page) {
                wp_insert_post(array(
                    'post_title'    => $title,
                    'post_content'  => $shortcode,
                    'post_status'   => 'publish',
                    'post_author'   => 1,
                    'post_type'     => 'page'
                ));
            }
        }
    }
}

class Todolist_Plugin_Deactivator {

    /**
     * Function to run on plugin deactivation for cleanup.
     */
    public static function deactivate() {
        // Delete the option that stores the plugin's database version
        delete_option('todolist_plugin_db_version');
        
        // Flush rewrite rules to prevent any issues with URLs
        flush_rewrite_rules();
    }
}

// Hook the activation and deactivation functions
register_activation_hook(__FILE__, array('Todolist_Plugin_Activator', 'create_table'));
register_activation_hook(__FILE__, array('Todolist_Plugin_Activator', 'create_pages'));
register_deactivation_hook(__FILE__, array('Todolist_Plugin_Deactivator', 'deactivate'));
