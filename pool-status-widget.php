<?php
/*
Plugin Name: Pool Status Widget
Description: Displays whether the pool is open based on weather conditions and allows manual setting on a protected page.
Version: 1.3
Author: Martin Leipert
Text Domain: pool-status-widget
Domain Path: /languages
*/

// Register the widget
class Pool_Status_Widget extends WP_Widget {
    public function __construct() {
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
        $opening_hours = $this->get_opening_hours();

        echo $args['before_widget'];
        echo $args['before_title'] . __('Pool Status', 'pool-status-widget') . $args['after_title'];
        echo '<p>' . sprintf(__('Status: %s', 'pool-status-widget'), $status) . '</p>';
        if ($temp !== false) {
            echo '<p>' . sprintf(__('Water Temperature: %.1f°C', 'pool-status-widget'), $temp) . '</p>';
        }
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

// Register and load the widget
function load_pool_status_widget() {
    register_widget('Pool_Status_Widget');
}
add_action('widgets_init', 'load_pool_status_widget');

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
add_action('admin_menu', 'pool_status_logs_menu');

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

// Add settings page for manually setting the status, API key, and opening hours
function pool_status_settings_page() {
    add_menu_page(
        __('Pool Status Settings', 'pool-status-widget'),
        __('Pool Status Settings', 'pool-status-widget'),
        'manage_options',
        'pool-status-settings',
        'display_pool_status_settings',
        'dashicons-admin-generic',
        25
    );
}
add_action('admin_menu', 'pool_status_settings_page');

function display_pool_status_settings() {
    // Check if the form is submitted
    if (isset($_POST['pool_status_nonce']) && wp_verify_nonce($_POST['pool_status_nonce'], 'pool_status_settings')) {
        $manual_status = sanitize_text_field($_POST['manual_status']);
        $manual_temp = sanitize_text_field($_POST['manual_temp']);
        $api_key = sanitize_text_field($_POST['pool_api_key']);
        $city_id = sanitize_text_field($_POST['pool_city_id']);
        $opening_hours = array();

        foreach (array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') as $day) {
            $opening_hours[$day] = sanitize_text_field($_POST['pool_opening_hours_' . $day]);
        }

        update_option('pool_manual_status', $manual_status);
        update_option('pool_manual_temp', $manual_temp);
        update_option('pool_api_key', $api_key);
        update_option('pool_city_id', $city_id);

        foreach ($opening_hours as $day => $hours) {
            update_option('pool_opening_hours_' . $day, $hours);
        }

        log_manual_change('status', $manual_status);
        log_manual_change('temperature', $manual_temp);

        echo '<div class="updated"><p>' . __('Settings saved.', 'pool-status-widget') . '</p></div>';
    }

    // Get current settings
    $manual_status = get_option('pool_manual_status', '');
    $manual_temp = get_option('pool_manual_temp', '');
    $api_key = get_option('pool_api_key', '');
    $city_id = get_option('pool_city_id', '');
    $opening_hours = array();
    foreach (array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') as $day) {
        $opening_hours[$day] = get_option('pool_opening_hours_' . $day, '');
    }

    ?>
    <div class="wrap">
        <h1><?php _e('Pool Status Settings', 'pool-status-widget'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('pool_status_settings', 'pool_status_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="manual_status"><?php _e('Manual Status', 'pool-status-widget'); ?></label></th>
                    <td>
                        <select id="manual_status" name="manual_status">
                            <option value="" <?php selected($manual_status, ''); ?>><?php _e('Auto', 'pool-status-widget'); ?></option>
                            <option value="Open" <?php selected($manual_status, 'Open'); ?>><?php _e('Open', 'pool-status-widget'); ?></option>
                            <option value="Closed" <?php selected($manual_status, 'Closed'); ?>><?php _e('Closed', 'pool-status-widget'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="manual_temp"><?php _e('Manual Water Temperature', 'pool-status-widget'); ?></label></th>
                    <td><input name="manual_temp" type="number" step="0.1" id="manual_temp" value="<?php echo esc_attr($manual_temp); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="pool_api_key"><?php _e('API Key', 'pool-status-widget'); ?></label></th>
                    <td><input name="pool_api_key" type="text" id="pool_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="pool_city_id"><?php _e('City ID', 'pool-status-widget'); ?></label></th>
                    <td><input name="pool_city_id" type="text" id="pool_city_id" value="<?php echo esc_attr($city_id); ?>" class="regular-text"></td>
                </tr>
                <?php
                foreach (array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') as $day) {
                    ?>
                    <tr>
                        <th scope="row"><label for="pool_opening_hours_<?php echo $day; ?>"><?php _e(ucfirst($day) . ' Opening Hours', 'pool-status-widget'); ?></label></th>
                        <td><input name="pool_opening_hours_<?php echo $day; ?>" type="text" id="pool_opening_hours_<?php echo $day; ?>" value="<?php echo esc_attr($opening_hours[$day]); ?>" class="regular-text"></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Shortcode for embedding the status setting form in a password-protected page
function pool_status_settings_shortcode() {
    // Check if the form is submitted
    if (isset($_POST['pool_status_nonce']) && wp_verify_nonce($_POST['pool_status_nonce'], 'pool_status_settings')) {
        $manual_status = sanitize_text_field($_POST['manual_status']);
        $manual_temp = sanitize_text_field($_POST['manual_temp']);
        $api_key = sanitize_text_field($_POST['pool_api_key']);
        $city_id = sanitize_text_field($_POST['pool_city_id']);
        $opening_hours = array();

        foreach (array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') as $day) {
            $opening_hours[$day] = sanitize_text_field($_POST['pool_opening_hours_' . $day]);
        }

        update_option('pool_manual_status', $manual_status);
        update_option('pool_manual_temp', $manual_temp);
        update_option('pool_api_key', $api_key);
        update_option('pool_city_id', $city_id);

        foreach ($opening_hours as $day => $hours) {
            update_option('pool_opening_hours_' . $day, $hours);
        }

        log_manual_change('status', $manual_status);
        log_manual_change('temperature', $manual_temp);

        echo '<div class="updated"><p>' . __('Settings saved.', 'pool-status-widget') . '</p></div>';
    }

    // Get current settings
    $manual_status = get_option('pool_manual_status', '');
    $manual_temp = get_option('pool_manual_temp', '');
    $api_key = get_option('pool_api_key', '');
    $city_id = get_option('pool_city_id', '');
    $opening_hours = array();
    foreach (array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') as $day) {
        $opening_hours[$day] = get_option('pool_opening_hours_' . $day, '');
    }

    ob_start();
    ?>
    <div class="wrap">
        <h1><?php _e('Pool Status Settings', 'pool-status-widget'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('pool_status_settings', 'pool_status_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="manual_status"><?php _e('Manual Status', 'pool-status-widget'); ?></label></th>
                    <td>
                        <select id="manual_status" name="manual_status">
                            <option value="" <?php selected($manual_status, ''); ?>><?php _e('Auto', 'pool-status-widget'); ?></option>
                            <option value="Open" <?php selected($manual_status, 'Open'); ?>><?php _e('Open', 'pool-status-widget'); ?></option>
                            <option value="Closed" <?php selected($manual_status, 'Closed'); ?>><?php _e('Closed', 'pool-status-widget'); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="manual_temp"><?php _e('Manual Water Temperature', 'pool-status-widget'); ?></label></th>
                    <td><input name="manual_temp" type="number" step="0.1" id="manual_temp" value="<?php echo esc_attr($manual_temp); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="pool_api_key"><?php _e('API Key', 'pool-status-widget'); ?></label></th>
                    <td><input name="pool_api_key" type="text" id="pool_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="pool_city_id"><?php _e('City ID', 'pool-status-widget'); ?></label></th>
                    <td><input name="pool_city_id" type="text" id="pool_city_id" value="<?php echo esc_attr($city_id); ?>" class="regular-text"></td>
                </tr>
                <?php
                foreach (array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') as $day) {
                    ?>
                    <tr>
                        <th scope="row"><label for="pool_opening_hours_<?php echo $day; ?>"><?php _e(ucfirst($day) . ' Opening Hours', 'pool-status-widget'); ?></label></th>
                        <td><input name="pool_opening_hours_<?php echo $day; ?>" type="text" id="pool_opening_hours_<?php echo $day; ?>" value="<?php echo esc_attr($opening_hours[$day]); ?>" class="regular-text"></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('pool_status_settings', 'pool_status_settings_shortcode');

// Load plugin text domain
function pool_status_load_textdomain() {
    load_plugin_textdomain('pool-status-widget', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'pool_status_load_textdomain');
