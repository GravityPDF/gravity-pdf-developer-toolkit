<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer\Processes;

use GFPDF\Plugins\DeveloperToolkit\Writer\AbstractWriter;
use BadMethodCallException;

/**
 * @package     Gravity PDF Developer Toolkit
 * @copyright   Copyright (c) 2020, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package GFPDF\Plugins\DeveloperToolkit\Writer\Processes
 *
 * @since   1.0
 */
class Multi extends AbstractWriter {

	/**
	 * @var int The default font size in points. This is overridden by the UI Font Size.
	 * @see   \GFPDF\Plugins\DeveloperToolkit\Loader\Loader::setDefaultStyles()
	 * @since 1.0
	 */
	protected $fontSize = 10;

	/**
	 * @var int The default line height in points. This is overridden by the (UI Font Size * 1.4).
	 * @see   \GFPDF\Plugins\DeveloperToolkit\Loader\Loader::setDefaultStyles()
	 * @since 1.0
	 */
	protected $lineHeight = 14;

	/**
	 * @var bool Whether to remove BR tags from the output
	 * @since 1.0
	 */
	protected $stripBr = false;

	/**
	 * Add Multi-line content to the PDF
	 *
	 * Add content to the PDF which has a fixed positioned and is better configured for multiline output (better line
	 * height defaults at the expense of less accurate Y positioning).
	 *
	 * <b>The `$position` is always calculated in millimeters and the units should NOT be included</b>.
	 *
	 * The default configuration is as follows, but can be overriden for each call:
	 *
	 * - font-size: `UI Font Size|10pt` - Controls the font size used
	 * - line-height: `(UI Font Size * 1.4)|14pt` - Controls the line height used
	 * - strip-br: `false` - Whether to strip BR tags and replace them with 3 hard spaces.
	 *
	 * By default, if the text extends outside the container it will be shrunk to fit (this can be overriden). All content
	 * added with this method will be wrapped in a DIV with the class `.multi` for more convenient styling. The X/Y
	 * positioning is from the top-left of the element being included.
	 *
	 * The `font-size` and `line-height` are always calculated in points and the units should NOT be included when changing
	 * the configuration defaults.
	 *
	 * ## Example
	 *
	 *      // Add multi-line content to the current page positioned 20mm from the left, 50mm from the top, with a width of 30mm and a height of 5mm
	 *      $w->addMulti( 'Line 1<br>Line2<br>Line3', [ 20, 50, 30, 5 ] );
	 *
	 *      // Instead of shrinking the content to fit the container, the 'visible' property will allow it to overflow the container
	 *      $w->addMulti( 'Line 1<br>Line2<br>Line3', [ 20, 50, 30, 5 ], 'visible' );
	 *
	 *      // Will override the default configuration and auto-strip BR tags and increase the font size on a one-time basis
	 *      $w->addMulti( 'Line 1<br>Line2<br>Line3', [ 20, 50, 30, 5 ], 'auto', [ 'font-size' => 14, 'line-height' => 20, 'strip-br' => true ] );
	 *
	 * @param string $html     The content to add to the PDF being rendered
	 * @param array  $position The X, Y, Width and Height of the element
	 * @param string $overflow Whether to show or resize the $html if the content doesn't fit inside the width/height. Accepted arguments include "auto" or "visible"
	 * @param array  $config   Override the default configuration on a per-element basis. Accepted array keys include 'font-size', 'line-height', 'strip-br'
	 *
	 * @throws BadMethodCallException Will be thrown if `$position` doesn't include four array items (x, y, width, height), if `$overflow` doesn't include an accepted argument, or $html is not a string
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function addMulti( $html, $position = [], $overflow = 'auto', $config = [] ) {

		if ( ! is_array( $position ) || count( $position ) !== 4 ) {
			throw new BadMethodCallException( '$position needs to include an array with four elements: $x, $y, $width, $height' );
		}

		if ( ! in_array( $overflow, [ 'auto', 'visible' ], true ) ) {
			throw new BadMethodCallException( '$overflow can only be "auto" or "visible".' );
		}

		$fontSize   = ( isset( $config['font-size'] ) ) ? (int) $config['font-size'] : $this->fontSize;
		$lineHeight = ( isset( $config['line-height'] ) ) ? (int) $config['line-height'] : $this->lineHeight;
		$stripBr    = ( isset( $config['strip-br'] ) ) ? (int) $config['strip-br'] : $this->stripBr;

		if ( $stripBr ) {
			$html = str_replace( [ "\r", "\n" ], '', $html ); /* Strip new line characters */
			$html = str_replace( [ '<br>', '<br/>', '<br />' ], ' &nbsp; ', $html ); /* Convert BR tags to non-breaking spaces */

