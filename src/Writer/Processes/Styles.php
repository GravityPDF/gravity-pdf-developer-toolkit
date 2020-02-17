<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer\Processes;

use GFPDF\Plugins\DeveloperToolkit\Writer\AbstractWriter;

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
