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
	protected $pageId = [];

	/**
	 * The current PDF page sizes being used by Mpdf
	 *
	 * @var array A multidimentional array containing the PDF page width and height
	 */
	protected $pageSizes = [];

	/**
	 * Load a PDF with version 1.4 or 1.5 of the Adobe Spec for use with Mpdf.
	 *
	 * ## Example
	 *
	 *      // Import a PDF
	 *      $w->addPdf( __DIR__ . '/pdfs/load-document.pdf' );
	 *
	 *      // Load page #1 from PDF imported
	 *      $w->addPage(1);
	 *
	 *      // Import a 2nd PDF (you won't be able to load pages from PDF #1 after importing PDF #2)
	 *      $w->addPdf( __DIR__ . '/pdfs/load-another-document.pdf' );
	 *
	 *      // Load page #1 from 2nd PDF Imported
	 *      $w->addPage(1);
	 *
	 * @param string $path The absolute path to the PDF being loaded
	 *
	 * @throws BadMethodCallException Thrown if the PDF file could not be found
	 * @throws \Exception An exception will be thrown if you load a PDF that isn't version 1.4 or 1.5 of the Adobe Specification
	 *
	 * @since 1.0
	 */
	public function addPdf( $path ) {
		if ( ! is_file( $path ) ) {
			throw new BadMethodCallException( sprintf( 'Could not find %s', $path ) );
		}

		$this->path = $path;
		$this->setPdfPageSizes( $path );
		$this->loadPdfPages( $this->mpdf, $path );
	}

	/**
	 * Display a page, or range of pages, from the loaded PDF in the PDF being rendered
	 *
	 * ## Example
	 *
	 *      // Import a PDF
	 *      $w->addPdf( __DIR__ . '/pdfs/load-document.pdf' );
	 *
	 *      // Load page #1 from PDF
	 *      $w->addPage( 1 );
	 *
	 *      // Load a range of pages (pages 2 / 3 / 4 / 5) from the PDF
	 *      $w->addPage( [ 2, 5 ] );
	 *
	 *      // Load page #2 and set the top-margin to 20mm from the top of the page. This is useful when used with `$w->html()`
	 *      $w->addPage( 2, [ 'margin-top' => 20 ] )
	 *
	 * @param int|array $id   The current page to load, or a range of pages to load (max 2 array items)
	 * @param array     $args Additional page settings to pass to Mpdf. We recommend against trying to override `sheet-size` or `orientation` as the method calculates this automatically based off the PDF page size. See http://mpdf.github.io/reference/mpdf-functions/addpagebyarray.html for all available options
	 *
	 * @throws BadMethodCallException When `$id` is an array it should only contain two array items that signify the start and end of the PDF pages to load
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
	 * Add a blank page to the PDF being rendered. Use in conjunction with `$w->html()`.
	 *
	 * ## Example
	 *
	 *      // Load a new page in the generated PDF with a sheet size of 200mm wide by 400mm heigh
	 *      $w->addBlankPage( [ 'sheet-size' => [ 200, 400 ] ] );
	 *
	 * @param array $args Additional page settings to pass to Mpdf. See http://mpdf.github.io/reference/mpdf-functions/addpagebyarray.html for all available options
	 *
	 * @since 1.0
	 */
	public function addBlankPage( $args = [] ) {
		$this->mpdf->AddPageByArray(
			array_merge(
				[
					'orientation' => 'P',
				],
				$args
			)
		);
	}

	/**
	 * Returns the current imported PDF page sizes (with `$w->addPdf()`). The class handles the page sizes internally, so you shouldn't need to use this method in your templates.
	 *
	 * @return array The returned format will include the array key referencing the page number and the value referencing the page width and height: [ 1 => [ 200, 400 ], 2 => [ 150, 400 ] ]
	 *
	 * @since 1.0
	 */
	public function getPdfPageSize() {
		return $this->pageSizes;
	}

	/**
	 * Returns the current loaded PDF page IDs. The class handles the IDs internally, so you shouldn't need to use this method in your templates.
	 *
	 * @return array The returned format will include the array key referencing the page number and the value referencing the page ID: [ 1 => 'ID1', 2 => 'ID2' ]
	 *
	 * @since 1.0
	 */
	public function getPdfPageIds() {
		return $this->pageId;
	}

	/**
	 * Create a temporary Mpdf object and find out/store the PDF page sizes
	 *
	 * @param string $path The path to the PDF being loaded
	 *
	 * @since 1.0
	 */
	protected function setPdfPageSizes( $path ) {
		$class  = get_class( $this->mpdf );
		$config = ( $class === 'mPDF' ) ? 'c' : [
			'mode'    => 'c',
			'tempDir' => $this->mpdf->tempDir,
		];
		$this->loadPdfPages( new $class( $config ), $path, true );
	}

	/**
	 * Load up the PDF pages for Mpdf to use
	 *
	 * @param \mPDF|\Mpdf\Mpdf $mpdf     The current Mpdf object we're loading the pages into
	 * @param string           $path     The path to the PDF being loaded
	 * @param bool             $getSizes Whether to load the PDF pages and get the sizes, or just import the pages
	 *
	 * @throws \BadMethodCallException Throws when $mpdf isn't valid
	 *
	 * @since 1.0
	 */
	protected function loadPdfPages( $mpdf, $path, $getSizes = false ) {

		if ( ! $mpdf instanceof \mPDF && ! $mpdf instanceof \Mpdf\Mpdf ) {
			throw new \BadMethodCallException( '$mpdf must be \mPDF or \Mpdf\Mpdf' );
		}

		$mpdf->SetImportUse();

		if ( $getSizes ) {
			$this->pageId    = [];
			$this->pageSizes = [];
		}

		$page_total = $mpdf->SetSourceFile( $path );
		for ( $i = 1; $i <= $page_total; $i++ ) {
			$this->pageId[ $i ] = $mpdf->ImportPage( $i );

			if ( $getSizes ) {
				$this->pageSizes[ $i ] = $mpdf->useTemplate( $this->pageId[ $i ] );
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
		if ( ! isset( $this->pageId[ $id ] ) ) {
			throw new BadMethodCallException( sprintf( 'The loaded PDF "%s" does not have page #%s', $this->path, $id ) );
		}

		$this->mpdf->AddPageByArray(
			array_merge(
				[
					'orientation' => 'P',
					'sheet-size'  => [
						$this->pageSizes[ $id ]['w'],
						$this->pageSizes[ $id ]['h'],
					],
				],
				$args
			)
		);

		$this->mpdf->useTemplate( $this->pageId[ $id ] );
	}
}
