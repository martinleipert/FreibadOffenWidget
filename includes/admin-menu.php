<?php
// Add admin menu
function pool_status_admin_menu() {
    add_menu_page(
        __('Pool Status', 'pool-status-widget'),
        __('Pool Status', 'pool-status-widget'),
        'manage_options',
        'pool-status',
        'pool_status_settings_page',
        'dashicons-admin-site',
        6
    );

    add_submenu_page(
        'pool-status',
        __('Settings', 'pool-status-widget'),
        __('Settings', 'pool-status-widget'),
        'manage_options',
        'pool-status-settings',
        'pool_status_settings_page'
    );

    add_submenu_page(
        'pool-status',
        __('Logs', 'pool-status-widget'),
        __('Logs', 'pool-status-widget'),
        'manage_options',
        'pool-status-logs',
        'pool_status_logs_page'
    );
}
add_action('admin_menu', 'pool_status_admin_menu');
