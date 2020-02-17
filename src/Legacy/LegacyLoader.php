<?php

namespace GFPDF\Plugins\DeveloperToolkit\Legacy;

use GFPDF\Helper\Helper_Interface_Actions;
use GFPDF\Helper\Helper_Interface_Filters;

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
 * Detects when the legacy Advanced Template option is enabled, bypasses the PDF sandbox, and injects our Toolkit helper
 * classes and legacy variables automatically.
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Legacy
 *
 * @since   1.0
 */
class LegacyLoader implements Helper_Interface_Filters, Helper_Interface_Actions {

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
		add_filter( 'gfpdf_skip_pdf_html_render', [ $this, 'maybeSkipPdfHtmlRender' ], 10, 2 );
		add_filter( 'gfpdf_developer_toolkit_disable_default_styles', [ $this, 'maybeSkipPdfHtmlRender' ], 10, 2 );
		add_filter( 'gfpdf_developer_toolkit_template_args', [ $this, 'maybeAddLegacyTemplateArgs' ], 10, 2 );
	}

	/**
	 * Add WordPress actions
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function add_actions() {
		add_action( 'gfpdf_developer_toolkit_post_load_template', [ $this, 'overloadMpdfClass' ], 10, 3 );
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
	public function maybeSkipPdfHtmlRender( $skip, $args ) {
		if ( $this->isLegacyAdvancedTemplate( $args ) ) {
			return true;
		}

		return $skip;
	}

	/**
	 * Include variables needed for legacy templates
	 *
	 * Triggered via the `gfpdf_developer_toolkit_template_args` filter
	 *
	 * @param array $args    New variables being injected into PDF template
	 * @param array $oldArgs Old variables that we're overriding
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function maybeAddLegacyTemplateArgs( $args, $oldArgs ) {
		if ( $this->isLegacyAdvancedTemplate( $args ) ) {
			global $pdf, $writer;

			$pdf    = $args['mpdf'];
			$writer = $args['w'];

			$args['form_id']  = $oldArgs['form_id'];
			$args['lead_ids'] = $oldArgs['lead_ids'];
			$args['lead_id']  = $oldArgs['lead_id'];
		}

		return $args;
	}

	/**
	 * Overload the current Mpdf object being processed if template creates its own object
	 *
	 * @param $template
	 * @param $args
	 * @param $pdfHelper
	 *
	 * @since 1.0
	 */
	public function overloadMpdfClass( $template, $args, $pdfHelper ) {
		global $pdf;

		if ( $this->isLegacyAdvancedTemplate( $args ) && method_exists( $pdfHelper, 'set_pdf_class' ) ) {
			if ( get_class( $pdf ) === get_class( $args['mpdf'] ) && $pdf !== $args['mpdf'] ) {
				$pdfHelper->set_pdf_class( $pdf );
			}
		}
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
	protected function isLegacyAdvancedTemplate( $args ) {
		return ( isset( $args['settings']['advanced_template'] ) && $args['settings']['advanced_template'] === 'Yes' );
	}
}
