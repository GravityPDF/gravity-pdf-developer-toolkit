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
 * Class Tick
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Writer\Processes
 *
 * @since   1.0
 */
class Tick extends AbstractWriter {

	/**
	 * @var string The default HTML to display
	 * @since 1.0
	 */
	protected $markup = '&#10004;';

	/**
	 * @var string The default font to use
	 * @since 1.0
	 */
	protected $font = 'DejavuSansCondensed';

	/**
	 * @var int The default font size in points
	 * @since 1.0
	 */
	protected $font_size = '16';

	/**
	 * @var int The default line height in points
	 * @since 1.0
	 */
	protected $line_height = '16';

	/**
	 * Sets the new default configuration to apply to all new tick elements
	 *
	 * @param array $config Accepted array keys include 'markup', 'font', 'font-size', 'line-height'
	 *
	 * @since 1.0
	 */
	public function configTick( $config = [] ) {
		foreach ( $config as $name => $value ) {
			switch ( $name ) {
				case 'markup':
					$this->markup = (string) $value;
				break;

				case 'font':
					$this->font = (string) $value;
				break;

				case 'font-size':
					$this->font_size = (int) $value;
				break;

				case 'line-height':
					$this->line_height = (int) $value;
				break;
			}
		}
	}

	/**
	 * Adds a tick character to the PDF at the requested coordinates.
	 *
	 * @param array $position The X and Y position of the element
	 * @param array $config   Override the default configuration on a per-element basis. Accepted array keys include 'markup', 'font', 'font-size', 'line-height'
	 *
	 * @throws BadMethodCallException
	 *
	 * @since 1.0
	 */
	public function tick( $position = [], $config = [] ) {
		if ( count( $position ) !== 2 ) {
			throw new BadMethodCallException( '$position needs to include an array with two elements: $x, $y' );
		}

		$font        = ( isset( $config['font'] ) ) ? (string) $config['font'] : $this->font;
		$font_size   = ( isset( $config['font-size'] ) ) ? (int) $config['font-size'] : $this->font_size;
		$line_height = ( isset( $config['line-height'] ) ) ? (int) $config['line-height'] : $this->line_height;
		$markup      = ( isset( $config['markup'] ) ) ? (string) $config['markup'] : $this->markup;

		$output = sprintf(
			'<div class="tick" style="font: %s; font_size: %s; line-height: %s">%s</div> &nbsp;',
			$font,
			$font_size . 'pt',
			$line_height . 'pt',
			$markup
		);

		$this->mpdf->WriteFixedPosHTML( $output, $position[0], $position[1], 5, 5, 'visible' );
	}
}