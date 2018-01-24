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
 * @package GFPDF\Plugins\DeveloperToolkit\Writer\Processes
 *
 * @since   1.0
 */
class Styles extends AbstractWriter {

	/**
	 * Begin buffering output to capture the styles
	 *
	 * ## Example
	 *
	 * See `$this->endStyles()` below.
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function beginStyles() {
		ob_start();
	}

	/**
	 * Import our styles to the PDF generator.
	 *
	 * This method takes the captured buffer (which started when `$w->beginStyles()` was called), strips the `style` HTML tags (we include them for syntax sugar in our IDEs)
	 * and adds it to Mpdf.
	 *
	 * Refer to Mpdf's documentation to see what classes as valid CSS http://mpdf.github.io/css-stylesheets/supported-css.html
	 *
	 * ## Example
	 *
	 *      // Load our custom CSS styles that'll apply to the PDF template
	 *      $w->beginStyles();
	 *      ?>
	 *      <style>
	 *          body {
	 *              color: #999;
	 *          }
	 *
	 *          // Add styles to content added via `$w->add()`
	 *          .single {
	 *              background: #999;
	 *              color: #FFF;
	 *          }
	 *      </style>
	 *      <?php
	 *      $w->endStyles();
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function endStyles() {
		$styles = ob_get_clean();
		$styles = str_replace( [ '<style>', '</style>' ], '', $styles );

		$this->mpdf->WriteHTML( $styles, 1 );
	}
}