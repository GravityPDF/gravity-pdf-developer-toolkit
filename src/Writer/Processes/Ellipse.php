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
class Ellipse extends AbstractWriter {

	/**
	 * Adds an Ellipse to the PDF being rendered
	 *
	 * <b>The positioning is always calculated in millimeters and the units should NOT be included<b>.
	 *
	 * ## Example
	 *
	 * The X/Y position references the center of the ellipse (and not the top-left corner like when using `$w->add()` or `$w->addMulti()`)
	 *
	 *      // Add an Ellipse at 120mm from the left and 50mm from the top (this will mark the center of the ellipse), with a 5mm width and 3mm height
	 *      $w->ellipse( [ 120, 50, 5, 3 ] );
	 *
	 *      // Create a circle with a 5mm radius
	 *      $w->ellipse( [ 120, 50, 5 ] );
	 *
	 * @param array $position The X, Y, Width and Height of the Ellipse to be added to the PDF. To make a circle you can exclude the height.
	 *
	 * @throws BadMethodCallException
	 *
	 * @since 1.0
	 */
	public function ellipse( $position = [] ) {
		if ( ! isset( $position[3] ) && isset( $position[2] ) ) {
			$position[3] = $position[2];
		}

		if ( ! is_array( $position ) || count( $position ) !== 4 ) {
			throw new \BadMethodCallException( '$position needs to include an array with four elements: $x, $y, $width, $height' );
		}

		$this->mpdf->Ellipse( $position[0], $position[1], $position[2], $position[3] );
	}
}