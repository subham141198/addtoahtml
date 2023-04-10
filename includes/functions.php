<?php

function atah_insert_fields_data($atah_fields){
    global $wpdb;
	$table_name = $wpdb->prefix . 'addtoahtml';
    $wpdb->insert($table_name, $atah_fields);
    
}
function atah_update_fields_data($post_id,$atah_fields){
    global $wpdb;
	$table_name = $wpdb->prefix . 'addtoahtml';
    $wpdb->update($table_name, $atah_fields, array('atah_post_id' => $post_id));
    
}
function atah_get_fields_data($post_id){
    global $wpdb;
	$table_name = $wpdb->prefix . 'addtoahtml';
    $result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE atah_post_id = %d", $post_id));

    return $result;
}
