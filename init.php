<?php

/**
 * Plugin Name: MailPoet Gutenberg Forms
 * Plugin URI: https://www.gutenbergforms.com
 * Description: This add-on connects Gutenberg Forms with MailPoet. This allows you to send leads/subscribers to your MailPoet list with the form submissions.
 * Author: munirkamal
 * Author URI: https://cakewp.com/
 * Version: 1.0.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: cwp-gutenberg-forms-mailpoet
 */

require_once plugin_dir_path( __FILE__ ) . 'addon.php';
require_once plugin_dir_path( __FILE__ ) . 'api/api.php';
require_once plugin_dir_path( __FILE__ ) . 'message.php';



// redirecting to the form settings dashboard when the addon activates

register_activation_hook(__FILE__, function() {
    add_option('gutenberg-forms-mailpoet-addon-activated', true);
});

add_action('admin_init', function() {
    if (get_option('gutenberg-forms-mailpoet-addon-activated', false)) {
        delete_option('gutenberg-forms-mailpoet-addon-activated');
         exit( wp_redirect("admin.php?page=gutenberg_forms#/integrations") );
    }
});



