<?php
/**
 * @package PVS Regular Events
 * @version 1.0
 */
/*
Plugin Name: PVS Regular Events
Plugin URI: http://suesdesign.co.uk/
Description: PVS Regular Events listing by day of the week
Author: Sue Johnson
Version: 1.0
Author URI: http://suesdesign.co.uk/
*/

/*
 * register pvs events
*/

function pvs_register_post_type() {
	$labels = array( 
		'name'               => _x( 'Regular Events', 'pvs_regular_events' ),
		'singular name'      => _x( 'Regular Event', 'pvs_regular_events' ),
		'add_new'            => _x( 'Add new Regular Event', 'pvs_regular_events' ),
		'add_new_item'       => __( 'Add new Regular Event', 'pvs_regular_events' ),
		'edit_item'          => __( 'Edit Regular Event', 'pvs_regular_events' ),
		'new_item'           => __( 'New Regular Event', 'pvs_regular_events' ),
		'all_items'          => __( 'All Regular Events', 'pvs_regular_events' ),
		'view_item'          => __( 'View Regular Event', 'pvs_regular_events' ),
		'search_items'       => __( 'Search Regular Events', 'pvs_regular_events' ),
		'not_found'          => __( 'No Regular Events', 'pvs_regular_events' ),
		'not_found_in_trash' => __( 'No Regular Events found in trash', 'pvs_regular_events' )
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'has archive' => true,
		'show_in_rest' => true,
		'supports' => array( 'title', 'thumbnail' ),
	);
	
	register_post_type( 'pvs_events', $args );
}

add_action( 'init', 'pvs_register_post_type' );

/*
 * Create day of the weeks of the week as a taxonomy
*/

function pvs_events_create_taxonomy() {

// Labels part for the GUI

  $labels = array(
    'name' => _x( 'Day of the week', 'taxonomy general name', 'pvs_regular_events' ),
    'singular_name' => _x( 'day of the week', 'taxonomy singular name', 'pvs_regular_events'),
    'search_items' =>  __( 'Search day of the week', 'pvs_regular_events'),
    'popular_items' => __( 'Popular day of the week', 'pvs_regular_events'),
    'all_items' => __( 'All days of the week', 'pvs_regular_events'),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit day of the week', 'pvs_regular_events'), 
    'update_item' => __( 'Update day of the week', 'pvs_regular_events'),
    'add_new_item' => __( 'Add new day of the week' , 'pvs_regular_events'),
    'new_item_name' => __( 'New day of the week', 'pvs_regular_events'),
    'separate_items_with_commas' => __( 'Separate day of the week with commas', 'pvs_regular_events'),
    'add_or_remove_items' => __( 'Add or remove day of the week', 'pvs_regular_events' ),
    'choose_from_most_used' => __( 'Choose from the most used day of the week', 'pvs_regular_events' ),
    'menu_name' => __( 'Day of the week', 'pvs_regular_events' ),
  ); 

// Register the taxonomy

  register_taxonomy( 'day_of_the_week','pvs_events', array(
    'hierarchical' => false,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'show_in_rest' => true,
    'rewrite' => array( 'slug' => 'day-of-the-week' ),
    'exclude_from_search' => false
    
  ));
}
	
add_action( 'init', 'pvs_events_create_taxonomy', 0 );

/*
 * Get template from theme, if not in theme get template from plugin
*/	

function pvs_include_template_function( $template_path ) {
   
	if ( is_page('regular-events') ) {
	// checks if the file exists in the theme first,
	// otherwise serve the file from the plugin
		if ( $theme_file = locate_template( array ( 'pvs_regular_events-page.php' ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( __FILE__ ) . 'templates/pvs_regular_events-page.php';
			}
	} else if ( is_tax( 'day_of_the_week' ) ) {
		if ( $theme_file = locate_template( array ( 'taxonomy-day_of_the_week.php' ) ) ) {
				$template_path = $theme_file;
			} else {
				$template_path = plugin_dir_path( __FILE__ ) . 'templates/taxonomy-day_of_the_week.php';
			}

	}
   
	return $template_path;
}

add_filter( 'template_include', 'pvs_include_template_function', 1 );

/*
 * Create menu for days
*/

function pvs_day_menu_hook() {
	do_action('pvs_day_menu_hook');
}

function pvs_days_menu() {
	$days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');
	$days_of_week = '';
	$tax = get_queried_object();
	
	foreach( $days as $day ) {	
		if( lcfirst($tax->name) === $day ) {
			$class = 'current';
		} else {
			$class = 'other';
		}
		$days_of_week .= '<li><a class="'
			. $class
			.'" href="' 
			//. get_site_url()
			. get_home_url()
			. '/day-of-the-week/'
			. $day
			. '/">'
			. ucfirst($day)
			. '</a></li>';
	}
	$permalink = get_permalink();
	$days_url = get_site_url() . '/regular-events/';
	if ( $permalink ===  $days_url) {
		$days_class = 'current';
	} else {
		$days_class = 'other';
	}
	$str =  '<div class="days-menu-container">'
		. '<ul class="days-menu container"><li><a class = "'
		. $days_class
		. '" href="' 
		. get_site_url() 
		. '/regular-events/">All Days</a></li>'
		. $days_of_week
		. '</ul></div>';
	echo $str;
}

add_action( 'pvs_day_menu_hook', 'pvs_days_menu', 40 );

/*
 * Order events on taxonomy page by reverse date
*/

add_action( 'pre_get_posts', 'pvs_order_events' );
			function pvs_order_events($wp_query) {
			if ( is_tax('day_of_the_week') ) {
				$wp_query->set( 'order', 'ASC' );
				return $wp_query;
			}
		}


/* 
 * Flush permalinks on plugin activation
*/

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'pvs_posts_flush_rewrites' );
function pvs_posts_flush_rewrites() {
	// call Regular Events registration function
	pvs_register_post_type();
	pvs_events_create_taxonomy();
	flush_rewrite_rules();
}