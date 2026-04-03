<?php
/**
 * Render the FAQ Select block on the front end.
 *
 * @param array    $attributes The block attributes.
 * @param string   $content    The rendered inner blocks.
 * @param WP_Block $block      The block instance.
 *
 * @package IDC_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( trim( $content ) ) ) {
	return;
}

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Inner block output.
echo $content;
