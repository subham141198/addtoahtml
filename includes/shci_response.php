<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function shci_fetch_post()
{


    global $wpdb;
    $table_name = $wpdb->prefix . 'htmlinjector';
    $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE shci_target_page = %s",  get_post_field( 'post_name', url_to_postid($_POST["page_url"]))));
    if ($result > 0) {
        echo json_encode(array('shci_html_data' => $result));
    } else {
        echo json_encode(array('shci_html_data' => 'there is noting',));
    }
    exit();
}

add_action('wp_ajax_nopriv_shci_fetch_post_data', 'shci_fetch_post');
add_action('wp_ajax_shci_fetch_post_data', 'shci_fetch_post');
