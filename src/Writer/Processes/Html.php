<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer\Processes;

use GFPDF\Plugins\DeveloperToolkit\Writer\AbstractWriter;

use BadMethodCallException;

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
 * @package GFPDF\Plugins\DeveloperToolkit\Writer\Processes
 *
 * @since   1.0
 */
class Html extends AbstractWriter {

	/**
	 * Add HTML directly to Mpdf using the current Mpdf Y pointer position
	 *
	 * Normal PDF templates are sandboxed directly to this method. The Toolkit-templates are not and give you full access
	 * to the Mpdf object, as well as our helper object `$w`. Use this method when you aren't auto-filling a PDF, or want
	 * to create a hybrid (some pages will auto-fill a PDF, while others will be dynamically generated with HTML).
	 *
	 * ## Example
	 *
	 *      // Add HTML to PDF
	 *      $w->addHtml( '<h1>This is my title</h1><p>This is my content</p>' );
	 *
	 * @param string $html The freeflow HTML markup to add to Mpdf. Refer to the Mpdf documentation about supported markup: http://mpdf.github.io/
	 *
	 * @throws BadMethodCallException
	 *
	 * @since 1.0
	 */
	public function addHtml( $html ) {
		$this->mpdf->WriteHTML( (string) $html );
	}
}
