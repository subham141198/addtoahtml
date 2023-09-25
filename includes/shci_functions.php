<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function shci_insert_fields_data($shci_fields)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'htmlinjector';
    $wpdb->insert($table_name, $shci_fields);
}
function shci_update_fields_data($post_id, $shci_fields)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'htmlinjector';
    $wpdb->update($table_name, $shci_fields, array('shci_post_id' => $post_id));
}
function shci_get_fields_data($post_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'htmlinjector';
    $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE shci_post_id = %d", $post_id));

    return $result;
}
