<?php
/*Register all CSS and JS to the plugin*/

function hi_style()
{
    wp_enqueue_style('style',  hi_ASSETS . "css/hi_style.css", false, mt_rand());
}
function hi_scripts()
{
    wp_register_script('hi_js', hi_ASSETS . 'js/hi_script.js', array('jquery'), '1.0');
    wp_localize_script('hi_js', 'wpAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_enqueue_script('jquery');
    wp_enqueue_script('hi_js');
}
add_action('admin_enqueue_scripts', 'hi_scripts');
add_action('wp_enqueue_scripts', 'hi_scripts');
add_action('admin_print_styles', 'hi_style');
add_action('wp_enqueue_scripts', 'hi_style');
