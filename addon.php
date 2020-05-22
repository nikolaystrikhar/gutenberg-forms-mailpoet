<?php 

require_once plugin_dir_path( __FILE__ ) . 'api/api.php';


add_filter('gutenberg_forms_integrations', function( $integrations ) {

    $api_exists = class_exists(\MailPoet\API\API::class);

    $guide = plugin_dir_path( __FILE__ ) . 'guide/guide.html';

    if (!$api_exists) return $integrations;

    $api = new MailPoet(\MailPoet\API\API::MP('v1'));
        
    $lists = $api->get_lists();
    $fields = $api->get_fields();

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

    $integrations['mailpoet'] = $configurations;

    return $integrations;

});

add_action('gutenberg_forms_submission__mailpoet', function($entry) {

    if (class_exists(\MailPoet\API\API::class)) {

        $api = new MailPoet( \MailPoet\API\API::MP('v1')  );

        $api->add_subscriber( $entry );

    }


});
