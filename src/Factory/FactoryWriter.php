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
 * Class FactoryWriter
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Factory
 *
 * @since   1.0
 */
class FactoryWriter {

	/**
	 * Build our PDF Writer object and inject all required writer classes
	 *
	 * @return Writer
	 *
	 * @since 1.0
	 */
	public static function get() {
		$writer = new Writer( [
			new Import(),
			new Styles(),
			new Single(),
			new Multi(),
			new Tick(),
			new Html(),
			new Ellipse(),
		] );

		return $writer;
	}
}