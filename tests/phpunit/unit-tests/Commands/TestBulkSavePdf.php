<?php

namespace GFPDF\Plugins\DeveloperToolkit\Cli\Commands;

use GFPDF\Plugins\DeveloperToolkit\Zip\Zip;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use WP_UnitTestCase;
use ReflectionClass;
use ReflectionMethod;
use Exception;
use GFAPI;
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
 * Class TestBulkSavePdf
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Cli\Commands
 *
 * @group   bulk-commands
 */
class TestBulkSavePdf extends WP_UnitTestCase {

	/**
	 * @var BulkSavePdf
	 * @since 1.0
	 */
	private $class;

	/**
	 * @var string
	 * @since 1.0
	 */
	private $path;

	/**
	 * @var int
	 * @since 1.0
	 */
	private $formId;

	/**
	 * @var array
	 * @since 1.0
	 */
	private $entryIds;

	/**
	 * @var array
	 * @since 1.0
	 */
	private $pdfIds;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		global $gfpdf;

		$this->path = __DIR__ . '/../../../../tmp/';

		$cli = new BulkSavePdfsCli();

		$filesystem  = new Filesystem( new Local( $gfpdf->data->template_tmp_location ) );
		$this->class = new BulkSavePdf(
			$cli, new SavePdf( $cli ),
			GPDFAPI::get_mvc_class( 'Model_PDF' ),
			$gfpdf->gform,
			$filesystem,
			new Zip()
		);

		$this->formId = GFAPI::add_form(
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

		$this->entryIds = GFAPI::add_entries(
			[
				[ 1 => 'Item 1' ],
				[ 1 => 'Item 2' ],
				[ 1 => 'Item 3' ],
				[ 1 => 'Item 4' ],
				[ 1 => 'Item 5' ],
				[
					1            => 'Item 6',
					'is_starred' => 1,
				],
				[
					1            => 'Item 7',
					'is_starred' => 1,
				],
				[
					1         => 'Item 8',
					'is_read' => 1,
				],
				[
					1         => 'Item 9',
					'is_read' => 1,
				],
				[
					1                => 'Item 10',
					'payment_status' => 'paid',
				],
				[
					1                => 'Item 11',
					'payment_status' => 'processing',
				],
				[
					1                => 'Item 12',
					'payment_status' => 'failed',
				],
				[
					1                => 'Item 13',
					'payment_status' => 'active',
				],
				[
					1                => 'Item 14',
					'payment_status' => 'cancelled',
				],
				[
					1                => 'Item 15',
					'payment_status' => 'pending',
				],
				[
					1                => 'Item 16',
					'payment_status' => 'refunded',
				],
				[
					1                => 'Item 17',
					'payment_status' => 'voided',
				],
				[
					1              => 'Item 18',
					'date_created' => '2010-01-01 00:00:00',
				],
				[
					1              => 'Item 19',
					'date_created' => '2010-01-02 00:00:00',
				],
				[
					1              => 'Item 20',
					'date_created' => '2010-01-03 00:00:00',
				],
				[
					1              => 'Item 21',
					'date_created' => '2010-02-01 00:00:00',
				],
				[
					1              => 'Item 22',
					'date_created' => '2010-05-01 00:00:00',
				],
				[
					1              => 'Item 23',
					'date_created' => '2010-06-01 00:00:00',
				],
				[
					1        => 'Item 24',
					'status' => 'trash',
				],
				[
					1        => 'Item 25',
					'status' => 'trash',
				],
			], $this->formId
		);

		$pdfId   = [];
		$pdfId[] = GPDFAPI::add_pdf(
			$this->formId, [
				'name'     => 'API PDF',
				'template' => 'zadani',
				'filename' => 'Custom_Filename_{:1}',
			]
		);

		$pdfId[] = GPDFAPI::add_pdf(
			$this->formId, [
				'name'             => 'API PDF 2',
				'template'         => 'zadani',
				'filename'         => 'Conditional_Template_{:1}',
				'conditionalLogic' => [
					'actionType' => 'show',
					'logicType'  => 'all',
					'rules'      => [
						[
							'fieldId'  => 1,
							'operator' => 'is',
							'value'    => 'Item 4',
						],
					],
				],
			]
		);

		$this->pdfIds = $pdfId;

		parent::setUp();
	}

