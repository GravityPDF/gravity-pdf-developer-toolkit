<?php

namespace GFPDF\Plugins\DeveloperToolkit\Legacy;

use GFPDF\Helper\Helper_Interface_Actions;
use GFPDF\Helper\Helper_Abstract_Options;

/**
 * @package     Gravity PDF Developer Toolkit
 * @copyright   Copyright (c) 2020, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Detects if the Gravity PDF Tier 2 plugin is enabled and deactivates it automatically.
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Legacy
 *
 * @since   1.0
 */
class Deactivate implements Helper_Interface_Actions {

	/**
	 * @var Helper_Abstract_Options
	 * @since 1.0
	 */
	protected $options;

	/**
	 * Deactivate constructor.
	 *
	 * @param Helper_Abstract_Options $options
	 *
	 * @since 1.0
	 */
	public function __construct( Helper_Abstract_Options $options ) {
		$this->options = $options;
	}

	/**
	 * Initialise class
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function init() {
		$this->add_actions();
	}

	/**
	 * Add WordPress actions
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function add_actions() {
		add_action( 'admin_init', [ $this, 'maybeDeactivateLegacyPlugin' ] );
	}

	/**
	 * Deactivate the Gravity PDF Tier 2 plugin (if it exists)
	 *
	 * The Gravity PDF Developer Toolkit is a drop-in replacement for the Gravity PDF Tier 2 plugin.
	 *
	 * Triggered via the `admin_init` action.
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function maybeDeactivateLegacyPlugin() {
		$legacy_plugin = 'gravity-pdf-tier-2/plus.php';

		if ( is_plugin_active( $legacy_plugin ) ) {
			/* Store a global setting to say we've upgraded from a legacy plugin */
			$settings                        = $this->options->get_settings();
			$settings['advanced_templating'] = true;
			$this->options->update_settings( $settings );

			deactivate_plugins( 'gravity-pdf-tier-2/plus.php' );
		}
	}

}
