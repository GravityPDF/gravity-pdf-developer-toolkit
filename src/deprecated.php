<?php

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
