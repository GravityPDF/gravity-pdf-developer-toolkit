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
 * @package GFPDF\Plugins\DeveloperToolkit\Writer
 *
 * @since   1.0
 */
abstract class AbstractWriter implements InterfaceWriter {

	/**
	 * @var \mPDF|\Mpdf\Mpdf
	 * @since 1.0
	 */
	protected $mpdf;

	/**
	 * Our Mpdf Setter
	 *
	 * @param \mPDF|\Mpdf\Mpdf $mpdf
	 *
	 * @throws \BadMethodCallException Throws when $mpdf isn't valid
	 *
	 * @since 1.0
	 */
	public function setMpdf( $mpdf ) {
		if ( ! class_exists( 'GravityPdfDeveloperToolkitUnitTestsBootstrap' ) && ! $mpdf instanceof \mPDF && ! $mpdf instanceof \Mpdf\Mpdf && ! $mpdf instanceof \GFPDF_Vendor\Mpdf\Mpdf ) {
			throw new \BadMethodCallException( '$mpdf must be \mPDF or \Mpdf\Mpdf or \GFPDF_Vendor\Mpdf\Mpdf' );
		}

		$this->mpdf = $mpdf;
	}

	/**
	 * Get the current Mpdf object
	 *
	 * @return \mPDF|\Mpdf\Mpdf
	 *
	 * @since 1.0
	 */
	public function getMpdf() {
		return $this->mpdf;
	}

	/**
	 * Determine if our Mpdf Setter has been run
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function isMpdfSet() {
		return $this->mpdf !== null;
	}
}
