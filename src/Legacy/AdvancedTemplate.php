<?php

namespace GFPDF\Plugins\DeveloperToolkit\Legacy;

use GFPDF\Helper\Helper_Interface_Filters;
use GFPDF\Helper\Helper_Abstract_Options;

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
 * Removes the Advanced Template option from the PDF settings if not upgrading from a legacy plugin
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Legacy
 *
 * @since   1.0
 */
class AdvancedTemplate implements Helper_Interface_Filters {

	/**
	 * @var Helper_Abstract_Options
	 * @since 1.0
	 */
	protected $options;

	/**
	 * Deactivate constructor.
	 *
	 * @param Helper_Abstract_Options $options
	 *
	 * @since 1.0
	 */
	public function __construct( Helper_Abstract_Options $options ) {
		$this->options = $options;
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
	 * Add WordPress actions
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function add_filters() {
		add_filter( 'gfpdf_form_settings_advanced', [ $this, 'modifyAdvancedTemplateField' ], 20 );
	}

	/**
	 * Checks if the legacy "Advanced Template" option should be shown in the PDF options
	 *
	 * @param array $fields
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function modifyAdvancedTemplateField( $fields ) {
		if ( isset( $fields['advanced_template'] ) ) {
			$settings = $this->options->get_settings();
			if ( empty( $settings['advanced_templating'] ) ) {
				unset( $fields['advanced_template'] );
			}
		}

		return $fields;
	}
}
