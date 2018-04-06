<?php

namespace GFPDF\Plugins\DeveloperToolkit\Loader;

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

    Copyright (C) 2018, Blue Liquid Designs

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
		$helper = $this->getMock( Helper::class, [ 'get_template_path' ] );
		$helper->method( 'get_template_path' )
		       ->will( $this->onConsecutiveCalls(
			       __DIR__ . '/../pdfs/sample1.php',
			       __DIR__ . '/../pdfs/sample2.php'
		       ) );

		$this->assertFalse( $this->class->maybeSkipPdfHtmlRender( false, [], $helper ) );

		$this->assertTrue( $this->class->maybeSkipPdfHtmlRender( false, [], $helper ) );
	}

	/**
	 * @since 1.0
	 */
	public function testHandleToolkitTemplate() {
		$this->class->handleToolkitTemplate( [
			'w'         => '',
			'mpdf'      => '',
			'form'      => '',
			'entry'     => '',
			'form_data' => '',
			'fields'    => '',
			'config'    => '',
			'settings'  => '',
			'gfpdf'     => '',
		], new Helper() );

		add_filter( 'gfpdf_developer_toolkit_template_args', function( $args ) {
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
		} );
	}
}

class Helper {
	public function get_template_path() {
	}

	public function get_pdf_class() {
		return new mPDF();
	}
}