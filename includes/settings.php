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

function pool_status_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Pool Status Settings', 'pool-status-widget'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('pool_status_settings_group');
            do_settings_sections('pool-status-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}
