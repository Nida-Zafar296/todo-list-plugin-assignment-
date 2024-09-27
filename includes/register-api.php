<?php
// File: includes/external/class-todolist-rest-api.php

function register_rest_api_routes() {
    $rest_api = new Rest_API();
    $rest_api->register_routes();
}

add_action('rest_api_init', 'register_rest_api_routes');
