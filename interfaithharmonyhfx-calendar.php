<?php
/**
 * @package IHH Calendar
 * @version 1.0.0
 */
/*
Plugin Name: IHH Calendar
Description: A calendar plugin for Interfaith Harmony Halifax
Author: Veronica Hatala
Version: 1.0.0
Author URI: https://github.com/veronica-hatala
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function multiblock_register_blocks() {
	register_block_type( __DIR__ . '/build/blocks/events' );
}
add_action( 'init', 'multiblock_register_blocks' );

/**
 * Begin creation of Events post type
 */

 function event_custom_post_type() {
	register_post_type('event',
		array(
			'labels'      => array(
				'name'          => __('Events', 'textdomain'),
				'singular_name' => __('Event', 'textdomain'),
				'add_new' => _x('Add New', 'event'),
				'add_new_item' => __('Add New Event'),
				'edit_item' => __('Edit Event'),
				'new_item' => __('New Event'),
				'view_item' => __('View Event'),
				'search_items' => __('Search Events'),
				'not_found' =>  __('No events found'),
				'not_found_in_trash' => __('Nothing found in Trash'),
				'parent_item_colon' => ''
			),
				'supports' => array('title'),
				'public'      => false,
				'has_archive' => false,
				'show_ui' => true,
				'show_in_menu' => true,
				'show_in_nav_menus' => true,
				'menu_icon' => 'dashicons-calendar-alt',
		)
	);
}
add_action('init', 'event_custom_post_type');

function add_event_submenu_item() { 
    add_submenu_page('edit.php', 'Event', 'Event', 'manage_options', 'edit-tags.php?taxonomy=event'); 
} 
add_action('admin_menu', 'add_event_submenu_item'); 


function event_meta() {
	global $post;
	$custom = get_post_custom($post->ID);
	$address = $custom["address"][0] ?? null;
	$description = $custom["description"][0] ?? null;;
	$datetime = $custom["datetime"][0] ?? null;;
	$online = $custom["online"][0] ?? null;;
	$is_checked = ((int)$online == 1) ? 'checked' : '';
	?>
	<p><label>Address / Zoom Link:</label><br />
	<textarea style="width:100%;" rows="2" name="address"><?php echo $address; ?></textarea></p>
	<p><label>Description:</label><br />
	<textarea style="width:100%;" rows="5" name="description"><?php echo $description; ?></textarea></p>
	<p><label>Date and Time:</label><br />
	<input type="datetime-local" name="datetime" value="<?php echo $datetime; ?>"></p>
	<p><label>Online? :</label><br />
	<input type="checkbox" name="online" value="1" <?php echo $is_checked; ?>></p>
	<?php
}

function admin_init(){
  add_meta_box("event_meta", "Event Information", "event_meta", "event", "normal", "high");
}
add_action("admin_init", "admin_init");

function save_details(){
	global $post;
  
	if (isset($post) && isset($post->ID)) {
		update_post_meta($post->ID, "address", $_POST["address"]);
		update_post_meta($post->ID, "description", $_POST["description"]);
		update_post_meta($post->ID, "datetime", $_POST["datetime"]);
		update_post_meta($post->ID, "online", $_POST["online"]);
	} else {
		error_log("Error: \$post is null or \$post->ID is undefined.");
	}
}
add_action('save_post', 'save_details');