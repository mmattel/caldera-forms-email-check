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
 * Plugin Name:       eMail Domain Check for Caldera Forms
 * Plugin URI:        https://wordpress.org/plugins/cf-email-domain-check/
 * Description:       Checks if the eMail domain has either an MX or A or AAAA record. This helps protecting against unreachable or invalid eMail domains.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            mmattel
 * Author URI:        https://github.com/mmattel/eMail-Domain-Check-Processor-for-Caldera-Forms
 * Text Domain:       EDCCF_email_domain_text_domain
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
 
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
load_plugin_textdomain('EDCCF_email_domain_text_domain', false, basename( dirname( __FILE__ ) ) . '/languages' );

define('EDCCF_EMAIL_DOMAIN_FIELD_NAME', 'eMail');

add_filter('caldera_forms_get_form_processors', 'EDCCF_email_domain_processor');

/**
 * Add a custom processor for eMail field validation
 *
 * @uses 'EDCCF_email_domain_processor'
 *
 * @param array $processors Processor configs
 *
 * @return array
 */
function EDCCF_email_domain_processor($processors){
	$processors['email_check_cf_validator'] = array(
		'name' => __('eMail Domain Check', 'EDCCF_email_domain_text_domain' ),
		'description' => __('Check if the eMail domain has an MX or A or AAAA record', 'EDCCF_email_domain_text_domain' ),
		'pre_processor' => 'EDCCF_email_domain_validator',
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
function EDCCF_email_domain_validator( array $config, array $form ){

	//Processor data object
	$data = new Caldera_Forms_Processor_Get_Data( $config, $form, EDCCF_email_domain_fields_for_validator() );

	//Value of field to be validated
	$value = $data->get_value( EDCCF_EMAIL_DOMAIN_FIELD_NAME );

	//check if false (OK) or text (error message)
	$has_no_mx = EDCCF_email_domain_has_no_mx( $value );

	// if there was an error, $has_no_mx contains the error message
	if ( $has_no_mx ){

		//get ID of field to put error on
		$fields = $data->get_fields();
		$field_id = $fields[ EDCCF_EMAIL_DOMAIN_FIELD_NAME ][ 'config_field' ];

		//Get label of field to use in error message above form
		$field = $form[ 'fields' ][ $field_id ];
		$label = $field[ 'label' ];

		//this is error data to send back
		return array(
			'type' => 'error',
			//this message will be shown above form
			'note' => sprintf(__( 'Please correct the %s field', 'EDCCF_email_domain_text_domain' ), $label ),
			//Add error messages for any form field
			'fields' => array(
				//This error message will be shown below the field that we are validating
				$field_id => $has_no_mx
			)
		);
	}
}


/**
 * Check if value has an dns MX or A or AAAA record
 * If no record can be found, the error text is returned, else false
 *
 * @return bool|string
 */
function EDCCF_email_domain_has_no_mx( $value ){
	$email_domain = substr($value, strpos($value, '@') + 1);
	if (!checkdnsrr($email_domain, "MX")){
		if (!(checkdnsrr($email_domain, "A")) or !(checkdnsrr($email_domain, "AAAA"))){
			return sprintf(__( 'No eMail can be sent to the specified domain ( %s )', 'EDCCF_email_domain_text_domain' ), $email_domain);
		}
	}
	return false;
}


/**
 * Processor fields
 *
 * @return array
 */
function EDCCF_email_domain_fields_for_validator(){
	return array(
		array(
			'id' => EDCCF_EMAIL_DOMAIN_FIELD_NAME,
			'type' => 'text',
			'required' => true,
			'magic' => true,
			'label' => __( 'Magic tag for field to validate. Use only one tag!', 'EDCCF_email_domain_text_domain' )
		),
	);
}
