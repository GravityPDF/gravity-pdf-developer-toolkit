<?php

namespace GFPDF\Plugins\DeveloperToolkit\Legacy;

use GFPDF\Helper\Helper_Interface_Actions;
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
 * Class Loader
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Loader
 *
 * @since   1.0
 */
class Loader implements Helper_Interface_Filters, Helper_Interface_Actions {

	/**
	 * @since 1.0
	 */
	public function init() {
		$this->add_filters();
		$this->add_actions();
	}

	/**
	 * @since 1.0
	 */
	public function add_filters() {
		add_filter( 'gfpdf_skip_pdf_html_render', [ $this, 'maybe_skip_pdf_html_render' ], 10, 2 );
		add_filter( 'gfpdf_developer_toolkit_template_args', [ $this, 'maybe_add_legacy_template_args' ], 10, 3 );
	}

	/**
	 * @since 1.0
	 */
	public function add_actions() {

	}

	/**
	 * Determine if the current template has the "Toolkit" header and skip the standard Mpdf HTML sandbox
	 *
	 * @param bool       $skip       Whether we should skip the HTML sandbox
	 * @param array      $args
	 * @param Helper_PDF $pdf_helper The current PDF Helper object handling the PDF generation
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function maybe_skip_pdf_html_render( $skip, $args ) {

		/* Check for Legacy template */
		if ( isset( $args['settings']['advanced_template'] ) && $args['settings']['advanced_template'] === 'Yes' ) {
			return true;
		}

		return $skip;
	}

	public function maybe_add_legacy_template_args( $args, $old_args, $pdf_helper ) {
		if ( isset( $args['settings']['advanced_template'] ) && $args['settings']['advanced_template'] === 'Yes' ) {
			global $pdf, $writer;

			$pdf    = $args['mpdf'];
			$writer = $args['w'];

			$args['form_id']  = $old_args['form_id'];
			$args['lead_ids'] = $old_args['lead_ids'];
			$args['lead_id']  = $old_args['lead_id'];
		}

		return $args;
	}

}