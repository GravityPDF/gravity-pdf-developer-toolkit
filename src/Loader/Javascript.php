<?php

namespace GFPDF\Plugins\DeveloperToolkit\Loader;

use GFPDF\Helper\Helper_Interface_Actions;
use GFPDF\Helper\Helper_Misc;
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
 * Adds support for our Toolkit Javascript in PDF Templates.
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Loader
 *
 * @since   1.0
 */
class Javascript implements Helper_Interface_Actions {

	/**
	 * @var Helper_Misc
	 * @since 1.0
	 */
	protected $misc;

	/**
	 * @var Helper_Templates
	 * @since 1.0
	 */
	protected $templates;

	/**
	 * Javascript constructor.
	 *
	 * @param Helper_Misc      $misc
	 * @param Helper_Templates $templates
	 *
	 * @since 1.0
	 */
	public function __construct( Helper_Misc $misc, Helper_Templates $templates ) {
		$this->misc      = $misc;
		$this->templates = $templates;
	}

	/**
	 * Initialise class
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function init() {
		$this->add_actions();
	}

	/**
	 * @since 1.0
	 */
	public function add_actions() {
		add_action( 'init', [ $this, 'registerAssets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueueScripts' ] );

		add_action( 'wp_ajax_gfpdf_get_template_headers', [ $this, 'getTemplateHeader' ] );
	}

	/**
	 * Register our plugin assets
	 *
	 * @since 1.0
	 */
	public function registerAssets() {
		$url     = plugin_dir_url( GFPDF_DEVELOPER_TOOLKIT_FILE );
		$version = GFPDF_DEVELOPER_TOOLKIT_VERSION;

		wp_register_script( 'gfpdf_js_dev_toolbox_settings', $url . 'dist/assets/js/gfpdf-settings.min.js', [ 'gfpdf_js_settings' ], $version );
	}

	/**
	 * Enqueue our JS on Gravity PDF pages
	 *
	 * @since 1.0
	 */
	public function enqueueScripts() {
		if ( $this->misc->is_gfpdf_page() ) {
			wp_enqueue_script( 'gfpdf_js_dev_toolbox_settings' );
		}
	}

	/**
	 * AJAX endpoint to get current template file headers
	 *
	 * @since 1.0
	 */
	public function getTemplateHeader() {
		$this->misc->handle_ajax_authentication( 'Get Current Template Header', 'gravityforms_edit_settings' );

		$templateId = ( isset( $_POST['template'] ) ) ? $_POST['template'] : '';
		$headers    = $this->templates->get_template_info_by_id( $templateId );

		echo json_encode( $headers );
		wp_die();
	}
}
