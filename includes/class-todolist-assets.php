<?php
class Todolist_Plugin_Assets {
    public function enqueue_assets() {
        wp_enqueue_style('custom-todolist-styles', plugin_dir_url(dirname(__FILE__)) . 'assets/css/style.css');

        wp_enqueue_script('custom-todolist-js', plugin_dir_url(dirname(__FILE__)) . 'assets/js/todolist.js', array('jquery'), null, true);
        wp_localize_script('custom-todolist-js', 'todolistAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('todolist_nonce')
        ));

        wp_enqueue_script('custom-todolist-login-js', plugin_dir_url(dirname(__FILE__)) . 'assets/js/login.js', array('jquery'), null, true);
        wp_localize_script('custom-todolist-login-js', 'todolistAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('todolist_nonce')
        ));

        wp_enqueue_script('custom-todolist-register-js', plugin_dir_url(dirname(__FILE__)) . 'assets/js/register.js', array('jquery'), null, true);
        wp_localize_script('custom-todolist-register-js', 'todolistAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('todolist_nonce')
        ));
    }
}

add_action('wp_enqueue_scripts', array(new Todolist_Plugin_Assets(), 'enqueue_assets'));
