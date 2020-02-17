<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer;

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
 * The Mpdf setter contract for use with our Writer classes
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Writer
 *
 * @since 1.0
 */
interface InterfaceWriter {

	/**
	 * Setter for our Mpdf class
	 *
	 * @param \mPDF|\Mpdf\Mpdf $mpdf
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function setMpdf( $mpdf );

	/**
	 * Check if our Mpdf Setter has been run
	 *
	 * @return bool
	 */
	public function isMpdfSet();
}
