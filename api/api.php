<?php 

# array(4) { ["list"]=> string(1) "4" ["EMAIL"]=> string(15) "sunny@email.com" ["FNAME"]=> string(5) "Hello" ["LNAME"]=> string(4) "Khan" }

$guide = plugin_dir_path( __FILE__ ) . 'guide/guide.html';

class MailPoet {

    const slug = 'mailpoet';
    

    // initializing...
    function __construct( $api ) {

        $this->api = $api;

    }


    public function get_lists() {

        try {

            # getting the list

            $lists = $this->api->getLists();
            $options = []; # options that will be listed in the plugin form block

            # we just need ID & label -> reOrganize List

            if ( array_key_exists(0 , $lists) and is_array($lists[0]) ) {
                # some confirmations

                foreach ($lists as $key => $list) {

                    $options[] = array(
                        'name'  => $list['name'],
                        'value' => $list['id']
                    );

                }

                
            }

            return $options;

        } catch (\Exception $e) {
            
            return []; # on any exception currently returning empty list

        }


        
    }

    public function validate_entry( $entry ) {

        # testing the entry before adding a subscriber...

        if ( 
            is_array( $entry ) and # checking if the $entry is an array
            array_key_exists( 'list', $entry ) and  # checking if a list is selected 
            !empty( $entry['list'] ) and  # checking if the selected list ID is not empty
            array_key_exists('EMAIL', $entry) and # checking if the email exists
            !empty($entry['EMAIL']) and # checking if the email is not empty
            filter_var( $entry['EMAIL'], FILTER_VALIDATE_EMAIL ) # checking if the email is valid
        ) {

            return true;

        }

        return false;

    }


    public function add_subscriber( $entry ) {

        $is_valid = $this->validate_entry( $entry );

        if (!$is_valid) return; # stop executing the function if the entry is not valid

        $subscriber = array(
            'email' => $entry['EMAIL'],
        );

        # checking for the "first name" and "last name" manually because these are optional fields

        if (array_key_exists('FNAME', $entry) and !empty($entry['FNAME'])) {

            $subscriber['first_name'] = $entry['FNAME'];

        }

        if (array_key_exists('LNAME', $entry) and !empty($entry['LNAME'])) {

            $subscriber['last_name'] = $entry['LNAME'];

        }


        $list_ids = array(
            $entry['list']
        );


        try {

            $this->api->addSubscriber(
                $subscriber,
                $list_ids

            ); # finally adding the subscriber to the list

        } catch (\Exception $e) {

            echo '<pre>';
            print_r($e->getMessage());
            echo '</pre>';

            return;

        }

    }

}