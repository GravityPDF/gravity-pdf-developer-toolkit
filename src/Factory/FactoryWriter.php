<?php

namespace GFPDF\Plugins\DeveloperToolkit\Factory;

use GFPDF\Plugins\DeveloperToolkit\Writer\Writer;
use GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Import;
use GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Styles;
use GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Single;
use GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Multi;
use GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Tick;
use GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Html;
use GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Ellipse;

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
 * @package GFPDF\Plugins\DeveloperToolkit\Factory
 *
 * @since   1.0
 */
class FactoryWriter {

	/**
	 * Initialise our PDF Writer object and inject all required writer classes automatically
	 *
	 * @return Writer
	 *
	 * @since 1.0
	 */
	public static function build() {
		$writer = new Writer(
			[
				new Import(),
				new Styles(),
				new Single(),
				new Multi(),
				new Tick(),
				new Html(),
				new Ellipse(),
			]
		);

		return $writer;
	}
}
