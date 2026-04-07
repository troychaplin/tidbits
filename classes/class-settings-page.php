<?php
/**
 * Settings page for the Tidbits plugin.
 *
 * @package Tidbits
 */

namespace Tidbits;

/**
 * Adds a settings page under the Tidbits admin menu.
 */
class Settings_Page extends Plugin_Module {

	/**
	 * Option key used in wp_options.
	 *
	 * @var string
	 */
	const OPTION_KEY = 'tidbits_settings';

	/**
	 * Initialize the module.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Return default settings.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			'post_type_singular' => 'Tidbit',
			'post_type_plural'   => 'Tidbits',
			'post_type_slug'     => 'tidbit',
			'post_type_public'   => false,
			'taxonomy_singular'  => 'Flavour',
			'taxonomy_plural'    => 'Flavours',
			'taxonomy_slug'      => 'flavour',
			'taxonomy_public'    => false,
		);
	}

	/**
	 * Get the current settings merged with defaults.
	 *
	 * @return array
	 */
	public static function get() {
		$saved = get_option( self::OPTION_KEY, array() );
		return wp_parse_args( $saved, self::get_defaults() );
	}

	/**
	 * Add the settings submenu page.
	 */
	public function add_menu_page() {
		add_submenu_page(
			'edit.php?post_type=' . Register_Tidbit_Post_Type::POST_TYPE,
			__( 'Tidbits Settings', 'tidbits' ),
			__( 'Settings', 'tidbits' ),
			'manage_options',
			'tidbits-settings',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Register settings, sections, and fields.
	 */
	public function register_settings() {
		register_setting(
			'tidbits_settings_group',
			self::OPTION_KEY,
			array(
				'sanitize_callback' => array( $this, 'sanitize' ),
			)
		);

		// Post type section.
		add_settings_section(
			'tidbits_post_type_section',
			__( 'Post Type', 'tidbits' ),
			'__return_null',
			'tidbits-settings'
		);

		add_settings_field(
			'post_type_singular',
			__( 'Singular Name', 'tidbits' ),
			array( $this, 'render_text_field' ),
			'tidbits-settings',
			'tidbits_post_type_section',
			array(
				'field' => 'post_type_singular',
				'label' => __( 'e.g. Tidbit, FAQ, Term', 'tidbits' ),
			)
		);

		add_settings_field(
			'post_type_plural',
			__( 'Plural Name', 'tidbits' ),
			array( $this, 'render_text_field' ),
			'tidbits-settings',
			'tidbits_post_type_section',
			array(
				'field' => 'post_type_plural',
				'label' => __( 'e.g. Tidbits, FAQs, Terms', 'tidbits' ),
			)
		);

		add_settings_field(
			'post_type_slug',
			__( 'URL Slug', 'tidbits' ),
			array( $this, 'render_text_field' ),
			'tidbits-settings',
			'tidbits_post_type_section',
			array(
				'field' => 'post_type_slug',
				'label' => __( 'Used in URLs when public is enabled', 'tidbits' ),
			)
		);

		add_settings_field(
			'post_type_public',
			__( 'Public', 'tidbits' ),
			array( $this, 'render_checkbox_field' ),
			'tidbits-settings',
			'tidbits_post_type_section',
			array(
				'field' => 'post_type_public',
				'label' => __( 'Enable frontend URLs and archives', 'tidbits' ),
			)
		);

		// Taxonomy section.
		add_settings_section(
			'tidbits_taxonomy_section',
			__( 'Taxonomy', 'tidbits' ),
			'__return_null',
			'tidbits-settings'
		);

		add_settings_field(
			'taxonomy_singular',
			__( 'Singular Name', 'tidbits' ),
			array( $this, 'render_text_field' ),
			'tidbits-settings',
			'tidbits_taxonomy_section',
			array(
				'field' => 'taxonomy_singular',
				'label' => __( 'e.g. Flavour, Category, Topic', 'tidbits' ),
			)
		);

		add_settings_field(
			'taxonomy_plural',
			__( 'Plural Name', 'tidbits' ),
			array( $this, 'render_text_field' ),
			'tidbits-settings',
			'tidbits_taxonomy_section',
			array(
				'field' => 'taxonomy_plural',
				'label' => __( 'e.g. Flavours, Categories, Topics', 'tidbits' ),
			)
		);

		add_settings_field(
			'taxonomy_slug',
			__( 'URL Slug', 'tidbits' ),
			array( $this, 'render_text_field' ),
			'tidbits-settings',
			'tidbits_taxonomy_section',
			array(
				'field' => 'taxonomy_slug',
				'label' => __( 'Used in URLs when public is enabled', 'tidbits' ),
			)
		);

		add_settings_field(
			'taxonomy_public',
			__( 'Public', 'tidbits' ),
			array( $this, 'render_checkbox_field' ),
			'tidbits-settings',
			'tidbits_taxonomy_section',
			array(
				'field' => 'taxonomy_public',
				'label' => __( 'Enable frontend URLs and archives', 'tidbits' ),
			)
		);
	}

	/**
	 * Sanitize settings on save.
	 *
	 * @param array $input Raw input from the form.
	 * @return array Sanitized settings.
	 */
	public function sanitize( $input ) {
		$defaults  = self::get_defaults();
		$sanitized = array();

		$sanitized['post_type_singular'] = ! empty( $input['post_type_singular'] )
			? sanitize_text_field( $input['post_type_singular'] )
			: $defaults['post_type_singular'];

		$sanitized['post_type_plural'] = ! empty( $input['post_type_plural'] )
			? sanitize_text_field( $input['post_type_plural'] )
			: $defaults['post_type_plural'];

		$sanitized['post_type_slug'] = ! empty( $input['post_type_slug'] )
			? sanitize_title( $input['post_type_slug'] )
			: $defaults['post_type_slug'];

		$sanitized['post_type_public'] = ! empty( $input['post_type_public'] );

		$sanitized['taxonomy_singular'] = ! empty( $input['taxonomy_singular'] )
			? sanitize_text_field( $input['taxonomy_singular'] )
			: $defaults['taxonomy_singular'];

		$sanitized['taxonomy_plural'] = ! empty( $input['taxonomy_plural'] )
			? sanitize_text_field( $input['taxonomy_plural'] )
			: $defaults['taxonomy_plural'];

		$sanitized['taxonomy_slug'] = ! empty( $input['taxonomy_slug'] )
			? sanitize_title( $input['taxonomy_slug'] )
			: $defaults['taxonomy_slug'];

		$sanitized['taxonomy_public'] = ! empty( $input['taxonomy_public'] );

		// Flush rewrite rules since slug or public may have changed.
		flush_rewrite_rules();

		return $sanitized;
	}

	/**
	 * Render the settings page.
	 */
	public function render_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'tidbits_settings_group' );
				do_settings_sections( 'tidbits-settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render a text input field.
	 *
	 * @param array $args Field arguments.
	 */
	public function render_text_field( $args ) {
		$settings = self::get();
		$field    = $args['field'];
		$value    = $settings[ $field ] ?? '';
		?>
		<input
			type="text"
			id="<?php echo esc_attr( $field ); ?>"
			name="<?php echo esc_attr( self::OPTION_KEY . '[' . $field . ']' ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			class="regular-text"
		/>
		<?php if ( ! empty( $args['label'] ) ) : ?>
			<p class="description"><?php echo esc_html( $args['label'] ); ?></p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render a checkbox field.
	 *
	 * @param array $args Field arguments.
	 */
	public function render_checkbox_field( $args ) {
		$settings = self::get();
		$field    = $args['field'];
		$checked  = ! empty( $settings[ $field ] );
		?>
		<label for="<?php echo esc_attr( $field ); ?>">
			<input
				type="checkbox"
				id="<?php echo esc_attr( $field ); ?>"
				name="<?php echo esc_attr( self::OPTION_KEY . '[' . $field . ']' ); ?>"
				value="1"
				<?php checked( $checked ); ?>
			/>
			<?php echo esc_html( $args['label'] ); ?>
		</label>
		<?php
	}
}
