<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer\Processes;

use GFPDF\Plugins\DeveloperToolkit\Writer\AbstractWriter;
use blueliquiddesigns\Mpdf\mPDF;
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
 * Class Import
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Writer\Processes
 *
 * @since   1.0
 */
class Import extends AbstractWriter {

	/**
	 * @var string The path to the currently-loaded PDF document
	 * @since 1.0
	 */
	protected $path;

	/**
	 * The current PDF page IDs in Mpdf
	 *
	 * @var array
	 * @since 1.0
	 */
	protected $page_id = [];

	/**
	 * The current PDF page sizes being used by Mpdf
	 *
	 * @var array A multidimentional array containing the PDF page width and height
	 */
	protected $page_sizes = [];

	/**
	 * Load a PDF with verison 1.4/1.5 of the Adobe Spec for use with Mpdf
	 *
	 * @param string $path The absolute path to the PDF being loaded
	 *
	 * @since 1.0
	 */
	public function addPdf( $path ) {
		$this->path = $path;
		$this->set_pdf_page_sizes( $path );
		$this->load_pdf_pages( $this->mpdf, $path );
	}

	/**
	 * Display a page, or range of pages, from the loaded PDF in the PDF being rendered
	 *
	 * @param int|array $id   The current page to load, or a range of pages to load
	 * @param array     $args Additional Mpdf page settings to pass to Mpdf. See http://mpdf.github.io/reference/mpdf-functions/addpagebyarray.html for all available options
	 *
	 * @throws BadMethodCallException
	 *
	 * @since 1.0
	 */
	public function addPage( $id, $args = [] ) {
		/* Load a PDF range */
		if ( is_array( $id ) ) {
			if ( count( $id ) !== 2 ) {
				throw new BadMethodCallException( 'When $id is an array it should only contain two array items that signify the start and end of the PDF pages to load' );
			}

			for ( $i = (int) $id[0]; $i <= (int) $id[1]; $i++ ) {
				$this->addPageTemplate( $i, $args );
			}
		} else {
			$this->addPageTemplate( $id, $args );
		}
	}

	/**
	 * Add a blank page to the PDF being rendered
	 *
	 * @param array $args Additional Mpdf page settings to pass to Mpdf. See http://mpdf.github.io/reference/mpdf-functions/addpagebyarray.html for all available options
	 *
	 * @since 1.0
	 */
	public function addBlankPage( $args = [] ) {
		$this->mpdf->AddPageByArray(
			array_merge( [
				'orientation' => 'P',
			], $args )
		);
	}

	/**
	 * Returns the current loaded PDF page sizes
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function getPdfPageSize() {
		return $this->page_sizes;
	}

	/**
	 * Returns the current loaded PDF page IDs
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function getPdfPageIds() {
		return $this->page_id;
	}

	/**
	 * Create a temporary Mpdf object and find out/store the PDF page sizes
	 *
	 * @param string $path The path to the PDF being loaded
	 *
	 * @since 1.0
	 */
	protected function set_pdf_page_sizes( $path ) {
		$class = get_class( $this->mpdf );
		$this->load_pdf_pages( new $class(), $path, true );
	}

	/**
	 * Load up the PDF pages for Mpdf to use
	 *
	 * @param mPDF   $mpdf      The current Mpdf object we're loading the pages into
	 * @param string $path      The path to the PDF being loaded
	 * @param bool   $get_sizes Whether to load the PDF pages and get the sizes, or just import the pages
	 *
	 * @since 1.0
	 */
	protected function load_pdf_pages( mPDF $mpdf, $path, $get_sizes = false ) {
		$mpdf->SetImportUse();

		$page_total = $mpdf->SetSourceFile( $path );

		for ( $i = 1; $i <= $page_total; $i++ ) {
			$this->page_id[ $i ] = $mpdf->ImportPage( $i );

			if ( $get_sizes ) {
				$this->page_sizes[ $i ] = $mpdf->useTemplate( $this->page_id[ $i ] );
			}
		}
	}

	/**
	 * Adds a new page to the PDF being rendered and also adds the currently loaded PDF page.
	 *
	 * @param int   $id
	 * @param array $args
	 *
	 * @throws BadMethodCallException
	 *
	 * @since 1.0
	 */
	protected function addPageTemplate( $id, $args = [] ) {
		if ( ! isset( $this->page_id[ $id ] ) ) {
			throw new BadMethodCallException( sprintf( 'The loaded PDF "%s" does not have page #%s', $this->path, $id ) );
		}

		$this->mpdf->AddPageByArray(
			array_merge( [
				'orientation' => 'P',
				'sheet-size'  => [
					$this->page_sizes[ $id ]['w'],
					$this->page_sizes[ $id ]['h'],
				],
			], $args )
		);

		$this->mpdf->useTemplate( $this->page_id[ $id ] );
	}
}