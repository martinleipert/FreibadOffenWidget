<?php
function pool_status_logs_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Pool Status Logs', 'pool-status-widget'); ?></h1>
        <?php pool_status_display_logs(); ?>
    </div>
    <?php
}

function pool_status_display_logs() {
    $logs = get_option('pool_status_logs', array());

    if (empty($logs)) {
        echo '<p>' . __('No logs found', 'pool-status-widget') . '</p>';
    } else {
        echo '<ul>';
        foreach ($logs as $log) {
            echo '<li>' . esc_html($log) . '</li>';
        }
        echo '</ul>';
    }
}
