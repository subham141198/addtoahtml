<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*Register all CSS and JS to the plugin*/

function shci_style()
{
    wp_enqueue_style('style',  shci_ASSETS . "css/shci_style.css", false, mt_rand());
}
function shci_scripts()
{
    wp_register_script('shci_js', shci_ASSETS . 'js/shci_script.js', array('jquery'), '1.0');
    wp_localize_script('shci_js', 'wpAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
    wp_enqueue_script('jquery');
    wp_enqueue_script('shci_js');
}
add_action('admin_enqueue_scripts', 'shci_scripts');
add_action('wp_enqueue_scripts', 'shci_scripts');
add_action('admin_print_styles', 'shci_style');
add_action('wp_enqueue_scripts', 'shci_style');
