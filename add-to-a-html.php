<?php
/*
 * Plugin Name:       Add To A HTML
 * Description:       Add a HTML snippet after before or InnerHTML with classname or ID of an Element
 * Version:           1.0
 * Author:            Subham Banerjee
 */


/********Create Table on activate*********/
function atah_create_table()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'addtoahtml';
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		selector_name varchar(255) NOT NULL,
		target_html varchar(255) NOT NULL,
		target_page varchar(255) NOT NULL,
        target_location varchar(255) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta($sql);
}



function atah_create_post_type()
{
	register_post_type(
		'add_html',
		array(
			'labels'      => array(
				'name'          => __('Add to a Html', 'textdomain'),
				'singular_name' => __('Html Snippet', 'textdomain'),
			),
			'public'      => true,
			'has_archive' => true,
			'supports' => array(
				'title',
				'editor',
				'author',
			),
			'register_meta_box_cb' => 'atah_meta_box'  
		)
	);
}
add_action('init', 'atah_create_post_type');

function atah_activate()
{
	atah_create_table();
	atah_create_post_type();
	flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'atah_activate');/********action Hook for activation of plugin*********/

/********Delete Table on deactivate*********/
function atah_delete_plugin_data()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'addtoahtml';
	$sql = "DROP TABLE IF EXISTS $table_name;";
	$wpdb->query($sql);
}
register_deactivation_hook(__FILE__, 'atah_delete_plugin_data');
/********action Hook for deactivation of plugin*********/

function atah_meta_box()
{
	add_meta_box(
		'add_html_url_meta_box',
		'Add HTML',
		'atah_custom_post_field',
		'add_html',
		'normal',
		'default'
	);
}
add_action('add_meta_boxes', 'atah_meta_box');

function atah_custom_post_field()
{
	
	echo '<label for="add_html_url_field">Selector Name: </label>';
	echo '<input type="text" name="atah_selector_name" size="30" />';
	echo '<br>';
	echo '<br>';
	echo '<label for="add_html_url_field">Target HTML: </label>';
	echo '<textarea name="atah_target_html" size="30" ></textarea>';
	echo '<br>';
	echo '<br>';
	echo '<label for="add_html_url_field">Target Page:</label>';
	echo '<input type="text"  name="atah_target_page" size="30" />';
	echo '<br>';
	echo '<br>';
	echo '<label for="add_html_url_field">Target Location: </label>';
	echo '<select name="atah_target_location">';
	echo '<option>Before</option>';
	echo '<option>After</option>';
	echo '<option>InnerHTML</option>';
	echo '</select>';

	
}
$atah_fields = array(
	'selector_name' => $_POST['atah_selector_name'],
	'target_html' => $_POST['atah_target_html'],
	'target_page' => $_POST['atah_target_page'],
	'target_location' => $_POST['atah_target_location']
);
var_dump($atah_fields);
global $wpdb;
$table_name = $wpdb->prefix . 'addtoahtml';
foreach($atah_fields as $field_name => $field_data){
	$data = array($field_name => $field_data);
}
$wpdb->insert($table_name, $data);

