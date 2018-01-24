<?php

namespace GFPDF\Plugins\DeveloperToolkit\Legacy;

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
 * Detects when the legacy Advanced Template option is enabled, bypasses the PDF sandbox, and injects our Toolkit helper
 * classes and legacy variables automatically.
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Legacy
 *
 * @since   1.0
 */
class Loader implements Helper_Interface_Filters {

	/**
	 * Initialise class
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function init() {
		$this->add_filters();
	}

	/**
	 * Add WordPress filters
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function add_filters() {
		add_filter( 'gfpdf_skip_pdf_html_render', [ $this, 'maybe_skip_pdf_html_render' ], 10, 2 );
		add_filter( 'gfpdf_developer_toolkit_template_args', [ $this, 'maybe_add_legacy_template_args' ], 10, 2 );
	}

	/**
	 * Check if the legacy setting is enabled and skip the sandbox
	 *
	 * Check if the `advanced_template` PDF setting exists and is set to "Yes" and skip the PDF sandbox
	 *
	 * Triggered via the `gfpdf_skip_pdf_html_render` filter.
	 *
	 * @param bool  $skip Whether we should skip the HTML sandbox
	 * @param array $args The current PDF settings
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function maybe_skip_pdf_html_render( $skip, $args ) {
		if ( $this->is_legacy_advanced_template( $args ) ) {
			return true;
		}

		return $skip;
	}

	/**
	 * Include variables needed for legacy templates
	 *
	 * Triggered via the `gfpdf_developer_toolkit_template_args` filter
	 *
	 * @param array $args     New variables being injected into PDF template
	 * @param array $old_args Old variables that we're overriding
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function maybe_add_legacy_template_args( $args, $old_args ) {
		if ( $this->is_legacy_advanced_template( $args ) ) {
			global $pdf, $writer;

			$pdf    = $args['mpdf'];
			$writer = $args['w'];

			$args['form_id']  = $old_args['form_id'];
			$args['lead_ids'] = $old_args['lead_ids'];
			$args['lead_id']  = $old_args['lead_id'];
		}

		return $args;
	}

	/**
	 * Check if the legacy setting is enabled
	 *
	 * @param array $args
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	protected function is_legacy_advanced_template( $args ) {
		return ( isset( $args['settings']['advanced_template'] ) && $args['settings']['advanced_template'] === 'Yes' );
	}
}