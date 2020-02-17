<?php

namespace GFPDF\Plugins\DeveloperToolkit\Cli;

use GFPDF\Plugins\DeveloperToolkit\Cli\Commands\Cli;
use GFPDF\Plugins\DeveloperToolkit\Cli\Commands\CreateTemplate;
use GFPDF\Plugins\DeveloperToolkit\Cli\Commands\GetPdfStatus;
use WP_CLI;

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
 * Registers our WP CLI Commands
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Cli
 */
class Register {

	/**
	 * Register our WP CLI Commands
	 *
	 * @since 1.0
	 */
	public function init() {
		global $gfpdf;

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::add_command( 'gpdf', new GetPdfStatus( $gfpdf->data, $gfpdf->options, new Cli() ) );
			WP_CLI::add_command( 'gpdf create-template', new CreateTemplate( $gfpdf->data->template_location, new Cli() ) );
		}
	}
}
