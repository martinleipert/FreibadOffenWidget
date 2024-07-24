<?php
/*
Plugin Name: Pool Status Widget
Description: Displays whether the pool is open based on weather conditions.
Version: 1.1
Author: Your Name
*/

// Register the widget
class Pool_Status_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'pool_status_widget',
            __('Pool Status Widget', 'text_domain'),
            array('description' => __('Displays whether the pool is open based on weather conditions.', 'text_domain'))
        );
    }

    // Front-end display of widget
    public function widget($args, $instance) {
        $status = $this->get_pool_status();
        echo $args['before_widget'];
        echo $args['before_title'] . __('Pool Status', 'text_domain') . $args['after_title'];
        echo '<p>' . $status . '</p>';
        echo $args['after_widget'];
    }

    // Get pool status based on weather conditions and manual override
    private function get_pool_status() {
        $manual_status = get_option('pool_manual_status');
        $manual_temp = get_option('pool_manual_temp');

        if ($manual_status) {
            return $manual_status;
        }

        $weather_data = $this->get_weather_data();
        if ($weather_data) {
            $temp = $manual_temp ? $manual_temp : $weather_data->main->temp;
            $rain = isset($weather_data->rain) ? $weather_data->rain : 0;
            if ($temp > 19 && $rain == 0) {
                return 'Open';
            } else {
                return 'Closed';
            }
        }

        return 'Unable to determine';
    }

    // Fetch weather data from an API
    private function get_weather_data() {
        $api_key = 'YOUR_API_KEY';
        $city_id = 'YOUR_CITY_ID';
        $api_url = "http://api.openweathermap.org/data/2.5/weather?id={$city_id}&units=metric&appid={$api_key}";

        $response = wp_remote_get($api_url);
        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        return json_decode($body);
    }

    // Back-end widget form
    public function form($instance) {
        $manual_status = !empty($instance['manual_status']) ? $instance['manual_status'] : '';
        $manual_temp = !empty($instance['manual_temp']) ? $instance['manual_temp'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('manual_status'); ?>"><?php _e('Manual Status:'); ?></label>
            <select id="<?php echo $this->get_field_id('manual_status'); ?>" name="<?php echo $this->get_field_name('manual_status'); ?>">
                <option value=""><?php _e('Auto', 'text_domain'); ?></option>
                <option value="Open" <?php selected($manual_status, 'Open'); ?>><?php _e('Open', 'text_domain'); ?></option>
                <option value="Closed" <?php selected($manual_status, 'Closed'); ?>><?php _e('Closed', 'text_domain'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('manual_temp'); ?>"><?php _e('Manual Water Temperature:'); ?></label>
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

// Register and load the widget
function load_pool_status_widget() {
    register_widget('Pool_Status_Widget');
}
add_action('widgets_init', 'load_pool_status_widget');

// Log manual changes
function log_manual_change($type, $value) {
    global $wpdb;
    $table_name = $wpdb->prefix
