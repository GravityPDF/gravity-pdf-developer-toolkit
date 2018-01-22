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
	 * A legacy class used in the Tier 2 add-on which is included so this plugin can be a drop-in replacement
	 *
	 * @since 1.0
	 */
	class gfpdfe_business_plus {

		/**
		 * Import a PDF and return the data
		 *
		 * @param string $path The absolute path to the PDF
		 *
		 * @return array
		 *
		 * @since 1.0
		 */
		public static function initilise( $path ) {
			/** @var GFPDF\Plugins\DeveloperToolkit\Writer\Writer $writer */
			global $writer;

			$writer->addPdf( $path );

			return [
				'load' => $writer->getPdfPageIds(),
				'size' => $writer->getPdfPageSize(),
			];
		}

		/**
		 * @param string $path The absolute path to the PDF
		 *
		 * @since 1.0
		 */
		public static function add_template( $path ) {
			self::initilise( $path );
		}
	}
}