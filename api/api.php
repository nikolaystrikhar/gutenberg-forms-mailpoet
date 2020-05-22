<?php 

$guide = plugin_dir_path( __FILE__ ) . 'guide/guide.html';

class MailPoet {

    const slug = 'mailpoet';
    

    // initializing...
    function __construct( $api ) {

        $this->api = $api;

    }


    public function get_lists() : array {

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

        } catch ( \Exception $e ) {
            
            return []; # on any exception currently returning empty list

        }


        
    }

    public function validate_entry( $entry, $subscriber_fields  ) {

        # testing the entry before adding a subscriber...

        foreach ( $subscriber_fields as $key => $subscriber_field ) {

            # validations

            if ($this->array_keys_exists(['name', 'type', 'params'], $subscriber_field)) {

                $field_params = $subscriber_field['params'];

                $field_required = array_key_exists('required', $field_params) ? $field_params['required'] : '0';

                $field_requirement_parsed = empty($field_required) ? false : true;

                $ID = $subscriber_field['id'];

                if ( $ID !== 'checkbox' and $field_requirement_parsed === true and array_key_exists($ID, $entry) and empty($entry[$ID])) {

                    // this means a required field is not filled
                    return false;
                    break;

                } 


            }

        }

        return true;

    }

    public function add_subscriber( $entry ) {


        try {
            $subscriber_fields =  $this->api->getSubscriberFields();
            $is_valid = $this->validate_entry( $entry, $subscriber_fields );

            if (!$is_valid) return; # stop executing the function if the entry is not valid

            $subscriber = array();

            foreach ($subscriber_fields as $key => $subscriber_field) {
                
                if ( $this->array_keys_exists(['name', 'type', 'params', 'id', 'values'] , $subscriber_field) ) {

                    $ID = $subscriber_field['id'];
                    $type = $subscriber_field['type'];

                    if (!empty( $entry[ $ID ] ) and $type !== 'checkbox') {

                        $subscriber[ $ID ] = $entry[ $ID ];

                    } else if (!empty( $entry[ $ID ] ) and $type === 'checkbox') {

                        $value = $subscriber_field['params']['values'][0]['value'];

                        $subscriber[ $ID ] = true;

                    }


                }

            }

            $list_ids = array(
                $entry['list']
            );

            $this->api->addSubscriber(
                $subscriber,
                $list_ids
            ); # finally adding the subscriber to the list

        } catch ( \Exception $e ) {}


    }

    private function array_keys_exists(array $keys, array $arr) {
        return !array_diff_key(array_flip($keys), $arr);
    }

    private function get_corresponding_restriction( $type ) {
         //? type can contain -> text, date, textarea, radio, checkbox, select

        // restricting the custom field type to match our fields 

        switch ( $type ) {

            # user can only take certain field blocks according to custom fields type

            case 'date':
                return 'cwp/datepicker'; 
            case 'textarea':
                return 'cwp/message';
            case 'radio':
                return 'cwp/radio';
            case 'checkbox':
                return 'cwp/checkbox';
            case 'select':
                return 'cwp/select';
            default:
                return '';

        }

    }

    public function get_fields() {

        $fields = array();

        try {

            # getting the subscriber fields data
            $subscriber_fields = $this->api->getSubscriberFields(); 

            # looping all fields
            foreach ( $subscriber_fields as $key => $subscriber_field ) {

                # some validations.. 
                if ($this->array_keys_exists(['name', 'type', 'params'], $subscriber_field)) {

                    //? type can contain -> text, date, textarea, radio, checkbox, select
                    $field_name = $subscriber_field['name'];
                    $field_params = $subscriber_field['params'];
                    $field_type = $subscriber_field['type'];
                    $field_id = $subscriber_field['id'];

                    $field_required = array_key_exists('required', $field_params) ? $field_params['required'] : '0';

                    $field_requirement_parsed = $field_required === '1' ? true : false;

                    if ($field_id === 'email') {
                        
                        $fields[$field_id] = array(
                            'label' => $field_name,
                            'restriction' => 'cwp/email', // adding mail restriction
                            'required'  => $field_requirement_parsed,
                        );

                    } else {
                        $fields[$field_id] = array(
                            'label' => $field_name,
                            'restriction' => $this->get_corresponding_restriction( $field_type ), //adding mail restriction
                            'required'  => $field_requirement_parsed,
                        );
                    }

                }

            }

            return $fields;

        } catch (\Exception $e) {}

    }

}