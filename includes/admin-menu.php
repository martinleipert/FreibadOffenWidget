<?php


// Empty description page
function display_pool_status_description() {
    ?>
    <div class="wrap">
        <h1><?php _e('Pool Status Widget Description', 'pool-status-widget'); ?></h1>
        <p><?php _e('This page will contain the description and information about the Pool Status Widget plugin.', 'pool-status-widget'); ?></p>
        <!-- Add more content here as needed -->
    </div>
    <?php
}
?>

// Add admin menu
function pool_status_admin_menu() {
    add_menu_page(
        __('Pool Status', 'pool-status-widget'),
        __('Pool Status', 'pool-status-widget'),
        'manage_options',
        'pool-status',
        'pool_status_settings_page',
        'display_pool_status_description',
        6
    );

    add_submenu_page(
        'pool-status',
        __('Settings', 'pool-status-widget'),
        __('Settings', 'pool-status-widget'),
        'manage_options',
        'pool-status-settings',
        'display_pool_status_settings'
    );

    add_submenu_page(
        'pool-status',
        __('Logs', 'pool-status-widget'),
        __('Logs', 'pool-status-widget'),
        'manage_options',
        'pool-status-logs',
        'display_pool_status_logs'
    );
}
add_action('admin_menu', 'pool_status_admin_menu');
