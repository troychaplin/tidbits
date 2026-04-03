<?php
/**
 * Base class for plugin modules which can be initialized.
 *
 * @package Tidbits
 */

namespace Tidbits;

/**
 * Plugin module extended by other classes.
 */
abstract class Plugin_Module {

	/**
	 * Initialize the module by registering hooks.
	 */
	abstract public function init();
}
