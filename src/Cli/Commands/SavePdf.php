<?php

namespace GFPDF\Plugins\DeveloperToolkit\Cli\Commands;

use RuntimeException;
use UnexpectedValueException;
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
 * Processes the `wp gpdf save-pdf` WP CLI Command
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Cli\Commands
 */
class SavePdf {

	/**
	 * @var \WP_CLI
	 */
	protected $cli;

	/**
	 * @param InterfaceCli $cli The WP_CLI class, or a suitable drop-in replacement (for testing)
	 *
	 * @since 1.0
	 */
	public function __construct( InterfaceCli $cli ) {
		$this->cli = $cli;
	}

	/**
	 * Generates a single PDF and saves to disk or STDOUT
	 *
	 * ## OPTIONS
	 *
	 * <entry-id>
	 * : The ID of the Gravity Forms entry
	 *
	 * <pdf-id>
	 * : The ID of the PDF settings we should use to generate the PDF
	 *
	 * [--path=<path>]
	 * : The path to save the PDF to. If not passed, PDF output to STDOUT
	 *
	 * [--exists=<exists>]
	 * : If the PDF already exists in <path> 'increment' will add a counter to the PDF filename until a unique name is found. 'override' will override the conflicting file. 'error' will throw an error if the PDF already exists.
	 * ---
	 * default: increment
	 * options:
	 *     - increment
	 *     - override
	 *     - error
	 * ---
	 *
	 * [--warn]
	 * : If error occurs generating / saving PDF a warning will be logged to the CLI instead of an error, which stops execution
	 *
	 * ## EXAMPLES
	 *
	 *     # Generate a PDF for entry 120 with the PDF ID 5886dcb38f30b and save it to ./pdfs/
	 *     $ wp gpdf pdf 120 5886dcb38f30b ./pdfs/
	 *
	 *     # Generate a PDF for entry 120 with the PDF ID 5886dcb38f30b and save it to ./pdfs/, overriding any PDF with the same name
	 *     $ wp gpdf pdf 120 5886dcb38f30b ./pdfs/ --alreadyExists=override
	 *
	 *     # Generate a PDF for entry 120 with the PDF ID 5886dcb38f30b and save PDF stream to sample.pdf
	 *     $ wp gpdf pdf 120 5886dcb38f30b > sample.pdf
	 *
	 * @since 1.0
	 *
	 * @param array $args           The Entry ID, PDF ID and Path
	 * @param array $additionalArgs The additional arguments passed to the cli.
	 */
	public function __invoke( $args, $additionalArgs = [] ) {
		$entryId        = (int) $args[0];
		$pdfId          = $args[1];
		$savePath       = ( isset( $additionalArgs['path'] ) ) ? trailingslashit( $additionalArgs['path'] ) : false;
		$handleConflict = ( isset( $additionalArgs['exists'] ) ) ? $additionalArgs['exists'] : 'increment';

		if ( $savePath ) {
			try {
				$this->createDirIfNotExists( $savePath );
			} catch ( RuntimeException $e ) {
				$this->cli->error( $e->getMessage() );
			}
		}

		try {
			$pdfPath = \GPDFAPI::create_pdf( $entryId, $pdfId );
			if ( is_wp_error( $pdfPath ) ) {
				if ( in_array( $pdfPath->get_error_code(), [ 'invalid_entry', 'invalid_pdf_setting' ] ) ) {
					throw new UnexpectedValueException( $pdfPath->get_error_message() );
				} else {
					throw new Exception( $pdfPath->get_error_message() );
				}
			}

			if ( $savePath ) {
				$filename = $this->getPdfFilename( $pdfPath, $savePath, $handleConflict );
				$this->checkFileExistsAndDelete( $savePath, $filename, $handleConflict );
				$this->movePdfToSavePath( $pdfPath, $savePath, $filename );
				$this->displaySuccessMessage( $savePath, $filename );
			} else {
				$this->cli->log( file_get_contents( $pdfPath ) );
				unlink( $pdfPath );
			}
		} catch ( Exception $e ) {
			if ( isset( $additionalArgs['warn'] ) ) {
				$this->cli->warning( $e->getMessage() );
			} else {
				$this->cli->error( $e->getMessage() );
			}
		}
	}

	/**
	 * @param $pdfPath
	 * @param $savePath
	 * @param $handleConflict
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	protected function getPdfFilename( $pdfPath, $savePath, $handleConflict ) {

		$fileDetails = pathinfo( $pdfPath );
		$filename    = $fileDetails['basename'];
		$testFile    = $savePath . $filename;

		/* If increment handler, and file exists, add number to end of filename until no further conflict exists */
		if ( $handleConflict === 'increment' ) {
			$i = 1;

			while ( is_file( $testFile ) ) {
				$filename = $fileDetails['filename'] . $i . '.' . $fileDetails['extension'];
				$testFile = $savePath . $filename;

				$i++;
			}
		}

		return $filename;
	}

	/**
	 * @param $savePath
	 * @param $filename
	 *
	 * @since 1.0
	 */
	protected function displaySuccessMessage( $savePath, $filename ) {
		$this->cli->success(
			sprintf(
				__( 'Saved %1$s to %2$s', 'gravity-pdf-developer-toolkit' ),
				$filename,
				$savePath
			)
		);
	}

	/**
	 * @param $pdfPath
	 * @param $savePath
	 * @param $filename
	 *
	 * @since 1.0
	 */
	protected function movePdfToSavePath( $pdfPath, $savePath, $filename ) {
		$saveFile = $savePath . $filename;
		if ( ! rename( $pdfPath, $saveFile ) ) {
			throw new RuntimeException(
				sprintf(
					__( 'Could not save %1$s to %2$s', 'gravity-pdf-developer-toolkit' ),
					$filename,
					$savePath
				)
			);
		}
	}

	/**
	 * @param $savePath
	 * @param $filename
	 * @param $handleConflict
	 *
	 * @since 1.0
	 */
	protected function checkFileExistsAndDelete( $savePath, $filename, $handleConflict ) {
		if ( is_file( $savePath . $filename ) ) {
			if ( $handleConflict === 'override' ) {
				if ( ! unlink( $savePath . $filename ) ) {
					throw new RuntimeException(
						sprintf(
							__( 'Could not delete %1$s found in %2$s', 'gravity-pdf-developer-toolkit' ),
							$filename,
							$savePath
						)
					);
				}
			} else {
				throw new RuntimeException(
					sprintf(
						__( 'The PDF %1$s already exists in %2$s', 'gravity-pdf-developer-toolkit' ),
						$filename,
						$savePath
					)
				);
			}
		}
	}

	/**
	 * @param $path
	 *
	 * @since 1.0
	 */
	public function createDirIfNotExists( $path ) {
		if ( ! is_dir( $path ) ) {
			if ( ! wp_mkdir_p( $path ) ) {
				throw new RuntimeException(
					sprintf(
						__( 'Could not create directory %s', 'gravity-pdf-developer-toolkit' ),
						$path
					)
				);
			}
		}
	}
}
