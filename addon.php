<?php 

require_once plugin_dir_path( __FILE__ ) . 'api/api.php';


add_filter('gutenberg_forms_integrations', function( $integrations ) {

    $api_exists = class_exists(\MailPoet\API\API::class);

    $guide = plugin_dir_path( __FILE__ ) . 'guide/guide.html';

    $api = null;
    $lists = [];
    $fields = [];

    if ($api_exists) {
        $api = new MailPoet(\MailPoet\API\API::MP('v1'));
        $lists = $api->get_lists();
        $fields = $api->get_fields();
    }

    $configurations = array(
        'title' => 'MailPoet v3',
        'is_pro'  => false,
        'type'  => 'autoResponder',
        'guide' => file_get_contents( $guide ),
        'description' => 'MailPoet Addon allows you to send leads/subscribers to your MailPoet subscribers list with the form submission.',
        'banner'    => 'https://p111.p2.n0.cdn.getcloudapp.com/items/kpuL2R0w/mailpoet-banner.png',
        'fields' => array(),
        'query_fields' => array(
            'list' => array(
                'label' => 'Select List',
                'value' => $lists,
                'type'  => 'select',
                'required'  => true
            )
        ),
        'api_fields' => $fields
    ); 


    if (!$api_exists) {
        # if the user does not have mailpoet plugin 
        # disabling the integration by adding some options
        # & showing a notice prompting to install MailPoet v3 addon

        $plugin_repo_url = "https://wordpress.org/plugins/mailpoet/";

        $configurations['is_disabled'] = true; // disabling the integration
        $configurations['error'] = array(
            'status'    => 'error',
            'message'   => sprintf('Unable to access MailPoet API please make sure that <a href="%1$s" target="__blank">MailPoet Plugin</a> is installed & active before activating MailPoet Addon', $plugin_repo_url)
        );

        
    }


    $integrations['mailpoet'] = $configurations;

    return $integrations;

});

add_action('gutenberg_forms_submission__mailpoet', function($entry) {

    if (class_exists(\MailPoet\API\API::class)) {

        $api = new MailPoet( \MailPoet\API\API::MP('v1')  );

        $api->add_subscriber( $entry );

    }

});