	/**
	 * Sets a method to public for testing
	 *
	 * @param string $name Name of protected / private method
	 *
	 * @return ReflectionMethod
	 */
	protected function getMethod( $name ) {
		$class  = new ReflectionClass( BulkSavePdf::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );
		return $method;
	}

	/**
	 * @since 1.0
	 */
	//public function testBulkSavePdfs() {
	//ob_start();
	//$this->class->search( [ $this->formId, '../../../tmp/pdfs' ], [ 'group' => 'Y' ] );
	//$content = ob_get_clean();
	//}

	/**
	 * @since 1.0
	 */
	public function testTranslateGroupName() {
		$method = $this->getMethod( 'translateGroupName' );

		$this->assertFalse( $method->invokeArgs( $this->class, [ false, false ] ) );
		$this->assertFalse( $method->invokeArgs( $this->class, [ '', false ] ) );
		$this->assertEquals( 5, $method->invokeArgs( $this->class, [ 'id', [ 'id' => 5 ] ] ) );

		$this->assertEquals( '05-11-2018', $method->invokeArgs( $this->class, [ 'd/m/Y', [ 'date_created' => '2018-11-05 10:30:00' ] ] ) );
		$this->assertEquals( 'Mon November 18', $method->invokeArgs( $this->class, [ 'D F y', [ 'date_created' => '2018-11-05 10:30:00' ] ] ) );
		$this->assertEquals( '6-5-2018', $method->invokeArgs( $this->class, [ 'n/j/Y', [ 'date_created' => '2018-06-05 10:30:00' ] ] ) );

		$this->assertEquals( 'Folder- Item 1', $method->invokeArgs( $this->class, [ 'Folder: {:1}', GFAPI::get_entry( $this->entryIds[0] ) ] ) );
		$this->assertEquals( 'Folder- Item 2', $method->invokeArgs( $this->class, [ 'Folder: {:1}', GFAPI::get_entry( $this->entryIds[1] ) ] ) );
	}

	/**
	 * @since 1.0
	 */
	public function testCreateProgressBar() {
		$method         = $this->getMethod( 'createProgressBar' );
		$formPdfsMethod = $this->getMethod( 'getFormPdfs' );
		$formPdfs       = $formPdfsMethod->invokeArgs( $this->class, [ $this->formId ] );

		$entries = [];
		foreach ( $this->entryIds as $entry_id ) {
			$entry = GFAPI::get_entry( $entry_id );
			if ( ! is_wp_error( $entry ) ) {
				$entries[] = $entry;
			}
		}

		$this->assertEquals( 26, $method->invokeArgs( $this->class, [ $entries, $formPdfs, $this->pdfIds ] ) );
		$this->assertEquals( 25, $method->invokeArgs( $this->class, [ $entries, $formPdfs, [ $this->pdfIds[0] ] ] ) );
		$this->assertEquals( 1, $method->invokeArgs( $this->class, [ $entries, $formPdfs, [ $this->pdfIds[1] ] ] ) );
	}

	public function testGetEntriesBySearch() {
		$method = $this->getMethod( 'getEntriesBySearch' );

	}
}

class BulkSavePdfsCli implements InterfaceCli {
	public function log( $text ) {
		echo $text;
	}

	public function warning( $text ) {
		echo $text;
	}

	public function success( $text ) {
		echo $text;
	}

	public function error( $text, $exit = true ) {
		echo $text;
		throw new Exception();
	}

	public function getResponse( $text ) {

	}

	public function outputInFormat( $format, $data, $keys ) {
		echo json_encode( [ $format, $data, $keys ] );
	}

	public function createProgressBar( $message, $ticks ) {
		return $ticks;
	}
}
