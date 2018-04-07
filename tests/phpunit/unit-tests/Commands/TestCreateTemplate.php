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
 * Class TestCreateTemplate
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Cli\Commands
 *
 * @group   commands
 */
class TestCreateTemplate extends WP_UnitTestCase {

	/**
	 * @var CreateTemplate
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

		$cli = $this->getMockBuilder( Cli::class )
		            ->setMethods( [ 'getResponse' ] )
		            ->getMock();

		$cli->method( 'getResponse' )
		    ->will( $this->onConsecutiveCalls(
			    'This is the template description',
			    'Gravity PDF',
			    'https://gravitypdf',
			    'Universal',
			    'GPLv2',
			    '4.4',
			    'funny, sad, happy'
		    ) );

		$this->class = new CreateTemplate( $this->path, $cli );

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function tearDown() {
		@unlink( $this->path . 'my-template.php' );
		@unlink( $this->path . 'config/my-template.php' );
		@rmdir( $this->path . 'config' );

		parent::tearDown();
	}

	/**
	 * @since 1.0
	 */
	public function testCreateTemplate() {
		/* Test file is created */
		ob_start();

		$class = $this->class;
		$class( [ 'My Template' ], [] );
		$content = ob_get_clean();

		$this->assertRegExp( '/Your template has been generated and saved to/', $content );
		$this->assertRegExp( '/Happy PDFing!/', $content );

		/* Test the contents of the file is correct */
		$fileContents = file_get_contents( $this->path . 'my-template.php' );

		$this->assertRegExp( '/\* Template Name: My Template/', $fileContents );
		$this->assertRegExp( '/\* Version: 1.0/', $fileContents );
		$this->assertRegExp( '/\* Description: This is the template description/', $fileContents );
		$this->assertRegExp( '/\* Author: Gravity PDF/', $fileContents );
		$this->assertRegExp( '/\* Author URI: https:\/\/gravitypdf/', $fileContents );
		$this->assertRegExp( '/\* Group: Universal/', $fileContents );
		$this->assertRegExp( '/\* License: GPLv2/', $fileContents );
		$this->assertRegExp( '/\* Required PDF Version: 4.4/', $fileContents );
		$this->assertRegExp( '/\* Tags: funny, sad, happy/', $fileContents );

		/* Test error is thrown due to a file with the same name already existing */
		ob_start();
		try {
			$class( [ 'My Template' ], [] );
		} catch ( Exception $e ) {

		}

		$content = ob_get_clean();

		$this->assertRegExp( '/A PDF template with the name "my-template.php" already exists./', $content );
	}

	/**
	 * @since 1.0
	 */
	public function testCreateToolkitTemplate() {
		ob_start();

		$class = $this->class;
		$class( [ 'My Template' ], [ 'enable-toolkit' => true ] );
		$content = ob_get_clean();

		$this->assertRegExp( '/Your template has been generated and saved to/', $content );
		$this->assertRegExp( '/Happy PDFing!/', $content );

		/* Test the contents of the file is correct */
		$fileContents = file_get_contents( $this->path . 'my-template.php' );

		$this->assertRegExp( '/\* Template Name: My Template/', $fileContents );
		$this->assertRegExp( '/\* Version: 1.0/', $fileContents );
		$this->assertRegExp( '/\* Description: This is the template description/', $fileContents );
		$this->assertRegExp( '/\* Author: Gravity PDF/', $fileContents );
		$this->assertRegExp( '/\* Author URI: https:\/\/gravitypdf/', $fileContents );
		$this->assertRegExp( '/\* Group: Universal/', $fileContents );
		$this->assertRegExp( '/\* License: GPLv2/', $fileContents );
		$this->assertRegExp( '/\* Required PDF Version: 4.4/', $fileContents );
		$this->assertRegExp( '/\* Tags: funny, sad, happy/', $fileContents );
		$this->assertRegExp( '/\* Toolkit: true/', $fileContents );

		$this->assertRegExp( '/\* \$w \(A helper class that does the heavy lifting and PDF manipulation\)/', $fileContents );
		$this->assertRegExp( '/\* \$mpdf \(The raw Mpdf object\)/', $fileContents );
		$this->assertRegExp( '/\* \@var GFPDF\\\Plugins\\\DeveloperToolkit\\\Writer\\\Writer \$w/', $fileContents );
		$this->assertRegExp( '/\* \@var mPDF \$mpdf/', $fileContents );

		$this->assertRegExp( '/\$w->beginStyles\(\);/', $fileContents );
		$this->assertRegExp( '/\$w->endStyles\(\);/', $fileContents );
		$this->assertRegExp( '/\$w->addPdf\( \_\_DIR\_\_ \. \'\/pdfs\/my-pdf-document\.pdf\' \);/', $fileContents );
	}

