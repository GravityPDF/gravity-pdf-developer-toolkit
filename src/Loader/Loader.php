<?php

namespace GFPDF\Plugins\DeveloperToolkit\Loader;

use GFPDF\Helper\Helper_Interface_Actions;
use GFPDF\Helper\Helper_Interface_Filters;
use GFPDF\Helper\Helper_PDF;
use GFPDF\Plugins\DeveloperToolkit\Writer\InterfaceWriter;

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


class Loader implements Helper_Interface_Filters, Helper_Interface_Actions {

	protected $writer;

	public function __construct( InterfaceWriter $writer ) {
		$this->writer = $writer;
	}

	public function init() {
		$this->add_filters();
		$this->add_actions();
	}

	public function add_filters() {
		add_filter( 'gfpdf_skip_pdf_html_render', [ $this, 'maybe_skip_pdf_html_render' ], 10, 3 );
	}

	public function add_actions() {
		add_action( 'gfpdf_skipped_html_render', [ $this, 'handle_toolkit_template' ], 10, 2 );
	}

	public function maybe_skip_pdf_html_render( $skip, $args, $pdf_helper ) {
		/* Read template Header */
		$template = \GPDFAPI::get_templates_class();
		$headers  = $template->get_template_info_by_path( $pdf_helper->get_template_path() );

		if ( isset( $headers['toolkit'] ) && $headers['toolkit'] == 'true' ) {
			return true;
		}

		return $skip;
	}

	public function handle_toolkit_template( $args, $pdf_helper ) {
		$args = $this->prepare_arguments( $args, $pdf_helper );
		$this->load_template( $pdf_helper->get_template_path(), $args );
	}

	/**
	 * @param            $args
	 * @param Helper_PDF $pdf_helper
	 *
	 * @return array
	 */
	protected function prepare_arguments( $args, $pdf_helper ) {
		$this->writer->set_mpdf( $pdf_helper->get_pdf_class() );

		$new_args = [
			'w'         => $this->writer,
			'mpdf'      => $pdf_helper->get_pdf_class(),
			'form'      => $args['form'],
			'entry'     => $args['entry'],
			'form_data' => $args['form_data'],
			'fields'    => $args['fields'],
			'config'    => $args['config'],
			'settings'  => $args['settings'],
			'gfpdf'     => $args['gfpdf'],
		];

		return $new_args;
	}

	protected function load_template( $template, $args ) {
		extract( $args, EXTR_SKIP );
		include $template;
	}

}