<?php
define('hi_PATH', plugin_dir_path(__FILE__) . 'includes/');
define('hi_ASSETS', plugin_dir_url(__FILE__) . 'assets/');
/*
 * Plugin Name:       HTML Injector
 * Description:       Add a HTML snippet after before or InnerHTML with classname or ID of an Element
 * Plugin URI:        https://www.ewaycorp.com
 * Version:           1.0.0
 * Author:            eWay Corp
 * Author URI:        https://www.ewaycorp.com
 */

function hi_activate_init()
{
	include_once(hi_PATH . sanitize_file_name('hi_functions.php'));
	include_once(hi_PATH . sanitize_file_name('hi_register_script.php'));
	include_once(hi_PATH . sanitize_file_name('hi_response.php'));
}
add_action('init', 'hi_activate_init');

/********Create Table on activate*********/
function hi_create_table()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'htmlinjector';
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		hi_post_id int(10),
		hi_selector_name varchar(255) NOT NULL,
		hi_target_html text NOT NULL,
		hi_target_page varchar(255) NOT NULL,
        hi_target_location varchar(255) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta($sql);
}

function hi_create_post_type()
{
	register_post_type(
		'html_inject',
		array(
			'labels'      => array(
				'name'          => esc_html('Html Injector', 'textdomain'),
				'singular_name' => esc_html('Html Snippet', 'textdomain'),
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
			'register_meta_box_cb' => 'hi_meta_box'
		)
	);
}
add_action('init', 'hi_create_post_type');

function hi_activate()
{
	hi_create_table();
	hi_create_post_type();
	flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'hi_activate');
/********action Hook for activation of plugin*********/


function hi_delete_plugin_data()
{
	global $wpdb;

	//delete costom post type
	$args = array(
		'post_type' => 'html_inject',
		'posts_per_page' => -1,
		'post_status' => 'any'
	);
	$posts = get_posts($args);
	foreach ($posts as $post) {
		wp_delete_post($post->ID, true);
	}

	// delete plugin database
	$table_name = $wpdb->prefix . 'htmlinjector';
	$sql = "DROP TABLE IF EXISTS $table_name;";
	$wpdb->query($sql);
}
register_deactivation_hook(__FILE__, 'hi_delete_plugin_data');
/********action Hook for deactivation of plugin*********/


function hi_meta_box()   //hi_data field box creation
{
	add_meta_box(
		'html_inject_meta_box',
		'Fill up to Inject HTML',
		'hi_custom_post_field',    // callback function for the fields
		'html_inject',
		'normal',
		'default'
	);
}
add_action('add_meta_boxes', 'hi_meta_box');

function hi_custom_post_field($post)    //
{

	if ($result = hi_get_fields_data($post->ID)) {
		$hi_data = hi_get_fields_data($post->ID);
	} else {

		$hi_data = new stdClass();
		$hi_data->hi_selector_name = '';
		$hi_data->hi_target_html = '';
		$hi_data->hi_target_page = '';
		$hi_data->hi_target_location = '';
	}

	$pages = get_pages();
	$hi_selected_page_option = $hi_data->hi_target_page;

	$options = array(
		'before' => 'Before',
		'after' => 'After',
		'innerhtml' => 'InnerHTML'
	);
	$hi_selected_location_option = $hi_data->hi_target_location;
	echo '<div class="hi_form_wrapper">';
	echo '<label class="hi_selector_label" for="hi_selector_name">Selector Name: </label>';
	echo '<input class="hi_selector_input" placeholder="Enter the ID, Classname or Element with prefix" type="text" name="hi_selector_name" value="' . esc_attr($hi_data->hi_selector_name) . '" size="30" />';
	echo '<br>';
	echo '<br>';
	echo '<label class="hi_insert_html_label" for="hi_selector_name">Insert Html: </label>';
	$content = $hi_data->hi_target_html;
	wp_editor($content, 'hi_custom_post_type_editor');
	echo '<br>';
	echo '<br>';
	echo '<label class="hi_target_page_label" for="hi_target_page">Target Page: </label>';
	echo '<select class="hi_target_page_selector" name="hi_target_page">';
	foreach ($pages as $page) {
		echo '<option value="' . esc_attr($page->ID) . '"' . esc_attr(selected($hi_selected_page_option, $page->ID, false)) . '>' . esc_html($page->post_title) . '</option>';
	}
	echo '</select>';
	echo '<br>';
	echo '<br>';
	echo '<label class="hi_target_location_label" for="hi_target_location">Target Location: </label>';
	echo '<select class="hi_select_location_selector" name="hi_target_location">';
	foreach ($options as $value => $label) {

		echo '<option value="' . esc_attr($value) . '"' . esc_attr(selected($hi_selected_location_option, $value, false)) . '>' . esc_html($label) . '</option>';
	}
	echo '</select>';
	echo '</div>';
}
function hi_save_meta_box($post_id)
{
	if (
		empty($_POST['hi_selector_name']) &&
		empty($_POST['hi_custom_post_type_editor']) &&
		empty($_POST['hi_target_page']) &&
		empty($_POST['hi_target_location'])
	) {
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}


	$hi_fields = array(
		'hi_post_id' => $post_id,
		'hi_selector_name' => sanitize_html_class($_POST['hi_selector_name']),
		'hi_target_html' => wp_kses_post($_POST['hi_custom_post_type_editor']),
		'hi_target_page' => sanitize_text_field($_POST['hi_target_page']),
		'hi_target_location' => sanitize_text_field($_POST['hi_target_location'])
	);
	if ($results = hi_get_fields_data($post_id)) {
		hi_update_fields_data($post_id, $hi_fields);
	} else {
		hi_insert_fields_data($hi_fields);
	}
}
add_action('save_post_html_inject', 'hi_save_meta_box');
add_filter('manage_html_inject_posts_columns', 'hi_set_custom_edit_book_columns');

function hi_set_custom_edit_book_columns($columns)
{
	unset($columns['author']);
	unset($columns['date']);
	$columns['hi_selector_name'] = __('Selector Name', 'your_text_domain');
	$columns['hi_target_page'] = __('Target Page', 'your_text_domain');
	$columns['hi_target_location'] = __('Location', 'your_text_domain');
	return $columns;
}

add_action('manage_html_inject_posts_custom_column', 'hi_custom_html_inject_column', 10, 2);
function hi_custom_html_inject_column($column, $post_id)
{
	$hi_fields_data = hi_get_fields_data($post_id);
	switch ($column) {

		case 'hi_selector_name':
			if ($hi_fields_data != '') {
				esc_html_e($hi_fields_data->hi_selector_name) ;
			} else
				_e('Unable to get selector name(s)', 'your_text_domain');
			break;
		case 'hi_target_page':
			if ($hi_fields_data != '') {
				esc_html_e($hi_fields_data->hi_target_page);
			} else
				_e('Unable to get the target page(s)', 'your_text_domain');
			break;
		case 'hi_target_location':
			if ($hi_fields_data != '') {
				esc_html_e($hi_fields_data->hi_target_location);
			} else
				_e('Unable to get target location(s)', 'your_text_domain');
			break;
	}
}
