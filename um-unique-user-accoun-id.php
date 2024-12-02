<?php
/**
 * Plugin Name:     Ultimate Member - Unique User Account ID
 * Description:     Extension to Ultimate Member for setting a prefixed Unique User Account ID per UM Registration Form.
 * Version:         2.6.0
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica
 * Plugin URI:      https://github.com/MissVeronica/um-unique-user-account-id
 * Update URI:      https://github.com/MissVeronica/um-unique-user-account-id
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.9.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;
if ( ! class_exists( 'UM' ) ) return;

class UM_Unique_Account_ID {

    public $um_unique_account_meta_key = false;

    function __construct() {

        define( 'Plugin_Basename_UUID', plugin_basename(__FILE__));

        add_action( 'um_user_register',              array( $this, 'um_user_register_with_unique_account_id' ), -1, 2 );
        add_filter( 'um_settings_structure',         array( $this, 'um_settings_structure_unique_account_id' ), 10, 1 );
        add_filter( 'um_predefined_fields_hook',     array( $this, 'custom_predefined_fields_unique_account_id' ), 10, 1 );

        add_filter( 'um_account_tab_general_fields', array( $this, 'um_account_tab_general_fields' ), 10, 2 );
        add_filter( 'um_is_field_disabled',          array( $this, 'um_account_tab_general_fields_disable' ), 10, 2 );

        if ( UM()->options()->get( 'um_unique_account_id_column' ) == 1 ) {

            add_filter( 'manage_users_sortable_columns', array( $this, 'register_sortable_columns_custom' ), 1, 1 );
            add_filter( 'manage_users_columns',          array( $this, 'manage_users_columns_custom_um' ), 10, 1 );
            add_filter( 'manage_users_custom_column',    array( $this, 'manage_users_custom_column_um' ), 10, 3 );
        }

        add_filter( 'plugin_action_links_' . Plugin_Basename_UUID, array( $this, 'plugin_settings_link' ), 10 );

        $this->um_unique_account_meta_key = sanitize_key( UM()->options()->get( 'um_unique_account_id_meta_key' ));

        if ( empty( $this->um_unique_account_meta_key )) {
            $this->um_unique_account_meta_key = 'um_unique_account_id';
        }
    }

    public function unique_account_id_exists( $value ) {

        global $wpdb;

        return $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key = '{$this->um_unique_account_meta_key}' AND meta_value = '{$value}' " );
    }

    public function um_user_register_with_unique_account_id( $user_id, $args ) {

        if ( isset( $args['form_id'] ) && ! empty( $args['form_id'] ) && is_int( $user_id ) && (int)$user_id > 0 ) {

            $um_unique_account_id = array_map( 'sanitize_text_field', array_map( 'trim', explode( "\n", UM()->options()->get( 'um_unique_account_id' ))));
            if ( is_array( $um_unique_account_id )) {

                foreach( $um_unique_account_id as $form_prefix ) {

                    $array = array_map( 'trim', array_map( 'sanitize_text_field', explode( ':', $form_prefix )));

                    if ( isset( $array[0] ) && ! empty( $array[0] )) {

                        if ( $args['form_id'] == $array[0] && isset( $array[1] ) && ! empty( $array[1] )) {

                            $digits = absint( UM()->options()->get( 'um_unique_account_id_digits' ));
                            if ( empty( $digits )) {
                                $digits = 5;
                            }

                            $prefix = '';
                            $string_pad = '';
                            $um_unique_account_id = '';

                            if ( $array[1] == 'meta_key' && isset( $array[2] ) && ! empty( $array[2] )) {

                                if ( isset( $args[$array[2]] ) && ! empty( $args[$array[2]] )) {

                                    $prefix = sanitize_text_field( $args[$array[2]] );

                                    if ( isset( $array[4] ) && ! empty( $array[4] )) {

                                        if ( $array[4] == 'random' ) {

                                            if ( isset( $array[3] ) && ! empty( $array[3] ) && strlen( $array[3] ) == 1 ) {
                                                $prefix .= $array[3];
                                            }

                                            $string_pad = str_pad( rand( 0, pow( 10, $digits ) -1 ), $digits, '0', STR_PAD_LEFT );

                                            while( $this->unique_account_id_exists( $prefix . $string_pad )) {
                                                $string_pad = str_pad( rand( 0, pow( 10, $digits ) -1 ), $digits, '0', STR_PAD_LEFT );
                                            }

                                            $um_unique_account_id = $prefix . $string_pad;
                                        }

                                        if ( $array[4] == 'permalink' ) {

                                            $dash = '-';
                                            if ( isset( $array[3] ) && ! empty( $array[3] ) && strlen( $array[3] ) == 1 ) {
                                                $dash = $array[3];
                                            }

                                            $prefix  = str_replace( ' ', $dash, $prefix );
                                            $replace = str_replace( 'dash', $dash, "/[^A-Za-z0-9\dash]/" );
                                            $prefix  = preg_replace( $replace, '', $prefix );
                                            $prefix  = strtolower( $prefix );

                                            $i = 2;
                                            while( $this->unique_account_id_exists( $prefix . $string_pad )) {
                                                $string_pad = $dash . $i++;
                                            }

                                            $um_unique_account_id = $prefix . $string_pad;
                                        }

                                    } else {

                                        if ( isset( $array[3] ) && ! empty( $array[3] ) && strlen( $array[3] ) == 1 ) {
                                            $prefix .= $array[3];
                                        }

                                        $string_pad = str_pad( strval( $user_id ), $digits, '0', STR_PAD_LEFT );

                                        $i = 1;
                                        $string_pad_saved = $string_pad;

                                        while( $this->unique_account_id_exists( $prefix . $string_pad )) {
                                            $string_pad = $string_pad_saved . '-' . str_pad( strval( $i++ ), 3, '0', STR_PAD_LEFT );
                                        }

                                        $um_unique_account_id = $prefix . $string_pad;
                                    }
                                }

                            } else {

                                $prefix = $array[1];

                                if ( $array[1] == 'custom_keys' ) {

                                    $prefix = $this->custom_keys_unique_account_id( $args, $array );
                                }

                                if ( isset( $array[2] ) && $array[2] == 'random' ) {

                                    $string_pad = str_pad( rand( 0, pow( 10, $digits ) -1 ), $digits, '0', STR_PAD_LEFT );

                                    while( $this->unique_account_id_exists( $prefix . $string_pad )) {
                                        $string_pad = str_pad( rand( 0, pow( 10, $digits ) -1 ), $digits, '0', STR_PAD_LEFT );
                                    }

                                } else {

                                    $string_pad = str_pad( strval( $user_id ), $digits, '0', STR_PAD_LEFT );

                                    $i = 1;
                                    $string_pad_saved = $string_pad;

                                    while( $this->unique_account_id_exists( $prefix . $string_pad )) {
                                        $string_pad = $string_pad_saved . '-' . str_pad( strval( $i++ ), 3, '0', STR_PAD_LEFT );
                                    }
                                }

                                $um_unique_account_id = $prefix . $string_pad;
                            }

                            if ( ! empty( $um_unique_account_id )) {

                                update_user_meta( $user_id, $this->um_unique_account_meta_key, $um_unique_account_id );
                                break;
                            }
                        }
                    }
                }
            }
        }
    }

    public function custom_keys_unique_account_id( $args, $array ) {

        $delimiter = $array[2];

        $meta_keys = $array;
        unset( $meta_keys[0], $meta_keys[1], $meta_keys[2] );

        $meta_values = array();

        foreach( $meta_keys as $meta_key ) {

            if ( isset( $args[$meta_key] )) {

                if ( ! is_array( $args[$meta_key] )) {
                    $meta_values[] = sanitize_text_field( $args[$meta_key] );

                } else {
                    $meta_values[] = sanitize_text_field( implode( '+', $args[$meta_key] ));
                }
            }
        }

        $prefix = str_replace( ' ', '_', implode( $delimiter, $meta_values )) . $delimiter;

        return $prefix;
    }

    public function plugin_settings_link( $links ) {

        $url = get_admin_url() . 'admin.php?page=um_options&tab=appearance&section=registration_form';
        $links[] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings' ) . '</a>';

        return $links;
    }

    public function um_settings_structure_unique_account_id( $settings_structure ) {

        if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'um_options' ) {
            if ( isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] == 'appearance' ) {
                if ( isset( $_REQUEST['section'] ) && $_REQUEST['section'] == 'registration_form' ) {

                    if ( ! isset( $settings_structure['appearance']['sections']['']['form_sections']['unique_account_id']['fields'] )) {

                        $plugin_data = get_plugin_data( __FILE__ );

                        $link = sprintf( '<a href="%s" target="_blank" title="%s">%s</a>',
                                                    esc_url( $plugin_data['PluginURI'] ),
                                                    esc_html__( 'GitHub plugin documentation and download', 'ultimate-member' ),
                                                    esc_html__( 'Plugin', 'ultimate-member' )
                                        );

                        $header = array(
                                            'title'       => __( 'Unique User Account ID', 'ultimate-member' ),
                                            'description' => sprintf( esc_html__( '%s version %s - tested with UM 2.9.1', 'ultimate-member' ),
                                                                        $link, esc_attr( $plugin_data['Version'] )),
                                                        );

                        $prefix = '&nbsp; * &nbsp;';
                        $section_fields = array();

                        $section_fields[] = array(
                                        'id'          => 'um_unique_account_id',
                                        'type'        => 'textarea',
                                        'label'       => $prefix . esc_html__( "Form ID:prefix or meta_key or custom_keys formats", 'ultimate-member' ),
                                        'description' => esc_html__( "Enter the UM Registration Form ID and the Unique User Account ID Prefix or meta_key or custom_keys formats one setting per line.", 'ultimate-member' ),
                                        'args'        => array( 'textarea_rows' => 6 ));

                        $section_fields[] = array(
                                        'id'          => 'um_unique_account_id_digits',
                                        'type'        => 'number',
                                        'label'       => $prefix . esc_html__( "Number of digits in user ID", 'ultimate-member' ),
                                        'description' => esc_html__( "Enter the number of digits for the user ID. Default value is 5. Set to 1 for no prefilled zeros.", 'ultimate-member' ),
                                        'size'        => 'small' );

                        $section_fields[] = array(
                                        'id'          => 'um_unique_account_id_meta_key',
                                        'type'        => 'text',
                                        'label'       => $prefix . esc_html__( "Unique User Account ID meta_key", 'ultimate-member' ),
                                        'description' => esc_html__( "Enter the meta_key name of the Unique User Account ID field. Default name is 'um_unique_account_id'", 'ultimate-member' ),
                                        'size'        => 'small' );

                        $section_fields[] = array(
                                        'id'             => 'um_unique_account_id_column',
                                        'type'           => 'checkbox',
                                        'label'          => $prefix . esc_html__( 'All Users column', 'ultimate-member' ),
                                        'checkbox_label' => esc_html__( "Tick to create an All Users column with the Unique User Account ID", 'ultimate-member' ),
                                    );

                        $settings_structure['appearance']['sections']['registration_form']['form_sections']['unique_account_id'] = $header;
                        $settings_structure['appearance']['sections']['registration_form']['form_sections']['unique_account_id']['fields'] = $section_fields;

                    }
                }
            }
        }

        return $settings_structure;
    }

    public function custom_predefined_fields_unique_account_id( $predefined_fields ) {

        $predefined_fields[$this->um_unique_account_meta_key] = array(
                                                                        'title'    => __( 'Unique Account ID', 'ultimate-member' ),
                                                                        'metakey'  => $this->um_unique_account_meta_key,
                                                                        'type'     => 'text',
                                                                        'label'    => __( 'Unique Account ID', 'ultimate-member' ),
                                                                        'required' => 0,
                                                                        'public'   => 1,
                                                                        'editable' => false,
                                                                        'validate' => 'unique_value',
                                                                    );

        return $predefined_fields;
    }

    public function um_account_tab_general_fields( $args, $shortcode_args ) {


        if ( ! strpos( $args, ',' . $this->um_unique_account_meta_key )) {
            $args = str_replace( 'user_login,', 'user_login,' . $this->um_unique_account_meta_key . ',', $args );
        }

        return $args;
    }

    public function um_account_tab_general_fields_disable( $disabled, $data ) {

        if ( isset( $data['metakey'] ) && $data['metakey'] == $this->um_unique_account_meta_key ) {

            if ( ! current_user_can( 'administrator' )) {
                $disabled = ' disabled="disabled" ';
            }
        }

        return $disabled;
    }

    public function register_sortable_columns_custom( $columns ) {

        $columns[$this->um_unique_account_meta_key] = $this->um_unique_account_meta_key;

        return $columns;
    }

    public function manage_users_columns_custom_um( $columns ) {

        $columns[$this->um_unique_account_meta_key] = esc_html__( 'Unique User Account ID', 'ultimate-member' );

        return $columns;
    }

    public function manage_users_custom_column_um( $value, $column_name, $user_id ) {

        if ( $column_name == $this->um_unique_account_meta_key ) {

            um_fetch_user( $user_id );
            $value = um_user( $this->um_unique_account_meta_key );
            um_reset_user();
        }

        return $value;
    }

}

new UM_Unique_Account_ID ();


