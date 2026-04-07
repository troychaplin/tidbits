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
	 * Post type slug (database key — never changes).
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
		$settings = Settings_Page::get();
		$singular = $settings['post_type_singular'];
		$plural   = $settings['post_type_plural'];
		$is_public = $settings['post_type_public'];

		$labels = array(
			'name'                  => $plural,
			'singular_name'         => $singular,
			'menu_name'             => $plural,
			'name_admin_bar'        => $singular,
			'add_new'               => __( 'Add New', 'tidbits' ),
			/* translators: %s: singular post type name */
			'add_new_item'          => sprintf( __( 'Add New %s', 'tidbits' ), $singular ),
			/* translators: %s: singular post type name */
			'new_item'              => sprintf( __( 'New %s', 'tidbits' ), $singular ),
			/* translators: %s: singular post type name */
			'edit_item'             => sprintf( __( 'Edit %s', 'tidbits' ), $singular ),
			/* translators: %s: singular post type name */
			'view_item'             => sprintf( __( 'View %s', 'tidbits' ), $singular ),
			/* translators: %s: plural post type name */
			'all_items'             => sprintf( __( 'All %s', 'tidbits' ), $plural ),
			/* translators: %s: plural post type name */
			'search_items'          => sprintf( __( 'Search %s', 'tidbits' ), $plural ),
			/* translators: %s: plural post type name */
			'not_found'             => sprintf( __( 'No %s found.', 'tidbits' ), $plural ),
			/* translators: %s: plural post type name */
			'not_found_in_trash'    => sprintf( __( 'No %s found in Trash.', 'tidbits' ), $plural ),
			/* translators: %s: plural post type name */
			'filter_items_list'     => sprintf( __( 'Filter %s list', 'tidbits' ), $plural ),
			/* translators: %s: plural post type name */
			'items_list_navigation' => sprintf( __( '%s list navigation', 'tidbits' ), $plural ),
			/* translators: %s: plural post type name */
			'items_list'            => sprintf( __( '%s list', 'tidbits' ), $plural ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => $is_public,
			'publicly_queryable' => $is_public,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'menu_position'      => 25,
			'menu_icon'          => 'dashicons-image-filter',
			'capability_type'    => 'post',
			'has_archive'        => $is_public,
			'hierarchical'       => false,
			'supports'           => array( 'title', 'editor', 'revisions' ),
			'rewrite'            => $is_public
				? array( 'slug' => $settings['post_type_slug'] )
				: false,
		);

		register_post_type( self::POST_TYPE, $args );
	}
}
