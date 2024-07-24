<?php
/*
Plugin Name: Pool Status Widget
Description: Displays whether the pool is open based on weather conditions and allows manual setting on a protected page.
Version: 1.2
Author: Martin Leipert
E-Mail: martin.leipert@fau.de
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
        $city_id = 'YOUR_CITY_ID'; # Graefenberg, DE: 2918350 
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

// Add admin menu item
function pool_status_logs_menu() {
    add_menu_page(
        'Pool Status Logs',
        'Pool Status Logs',
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

    echo '<div class="wrap"><h1>Pool Status Logs</h1>';
    echo '<table class="widefat fixed" cellspacing="0">
        <thead>
            <tr>
                <th id="type" class="manage-column column-type" scope="col">Change Type</th>
                <th id="value" class="manage-column column-value" scope="col">Value</th>
                <th id="timestamp" class="manage-column column-timestamp" scope="col">Timestamp</th>
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
        echo '<tr><td colspan="3">No logs found</td></tr>';
    }

    echo '</tbody></table></div>';
}

// Add settings page for manually setting the status
function pool_status_settings_page() {
    add_menu_page(
        'Pool Status Settings',
        'Pool Status Settings',
        'manage_options',
        'pool-status-settings',
        'display_pool_status_settings',
        'dashicons-admin-generic',
        27
    );
}
add_action('admin_menu', 'pool_status_settings_page');

function display_pool_status_settings() {
    // Check if the form is submitted
    if (isset($_POST['pool_status_nonce']) && wp_verify_nonce($_POST['pool_status_nonce'], 'pool_status_settings')) {
        $manual_status = sanitize_text_field($_POST['manual_status']);
        $manual_temp = sanitize_text_field($_POST['manual_temp']);

        update_option('pool_manual_status', $manual_status);
        update_option('pool_manual_temp', $manual_temp);

        log_manual_change('status', $manual_status);
        log_manual_change('temperature', $manual_temp);

        echo '<div class="updated"><p>Settings saved.</p></div>';
    }

    // Get current settings
    $manual_status = get_option('pool_manual_status', '');
    $manual_temp = get_option('pool_manual_temp', '');

    ?>
    <div class="wrap">
        <h1>Pool Status Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('pool_status_settings', 'pool_status_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="manual_status">Manual Status</label></th>
                    <td>
                        <select id="manual_status" name="manual_status">
                            <option value="" <?php selected($manual_status, ''); ?>>Auto</option>
                            <option value="Open" <?php selected($manual_status, 'Open'); ?>>Open</option>
                            <option value="Closed" <?php selected($manual_status, 'Closed'); ?>>Closed</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="manual_temp">Manual Water Temperature</label></th>
                    <td><input name="manual_temp" type="number" step="0.1" id="manual_temp" value="<?php echo esc_attr($manual_temp); ?>" class="regular-text"></td>
                </tr>
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

        update_option('pool_manual_status', $manual_status);
        update_option('pool_manual_temp', $manual_temp);

        log_manual_change('status', $manual_status);
        log_manual_change('temperature', $manual_temp);

        echo '<div class="updated"><p>Settings saved.</p></div>';
    }

    // Get current settings
    $manual_status = get_option('pool_manual_status', '');
    $manual_temp = get_option('pool_manual_temp', '');

    ob_start();
    ?>
    <div class="wrap">
        <h1>Pool Status Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('pool_status_settings', 'pool_status_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="manual_status">Manual Status</label></th>
                    <td>
                        <select id="manual_status" name="manual_status">
                            <option value="" <?php selected($manual_status, ''); ?>>Auto</option>
                            <option value="Open" <?php selected($manual_status, 'Open'); ?>>Open</option>
                            <option value="Closed" <?php selected($manual_status, 'Closed'); ?>>Closed</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="manual_temp">Manual Water Temperature</label></th>
                    <td><input name="manual_temp" type="number" step="0.1" id="manual_temp" value="<?php echo esc_attr($manual_temp); ?>" class="regular-text"></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('pool_status_settings', 'pool_status_settings_shortcode');
