<?php

namespace GFPDF\Plugins\DeveloperToolkit\Cli\Commands;

use GFPDF\Helper\Helper_Abstract_Options;
use GFPDF\Helper\Helper_Data;

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
	 * : Accepted values: table, json, csv, yaml. Defaults to table.
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
		if ( isset( $assocArgs['format'] ) && in_array( $assocArgs['format'], [ 'csv', 'json', 'yaml' ], true ) ) {
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
			$settingKey => __( 'Temporary Directory Path', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => $this->data->template_tmp_location,
		];

		$data[] = [
			$settingKey => __( 'Mpdf Temporary Directory Path', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => $this->data->mpdf_tmp_location,
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
			$valueKey   => isset( $settings['default_template'] ) ? $settings['default_template'] : 'zadani',
		];

		$data[] = [
			$settingKey => __( 'Default Font', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => isset( $settings['default_font'] ) ? $settings['default_font'] : 'dejavusanscondensed',
		];

		$data[] = [
			$settingKey => __( 'Default Font Size', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => isset( $settings['default_font_size'] ) ? $settings['default_font_size'] : '10',
		];

		$data[] = [
			$settingKey => __( 'Default RTL', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => isset( $settings['default_rtl'] ) ? $settings['default_rtl'] : __( 'No', 'gravity-forms-pdf-extended' ),
		];

		$data[] = [
			$settingKey => __( 'Default PDF Action', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => isset( $settings['default_action'] ) ? $settings['default_action'] : 'view',
		];

		$data[] = [
			$settingKey => __( 'Debug Mode', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => isset( $settings['debug_mode'] ) ? $settings['debug_mode'] : __( 'No', 'gravity-forms-pdf-extended' ),
		];

		$data[] = [
			$settingKey => __( 'PDF Admin Capabilities', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => implode( ', ', isset( $settings['admin_capabilities'] ) ? $settings['admin_capabilities'] : [] ),
		];

		$data[] = [
			$settingKey => __( 'Default Restrict Current Owner', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => isset( $settings['default_restrict_owner'] ) ? $settings['default_restrict_owner'] : __( 'No', 'gravity-forms-pdf-extended' ),
		];

		$data[] = [
			$settingKey => __( 'Logged Out Timeout', 'gravity-pdf-developer-toolkit' ),
			$valueKey   => isset( $settings['logged_out_timeout'] ) ? $settings['logged_out_timeout'] : '20',
		];

		if (
			isset( $settings['custom_fonts'] ) &&
			is_array( $settings['custom_fonts'] ) &&
			count( $settings['custom_fonts'] ) > 0
		) {
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
