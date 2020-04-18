<?php
/**
 * Plugin Name
 *
 * @package           PluginPackage
 * @author            mmattel
 * @copyright         2020 mmattel
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Caldera Forms eMail Domain Check
 * Plugin URI:        https://wordpress.org/plugins/caldera-forms-email-domain-check/
 * Description:       Checks if the eMail domain has either an MX or A or AAAA record. This helps protecting against unreachable or invalid eMail domains.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            mmattel
 * Author URI:        https://github.com/mmattel/caldera-forms-email-check
 * Text Domain:       caldera-forms-email-domain-check
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
load_plugin_textdomain('caldera-forms-email-domain-check', false, basename( dirname( __FILE__ ) ) . '/languages' );

define('FIELD_NAME', 'eMail');

add_filter('caldera_forms_get_form_processors', 'email_check_cf_validator_processor');

/**
 * Add a custom processor for eMail field validation
 *
 * @uses 'email_check_cf_validator_processor'
 *
 * @param array $processors Processor configs
 *
 * @return array
 */

function email_check_cf_validator_processor($processors){
    $processors['email_check_cf_validator'] = array(
        'name' => __('eMail Domain Check', 'caldera-forms-email-domain-check' ),
        'description' => 'Check if the eMail domain has an MX or A or AAAA record',
        'pre_processor' => 'email_check_validator',
        'template' => dirname(__FILE__) . '/config.php'

    );

    return $processors;
}


/**
 * Run field validation
 *
 * @param array $config Processor config
 * @param array $form Form config
 *
 * @return array|void Error array if needed, else void.
 */
function email_check_validator( array $config, array $form ){

    //Processor data object
    $data = new Caldera_Forms_Processor_Get_Data( $config, $form, email_check_cf_validator_fields() );

    //Value of field to be validated
    $value = $data->get_value( FIELD_NAME );

    //check if false (OK) or text (error message)
    $has_mx = email_check_has_mx( $value );

    // if there was an error, $has_mx contains the error message
    if ( $has_mx ){

        //get ID of field to put error on
        $fields = $data->get_fields();
        $field_id = $fields[ FIELD_NAME ][ 'config_field' ];

        //Get label of field to use in error message above form
        $field = $form[ 'fields' ][ $field_id ];
        $label = $field[ 'label' ];

        //this is error data to send back
        return array(
            'type' => 'error',
            //this message will be shown above form
            'note' => sprintf(__( 'Please Correct %s', 'caldera-forms-email-domain-check' ), $label ),
            //Add error messages for any form field
            'fields' => array(
                //This error message will be shown below the field that we are validating
                $field_id => $has_mx
            )
        );
    }
}


/**
 * Check if value has an dns MX or A or AAAA record
 *
 * @return bool
 */
function email_check_has_mx( $value ){
     $email_domain = substr($value, strpos($value, '@') + 1);
     if (!checkdnsrr($email_domain, "MX")){
        if (!(checkdnsrr($email_domain, "A")) or !(checkdnsrr($email_domain, "AAAA"))){
           return sprintf(__( 'No eMail can be sent to the specified domain ( %s )', 'caldera-forms-email-domain-check' ), $email_domain);
        }
     }
     return false;
}


/**
 * Processor fields
 *
 * @return array
 */
function email_check_cf_validator_fields(){
    return array(
        array(
            'id' => FIELD_NAME,
            'type' => 'text',
            'required' => true,
            'magic' => true,
            'label' => __( 'Magic tag for field to validate. Use only one tag per processor!', 'caldera-forms-email-domain-check' )
        ),
    );
}
