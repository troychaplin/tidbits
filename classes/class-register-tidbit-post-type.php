<?php
/**
 * Register the Tidbit custom post type.
 *
 * @package Tidbits
 */

namespace Tidbits;

/**
 * Registers the `tidbit` post type with WordPress.
 */
class Register_Tidbit_Post_Type extends Plugin_Module {

	/**
	 * Post type slug.
	 *
	 * @var string
	 */
	const POST_TYPE = 'tidbit';

	/**
	 * Initialize the module.
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_post_type' ) );
	}

	/**
	 * Register the Tidbit post type.
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Tidbits', 'Post type general name', 'tidbits' ),
			'singular_name'         => _x( 'Tidbit', 'Post type singular name', 'tidbits' ),
			'menu_name'             => _x( 'Tidbits', 'Admin Menu text', 'tidbits' ),
			'name_admin_bar'        => _x( 'Tidbit', 'Add New on Toolbar', 'tidbits' ),
			'add_new'               => __( 'Add New', 'tidbits' ),
			'add_new_item'          => __( 'Add New Tidbit', 'tidbits' ),
			'new_item'              => __( 'New Tidbit', 'tidbits' ),
			'edit_item'             => __( 'Edit Tidbit', 'tidbits' ),
			'view_item'             => __( 'View Tidbit', 'tidbits' ),
			'all_items'             => __( 'All Tidbits', 'tidbits' ),
			'search_items'          => __( 'Search Tidbits', 'tidbits' ),
			'not_found'             => __( 'No Tidbits found.', 'tidbits' ),
			'not_found_in_trash'    => __( 'No Tidbits found in Trash.', 'tidbits' ),
			'filter_items_list'     => __( 'Filter Tidbits list', 'tidbits' ),
			'items_list_navigation' => __( 'Tidbits list navigation', 'tidbits' ),
			'items_list'            => __( 'Tidbits list', 'tidbits' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'menu_position'      => 25,
			'menu_icon'          => 'dashicons-image-filter',
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'supports'           => array( 'title', 'editor', 'revisions' ),
			'rewrite'            => false,
		);

		register_post_type( self::POST_TYPE, $args );
	}
}
