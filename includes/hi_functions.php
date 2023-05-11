<?php

function hi_insert_fields_data($hi_fields)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'htmlinjector';
    $wpdb->insert($table_name, $hi_fields);
}
function hi_update_fields_data($post_id, $hi_fields)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'htmlinjector';
    $wpdb->update($table_name, $hi_fields, array('hi_post_id' => $post_id));
}
function hi_get_fields_data($post_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'htmlinjector';
    $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE hi_post_id = %d", $post_id));

    return $result;
}
