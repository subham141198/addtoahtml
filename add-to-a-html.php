<?php
/*
 * Plugin Name:       Add To A HTML
 * Description:       Add a HTML snippet after before or InnerHTML with classname or ID of an Element
 * Version:           1.0
 * Author:            Subham Banerjee
 */


/********Create Table on activate*********/

 function atah_create_table() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'addtoahtml';
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		selector_name varchar(255) NOT NULL,
		selector_type varchar(255) NOT NULL,
		target_html varchar(255) NOT NULL,
		target_page varchar(255) NOT NULL,
        target_location varchar(255) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );	
}

function atah_activate(){
	atah_create_table();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__,'atah_activate');  /********action Hook for activation of plugin*********/


/********Delete Table on deactivate*********/
function atah_delete_plugin_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'addtoahtml';
    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);
}    
register_deactivation_hook( __FILE__, 'atah_delete_plugin_data' ); /********action Hook for deactivation of plugin*********/



 
function atah_plugin_setup_menu(){
    add_menu_page( 'Add to a HTML', 'Add to a HTML', 'manage_options', 'atah_plugin', 'atah_plugin_home_page',esc_url(plugins_url('add-to-a-html/images/atah_icon.png',dirname(__FILE__))), 2);

	add_submenu_page(
        'atah_plugin',
        'Add New',
        'Add New',
        'manage_options',
        'atah_submenu_add_new',
        'atah_plugin_addnew_page'
    );
}

add_action('admin_menu', 'atah_plugin_setup_menu');
function atah_plugin_home_page(){
   
}
 
function atah_plugin_addnew_page(){
    include('includes/add-new.php');
}
 