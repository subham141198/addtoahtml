<?php
function hi_fetch_post()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'htmlinjector';

    $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE hi_target_page = %d", url_to_postid(sanitize_url($_POST['page_url']))));
    if ($result > 0) {
        echo json_encode(array('hi_html_data' => $result));
    } else {
        echo json_encode(array('hi_html_data' => 'there is noting'));
    }
    exit();
}

add_action('wp_ajax_nopriv_hi_fetch_post_data', 'hi_fetch_post');
add_action('wp_ajax_hi_fetch_post_data', 'hi_fetch_post');
