<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer\Processes;

use GFPDF\Plugins\DeveloperToolkit\Writer\AbstractWriter;

use BadMethodCallException;

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
class Html extends AbstractWriter {

	/**
	 * Add HTML directly to Mpdf using the current Mpdf Y pointer position
	 *
	 * Normal PDF templates are sandboxed directly to this method. The Toolkit-templates are not and give you full access
	 * to the Mpdf object, as well as our helper object `$w`. Use this method when you aren't auto-filling a PDF, or want
	 * to create a hybrid (some pages will auto-fill a PDF, while others will be dynamically generated with HTML).
	 *
	 * ## Example
	 *
	 *      // Add HTML to PDF
	 *      $w->addHtml( '<h1>This is my title</h1><p>This is my content</p>' );
	 *
	 * @param string $html The freeflow HTML markup to add to Mpdf. Refer to the Mpdf documentation about supported markup: http://mpdf.github.io/
	 *
	 * @throws BadMethodCallException
	 *
	 * @since 1.0
	 */
	public function addHtml( $html ) {

		if ( ! is_string( $html ) ) {
			throw new BadMethodCallException( sprintf( '$html needs to be a string. You provided a %s', gettype( $html ) ) );
		}

		$this->mpdf->WriteHTML( $html );
	}
}