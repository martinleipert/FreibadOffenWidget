<?php
// Register settings and fields
function pool_status_register_settings() {
    register_setting('pool_status_settings_group', 'pool_api_key');
    register_setting('pool_status_settings_group', 'pool_city_id');
    register_setting('pool_status_settings_group', 'pool_opening_hours');

    add_settings_section(
        'pool_status_main_section',
        __('Main Settings', 'pool-status-widget'),
        'pool_status_main_section_callback',
        'pool-status-settings'
    );

    add_settings_field(
        'pool_api_key',
        __('API Key', 'pool-status-widget'),
        'pool_api_key_callback',
        'pool-status-settings',
        'pool_status_main_section'
    );

    add_settings_field(
        'pool_city_id',
        __('City ID', 'pool-status-widget'),
        'pool_city_id_callback',
        'pool-status-settings',
        'pool_status_main_section'
    );

    foreach (array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') as $day) {
        add_settings_field(
            'pool_opening_hours_' . $day,
            __(ucfirst($day) . ' Opening Hours', 'pool-status-widget'),
            'pool_opening_hours_callback',
            'pool-status-settings',
            'pool_status_main_section',
            array('day' => $day)
        );
    }
}
add_action('admin_init', 'pool_status_register_settings');

function pool_status_main_section_callback() {
    echo '<p>' . __('Set your main settings below:', 'pool-status-widget') . '</p>';
}

function pool_api_key_callback() {
    $api_key = get_option('pool_api_key');
    echo '<input type="text" name="pool_api_key" value="' . esc_attr($api_key) . '" class="regular-text">';
}

function pool_city_id_callback() {
    $city_id = get_option('pool_city_id');
    echo '<input type="text" name="pool_city_id" value="' . esc_attr($city_id) . '" class="regular-text">';
}

function pool_opening_hours_callback($args) {
    $day = $args['day'];
    $opening_hours = get_option('pool_opening_hours');
    $value = isset($opening_hours[$day]) ? $opening_hours[$day] : '';
    echo '<input type="text" name="pool_opening_hours[' . $day . ']" value="' . esc_attr($value) . '" class="regular-text">';
}


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
