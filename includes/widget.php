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

    // Front-end display of widget
    public function widget($args, $instance) {
        $status_data = $this->get_pool_status();
        $status = $status_data['status'];
        $temp = $status_data['temp'];
		$timestamp = $status_data['timestamp'];
        $opening_hours = $this->get_opening_hours();

        echo $args['before_widget'];
        echo $args['before_title'] . __('Pool Status', 'pool-status-widget') . $args['after_title'];
        echo '<p>' . sprintf(__('Status: %s', 'pool-status-widget'), $status) . '</p>';
        if ($temp !== false) {
            echo '<p>' . sprintf(__('Water Temperature: %.1f°C', 'pool-status-widget'), $temp) . '</p>';
        }
		echo '<p>' . sprintf(__('Last updated: %s', 'pool-status-widget'), date('d.m.Y H:i:s', $timestamp)) . '</p>';
        echo '<p>' . __('Opening Hours:', 'pool-status-widget') . '</p>';
        echo '<ul>';
        foreach ($opening_hours as $day => $hours) {
            echo '<li>' . $day . ': ' . $hours . '</li>';
        }
        echo '</ul>';
        echo $args['after_widget'];
    }

    // Get pool status based on weather conditions and manual override
    private function get_pool_status() {
        $manual_status = get_option('pool_manual_status');
        $manual_temp = get_option('pool_manual_temp');
        $api_key = get_option('pool_api_key');
        $city_id = get_option('pool_city_id');

        $temp = false;
        if ($manual_status) {
            $status = $manual_status;
        } else {
            $weather_data = $this->get_weather_data($api_key, $city_id);
            if ($weather_data) {
                $temp = $manual_temp ? $manual_temp : $weather_data->main->temp;
                $rain = isset($weather_data->rain) ? $weather_data->rain : 0;
                $status = ($temp > 19 && $rain == 0) ? __('Open', 'pool-status-widget') : __('Closed', 'pool-status-widget');
            } else {
                $status = __('Unable to determine', 'pool-status-widget');
            }
        }

        if ($manual_temp) {
            $temp = $manual_temp;
        }

        return array('status' => $status, 'temp' => $temp);
    }

    // Fetch weather data from an API
    private function get_weather_data($api_key, $city_id) {
        $api_url = "http://api.openweathermap.org/data/2.5/weather?id={$city_id}&units=metric&appid={$api_key}";

        $response = wp_remote_get($api_url);
        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        return json_decode($body);
    }

    // Get opening hours from options
    private function get_opening_hours() {
        $days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $opening_hours = array();
        foreach ($days as $day) {
            $opening_hours[$day] = get_option('pool_opening_hours_' . strtolower($day), 'Closed');
        }
        return $opening_hours;
    }

    // Back-end widget form
    public function form($instance) {
        $manual_status = !empty($instance['manual_status']) ? $instance['manual_status'] : '';
        $manual_temp = !empty($instance['manual_temp']) ? $instance['manual_temp'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('manual_status'); ?>"><?php _e('Manual Status:', 'pool-status-widget'); ?></label>
            <select id="<?php echo $this->get_field_id('manual_status'); ?>" name="<?php echo $this->get_field_name('manual_status'); ?>">
                <option value=""><?php _e('Auto', 'pool-status-widget'); ?></option>
                <option value="Open" <?php selected($manual_status, 'Open'); ?>><?php _e('Open', 'pool-status-widget'); ?></option>
                <option value="Closed" <?php selected($manual_status, 'Closed'); ?>><?php _e('Closed', 'pool-status-widget'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('manual_temp'); ?>"><?php _e('Manual Water Temperature:', 'pool-status-widget'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('manual_temp'); ?>" name="<?php echo $this->get_field_name('manual_temp'); ?>" type="number" step="0.1" value="<?php echo esc_attr($manual_temp); ?>">
        </p>
        <?php
    }

    // Update widget settings
    public function update($new_instance, $old_instance) {
        $instance = array();
        $manual_status = (!empty($new_instance['manual_status'])) ? strip_tags($new_instance['manual_status']) : '';
        $manual_temp = (!empty($new_instance['manual_temp'])) ? strip_tags($new_instance['manual_temp']) : '';

        if ($manual_status !== get_option('pool_manual_status')) {
            log_manual_change('status', $manual_status);
            update_option('pool_manual_status', $manual_status);
        }
        if ($manual_temp !== get_option('pool_manual_temp')) {
            log_manual_change('temperature', $manual_temp);
            update_option('pool_manual_temp', $manual_temp);
        }

        $instance['manual_status'] = $manual_status;
        $instance['manual_temp'] = $manual_temp;

        return $instance;
    }
}
