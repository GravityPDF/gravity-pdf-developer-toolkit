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
	protected $fontSize = 16;

	/**
	 * @var int The default line height in points
	 * @since 1.0
	 */
	protected $lineHeight = 16;

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
	 * - font-size: `16pt` - Controls the font size used
	 * - line-height: `16pt` - Controls the line height used
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
	 * @throws BadMethodCallException Called is $position does not contain two array elements, or if $config is not an array
	 *
	 * @since 1.0
	 */
	public function tick( $position = [], $config = [] ) {
		if ( ! is_array( $position ) || count( $position ) !== 2 ) {
			throw new BadMethodCallException( '$position needs to include an array with two elements: $x, $y' );
		}

		if ( ! is_array( $config ) ) {
			throw new BadMethodCallException( '$config must be an array.' );
		}

		$font       = ( isset( $config['font'] ) ) ? (string) $config['font'] : $this->font;
		$fontSize   = ( isset( $config['font-size'] ) ) ? (int) $config['font-size'] : $this->fontSize;
		$lineHeight = ( isset( $config['line-height'] ) ) ? (int) $config['line-height'] : $this->lineHeight;
		$markup     = ( isset( $config['markup'] ) ) ? (string) $config['markup'] : $this->markup;

		$output = sprintf(
			'<div class="tick" style="font-family: %s; font-size: %s; line-height: %s">%s</div> &nbsp;',
			$font,
			$fontSize . 'pt',
			$lineHeight . 'pt',
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
	 * - font-size: `16pt` - Controls the font size used
	 * - line-height: `16pt` - Controls the line height used
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
					$this->fontSize = (int) $value;
					break;

				case 'line-height':
					$this->lineHeight = (int) $value;
					break;
			}
		}
	}

	/**
	 * Return the current Tick configuration values
	 *
	 * ## Example
	 *
	 *      // Get the current Tick config
	 *      $config = $w->getTickConfig();
	 *
	 *      echo $config['markup'];
	 *      echo $config['font'];
	 *      echo $config['font-size'];
	 *      echo $config['line-height'];
	 *
	 * @return array Returned array keys include 'markup', 'font', 'font-size', 'line-height'
	 *
	 * @since 1.0
	 */
	public function getTickConfig() {
		return [
			'markup'      => $this->markup,
			'font'        => $this->font,
			'font-size'   => $this->fontSize,
			'line-height' => $this->lineHeight,
		];
	}
}
