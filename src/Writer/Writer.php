<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer;

use BadMethodCallException;

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
 * This class acts as a router for all public methods found in the GFPDF\Plugins\DeveloperToolkit\Writer namespace.
 * Objects that impliment InterfaceWriter are registered when this object is first created. When methods that don't exist
 * are called they get routed through the magic method __call(), which searches through each registered object for a matching
 * method name. If found, it passes the arguments directly to the method. This allows a simple API for the
 * Toolkit-enabled template files, while still making our code easily maintainable and testible.
 *
 * ## Examples
 *
 * Writer is automatically injected into your Toolkit-enabled templates and is accessible via `$w`. To enable this feature,
 * add `Toolkit: true` to the PDF template header information (where `Template Name` and `Group` goes). Alternatively,
 * use the WP CLI command `wp gpdf create-template "My Custom Template" --enable-toolkit`. To get more information about
 * the WP CLI command use `wp gpdf create-template --help`.
 *
 *      // Load our custom CSS styles that'll apply to the PDF template
 *      $w->beginStyles();
 *      ?>
 *      <style>
 *          body {
 *              color: #999;
 *          }
 *      </style>
 *      <?php
 *      $w->endStyles();
 *
 *      // Load our PDF we want to overlay content onto
 *      $w->addPdf( __DIR__ . '/pdfs/path-to-document.pdf' );
 *
 *      // Load page 1 which we can then overlay content to
 *      $w->addPage( 1 );
 *
 *      // Add the text 'My Content' at 50mm from the left, 100mm from the top with a width of 30mm and a height of 5mm
 *      $w->add( 'My content', [ 50, 100, 30, 5 ] );
 *
 *      // Add a multi-line text snippet (gives you better line-height)
 *      $w->addMulti( 'Content<br>Content<br>Content', [ 20, 80, 100, 50 ] );
 *
 *      // Load pages 2 thru 4
 *      $w->addPage( [ 2, 4 ] );
 *
 *      // Add a Checkbox to page 4 at 100mm from the left and 30mm from the top
 *      $w->tick( [ 100, 30 ] );
 *
 *      // Add an Ellipse to page 4 at 120mm from the left and 50mm from the top (this will mark the center of the ellipse), with a 5mm width and 3mm height
 *      $w->ellipse( [ 120, 50, 5, 3 ] );
 *
 * For more examples, view the documentation in GFPDF\Plugins\DeveloperToolkit\Writer\Processes
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Writer
 *
 * @method void addPdf( string $path ) Load a PDF with verison 1.4 or 1.5 of the Adobe Spec for use with Mpdf. See GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Import for full details.
 * @method void addPage( int $id, array $args = [] )  Display a page, or range of pages, from the loaded PDF in the PDF being rendered. See GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Import for full details.
 * @method void addBlankPage( array $args = [] )  Add a blank page to the PDF being rendered. See GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Import for full details.
 * @method array getPdfPageSize()  Returns the current loaded PDF page sizes. See GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Import for full details.
 * @method array getPdfPageId()  Returns the current loaded PDF page IDs. See GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Import for full details.
 * @method void add( string $html, array $position = [], string $overflow = 'auto' )  Add content to the PDF that has a fixed positioned and is better suited for a single line of text. See GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Single for full details.
 * @method void configMulti( array $config )  Sets the new default configuration to apply to all new multi elements. See GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Multi for full details.
 * @method void addMulti( string $html, array $position = [], string $overflow = 'auto', array $config = [] )  Add content to the PDF that has a fixed positioned and is better configured for multiline output. See GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Multi for full details.
 * @method void configTick( array $config )  Sets the new default configuration to apply to all new tick elements. See GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Tick for full details.
 * @method void tick( array $position, array $config = [] )  Adds a tick character to the PDF at the requested coordinates. See GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Tick for full details.
 * @method void ellipse( array $position )  Adds an Ellipse to the PDF being rendered. See GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Ellipse for full details.
 * @method void addHtml( string $html )  Add HTML directly to Mpdf using the current Y pointer position. See GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Html for full details.
 * @method void beginStyles()  Captures any output and stores it in our buffer. See GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Styles for full details.
 * @method void endStyles()  Takes the captured buffer, strips the `style` HTML tags and adds it to Mpdf. See GFPDF\Plugins\DeveloperToolkit\Writer\Processes\Styles for full details.
 *
 * @since   1.0
 */
class Writer extends AbstractWriter {

	/**
	 * @var InterfaceWriter[]
	 * @since 1.0
	 */
	protected $classes = [];

	/**
	 * Register all classes on initialisation
	 *
	 * @param InterfaceWriter[] $classes
	 *
	 * @since 1.0
	 */
	public function __construct( $classes = [] ) {
		foreach ( $classes as $class ) {
			$this->register_class( $class );
		}
	}

	/**
	 * Register the class with the Writer
	 *
	 * @param InterfaceWriter $class
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	public function register_class( InterfaceWriter $class ) {
		$this->classes[] = $class;
	}

	/**
	 * Search through the registered classes for a public method match and call it with the passed arguments
	 *
	 * @param string $name      The method being called
	 * @param array  $arguments The method arguments
	 *
	 * @return mixed
	 *
	 * @throws BadMethodCallException
	 */
	public function __call( $name, $arguments ) {
		foreach ( $this->classes as $class ) {
			if ( is_callable( [ $class, $name ] ) ) {
				$this->maybe_inject_mpdf( $class );

				return call_user_func_array( [ $class, $name ], $arguments );
			}
		}

		throw new BadMethodCallException( sprintf( 'The method "%s" could not be found.', $name ) );
	}

	/**
	 * Inject the Mpdf object to our registered classes (if required) right before calling the chosen method
	 *
	 * @param InterfaceWriter $class
	 *
	 * @since 1.0
	 */
	protected function maybe_inject_mpdf( $class ) {
		if ( ! $class->is_mpdf_set() ) {
			$class->set_mpdf( $this->mpdf );
		}
	}
}