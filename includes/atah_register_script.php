<?php
/*Register all CSS and JS to the plugin*/

function atah_style(){
    wp_enqueue_style( 'style',  atah_ASSETS . "css/atah_style.css" , false, mt_rand());
}
function atah_scripts() {
    wp_register_script( 'atah_js',atah_ASSETS.'js/atah_script.js', array('jquery'), '1.0' );
    wp_register_script('scap_tinymce_js', 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.4.1/tinymce.min.js', array('jquery'), '1.0');
    wp_localize_script( 'atah_js', 'wpAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
    wp_enqueue_script('jquery');
    wp_enqueue_script( 'atah_js');
    wp_enqueue_script('scap_tinymce_js');

}
add_action( 'admin_enqueue_scripts', 'atah_scripts' );
add_action( 'wp_enqueue_scripts', 'atah_scripts' );
add_action( 'admin_print_styles', 'atah_style');

