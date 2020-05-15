<?php 

require_once plugin_dir_path( __FILE__ ) . 'api/api.php';


add_filter('gutenberg_forms_integrations', function( $integrations ) {

    $api_exists = class_exists(\MailPoet\API\API::class);

    if (!$api_exists) return $integrations;

    $api = new MailPoet(\MailPoet\API\API::MP('v1'));
        
    $lists = $api->get_lists();

    $configurations = array(
        'title' => 'MailPoet',
        'is_pro'  => false,
        'type'  => 'autoResponder',
        'guide' => '',
        'description' => 'Mailpoet Addon for Gutenberg Forms lets you connect Mailpoet with your form. You can send leads to any of your lists in Mailchimp when a user submits the form.',
        'banner'    => 'https://ps.w.org/wysija-newsletters/assets/banner-772x250.jpg?rev=1703780',
        'fields' => array(),
        'query_fields' => array(
            'list' => array(
                'label' => 'Select List',
                'value' => $lists,
                'type'  => 'select'
            )
        ),
        'api_fields' => array(
            'EMAIL' => array(
                'label' => 'Email'
            ),
            'FNAME' => array(
                'label' => 'First Name'
            ),
            'LNAME' => array(
                'label' => 'Last Name'
            ),
        )
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
