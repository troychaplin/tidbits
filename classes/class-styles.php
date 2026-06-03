<?php
/**
 * Tidbits style token registry and global CSS output.
 *
 * This class is the single source of truth for the plugin's visual tokens. It
 * drives the global default CSS (this file), and will drive the global settings
 * panel (S2) and the per-block controls (S3). Tokens are emitted as CSS custom
 * properties scoped to `.tidbits` so they cascade to every display mode.
 *
 * @package Tidbits
 */

namespace Tidbits;

/**
 * Registers and outputs Tidbits style tokens.
 */
class Styles extends Plugin_Module {

	/**
	 * CSS custom property prefix.
	 *
	 * @var string
	 */
	const VAR_PREFIX = '--tidbits-';

	/**
	 * Initialize the module.
	 */
	public function init() {
		add_action( 'enqueue_block_assets', array( $this, 'enqueue_global_styles' ) );
	}

	/**
	 * Token registry.
	 *
	 * Each token: label, group, type (color|length|number|select|font-family),
	 * default, and (for selects) options.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public static function get_tokens() {
		return array(

			// Header typography.
			'term-font-family'   => array(
				'label'   => __( 'Header font family', 'tidbits' ),
				'group'   => 'typography',
				'type'    => 'font-family',
				'default' => 'inherit',
			),
			'term-font-size'     => array(
				'label'   => __( 'Header font size', 'tidbits' ),
				'group'   => 'typography',
				'type'    => 'length',
				'default' => '1rem',
			),
			'term-font-weight'   => array(
				'label'   => __( 'Header font weight', 'tidbits' ),
				'group'   => 'typography',
				'type'    => 'number',
				'default' => '600',
			),
			'term-line-height'   => array(
				'label'   => __( 'Header line height', 'tidbits' ),
				'group'   => 'typography',
				'type'    => 'number',
				'default' => '1.4',
			),
			'content-font-size'  => array(
				'label'   => __( 'Content font size', 'tidbits' ),
				'group'   => 'typography',
				'type'    => 'length',
				'default' => 'inherit',
			),

			// Colors & states.
			'term-color'         => array(
				'label'   => __( 'Header text color', 'tidbits' ),
				'group'   => 'colors',
				'type'    => 'color',
				'default' => 'inherit',
			),
			'term-color-hover'   => array(
				'label'   => __( 'Header hover color', 'tidbits' ),
				'group'   => 'colors',
				'type'    => 'color',
				'default' => 'inherit',
			),
			'header-bg'          => array(
				'label'   => __( 'Header background', 'tidbits' ),
				'group'   => 'colors',
				'type'    => 'color',
				'default' => 'transparent',
			),
			'panel-bg'           => array(
				'label'   => __( 'Panel background', 'tidbits' ),
				'group'   => 'colors',
				'type'    => 'color',
				'default' => 'transparent',
			),
			'content-color'      => array(
				'label'   => __( 'Content text color', 'tidbits' ),
				'group'   => 'colors',
				'type'    => 'color',
				'default' => 'inherit',
			),
			'focus-color'        => array(
				'label'   => __( 'Focus ring color', 'tidbits' ),
				'group'   => 'colors',
				'type'    => 'color',
				'default' => 'currentcolor',
			),

			// Spacing & dividers.
			'padding-block'      => array(
				'label'   => __( 'Morsel vertical padding', 'tidbits' ),
				'group'   => 'spacing',
				'type'    => 'length',
				'default' => '0.75rem',
			),
			'divider-color'      => array(
				'label'   => __( 'Divider color', 'tidbits' ),
				'group'   => 'spacing',
				'type'    => 'color',
				'default' => '#e5e5e5',
			),
			'divider-width'      => array(
				'label'   => __( 'Divider width', 'tidbits' ),
				'group'   => 'spacing',
				'type'    => 'length',
				'default' => '1px',
			),
			'divider-style'      => array(
				'label'   => __( 'Divider style', 'tidbits' ),
				'group'   => 'spacing',
				'type'    => 'select',
				'default' => 'solid',
				'options' => array( 'solid', 'dashed', 'dotted', 'none' ),
			),
			'panel-inset'        => array(
				'label'   => __( 'Panel content padding', 'tidbits' ),
				'group'   => 'spacing',
				'type'    => 'length',
				'default' => '0',
			),
			'columns-term-width' => array(
				'label'   => __( 'Columns term width', 'tidbits' ),
				'group'   => 'spacing',
				'type'    => 'length',
				'default' => '260px',
			),
			'columns-gap'        => array(
				'label'   => __( 'Columns gap', 'tidbits' ),
				'group'   => 'spacing',
				'type'    => 'length',
				'default' => '1.5rem',
			),
			'trigger-gap'        => array(
				'label'   => __( 'Title/icon gap', 'tidbits' ),
				'group'   => 'spacing',
				'type'    => 'length',
				'default' => '0.5rem',
			),

			// Icon.
			'icon-color'         => array(
				'label'   => __( 'Icon color', 'tidbits' ),
				'group'   => 'icon',
				'type'    => 'color',
				'default' => '#808080',
			),
			'icon-size'          => array(
				'label'   => __( 'Icon size', 'tidbits' ),
				'group'   => 'icon',
				'type'    => 'length',
				'default' => '20px',
			),
			'accordion-speed'    => array(
				'label'   => __( 'Animation speed', 'tidbits' ),
				'group'   => 'icon',
				'type'    => 'length',
				'default' => '0.3s',
			),
		);
	}

	/**
	 * Map of token key => default value.
	 *
	 * @return array<string,string>
	 */
	public static function get_defaults() {
		$defaults = array();
		foreach ( self::get_tokens() as $key => $token ) {
			$defaults[ $key ] = $token['default'];
		}
		return $defaults;
	}

	/**
	 * Build the global `.tidbits { --token: value }` CSS.
	 *
	 * S1 emits defaults only. S2 will pass saved global overrides here.
	 *
	 * @param array<string,string> $overrides Optional token key => value map.
	 * @return string CSS rule, or empty string if no declarations.
	 */
	public static function get_global_css( array $overrides = array() ) {
		$values       = array_merge( self::get_defaults(), $overrides );
		$declarations = '';

		foreach ( self::get_tokens() as $key => $token ) {
			if ( ! isset( $values[ $key ] ) || '' === $values[ $key ] ) {
				continue;
			}
			$declarations .= self::VAR_PREFIX . $key . ':' . $values[ $key ] . ';';
		}

		return '' === $declarations ? '' : '.tidbits{' . $declarations . '}';
	}

	/**
	 * Output the global token CSS for both the front end and the editor canvas.
	 *
	 * Fired on `enqueue_block_assets`, which runs in both contexts.
	 *
	 * - Front end: attach to the block's conditional style handle so the tokens
	 *   only load when the block is actually present.
	 * - Editor: inline styles on the block style handle don't reliably reach the
	 *   block-editor iframe, so enqueue a dedicated handle there instead.
	 */
	public function enqueue_global_styles() {
		$css = self::get_global_css();

		if ( '' === $css ) {
			return;
		}

		if ( is_admin() ) {
			wp_register_style( 'tidbits-tokens', false, array(), TIDBITS_VERSION );
			wp_enqueue_style( 'tidbits-tokens' );
			wp_add_inline_style( 'tidbits-tokens', $css );
			return;
		}

		$block_type = \WP_Block_Type_Registry::get_instance()->get_registered( 'tidbits/tidbit' );

		if ( $block_type && ! empty( $block_type->style_handles ) ) {
			wp_add_inline_style( $block_type->style_handles[0], $css );
		}
	}
}
