<?php
// File: includes/class-todolist-initialize.php

class Todolist_Plugin_Initializer {
    public function run_todolist_plugin() {
        $plugin = new Todolist_Plugin_Public();

        // Register AJAX actions
        add_action('wp_ajax_todolist_login_user', array($plugin, 'handle_ajax_requests'));
        add_action('wp_ajax_nopriv_todolist_login_user', array($plugin, 'handle_ajax_requests'));
        add_action('wp_ajax_todolist_register_user', array($plugin, 'handle_ajax_requests'));
        add_action('wp_ajax_nopriv_todolist_register_user', array($plugin, 'handle_ajax_requests'));
        add_action('wp_ajax_todolist_add_todo_task', array($plugin, 'handle_ajax_requests'));
        add_action('wp_ajax_nopriv_todolist_add_todo_task', array($plugin, 'handle_ajax_requests'));
        add_action('wp_ajax_todolist_fetch_todo_tasks', array($plugin, 'handle_ajax_requests'));
        add_action('wp_ajax_nopriv_todolist_fetch_todo_tasks', array($plugin, 'handle_ajax_requests'));
        add_action('wp_ajax_todolist_update_todo_task', array($plugin, 'handle_ajax_requests'));
        add_action('wp_ajax_nopriv_todolist_update_todo_task', array($plugin, 'handle_ajax_requests'));
        add_action('wp_ajax_todolist_delete_todo_task', array($plugin, 'handle_ajax_requests'));
        add_action('wp_ajax_nopriv_todolist_delete_todo_task', array($plugin, 'handle_ajax_requests'));
    }
}

add_action('plugins_loaded', array(new Todolist_Plugin_Initializer(), 'run_todolist_plugin'));
