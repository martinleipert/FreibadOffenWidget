<?php
/*
Plugin Name: Pool Status Widget
Description: Displays whether the pool is open based on weather conditions and allows manual setting on a protected page.
Version: 1.3
Author: Martin Leipert
Text Domain: pool-status-widget
Domain Path: /languages
*/
<?php

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/widget.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'includes/logs.php';
require_once plugin_dir_path(__FILE__) . 'includes/settings.php';

// Load plugin text domain
function pool_status_load_textdomain() {
    load_plugin_textdomain('pool-status-widget', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'pool_status_load_textdomain');
