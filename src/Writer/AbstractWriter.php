<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer;

use mPDF;

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
 * @package GFPDF\Plugins\DeveloperToolkit\Writer
 *
 * @since   1.0
 */
abstract class AbstractWriter implements InterfaceWriter {

	/**
	 * @var mPDF
	 * @since 1.0
	 */
	protected $mpdf;

	/**
	 * Our Mpdf Setter
	 *
	 * @param mPDF $mpdf
	 *
	 * @since 1.0
	 */
	public function set_mpdf( mPDF $mpdf ) {
		$this->mpdf = $mpdf;
	}

	/**
	 * Determine if our Mpdf Setter has been run
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function is_mpdf_set() {
		return $this->mpdf !== null;
	}
}