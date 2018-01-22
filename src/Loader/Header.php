<?php

namespace GFPDF\Plugins\DeveloperToolkit\Loader;

use GFPDF\Helper\Helper_Interface_Filters;

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
 * Class Header
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Loader
 *
 * @since   1.0
 */
class Header implements Helper_Interface_Filters {

	/**
	 * @since 1.0
	 */
	public function init() {
		$this->add_filters();
	}

	/**
	 * @since 1.0
	 */
	public function add_filters() {
		add_filter( 'gfpdf_template_header_details', [ $this, 'add_toolkit_header' ] );
	}

	/**
	 * Register a new PDF Template Header "Toolkit"
	 *
	 * @Internal Templates that impliment this header will override the standard Mpdf HTML sandbox
	 *
	 * @param array $headers
	 *
	 * @return array
	 *
	 * @since    1.0
	 */
	public function add_toolkit_header( $headers ) {
		$headers['toolkit'] = 'Toolkit';

		return $headers;
	}
}