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
 * Class TestGetPdfStatus
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Cli\Commands
 *
 * @group   commands
 */
class TestGetPdfStatus extends WP_UnitTestCase {

	/**
	 * @var GetPdfStatus
	 * @since 1.0
	 */
	private $class;

	/**
	 * @var string
	 * @since 1.0
	 */
	private $path;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->path = __DIR__ . '/../../../../tmp/';

		$cli = $this->getMockBuilder( GetPdfStatusCli::class )
		            ->setMethods( [ 'getResponse' ] )
		            ->getMock();

		$this->class = new GetPdfStatus( \GPDFAPI::get_data_class(), \GPDFAPI::get_options_class(), $cli );

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function testVersion() {
		ob_start();
		$this->class->version();
		$content = ob_get_clean();

		$this->assertEquals( PDF_EXTENDED_VERSION, $content );
	}

	/**
	 * @since 1.0
	 */
	public function testStatus() {
		ob_start();
		$this->class->status( [], [] );
		$results = json_decode( ob_get_clean(), true );

		$this->assertEquals( 'table', $results[0] );
		$this->assertCount( ( ! is_multisite() ) ? 17 : 18, $results[1] );
		$this->assertCount( 2, $results[2] );

		ob_start();
		$this->class->status( [], [ 'format' => 'json' ] );
		$results = json_decode( ob_get_clean(), true );

		$this->assertEquals( 'json', $results[0] );
	}
}

class GetPdfStatusCli implements InterfaceCli {
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
}