			/* Strip duplicate non-breaking spaces
			 * See https://stackoverflow.com/a/6723412/1614565
			 */
			$html = preg_replace( "/( \&nbsp\; )\\1+/", '$1', $html );
		}

		$output = sprintf(
			'<div class="multi" style="font-size: %s; line-height: %s">%s</div>',
			$fontSize . 'pt',
			$lineHeight . 'pt',
			(string) $html
		);

		$this->mpdf->WriteFixedPosHTML( $output, $position[0], $position[1], $position[2], $position[3], $overflow );
	}

	/**
	 * Add Multi-line content to the PDF
	 *
	 * @see   Multi::addMulti() for full documentation
	 *
	 * ## Example
	 *
	 *      // Add multi-line content to the current page positioned 20mm from the left, 50mm from the top, with a width of 30mm, a height of 5mm and aligned right
	 *      $w->addMultiCenter( 'Line 1<br>Line2<br>Line3', [ 20, 50, 30, 5 ] );
	 *
	 * @param string $html     The content to add to the PDF being rendered
	 * @param array  $position The X, Y, Width and Height of the element
	 * @param string $overflow Whether to show or resize the $html if the content doesn't fit inside the width/height. Accepted arguments include "auto" or "visible"
	 * @param array  $config   Override the default configuration on a per-element basis. Accepted array keys include 'font-size', 'line-height', 'strip-br'
	 *
	 * @since 1.0.0-beta2
	 */
	public function addMultiCenter( $html, $position = [], $overflow = 'auto', $config = [] ) {
		$html = '<div style="text-align: center">' . $html . '</div>';
		$this->addMulti( $html, $position, $overflow, $config );
	}

	/**
	 * Add Multi-line content to the PDF
	 *
	 * @see   Multi::addMulti() for full documentation
	 *
	 * ## Example
	 *
	 *      // Add content to the current page positioned 20mm from the left, 50mm from the top, with a width of 30mm, a height of 5mm and aligned right
	 *      $w->addMultiRight( 'Line 1<br>Line2<br>Line3', [ 20, 50, 30, 5 ] );
	 *
	 * @param string $html     The content to add to the PDF being rendered
	 * @param array  $position The X, Y, Width and Height of the element
	 * @param string $overflow Whether to show or resize the $html if the content doesn't fit inside the width/height. Accepted arguments include "auto" or "visible"
	 * @param array  $config   Override the default configuration on a per-element basis. Accepted array keys include 'font-size', 'line-height', 'strip-br'
	 *
	 * @since 1.0.0-beta2
	 */
	public function addMultiRight( $html, $position = [], $overflow = 'auto', $config = [] ) {
		$html = '<div style="text-align: right">' . $html . '</div>';
		$this->addMulti( $html, $position, $overflow, $config );
	}

	/**
	 * Sets the new mult-line configuration
	 *
	 * Once called, all future calls to `$w->addMulti()` will use these defaults
	 *
	 * The default configuration is:
	 *
	 * - font-size: `10pt` - Controls the font size used
	 * - line-height: `14pt` - Controls the line height used
	 * - strip-br: `false` - Whether to strip BR tags and replace them with 3 hard spaces.
	 *
	 * The `font-size` and `line-height` are always calculated in points and the units should NOT be included when changing
	 * the configuration defaults.
	 *
	 * ## Example
	 *
	 *      // Adds multi-line text with the default
	 *      $w->addMulti( 'Line 1<br>Line2<br>Line3', [ 20, 50, 30, 5 ] );
	 *
	 *      // Changes the default config font size and line height
	 *      $w->configMulti( [
	 *          'font-size' => 14,
	 *          'line-height' => 20,
	 *      ] );
	 *
	 *      // Adds multi-line text with the new defaults
	 *      $w->addMulti( 'Line 1<br>Line2<br>Line3', [ 20, 50, 30, 5 ] );
	 *
	 *      // Will override the defaults just for this content
	 *      $w->addMulti( 'Line 1<br>Line2<br>Line3', [ 20, 50, 30, 5 ], 'auto', [ 'font-size' => 8, 'line-height' => 12 ] );
	 *
	 * @param array $config Accepted array keys include 'font-size', 'line-height', 'strip-br'
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function configMulti( $config ) {
		foreach ( $config as $name => $value ) {
			switch ( $name ) {
				case 'font-size':
					$this->fontSize = (float) $value;
					break;

				case 'line-height':
					$this->lineHeight = (float) $value;
					break;

				case 'strip-br':
					$this->stripBr = (bool) $value;
					break;
			}
		}
	}

	/**
	 * Return the current Multi configuration values
	 *
	 * ## Example
	 *
	 *      // Get the current Multi config
	 *      $config = $w->getMultiConfig();
	 *
	 *      echo $config['font-size'];
	 *      echo $config['line-height'];
	 *      echo $config['strip-br'];
	 *
	 * @return array Returned array keys include 'font-size', 'line-height', 'strip-br'
	 *
	 * @since 1.0
	 */
	public function getMultiConfig() {
		return [
			'font-size'   => $this->fontSize,
			'line-height' => $this->lineHeight,
			'strip-br'    => $this->stripBr,
		];
	}
}
