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


class Single extends AbstractWriter {
	public function add( $html, $position = [], $overflow = 'auto' ) {
		if ( count( $position ) !== 4 ) {
			throw new \BadMethodCallException( '$position needs to include an array with four elements: $x, $y, $width, $height' );
		}

		if ( ! in_array( $overflow, [ 'auto', 'visible', 'hidden' ] ) ) {
			throw new \BadMethodCallException( '$overflow can only be "auto", "visible" or "hidden".' );
		}

		$output = sprintf(
			'<div class="single">%s</div>',
			$html
		);

		$this->mpdf->WriteFixedPosHTML( $output, $position[0], $position[1], $position[2], $position[3], $overflow );
	}
}