	/**
	 * @since 1.0
	 */
	public function testCreateTemplateNoHeaders() {
		ob_start();
		$class = $this->class;
		$class( [ 'My Template' ], [ 'skip-headers' => true ] );
		$content = ob_get_clean();

		$this->assertRegExp( '/Your template has been generated and saved to/', $content );
		$this->assertRegExp( '/Happy PDFing!/', $content );

		/* Test the contents of the file is correct */
		$fileContents = file_get_contents( $this->path . 'my-template.php' );

		$this->assertRegExp( '/\* Template Name: My Template/', $fileContents );
		$this->assertRegExp( '/\* Version: 1.0/', $fileContents );
		$this->assertRegExp( '/\* Description: /', $fileContents );
		$this->assertRegExp( '/\* Author: /', $fileContents );
		$this->assertRegExp( '/\* Author URI: /', $fileContents );
		$this->assertRegExp( '/\* Group: /', $fileContents );
		$this->assertRegExp( '/\* Required PDF Version: 4.4.0/', $fileContents );
	}

	/**
	 * @since 1.0
	 */
	public function testCreateToolkitTemplateNoHeaders() {
		ob_start();
		$class = $this->class;
		$class( [ 'My Template' ], [ 'skip-headers' => true, 'enable-toolkit' => true ] );
		$content = ob_get_clean();

		$this->assertRegExp( '/Your template has been generated and saved to/', $content );
		$this->assertRegExp( '/Happy PDFing!/', $content );

		/* Test the contents of the file is correct */
		$fileContents = file_get_contents( $this->path . 'my-template.php' );

		$this->assertRegExp( '/\* Template Name: My Template/', $fileContents );
		$this->assertRegExp( '/\* Version: 1.0/', $fileContents );
		$this->assertRegExp( '/\* Description: /', $fileContents );
		$this->assertRegExp( '/\* Author: /', $fileContents );
		$this->assertRegExp( '/\* Author URI: /', $fileContents );
		$this->assertRegExp( '/\* Group: /', $fileContents );
		$this->assertRegExp( '/\* Required PDF Version: 4.4.0/', $fileContents );
		$this->assertRegExp( '/\* Toolkit: true/', $fileContents );
	}

	/**
	 * @since 1.0
	 */
	public function testCreateTemplateConfig() {
		ob_start();
		$class = $this->class;
		$class( [ 'My Template' ], [ 'skip-headers' => true, 'enable-config' => true ] );
		$content = ob_get_clean();

		$this->assertRegExp( '/Your template has been generated and saved to/', $content );
		$this->assertRegExp( '/Your template configuration file has been generated and saved to/', $content );
		$this->assertRegExp( '/Happy PDFing!/', $content );

		/* Test the contents of the file is correct */
		$fileContents = file_get_contents( $this->path . '/config/my-template.php' );

		$this->assertRegExp( '/class My\_Template implements Helper\_Interface\_Config, Helper\_Interface\_Setup\_TearDown/', $fileContents );
		$this->assertRegExp( '/public function setUp\(\) {/', $fileContents );
		$this->assertRegExp( '/public function tearDown\(\) {/', $fileContents );
		$this->assertRegExp( '/public function configuration\(\) {/', $fileContents );
	}

	/**
	 * @since 1.0
	 */
	public function testCreateTemplateConfigWithExistingTemplate() {
		ob_start();
		$class = $this->class;
		$class( [ 'My Template' ], [ 'skip-headers' => true ] );
		$content = ob_get_clean();

		$this->assertRegExp( '/Your template has been generated and saved to/', $content );
		$this->assertRegExp( '/Happy PDFing!/', $content );

		ob_start();
		$class = $this->class;
		$class( [ 'My Template' ], [ 'enable-config' => true ] );
		$content = ob_get_clean();

		$this->assertNotRegExp( '/Your template has been generated and saved to/', $content );
		$this->assertRegExp( '/Your template configuration file has been generated and saved to/', $content );
		$this->assertRegExp( '/Happy PDFing!/', $content );
	}
}

class Cli implements InterfaceCli {
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
}