<?php
/**
 * Register the Flavour taxonomy for Tidbits.
 *
 * @package Tidbits
 */

namespace Tidbits;

/**
 * Registers the `flavour` taxonomy attached to the `tidbit` post type.
 */
class Register_Flavour_Taxonomy extends Plugin_Module {

	/**
	 * Taxonomy slug.
	 *
	 * @var string
	 */
	const TAXONOMY = 'flavour';

	/**
	 * Initialize the module.
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_taxonomy' ) );
	}

	/**
	 * Register the Flavour taxonomy.
	 */
	public function register_taxonomy() {
		$labels = array(
			'name'                       => _x( 'Flavours', 'Taxonomy general name', 'tidbits' ),
			'singular_name'              => _x( 'Flavour', 'Taxonomy singular name', 'tidbits' ),
			'menu_name'                  => _x( 'Flavours', 'Admin Menu text', 'tidbits' ),
			'all_items'                  => __( 'All Flavours', 'tidbits' ),
			'edit_item'                  => __( 'Edit Flavour', 'tidbits' ),
			'view_item'                  => __( 'View Flavour', 'tidbits' ),
			'update_item'                => __( 'Update Flavour', 'tidbits' ),
			'add_new_item'               => __( 'Add New Flavour', 'tidbits' ),
			'new_item_name'              => __( 'New Flavour Name', 'tidbits' ),
			'search_items'               => __( 'Search Flavours', 'tidbits' ),
			'not_found'                  => __( 'No Flavours found.', 'tidbits' ),
			'no_terms'                   => __( 'No Flavours', 'tidbits' ),
			'filter_by_item'             => __( 'Filter by Flavour', 'tidbits' ),
			'items_list_navigation'      => __( 'Flavours list navigation', 'tidbits' ),
			'items_list'                 => __( 'Flavours list', 'tidbits' ),
			'back_to_items'              => __( '&larr; Back to Flavours', 'tidbits' ),
			'item_link'                  => __( 'Flavour Link', 'tidbits' ),
			'item_link_description'      => __( 'A link to a Flavour', 'tidbits' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'show_admin_column'  => true,
			'hierarchical'       => true,
			'rewrite'            => false,
		);

		register_taxonomy( self::TAXONOMY, Register_Tidbit_Post_Type::POST_TYPE, $args );
	}
}
