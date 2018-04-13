<?php

namespace GFPDF\Plugins\DeveloperToolkit\Cli\Commands;

use WP_UnitTestCase;
use Exception;

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
 * Class TestSavePdf
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Cli\Commands
 *
 * @group   commands
 */
class TestSavePdf extends WP_UnitTestCase {

	/**
	 * @var SavePdf
	 * @since 1.0
	 */
	private $class;

	/**
	 * @var string
	 * @since 1.0
	 */
	private $path;

	/**
	 * @var string
	 * @since 1.0
	 */
	private $formId;

	/**
	 * @var array
	 * @since 1.0
	 */
	private $entryIds;

	/**
	 * @var string
	 * @since 1.0
	 */
	private $pdfId;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->path  = __DIR__ . '/../../../../tmp/samples/';
		$this->class = new SavePdf( new SavePdfsCli() );

		$this->formId = \GFAPI::add_form(
			[
				'title'  => 'Sample Form',
				'fields' => [
					[
						'id'    => 1,
						'label' => 'Test',
						'type'  => 'text',
					],
				],
			]
		);

		$this->entryIds = \GFAPI::add_entries(
			[
				[ 1 => 'Item 1' ],
				[ 1 => 'Item 2' ],
			], $this->formId
		);

		$this->pdfId = \GPDFAPI::add_pdf(
			$this->formId, [
				'name'     => 'API PDF',
				'template' => 'zadani',
				'filename' => 'sample',
			]
		);

		parent::setUp();
	}

	public function tearDown() {
		$misc = \GPDFAPI::get_misc_class();
		$misc->rmdir( $this->path );

		parent::tearDown();
	}

	/**
	 * Test the PDF generates and saves to file, if needed
	 *
	 * @since 1.0
	 */
	public function testSavePdf() {
		/* Test PDF generates */
		ob_start();
		$class = $this->class;
		$class( [ $this->entryIds[0], $this->pdfId ] );
		$this->assertStringStartsWith( '%PDF-', ob_get_clean() );

		/* Test PDF Saves to file */
		ob_start();

		$class( [ $this->entryIds[0], $this->pdfId ], [ 'path' => $this->path ] );
		$class( [ $this->entryIds[0], $this->pdfId ], [ 'path' => $this->path ] );

		$this->assertFileExists( $this->path . 'sample.pdf' );
		$this->assertFileExists( $this->path . 'sample1.pdf' );

		/* Ensure error is thrown */
		$class(
			[ $this->entryIds[0], $this->pdfId ], [
				'exists' => 'override',
				'path'   => $this->path,
			]
		);

		try {
			$class(
				[ $this->entryIds[0], $this->pdfId ], [
					'exists' => 'error',
					'path'   => $this->path,
				]
			);
		} catch ( SavePdfsCliError $e ) {

		}

		$this->assertNotNull( $e );

		ob_end_clean();
	}

	/**
	 * Test the errors generated, and the warning/error switch
	 *
	 * @since 1.0
	 */
	public function testSavePdfErrors() {
		ob_start();
		$class = $this->class;

		/* Check for incorrect entry ID */
		try {
			$class( [ 1000, $this->pdfId ] );
		} catch ( SavePdfsCliError $entryError ) {

		}

		$this->assertNotNull( $entryError );
		$this->assertEquals( 'Make sure to pass in a valid Gravity Forms Entry ID', ob_get_clean() );

		/* Check for incorrect PDF ID */
		ob_start();
		try {
			$class( [ $this->entryIds[0], '000000' ], [ 'warn' => true ] );
		} catch ( SavePdfsCliWarning $pdfIdWarning ) {

		}

		$this->assertNotNull( $pdfIdWarning );
		$this->assertEquals( 'Could not located the PDF Settings. Ensure you pass in a valid PDF ID.', ob_get_clean() );
	}
}

class SavePdfsCliWarning extends Exception {
}

class SavePdfsCliError extends Exception {
}

class SavePdfsCli implements InterfaceCli {
	public function log( $text ) {
		echo $text;
	}

	public function warning( $text ) {
		echo $text;
		throw new SavePdfsCliWarning();
	}

	public function success( $text ) {
		echo $text;
	}

	public function error( $text, $exit = true ) {
		echo $text;
		throw new SavePdfsCliError();
	}

	public function getResponse( $text ) {

	}

	public function outputInFormat( $format, $data, $keys ) {
		echo json_encode( [ $format, $data, $keys ] );
	}

	public function createProgressBar( $message, $ticks ) {

	}
}
