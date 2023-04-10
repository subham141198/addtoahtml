<?php
error_reporting(E_ALL);
define('atah_PATH', plugin_dir_path(__FILE__) . 'includes/');
define('atah_ASSETS', plugin_dir_url(__FILE__) . 'assets/');
/*
 * Plugin Name:       Add To A HTML
 * Description:       Add a HTML snippet after before or InnerHTML with classname or ID of an Element
 * Plugin URI:        http://www.ewaycorp.com
 * Version:           1.0
 * Author:            Subham Banerjee
 */

function atah_activate_init()
{
	include_once(atah_PATH . 'atah_functions.php');
	include_once(atah_PATH . 'atah_register_script.php');
	include_once(atah_PATH . 'atah_response.php');
}
add_action('init', 'atah_activate_init');

/********Create Table on activate*********/
function atah_create_table()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'addtoahtml';
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		atah_post_id int(10),
		atah_selector_name varchar(255) NOT NULL,
		atah_target_html text NOT NULL,
		atah_target_page varchar(255) NOT NULL,
        atah_target_location varchar(255) NOT NULL,
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
			'public'      => false,
			'has_archive' => false,
			'show_ui' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'show_in_nav_menus' => false,
			'rewrite' => false,
			'supports' => array(
				'title',
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
register_activation_hook(__FILE__, 'atah_activate');
/********action Hook for activation of plugin*********/


function atah_delete_plugin_data()
{
	global $wpdb;

	//delete costom post type
	$args = array(
		'post_type' => 'add_html',
		'posts_per_page' => -1,
		'post_status' => 'any'
	);
	$posts = get_posts($args);
	foreach ($posts as $post) {
		wp_delete_post($post->ID, true);
	}

	// delete plugin database
	$table_name = $wpdb->prefix . 'addtoahtml';
	$sql = "DROP TABLE IF EXISTS $table_name;";
	$wpdb->query($sql);
}
register_deactivation_hook(__FILE__, 'atah_delete_plugin_data');
/********action Hook for deactivation of plugin*********/


function atah_meta_box()   //meta field box creation
{
	add_meta_box(
		'add_html_meta_box',
		'Add HTML',
		'atah_custom_post_field',    // callback function for the fields
		'add_html',
		'normal',
		'default'
	);
}
add_action('add_meta_boxes', 'atah_meta_box');

function atah_custom_post_field($post)    //
{
	if ($result = atah_get_fields_data($post->ID)) {
		$meta = atah_get_fields_data($post->ID);
	} else {

		$meta = new stdClass();
		$meta->atah_selector_name = 'Enter data';
		$meta->atah_target_html = 'Enter Data';
		$meta->atah_target_page = 'Home';
		$meta->atah_target_location = 'before';
	}

	$pages = get_pages();
	$atah_selected_page_option = $meta->atah_target_page;

	$options = array(
		'before' => 'Before',
		'after' => 'After',
		'innerhtml' => 'InnerHTML'
	);
	$atah_selected_location_option = $meta->atah_target_location;

	echo '<label for="atah_selector_name">Selector Name: </label>';
	echo '<input type="text" name="atah_selector_name" value="' . esc_attr($meta->atah_selector_name) . '" size="30" />';
	echo '<br>';
	echo '<br>';
	echo '<label for="atah_target_html">Target HTML: </label>';
	echo '<textarea id="myTextarea" name="atah_target_html" value="'.esc_attr($meta->atah_target_html).'" size="30"></textarea>';
	echo '<br>';
	echo '<br>';
	echo '<label for="atah_target_page">Target Page:</label>';
	echo '<select class="page_id" name="atah_target_page">';
	foreach ($pages as $page) {
		$selected = selected($atah_selected_page_option, $page->post_title, false);
		echo '<option value="' .$page->ID. '"' . $selected . '>' . $page->post_title . '</option>';
	}
	echo '</select>';
	echo '<br>';
	echo '<br>';
	echo '<label for="atah_target_location">Target Location: </label>';
	echo '<select name="atah_target_location">';
	foreach ($options as $value => $label) {
		$selected = selected($atah_selected_location_option, $value, false);
		echo '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
	}
	echo '</select>';
}
function atah_save_meta_box($post_id)
{
	if (
		!empty($_POST['atah_selector_name']) &&
		!empty($_POST['atah_target_html']) &&
		!empty($_POST['atah_target_page']) &&
		!empty($_POST['atah_target_location'])
	) {
		$atah_fields = array(
			'atah_post_id' => $post_id,
			'atah_selector_name' => sanitize_text_field($_POST['atah_selector_name']),
			'atah_target_html' => $_POST['atah_target_html'],
			'atah_target_page' => sanitize_text_field($_POST['atah_target_page']),
			'atah_target_location' => sanitize_text_field($_POST['atah_target_location'])
		);
		if ($results = atah_get_fields_data($post_id)) {
			atah_update_fields_data($post_id, $atah_fields);
		} else {
			atah_insert_fields_data($atah_fields);
		}
	}
	
}
add_action('save_post_add_html', 'atah_save_meta_box');
add_filter('manage_add_html_posts_columns', 'atah_set_custom_edit_book_columns');

function atah_set_custom_edit_book_columns($columns)
{
	unset($columns['author']);
	unset($columns['date']);
	$columns['atah_selector_name'] = __('Selector Name', 'your_text_domain');
	$columns['atah_target_page'] = __('Target Page', 'your_text_domain');
	$columns['atah_target_location'] = __('Location', 'your_text_domain');
	return $columns;
}

add_action('manage_add_html_posts_custom_column', 'atah_custom_add_html_column', 10, 2);
function atah_custom_add_html_column($column, $post_id)
{
	$fields_data = atah_get_fields_data($post_id);
	switch ($column) {

		case 'atah_selector_name':
			if ($fields_data != '') {
				echo $fields_data->atah_selector_name;
			} else
				_e('Unable to get selector name(s)', 'your_text_domain');
			break;
		case 'atah_target_page':
			if ($fields_data != '') {
				echo $fields_data->atah_target_page;
			} else
				_e('Unable to get the target page(s)', 'your_text_domain');
			break;
		case 'atah_target_location':
			if ($fields_data != '') {
				echo $fields_data->atah_target_location;
			} else
				_e('Unable to get target location(s)', 'your_text_domain');
			break;
	}
}
