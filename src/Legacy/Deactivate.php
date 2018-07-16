<?php

namespace GFPDF\Plugins\DeveloperToolkit\Legacy;

use GFPDF\Helper\Helper_Interface_Actions;
use GFPDF\Helper\Helper_Abstract_Options;

/**
 * @package     Gravity PDF Developer Toolkit
 * @copyright   Copyright (c) 2018, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
	This file is part of Gravity PDF Developer Toolkit.

	Copyright (c) 2018, Blue Liquid Designs

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

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
