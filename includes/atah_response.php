<?php 
function atah_fetch_post(){
    $page_url = $_POST['page_url'];
    $page_id = url_to_postid($page_url);
	global $wpdb;
    $table_name = $wpdb->prefix . 'addtoahtml';

    $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE atah_target_page = %d", $page_id));
    if($result > 0 ){
        echo json_encode(array('html' => $result));
    }else{
        echo json_encode(array('html' => 'there is noting'));
    }
   
    exit();
}

add_action('wp_ajax_nopriv_atah_fetch_post_data', 'atah_fetch_post');
add_action('wp_ajax_atah_fetch_post_data', 'atah_fetch_post');

