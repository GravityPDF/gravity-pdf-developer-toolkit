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
	 * Adds a tick character to the PDF
	 *
	 * A symbol will be added to the PDF with a fixed position.
	 *
	 * <b>The `$position` is always calculated in millimeters and the units should NOT be included</b>.
	 *
	 * The default configuration is as follows, but can be overriden for each call:
	 *
	 * - markup: `&#10004;` - The symbol to output
	 * - font: `Dejavusanscondensed` - The registered font name (use the Font Manager to install additional fonts)
	 * - font-size: `10pt` - Controls the font size used
	 * - line-height: `14pt` - Controls the line height used
	 *
	 * This element will not be resized to fit within a container. The symbol will be wrapped in a DIV with the class
	 * `tick` for more convenient styling (don't try style the `font`, `font-size` or `line-height` as this method handles those
	 * styles automatically).
	 *
	 * The `font-size` and `line-height` are always calculated in points and the units should NOT be included when changing
	 * the configuration defaults.
	 *
	 * ## Example
	 *
	 *      // Add tick positioned 20mm from the left and 50mm from the top
	 *      $w->tick( [ 20, 50 ] );
	 *
	 *      // Add X to PDF instead of default tick
	 *      $w->tick( [ 20, 60 ], [ 'markup' => 'X' ] );
	 *
	 *      // Change default font size
	 *      $w->configTick( [
	 *          'font-size' => 20,
	 *          'line-height' => 20,
	 *      ] );
	 *
	 *      // Add tick that uses new defaults (20pt font and 20pt line-height)
	 *      $w->tick( [ 20, 70 ] );
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
			'<div class="tick" style="font: %s; font-size: %s; line-height: %s">%s</div> &nbsp;',
			$font,
			$font_size . 'pt',
			$line_height . 'pt',
			$markup
		);

		$this->mpdf->WriteFixedPosHTML( $output, $position[0], $position[1], 5, 5, 'visible' );
	}

	/**
	 * Sets the new tick configuration
	 *
	 * Once called, all future calls to `$w->tick()` will use these defaults.
	 *
	 * The `font-size` and `line-height` are always calculated in points and the units should NOT be included when changing
	 * the configuration defaults.
	 *
	 * The default configuration is:
	 *
	 * - markup: `&#10004;` - The symbol to output
	 * - font: `Dejavusanscondensed` - The registered font name (use the Font Manager to install additional fonts)
	 * - font-size: `10pt` - Controls the font size used
	 * - line-height: `14pt` - Controls the line height used
	 *
	 * ## Example
	 *
	 *      // Adds a tick with the defaults
	 *      $w->tick( [ 100, 30 ] );
	 *
	 *      // Changes the default config font size and line height
	 *      $w->configTick( [
	 *          'font-size' => 20,
	 *          'line-height' => 20,
	 *      ] );
	 *
	 *      // Adds tick text with the new defaults
	 *      $w->tick( [ 100, 40 ] );
	 *
	 * @param array $config Accepted array keys include 'markup', 'font', 'font-size', 'line-height'
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function configTick( $config ) {
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
}