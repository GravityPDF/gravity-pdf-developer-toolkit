<?php

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'gfpdfe_business_plus' ) ) {

	/**
	 * A legacy (deprecated) class included so Gravity PDF Developer Toolkit can be a drop-in replacement for the Gravity PDF Tier 2 add-on. <b>Don't use its methods for any new templates</b>.
	 *
	 * @since 1.0
	 */
	class gfpdfe_business_plus {

		/**
		 * Deprecated method to import a PDF and return the data
		 *
		 * @param string $path The absolute path to the PDF
		 *
		 * @return array Returns a multidimensional array in the following format: [ 'load' => [ 'ID1', 'ID2' ], 'size' => [ [ 500, 500 ], [ 500, 400 ] ]
		 *
		 * @since 1.0
		 */
		public static function initilise( $path ) {
			/** @var GFPDF\Plugins\DeveloperToolkit\Writer\Writer $writer */
			global $writer;

			if ( $writer instanceof \GFPDF\Plugins\DeveloperToolkit\Writer\Writer ) {
				$writer->addPdf( $path );

				return [
					'load' => $writer->getPdfPageIds(),
					'size' => $writer->getPdfPageSize(),
				];
			}

			return [
				'load' => [],
				'size' => [],
			];
		}

		/**
		 * Deprecated method to import additional PDFs and return the data
		 *
		 * @param string $path The absolute path to the PDF
		 *
		 * @return array Returns a multidimensional array in the following format: [ 'load' => [ 'ID1', 'ID2' ], 'size' => [ [ 500, 500 ], [ 500, 400 ] ]
		 *
		 * @since 1.0
		 */
		public static function add_template( $path ) {
			return self::initilise( $path );
		}
	}
}
