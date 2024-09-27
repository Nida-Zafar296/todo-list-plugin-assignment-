<?php
/**
 * Plugin Name: Todo List Plugin
 * Description: A plugin that provides login, registration, and To-Do list functionalities using shortcodes.
 * Version: 1.0
 * Author: Nida
 */

if (!defined('ABSPATH')) {
    exit; 
}


// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/class-todolist-plugin-activator.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-todo-list-plugin.php';
require_once(plugin_dir_path(__FILE__) . 'includes/class-todolist-api.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-todolist-cron.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-todolist-initialize.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-todolist-assets.php');
require_once(plugin_dir_path(__FILE__) . 'includes/register_scheduler.php');
require_once(plugin_dir_path(__FILE__) . 'includes/register-api.php');
require_once(plugin_dir_path(__FILE__) . 'includes/register-wp-cli.php');

// Activation hook
register_activation_hook(__FILE__, function() {
    Todolist_Plugin_Activator::create_table();
    Todolist_Plugin_Activator::create_pages();
});

