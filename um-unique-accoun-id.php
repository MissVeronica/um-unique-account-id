<?php
/**
 * Plugin Name:     Ultimate Member - Unique User Account ID
 * Description:     Extension to Ultimate Member for setting a prefixed Unique User Account ID per UM Registration Form.
 * Version:         1.0.0
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.6.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; 
if ( ! class_exists( 'UM' ) ) return;

class UM_Unique_Account_ID {

    function __construct() {

        add_action( 'um_user_register',      array( $this, 'um_user_register_with_unique_account_id' ), -1, 2 );
        add_filter( 'um_settings_structure', array( $this, 'um_settings_structure_unique_account_id' ), 10, 1 );
    }

    public function um_user_register_with_unique_account_id( $user_id, $args ) {

        if ( isset( $args['form_id'] ) && is_int( $user_id )) {

            $um_unique_account_id = array_map( 'sanitize_text_field', array_map( 'trim', explode( "\n", UM()->options()->get( 'um_unique_account_id' ))));
            if ( is_array( $um_unique_account_id )) {

                $forms = array();
                foreach( $um_unique_account_id as $form_prefix ) {
                    $array = array_map( 'trim', explode( ':', $form_prefix ));

                    if ( count( $array ) == 2 && ! empty( $array[0] ) && ! empty( $array[1] )) {
                        $forms[$array[0]] = $array[1];
                    }
                }

                if ( array_key_exists( $args['form_id'], $forms )) {

                    $digits = absint( UM()->options()->get( 'um_unique_account_id_digits' ));
                    if ( empty( $digits )) {
                        $digits = 5;
                    }

                    $um_unique_account_id = $forms[$args['form_id']] . str_pad( strval( $user_id ), $digits, '0', STR_PAD_LEFT );
                    update_user_meta( $user_id, 'um_unique_account_id', $um_unique_account_id );
                }
            }
        }
    }

    public function um_settings_structure_unique_account_id( $settings_structure ) {

        $settings_structure['appearance']['sections']['registration_form']['fields'][] = array( 
                        'id'      => 'um_unique_account_id',
                        'type'    => 'textarea',
                        'label'   => __( "Unique User Account ID - Form ID:prefix", 'ultimate-member' ),
                        'tooltip' => __( "Enter the UM Registration Form ID and the Unique User Account ID Prefix colon separated one setting per line.", 'ultimate-member' ),
                        'args'    => array( 'textarea_rows' => 4 ),
                        'size'    => 'small' );

        $settings_structure['appearance']['sections']['registration_form']['fields'][] = array( 
                        'id'      => 'um_unique_account_id_digits',
                        'type'    => 'number',
                        'label'   => __( "Unique User Account ID - Number of digits", 'ultimate-member' ),
                        'tooltip' => __( "Enter the number of digits in the Unique User Account ID. Default value is 5.", 'ultimate-member' ),
                        'size'    => 'small' );

        return $settings_structure;
    }
}

new UM_Unique_Account_ID ();
