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
class Single extends AbstractWriter {

	/**
	 * Add Single Line content to PDF
	 *
	 * Add content to the PDF that has a fixed positioned and is better suited for a single line of text (the line height
	 * matches the font size for more accurate positioning). This is the main way you overlay content onto your PDF documents.
	 *
	 * <b>The `$position` is always calculated in millimeters and the units should NOT be included</b>.
	 *
	 * By default, if the text extends outside the container it will be shrunk to fit (this can be overriden).
	 *
	 * All content added with this method will be wrapped in a DIV with the class `.single` for more convenient styling.
	 *
	 * ## Example
	 *
	 *      // Add content to the current page positioned 20mm from the left, 50mm from the top, with a width of 30mm and a height of 5mm
	 *      $w->add( 'My content', [ 20, 50, 30, 5 ] );
	 *
	 *      // Instead of shrinking the content to fit the container, the 'visible' property will allow it to overflow the container
	 *      $w->add( 'My content', [ 20, 50, 30, 5 ], 'visible' );
	 *
	 * @param string $html     The content to add to the PDF being rendered
	 * @param array  $position The X, Y, Width and Height of the element
	 * @param string $overflow Whether to show or resize the $html if the content doesn't fit inside the width/height. Accepted parameters include "auto" or "visible"
	 *
	 * @throws BadMethodCallException Will be thrown if `$position` doesn't include four array items (x, y, width, height), if `$overflow` doesn't include an accepted argument, or $html is not a string
	 *
	 * @since 1.0
	 */
	public function add( $html, $position = [], $overflow = 'auto' ) {

		if ( ! is_array( $position ) || count( $position ) !== 4 ) {
			throw new BadMethodCallException( '$position needs to include an array with four elements: $x, $y, $width, $height' );
		}

		if ( ! in_array( $overflow, [ 'auto', 'visible' ], true ) ) {
			throw new BadMethodCallException( '$overflow can only be "auto" or "visible".' );
		}

		$output = sprintf(
			'<div class="single">%s</div>',
			(string) $html
		);

		$this->mpdf->WriteFixedPosHTML( $output, $position[0], $position[1], $position[2], $position[3], $overflow );
	}

	/**
	 * Add Single Line content to PDF with center alignment
	 *
	 * @see   Single::add() for full documentation
	 *
	 * ## Example
	 *
	 *      // Add content to the current page positioned 20mm from the left, 50mm from the top, with a width of 30mm, a height of 5mm and aligned right
	 *      $w->addCenter( 'My content', [ 20, 50, 30, 5 ] );
	 *
	 * @param string $html     The content to add to the PDF being rendered
	 * @param array  $position The X, Y, Width and Height of the element
	 * @param string $overflow Whether to show or resize the $html if the content doesn't fit inside the width/height. Accepted parameters include "auto" or "visible"
	 *
	 * @since 1.0.0-beta2
	 */
	public function addCenter( $html, $position = [], $overflow = 'auto' ) {
		$html = '<div style="text-align: center">' . $html . '</div>';
		$this->add( $html, $position, $overflow );
	}

	/**
	 * Add Single Line content to PDF with right alignment
	 *
	 * @see   Single::add() for full documentation
	 *
	 * ## Example
	 *
	 *      // Add content to the current page positioned 20mm from the left, 50mm from the top, with a width of 30mm, a height of 5mm and aligned right
	 *      $w->addRight( 'My content', [ 20, 50, 30, 5 ] );
	 *
	 * @param string $html     The content to add to the PDF being rendered
	 * @param array  $position The X, Y, Width and Height of the element
	 * @param string $overflow Whether to show or resize the $html if the content doesn't fit inside the width/height. Accepted parameters include "auto" or "visible"
	 *
	 * @since 1.0.0-beta2
	 */
	public function addRight( $html, $position = [], $overflow = 'auto' ) {
		$html = '<div style="text-align: right">' . $html . '</div>';
		$this->add( $html, $position, $overflow );
	}
}
