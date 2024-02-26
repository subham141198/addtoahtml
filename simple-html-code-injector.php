<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('shci_PATH', plugin_dir_path(__FILE__) . 'includes/');
define('shci_ASSETS', plugin_dir_url(__FILE__) . 'assets/');
/*
 * Plugin Name:       Simple Html Code Inector
 * Description:       Add a HTML snippet after before or InnerHTML with classname or ID of an Element
 * Plugin URI:        https://www.ewaycorp.com
 * Version:           1.0
 * Author:            eWayCorp
 * Author URI:        https://www.ewaycorp.com
 * Text Domain:       simple-html-code-injector
 */

function shci_activate_init()
{
	include_once(shci_PATH . sanitize_file_name('shci_functions.php'));
	include_once(shci_PATH . sanitize_file_name('shci_register_script.php'));
	include_once(shci_PATH . sanitize_file_name('shci_response.php'));
}
add_action('init', 'shci_activate_init');

/********Create Table on activate*********/
function shci_create_table(){
	global $wpdb;
	$table_name = $wpdb->prefix . 'htmlinjector';
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE $table_name (
		id int(11) NOT NULL AUTO_INCREMENT,
		shci_post_id int(10),
		shci_selector_type varchar(255) NOT NULL,
		shci_selector_name varchar(255) NOT NULL,
		shci_target_html text NOT NULL,
		shci_target_page varchar(255) NOT NULL,
        shci_target_location varchar(255) NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta($sql);
}

function shci_create_post_type(){
	register_post_type(
		'html_inject',
		array(
			'labels'      => array(
				'name'          => esc_html('Html Injector', 'simple-html-code-injector'),
				'singular_name' => esc_html('Html Snippet', 'simple-html-code-injector'),
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
			'register_meta_box_cb' => 'shci_meta_box'
		)
	);
}
add_action('init', 'shci_create_post_type');

function shci_activate()
{
	shci_create_table();
	shci_create_post_type();
	flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'shci_activate');
/********action Hook for activation of plugin*********/


function shci_delete_plugin_data()
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
register_deactivation_hook(__FILE__, 'shci_delete_plugin_data');
/********action Hook for deactivation of plugin*********/


function shci_meta_box()   //shci_data field box creation
{
	add_meta_box(
		'html_inject_meta_box',
		'Fill up to Inject HTML',
		'shci_custom_post_field',    // callback function for the fields
		'html_inject',
		'normal',
		'default'
	);
}
add_action('add_meta_boxes', 'shci_meta_box');

function shci_custom_post_field($post)   {



	if ($result = shci_get_fields_data($post->ID)) {
		$shci_data = shci_get_fields_data($post->ID);
	} else {

		$shci_data = new stdClass();
		$shci_data->shci_selector_type = '';
		$shci_data->shci_selector_name = '';
		$shci_data->shci_target_html = '';
		$shci_data->shci_target_page = '';
		$shci_data->shci_target_location = '';
	}

	$pages = get_pages();

	$shci_selected_page_option = $shci_data->shci_target_page;
	$shci_selected_selector_type_option = $shci_data->shci_selector_type;
	$selector_type_options = array(
		'class' => 'Class',
		'id' => 'ID',
	);

	$options = array(
		'before' => 'Before',
		'after' => 'After',
		'innerhtml' => 'InnerHTML'
	);
	$shci_selected_location_option = $shci_data->shci_target_location;
	echo '<div class="shci_form_wrapper">';
	 wp_nonce_field('shci_meta_box_nonce_action', 'shci_meta_box_nonce');
	echo '<label class="shci_target_location_label" for="shci_target_location">Selector Type </label>';
	echo '<select class="shci_selector_type" name="shci_selector_type">';
	foreach ($selector_type_options as $value => $label) {
		echo '<option value="' . esc_attr($value) . '"' . esc_attr(selected($shci_selected_selector_type_option, $value, false)) . '>' . esc_html($label) . '</option>';
	}
	echo '</select>';
	echo '<label class="shci_selector_label" for="shci_selector_name">Selector Name: </label>';
	echo '<input class="shci_selector_input" placeholder="Enter the ID, Classname or Element with prefix" type="text" name="shci_selector_name" value="' . esc_attr($shci_data->shci_selector_name) . '" size="30" />';
	echo '<br>';
	echo '<br>';
	echo '<label class="shci_insert_html_label" for="shci_selector_name">Insert Html: </label>';
	$content = $shci_data->shci_target_html;
	wp_editor($content, 'shci_custom_post_type_editor');
	echo '<br>';
	echo '<br>';
	echo '<label class="shci_target_page_label" for="shci_target_page">Target Page: </label>';
	echo '<select class="shci_target_page_selector" name="shci_target_page">';
	foreach ($pages as $page) {
		echo '<option value="' . esc_attr($page->post_name) . '"' . esc_attr(selected($shci_selected_page_option, $page->post_name, false)) . '>' . esc_html($page->post_title) . '</option>';
	}
	echo '</select>';
	echo '<br>';
	echo '<br>';
	echo '<label class="shci_target_location_label" for="shci_target_location">Target Location: </label>';
	echo '<select class="shci_select_location_selector" name="shci_target_location">';
	foreach ($options as $value => $label) {

		echo '<option value="' . esc_attr($value) . '"' . esc_attr(selected($shci_selected_location_option, $value, false)) . '>' . esc_html($label) . '</option>';
	}
	echo '</select>';
	echo '</div>';
}
function shci_save_meta_box($post_id){

	if (!isset($_POST['shci_meta_box_nonce'])) {
		return;
	}
	// Verify nonce
	if (!wp_verify_nonce($_POST['shci_meta_box_nonce'], 'shci_meta_box_nonce_action')) {
		return;
	}

	if (
		empty($_POST['shci_selector_name']) &&
		empty($_POST['shci_custom_post_type_editor']) &&
		empty($_POST['shci_target_page']) &&
		empty($_POST['shci_target_location'])
	) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	$shci_fields = array(
		'shci_post_id' => $post_id,
		'shci_selector_type' => sanitize_text_field($_POST['shci_selector_type']),
		'shci_selector_name' => sanitize_html_class($_POST['shci_selector_name']),
		'shci_target_html' => wp_kses_post($_POST['shci_custom_post_type_editor']),
		'shci_target_page' => sanitize_text_field($_POST['shci_target_page']),
		'shci_target_location' => sanitize_text_field($_POST['shci_target_location'])
	);

	if ($results = shci_get_fields_data($post_id)) {
		shci_update_fields_data($post_id, $shci_fields);
	} else {
		shci_insert_fields_data($shci_fields);
	}
}
add_action('save_post_html_inject', 'shci_save_meta_box');
add_action('save_post_html_inject', 'shci_save_meta_box');


add_filter('manage_html_inject_posts_columns', 'shci_set_custom_edit_book_columns');

function shci_set_custom_edit_book_columns($columns){
	unset($columns['author']);
	unset($columns['date']);
	$columns['shci_selector_type'] = __('Selector Type', 'simple-html-code-injector');
	$columns['shci_selector_name'] = __('Selector Name', 'simple-html-code-injector');
	$columns['shci_selector_name'] = __('Selector Name', 'simple-html-code-injector');
	$columns['shci_target_page'] = __('Target Page', 'simple-html-code-injector');
	$columns['shci_target_location'] = __('Location', 'simple-html-code-injector');
	return $columns;
}

add_action('manage_html_inject_posts_custom_column', 'shci_custom_html_inject_column', 10, 2);
function shci_custom_html_inject_column($column, $post_id){
	$shci_fields_data = shci_get_fields_data($post_id);
	switch ($column) {
		case 'shci_selector_type':
			if ($shci_fields_data != '') {
				esc_html_e($shci_fields_data->shci_selector_type,'simple-html-code-injector');
			} else
				esc_html_e('Unable to get selector type(s)', 'simple-html-code-injector');
			break;
		case 'shci_selector_name':
			if ($shci_fields_data != '') {
				esc_html_e($shci_fields_data->shci_selector_name,'simple-html-code-injector');
			} else
				esc_html_e('Unable to get selector name(s)', 'simple-html-code-injector');
			break;
		case 'shci_target_page':
			if ($shci_fields_data != '') {
				esc_html_e($shci_fields_data->shci_target_page,'simple-html-code-injector');
			} else
				esc_html_e('Unable to get the target page(s)', 'simple-html-code-injector');
			break;
		case 'shci_target_location':
			if ($shci_fields_data != '') {
				esc_html_e($shci_fields_data->shci_target_location,'simple-html-code-injector');
			} else
				esc_html_e('Unable to get target location(s)', 'simple-html-code-injector');
			break;
	}
}
