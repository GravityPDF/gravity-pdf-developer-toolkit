<?php

namespace GFPDF\Plugins\DeveloperToolkit\Factory;

use GFPDF\Plugins\DeveloperToolkit\Cli\Commands\BulkSavePdf;
use GFPDF\Plugins\DeveloperToolkit\Cli\Commands\Cli;
use GFPDF\Plugins\DeveloperToolkit\Cli\Commands\SavePdf;
use GFPDF\Plugins\DeveloperToolkit\Zip\Zip;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use GPDFAPI;

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
 * @package GFPDF\Plugins\DeveloperToolkit\Factory
 *
 * @since   1.0
 */
class FactoryCommandBulkSavePdf {

	/**
	 * Initialise the BulkSavePDF Command
	 *
	 * @return BulkSavePdf
	 *
	 * @since 1.0
	 */
	public static function build() {
		$data       = \GPDFAPI::get_data_class();
		$cli        = new Cli();
		$filesystem = new Filesystem( new Local( $data->template_tmp_location ) );

		$command = new BulkSavePdf(
			$cli,
			new SavePdf( $cli ),
			GPDFAPI::get_mvc_class( 'Model_PDF' ),
			GPDFAPI::get_form_class(),
			$filesystem,
			new Zip()
		);

		return $command;
	}
}
