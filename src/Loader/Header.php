<?php

namespace GFPDF\Plugins\DeveloperToolkit\Loader;

use GFPDF\Helper\Helper_Interface_Filters;
use GFPDF\Helper\Helper_Templates;

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
 * Adds support for our Toolkit header in PDF Templates.
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Loader
 *
 * @since   1.0
 */
class Header implements Helper_Interface_Filters {

	/**
	 * @var Helper_Templates
	 * @since 1.0
	 */
	protected $template;

	/**
	 * Header constructor.
	 *
	 * @param Helper_Templates $template
	 *
	 * @since 1.0
	 */
	public function __construct( Helper_Templates $template ) {
		$this->template = $template;
	}

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
		add_filter( 'gfpdf_template_header_details', [ $this, 'addToolkitHeader' ] );
		add_filter( 'gfpdf_form_add_pdf', [ $this, 'addToolkitSetting' ] );
		add_filter( 'gfpdf_form_update_pdf', [ $this, 'addToolkitSetting' ] );
	}

	/**
	 * Register a new PDF Template Header "Toolkit"
	 *
	 * Templates that impliment this header will override the standard Mpdf HTML sandbox
	 *
	 * Triggered via the `gfpdf_template_header_details` filter.
	 *
	 * @param array $headers
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function addToolkitHeader( $headers ) {
		$headers['toolkit'] = 'Toolkit';

		return $headers;
	}

	/**
	 * If the current template is a Toolkit template, save that value into the settings
	 *
	 * @param array $pdf
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function addToolkitSetting( $pdf ) {
		$headers = $this->template->get_template_info_by_id( $pdf['template'] );

		if ( isset( $headers['toolkit'] ) && $headers['toolkit'] === 'true' ) {
			$pdf['toolkit'] = true;
		} elseif ( isset( $pdf['toolkit'] ) ) {
			unset( $pdf['toolkit'] );
		}

		return $pdf;
	}
}
