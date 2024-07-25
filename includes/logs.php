<?php
// Log manual changes
function log_manual_change($type, $value) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pool_status_logs';
    $wpdb->insert($table_name, array(
        'type' => $type,
        'value' => $value,
        'timestamp' => current_time('mysql')
    ));
}

// Create logs table on plugin activation
function create_pool_status_logs_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pool_status_logs';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        type varchar(255) NOT NULL,
        value varchar(255) NOT NULL,
        timestamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'create_pool_status_logs_table');

// Add admin menu item for logs
function pool_status_logs_menu() {
    add_menu_page(
        __('Pool Status Logs', 'pool-status-widget'),
        __('Pool Status Logs', 'pool-status-widget'),
        'manage_options',
        'pool-status-logs',
        'display_pool_status_logs',
        'dashicons-clipboard',
        26
    );
}
// add_action('admin_menu', 'pool_status_logs_menu');

// Display logs in admin page
function display_pool_status_logs() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pool_status_logs';
    $logs = $wpdb->get_results("SELECT * FROM $table_name ORDER BY timestamp DESC");

    echo '<div class="wrap"><h1>' . __('Pool Status Logs', 'pool-status-widget') . '</h1>';
    echo '<table class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th id="type" class="manage-column column-type" scope="col">' . __('Change Type', 'pool-status-widget') . '</th>
                <th id="value" class="manage-column column-value" scope="col">' . __('Value', 'pool-status-widget') . '</th>
                <th id="timestamp" class="manage-column column-timestamp" scope="col">' . __('Timestamp', 'pool-status-widget') . '</th>
            </tr>
        </thead>
        <tbody>';

    if ($logs) {
        foreach ($logs as $log) {
            echo '<tr>';
            echo '<td>' . esc_html($log->type) . '</td>';
            echo '<td>' . esc_html($log->value) . '</td>';
            echo '<td>' . esc_html($log->timestamp) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="3">' . __('No logs found', 'pool-status-widget') . '</td></tr>';
    }

    echo '</tbody></table></div>';
}
