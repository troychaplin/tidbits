<?php
/**
 * Plugin Name:       Tidbits
 * Description:       Flexible blocks for organizing and displaying bite-sized paired content.
 * Requires at least: 6.6
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Troy Chaplin
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tidbits
 *
 * @package Tidbits
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Composer autoload.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

if ( ! class_exists( Tidbits\Plugin_Module::class ) ) {
	add_action(
		'admin_notices',
		function () {
			echo '<div class="notice notice-error"><p>';
			echo esc_html__( 'Tidbits: Composer autoload not found. Run `composer install`.', 'tidbits' );
			echo '</p></div>';
		}
	);
	return;
}

// Instantiate modules.
$tidbits_modules = array(
	new Tidbits\Register_Blocks( __DIR__ . '/build' ),
	new Tidbits\Register_Tidbit_Post_Type(),
	new Tidbits\Register_Flavour_Taxonomy(),
);

foreach ( $tidbits_modules as $tidbits_module ) {
	$tidbits_module->init();
}
