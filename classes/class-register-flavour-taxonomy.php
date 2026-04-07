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
	 * Taxonomy slug (database key — never changes).
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
		$settings  = Settings_Page::get();
		$singular  = $settings['taxonomy_singular'];
		$plural    = $settings['taxonomy_plural'];
		$is_public = $settings['taxonomy_public'];

		$labels = array(
			'name'                       => $plural,
			'singular_name'              => $singular,
			'menu_name'                  => $plural,
			/* translators: %s: plural taxonomy name */
			'all_items'                  => sprintf( __( 'All %s', 'tidbits' ), $plural ),
			/* translators: %s: singular taxonomy name */
			'edit_item'                  => sprintf( __( 'Edit %s', 'tidbits' ), $singular ),
			/* translators: %s: singular taxonomy name */
			'view_item'                  => sprintf( __( 'View %s', 'tidbits' ), $singular ),
			/* translators: %s: singular taxonomy name */
			'update_item'                => sprintf( __( 'Update %s', 'tidbits' ), $singular ),
			/* translators: %s: singular taxonomy name */
			'add_new_item'               => sprintf( __( 'Add New %s', 'tidbits' ), $singular ),
			/* translators: %s: singular taxonomy name */
			'new_item_name'              => sprintf( __( 'New %s Name', 'tidbits' ), $singular ),
			/* translators: %s: plural taxonomy name */
			'search_items'               => sprintf( __( 'Search %s', 'tidbits' ), $plural ),
			/* translators: %s: plural taxonomy name */
			'not_found'                  => sprintf( __( 'No %s found.', 'tidbits' ), $plural ),
			/* translators: %s: plural taxonomy name */
			'no_terms'                   => sprintf( __( 'No %s', 'tidbits' ), $plural ),
			/* translators: %s: singular taxonomy name */
			'filter_by_item'             => sprintf( __( 'Filter by %s', 'tidbits' ), $singular ),
			/* translators: %s: plural taxonomy name */
			'items_list_navigation'      => sprintf( __( '%s list navigation', 'tidbits' ), $plural ),
			/* translators: %s: plural taxonomy name */
			'items_list'                 => sprintf( __( '%s list', 'tidbits' ), $plural ),
			/* translators: %s: plural taxonomy name */
			'back_to_items'              => sprintf( __( '&larr; Back to %s', 'tidbits' ), $plural ),
			/* translators: %s: singular taxonomy name */
			'item_link'                  => sprintf( __( '%s Link', 'tidbits' ), $singular ),
			/* translators: %s: singular taxonomy name */
			'item_link_description'      => sprintf( __( 'A link to a %s', 'tidbits' ), $singular ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => $is_public,
			'publicly_queryable' => $is_public,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'show_admin_column'  => true,
			'hierarchical'       => true,
			'rewrite'            => $is_public
				? array( 'slug' => $settings['taxonomy_slug'] )
				: false,
		);

		register_taxonomy( self::TAXONOMY, Register_Tidbit_Post_Type::POST_TYPE, $args );
	}
}
