<?php

namespace GFPDF\Plugins\DeveloperToolkit\Loader;

use GFPDF\Plugins\DeveloperToolkit\Writer\Writer;
use WP_UnitTestCase;
use mPDF;

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
 * Class TestLoader
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Loader
 *
 * @group   loader
 */
class TestLoader extends WP_UnitTestCase {

	/**
	 * @var Loader
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = new Loader();
		$this->class->init();

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function testMaybeSkipPdfHtmlRender() {
		$this->assertFalse( $this->class->maybeSkipPdfHtmlRender( false, [ 'settings' => [] ] ) );
		$this->assertTrue( $this->class->maybeSkipPdfHtmlRender( false, [ 'settings' => [ 'toolkit' => true ] ] ) );
	}

	/**
	 * @since 1.0
	 */
	public function testHandleToolkitTemplate() {

		add_filter(
			'gfpdf_developer_toolkit_template_args', function( $args ) {
				$this->assertArrayHasKey( 'w', $args );
				$this->assertArrayHasKey( 'mpdf', $args );
				$this->assertArrayHasKey( 'form', $args );
				$this->assertArrayHasKey( 'entry', $args );
				$this->assertArrayHasKey( 'form_data', $args );
				$this->assertArrayHasKey( 'fields', $args );
				$this->assertArrayHasKey( 'config', $args );
				$this->assertArrayHasKey( 'settings', $args );
				$this->assertArrayHasKey( 'gfpdf', $args );

				$this->assertInstanceOf( Writer::class, $args['w'] );

				return $args;
			}
		);

		add_action(
			'gfpdf_developer_toolkit_pre_load_template', function( $template, $args ) {
				$mpdf    = $args['mpdf'];
				$content = $mpdf->sampleContent;

				$this->assertRegExp( '/font\-family\: \"My custom font\", sans\-serif\;/', $content );
				$this->assertRegExp( '/font\-size\: 14pt\;/', $content );
				$this->assertRegExp( '/line\-height\: 14pt\;/', $content );
				$this->assertRegExp( '/color\: \#EEE\;/', $content );

				$w           = $args['w'];
				$multiConfig = $w->getMultiConfig();

				$this->assertEquals( 14, $multiConfig['font-size'] );
				$this->assertEquals( 19.6, $multiConfig['line-height'] );
			}, 10, 2
		);

		$this->class->handleToolkitTemplate(
			[
				'w'         => '',
				'mpdf'      => '',
				'form'      => '',
				'entry'     => '',
				'form_data' => '',
				'fields'    => '',
				'config'    => '',
				'settings'  => [
					'font'        => 'My custom font',
					'font_size'   => 14,
					'font_colour' => '#EEE',
				],
				'gfpdf'     => '',
			], new Helper()
		);

	}
}

class Helper {
	public function get_template_path() {
		return __DIR__ . '/../../dummy-template.php';
	}

	public function get_pdf_class() {
		return new TestMpdf();
	}
}

class TestMpdf extends mPDF {
	public $sampleContent = '';

	public function WriteHTML( $content, $type = 1 ) {
		$this->sampleContent .= $content;
	}
}
