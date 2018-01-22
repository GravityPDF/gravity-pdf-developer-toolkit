<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer\Processes;

use GFPDF\Plugins\DeveloperToolkit\Writer\AbstractWriter;

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
 * Class Styles
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Writer\Processes
 *
 * @since 1.0
 */
class Styles extends AbstractWriter {

	/**
	 * Captures any output and stores it in our buffer
	 *
	 * @since 1.0
	 */
	public function beginStyles() {
		ob_start();
	}

	/**
	 * Takes the captured buffer, strips the <style> HTML tags and adds it to Mpdf
	 *
	 * @Internal Refer to Mpdf's document for valid CSS http://mpdf.github.io/css-stylesheets/supported-css.html
	 *
	 * @since 1.0
	 */
	public function endStyles() {
		$styles = ob_get_clean();
		$styles = str_replace( [ '<style>', '</style>' ], '', $styles );

		$this->mpdf->WriteHTML( $styles, 1 );
	}
}