<?php
// File: includes/external/class-todolist-wp-cli.php

if (defined('WP_CLI') && WP_CLI) {
    require_once(plugin_dir_path(__FILE__) . '../class-wp-todolist-cli.php');
    WP_CLI::add_command('todolist', 'WP_Todolist_CLI_Command');
}
