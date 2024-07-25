<?php
// Register the widget
function pool_status_register_widget() {
    register_widget('Pool_Status_Widget');
}
add_action('widgets_init', 'pool_status_register_widget');

// Define the widget class
class Pool_Status_Widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'pool_status_widget',
            __('Pool Status Widget', 'pool-status-widget'),
            array('description' => __('Displays whether the pool is open based on weather conditions and settings.', 'pool-status-widget'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        echo $args['before_title'] . apply_filters('widget_title', __('Pool Status', 'pool-status-widget')) . $args['after_title'];

        // Display the status and water temperature here
        $status = get_option('pool_manual_status', 'unknown');
        $temperature = get_option('pool_manual_temperature', 'unknown');

        echo '<p>' . __('Status:', 'pool-status-widget') . ' ' . esc_html($status) . '</p>';
        echo '<p>' . sprintf(__('Water Temperature: %.1f°C', 'pool-status-widget'), esc_html($temperature)) . '</p>';

        echo $args['after_widget'];
    }
}
