<?php

namespace GFPDF\Plugins\DeveloperToolkit\Loader;

use GFPDF\Plugins\DeveloperToolkit\Factory\FactoryWriter;
use GFPDF\Helper\Helper_Interface_Actions;
use GFPDF\Helper\Helper_Interface_Filters;
use GFPDF\Helper\Helper_PDF;
use GPDFAPI;

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
 * Detects when the Toolkit header is included, bypasses the PDF sandbox and injects our Toolkit helper classes automatically.
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Loader
 *
 * @since   1.0
 */
class Loader implements Helper_Interface_Filters, Helper_Interface_Actions {

	/**
	 * Initialise class
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function init() {
		$this->add_filters();
		$this->add_actions();
	}

	/**
	 * Add WordPress filters
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function add_filters() {
		add_filter( 'gfpdf_skip_pdf_html_render', [ $this, 'maybeSkipPdfHtmlRender' ], 10, 3 );
	}

	/**
	 * Add WordPress actions
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function add_actions() {
		add_action( 'gfpdf_skipped_html_render', [ $this, 'handleToolkitTemplate' ], 10, 2 );
	}

	/**
	 * Determine if the current template has the "Toolkit" header and skip the standard Mpdf HTML sandbox
	 *
	 * Triggered via the `gfpdf_skip_pdf_html_render` filter.
	 *
	 * @param bool       $skip      Whether we should skip the HTML sandbox
	 * @param array      $args
	 * @param Helper_PDF $pdfHelper The current PDF Helper object handling the PDF generation
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	public function maybeSkipPdfHtmlRender( $skip, $args, $pdfHelper ) {
		/* Read template Header */
		$template = GPDFAPI::get_templates_class();
		$headers  = $template->get_template_info_by_path( $pdfHelper->get_template_path() );

		if ( isset( $headers['toolkit'] ) && $headers['toolkit'] == 'true' ) {
			return true;
		}

		return $skip;
	}

	/**
	 * Loads the current PDF template and injects our Toolkit helper classes
	 *
	 * Triggered via the `gfpdf_skipped_html_render` action.
	 *
	 * @param array      $args      The variables to inject into the PDF template
	 * @param Helper_PDF $pdfHelper The PDF generation helper class
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function handleToolkitTemplate( $args, $pdfHelper ) {
		$args = $this->prepareArguments( $args, $pdfHelper );
		$this->loadTemplate( $pdfHelper->get_template_path(), $args );
	}

	/**
	 * Prepare the variables to be injected into the PDF template being loaded
	 *
	 * @param array      $args      The variables to inject into the PDF template
	 * @param Helper_PDF $pdfHelper The PDF generation helper class
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	protected function prepareArguments( $args, $pdfHelper ) {
		/* Create a new Writer object and inject the current Mpdf object */
		$writer = FactoryWriter::build();
		$writer->setMpdf( $pdfHelper->get_pdf_class() );

		$new_args = apply_filters( 'gfpdf_developer_toolkit_template_args', [
			'w'         => $writer,
			'mpdf'      => $pdfHelper->get_pdf_class(),
			'form'      => $args['form'],
			'entry'     => $args['entry'],
			'form_data' => $args['form_data'],
			'fields'    => $args['fields'],
			'config'    => $args['config'],
			'settings'  => $args['settings'],
			'gfpdf'     => $args['gfpdf'],
		], $args, $pdfHelper );

		return $new_args;
	}

	/**
	 * Load the PDF template
	 *
	 * @param string $template Absolute path to PDF template file
	 * @param array  $args     Variables to be passed to the template which are run through `extract()`
	 *
	 * @since 1.0
	 */
	protected function loadTemplate( $template, $args ) {
		extract( $args, EXTR_SKIP );
		include $template;
	}
}