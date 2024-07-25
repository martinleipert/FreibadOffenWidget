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
