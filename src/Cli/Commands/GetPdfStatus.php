<?php

namespace GFPDF\Plugins\DeveloperToolkit\Cli\Commands;

use GFPDF\Helper\Helper_Abstract_Options;
use GFPDF\Helper\Helper_Data;

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
 * The Gravity PDF CLI Commands
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Cli\Commands
 */
class GetPdfStatus {

	/**
	 * @var Helper_Data
	 */
	protected $data;

	/**
	 * @var Helper_Abstract_Options
	 */
	protected $options;

	/**
	 * @var \WP_CLI
	 */
	protected $cli;

	/**
	 * @param Helper_Data  $data The Gravity PDF data object
	 * @param InterfaceCli $cli  The WP_CLI class, or a suitable drop-in replacement (for testing)
	 *
	 * @since 1.0
	 */
	public function __construct( Helper_Data $data, Helper_Abstract_Options $options, InterfaceCli $cli ) {
		$this->data    = $data;
		$this->options = $options;
		$this->cli     = $cli;
	}

	/**
	 * Get the current Gravity PDF Version
	 *
	 * ## EXAMPLES
	 *
	 *     # Get current Gravity PDF version
	 *     $ wp gpdf version
	 *
	 * @since 1.0
	 */
	public function version() {
		$this->cli->log( PDF_EXTENDED_VERSION );
	}

	/**
	 * Get the Gravity PDF System Status
	 *
	 * ## OPTIONS
	 *
	 * [--format=<format>]
	 * : What format to output the system status
	 * ---
	 * default: table
	 * options:
	 *     - table
	 *     - json
	 *     - csv
	 *     - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     # Get current Gravity PDF Status in Tabular Format
	 *     $ wp gpdf status
	 *
	 *     # Get current Gravity PDF Status in JSON format
	 *     $ wp gpdf status --format=json
	 *
	 *     # Get current Gravity PDF Status in YAML format
	 *     $ wp gpdf status --format=yaml
	 *
	 *     # Get current Gravity PDF Status in CSV format
	 *     $ wp gpdf status --format=csv
	 *
	 * @param array $args      No standard arguments used
	 * @param array $assocArgs The additional arguments passed to the cli. May include `format`
	 *
	 * @since 1.0
	 */
	public function status( $args, $assocArgs ) {
		/* Get the desired format */
		$format = 'table';
		if ( isset( $assocArgs['format'] ) && in_array( $assocArgs['format'], [ 'csv', 'json', 'yaml' ] ) ) {
			$format = $assocArgs['format'];
		}

		$this->cli->outputInFormat(
			$format,
			$this->getStatusData(),
			[
				__( 'Setting', 'gravity-pdf-developer-toolkit' ),
				__( 'Value', 'gravity-pdf-developer-toolkit' ),
			]
		);
	}

	/**
	 * Get the PDF Status data
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	protected function getStatusData() {
		$settings = $this->options->get_settings();

		$settingKey = __( 'Setting', 'gravity-pdf-developer-toolkit' );
		$valueKey   = __( 'Value', 'gravity-pdf-developer-toolkit' );

		/* Prepare the data for display */
		$data = [];

		$data[] = [
			$settingKey => __( 'Version', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => PDF_EXTENDED_VERSION,
		];

		$data[] = [
			$settingKey => __( 'Working Directory Folder', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => $this->data->working_folder,
		];

		$data[] = [
			$settingKey => __( 'Working Directory Path', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => $this->data->template_location,
		];

		if ( is_multisite() ) {
			$data[] = [
				$settingKey => __( 'Subsite Working Directory', 'gravity-pdf-developer-toolkit' ),
				$valueKey   => $this->data->multisite_template_location,
			];
		}

		$data[] = [
			$settingKey => __( 'Font Directory Path', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => $this->data->template_font_location,
		];

		$data[] = [
			$settingKey => __( 'Font Data Directory Path', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => $this->data->template_fontdata_location,
		];

		$data[] = [
			$settingKey => __( 'Temporary Directory Path', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => $this->data->template_tmp_location,
		];

		$data[] = [
			$settingKey => __( 'Memory Limit', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => $this->data->memory_limit,
		];

		if ( count( $this->data->addon ) > 0 ) {
			$addons = [];
			foreach ( $this->data->addon as $addon ) {
				$addons[] = $addon->get_name();
			}

			$data[] = [
				$settingKey => __( 'Installed Extensions', 'gravity-pdf-developer-toolkit' ),
				$valueKey   => implode( ', ', $addons ),
			];
		}

		$data[] = [
			$settingKey => __( 'Default Template', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => $settings['default_template'],
		];

		$data[] = [
			$settingKey => __( 'Default Font', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => $settings['default_font'],
		];

		$data[] = [
			$settingKey => __( 'Default Font Size', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => $settings['default_font_size'],
		];

		$data[] = [
			$settingKey => __( 'Default RTL', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => $settings['default_rtl'],
		];

		$data[] = [
			$settingKey => __( 'Default PDF Action', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => $settings['default_action'],
		];

		$data[] = [
			$settingKey => __( 'Shortcode Debugging', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => ( $settings['shortcode_debug_messages'] === 'Yes' ) ? 'Enabled' : 'Disabled',
		];

		$data[] = [
			$settingKey => __( 'PDF Admin Capabilities', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => implode( ', ', $settings['admin_capabilities'] ),
		];

		$data[] = [
			$settingKey => __( 'Default Restrict Current Owner', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => $settings['default_restrict_owner'],
		];

		$data[] = [
			$settingKey => __( 'Logged Out Timeout', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => $settings['logged_out_timeout'],
		];

		if ( is_array( $settings['custom_fonts'] ) && count( $settings['custom_fonts'] ) > 0 ) {
			$fonts = [];
			foreach ( $settings['custom_fonts'] as $font ) {
				$fonts[] = $font['font_name'];
			}

			$data[] = [
				$settingKey => __( 'Installed Fonts', 'gravity-pdf-developer-toolkit' ),
				$valueKey   => implode( ', ', $fonts ),
			];
		}

		return $data;
	}
}
