<?php

namespace GFPDF\Plugins\DeveloperToolkit\Cli\Commands;

use GFPDF\Helper\Helper_Abstract_Form;
use GFPDF\Model\Model_PDF;
use GFPDF\Plugins\DeveloperToolkit\Zip\Zip;
use League\Flysystem\Filesystem;

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
 * Generates PDFs in bulk and saves to disk or STDOUT (as a zip)
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Cli\Commands
 */
class BulkSavePdf {

	/**
	 * @var Cli|object
	 * @since 1.0
	 */
	protected $cli;

	/**
	 * @var SavePdf
	 * @since 1.0
	 */
	protected $savePdf;

	/**
	 * @var Model_PDF
	 * @since 1.0
	 */
	protected $modelPdf;

	/**
	 * @var Helper_Abstract_Form
	 * @since 1.0
	 */
	protected $gform;

	/**
	 * @var array
	 * @since 1.0
	 */
	protected $activePdfs = [];

	/**
	 * @var Filesystem
	 * @since 1.0
	 */
	protected $filesystem;

	/**
	 * @var Zip
	 * @since 1.0
	 */
	protected $zip;

	/**
	 * BulkSavePdfs constructor.
	 *
	 * @param InterfaceCli         $cli
	 * @param SavePdf              $savePdf
	 * @param Model_PDF            $modelPdf
	 * @param Helper_Abstract_Form $gform
	 * @param Filesystem           $filesystem
	 * @param Zip                  $zip
	 *
	 * @since 1.0
	 */
	public function __construct( InterfaceCli $cli, SavePdf $savePdf, Model_PDF $modelPdf, Helper_Abstract_Form $gform, Filesystem $filesystem, Zip $zip ) {
		$this->cli        = $cli;
		$this->savePdf    = $savePdf;
		$this->modelPdf   = $modelPdf;
		$this->gform      = $gform;
		$this->filesystem = $filesystem;
		$this->zip        = $zip;
	}

	/**
	 * Generates and saves PDFs in bulk to a directory, with ability to filter entries and PDFs
	 *
	 * ## OPTIONS
	 *
	 * <form-id>
	 * : The ID of the Gravity Forms form
	 *
	 * [--path=<path>]
	 * : The path to save the PDF to. If not passed, PDFs are zipped and output to STDOUT
	 *
	 * [--filter=<filter>]
	 * : Filter by the current status of the entries
	 * ---
	 * options:
	 *   - read
	 *   - unread
	 *   - starred
	 *   - unstarred
	 *   - gvapproved
	 *   - gvdisapproved
	 *   - paid
	 *   - processing
	 *   - failed
	 *   - active
	 *   - cancelled
	 *   - pending
	 *   - refunded
	 *   - voided
	 * ---
	 *
	 * [--dates=<range>]
	 * : Filter entries by a specific set of date ranges. Format: yyyy-mm-dd/yyyy-mm-dd
	 *
	 * [--group=<group>]
	 * : PDFs will be saved into sub directories inside <path>. Can pass Gravity Form merge tag, `id` for the entry ID, or a valid date format `M Y` {@see http://php.net/manual/en/function.date.php}.
	 *
	 * [--pdf-id=<pdf-id>]
	 * : Only generate a specific PDF(s) for a range of entries. Pass the PDF ID, or multiple IDs separated by a comma.
	 *
	 * ## EXAMPLES
	 *
	 *     # Generate all PDFs for all entries in form #10 and save them to the root of ./pdfs/. Conflicting PDF names will be append with a counter.
	 *     $ wp gpdf bulk-save-pdf search 10 --path=./pdfs/
	 *
	 *     # Generate all PDFs for all entries in form #10 and save them to a directory inside ./pdfs/ using the entry ID as the name.
	 *     $ wp gpdf bulk-save-pdf search 10 --path=./pdfs/ --group=id
	 *
	 *     # Generate all PDFs for all entries in form #10 and save them to a directory inside ./pdfs/ using the value of field ID 3 as the name.
	 *     $ wp gpdf bulk-save-pdf search 10 --path=./pdfs/ --group={:3}
	 *
	 *     # Generate all PDFs for all entries in form #10 and save them to a directory inside ./pdfs/ using the submission date formatted as "February 2018"
	 *     $ wp gpdf bulk-save-pdf search 10 --path=./pdfs/ --group="F Y"
	 *
	 *     # Generate all PDFs for form #10 where entries are `unread`, and save them to the root of ./pdfs/.
	 *     $ wp gpdf bulk-save-pdf search 10 --path=./pdfs/ --filter=unread
	 *
	 *     # Generate all PDFs for form #10 where entries have been `starred`, and save them to the root of ./pdfs/.
	 *     $ wp gpdf bulk-save-pdf search 10 --path=./pdfs/ --filter=starred
	 *
	 *     # Generate all PDFs for all entries in form #10 between the dates `2018-01-01` and `2018-02-28`, and save them to the root of ./pdfs/.
	 *     $ wp gpdf bulk-save-pdf search 10 --path=./pdfs/ --dates=2018-01-01/2018-02-28
	 *
	 *     # Generate the PDF "598914733111" for all entries in form #10 and save them to the root of ./pdfs/.
	 *     $ wp gpdf bulk-save-pdf search 10 --path=./pdfs/ --pdf-id=598914733111
	 *
	 *     # Generate the PDF "598914733111" and "598914734975" for all entries in form #10 and save them to the root of ./pdfs/.
	 *     $ wp gpdf bulk-save-pdf search 10 --path=./pdfs/ --pdf-id=598914734975
	 *
	 *     # Generate the PDF "598914733111" and "598914734975" for all entries in form #10 and save PDF zip stream to sample.zip
	 *     $ wp gpdf bulk-save-pdf search 10 --path=./pdfs/ --pdf-id=598914734975 > sample.zip
	 *
	 * @since 1.0
	 *
	 * @param array $args           The Entry ID, PDF ID and Path
	 * @param array $additionalArgs The additional arguments passed to the cli.
	 */
	public function search( $args, $additionalArgs = [] ) {
		$savePdf = $this->savePdf;

		$formId   = (int) $args[0];
		$filter   = ( isset( $additionalArgs['filter'] ) ) ? $additionalArgs['filter'] : '';
		$savePath = ( isset( $additionalArgs['path'] ) ) ? trailingslashit( $additionalArgs['path'] ) : false;
		$dates    = ( isset( $additionalArgs['dates'] ) ) ? $this->validateDateRange( $additionalArgs['dates'] ) : [];
		$group    = ( isset( $additionalArgs['group'] ) ) ? $additionalArgs['group'] : false;
		$pdfIds   = ( isset( $additionalArgs['pdf-id'] ) ) ? array_map( 'trim', explode( ',', $additionalArgs['pdf-id'] ) ) : [];

		/* If we are returning a zip, ensure the ZipArchive class exists */
		if ( ! $savePath && ! class_exists( 'ZipArchive' ) ) {
			$this->cli->error( __( 'Cannot zip up PDFs as PHP is not compiled with zip support.', 'gravity-pdf-developer-toolkit' ) );
		}

		$pdfs        = $this->getFormPdfs( $formId );
		$entries     = $this->getEntriesBySearch( $formId, $filter, $dates );
		$progressBar = $this->createProgressBar( $entries, $pdfs, $pdfIds );

		$this->cli->log( __( 'Please be patient. Bulk PDF generate takes time.', 'gravity-pdf-developer-toolkit' ) );

		/* Loop through all entries and begin generating PDFs */
		$bulkSavePath = ( $savePath ) ? $savePath : $this->filesystem->getAdapter()->getPathPrefix() . time() . '/';
		foreach ( $entries as $entry ) {
			foreach ( $this->activePdfs[ $entry['id'] ] as $pdfId ) {
				/* Skip over PDF if requested */
				if ( count( $pdfIds ) > 0 && ! in_array( $pdfId, $pdfIds ) ) {
					continue;
				}

				$groupName   = $this->translateGroupName( $group, $entry );
				$newSavePath = ( $groupName ) ? $bulkSavePath . $groupName . '/' : $bulkSavePath;

				$savePdf(
					[
						$entry['id'],
						$pdfId,
					],
					[
						'path'   => $newSavePath,
						'exists' => 'increment',
						'warn'   => true,
					]
				);

				if ( $savePath ) {
					$progressBar->tick();
				}
			}
		}

		/* Zip up the directory and output */
		if ( ! $savePath ) {
			$dirToZip = dirname( $bulkSavePath ) . '/' . basename( $bulkSavePath );
			$zipPath  = "$dirToZip.zip";
			$zipFile  = $this->zip->create( $dirToZip, $zipPath );
			if ( $this->filesystem->has( $zipFile ) ) {
				$this->cli->log( $this->filesystem->readAndDelete( $zipFile ) );
				$this->filesystem->delete( $bulkSavePath );
				unlink( $zipFile );
			} else {
				$this->cli->error( __( 'Could not zip up generated PDFs. Please try again.', 'gravity-pdf-developer-toolkit' ) );
			}
		}
	}

	/**
	 * Generates and saves PDFs in bulk to a directory using entry IDs
	 *
	 * ## OPTIONS
	 *
	 * <entry-ids>
	 * : The IDs (separated by a space) of the Gravity Forms entries to generate PDFs for. These IDs must be from the same Gravity Form.
	 *
	 * [--path=<path>]
	 * : The path to save the PDF to. If not passed, PDFs are zipped and output to STDOUT
	 *
	 * [--group=<group>]
	 * : PDFs will be saved into sub directories inside <path>. Can pass Gravity Form merge tag, `id` for the entry ID, or a valid date format `M Y` {@see http://php.net/manual/en/function.date.php}.
	 *
	 * [--pdf-id=<pdf-id>]
	 * : Only generate a specific PDF(s) for a range of entries. Pass the PDF ID, or multiple IDs separated by a comma.
	 *
	 * ## EXAMPLES
	 *
	 *     # Generate all PDFs for the entries 56, 57 and 58 and save them to the root of ./pdfs/. Conflicting PDF names will be append with a counter.
	 *     $ wp gpdf bulk-save-pdf entries 56 57 58 --path=./pdfs/
	 *
	 *     # Generate all PDFs for the entries 56, 57 and 58 and save them to a directory inside ./pdfs/ using the entry ID as the name.
	 *     $ wp gpdf bulk-save-pdf entries 56 57 58 --path=./pdfs/ --group=id
	 *
	 *     # Generate all PDFs for the entries 56, 57 and 58 and save them to a directory inside ./pdfs/ using the value of field ID 3 as the name.
	 *     $ wp gpdf bulk-save-pdf entries 56 57 58 --path=./pdfs/ --group={:3}
	 *
	 *     # Generate all PDFs for the entries 56, 57 and 58 and save them to a directory inside ./pdfs/ using the submission date formatted as "February 2018"
	 *     $ wp gpdf bulk-save-pdf entries 56 57 58 --path=./pdfs/ --group="F Y"
	 *
	 *     # Generate the PDF "598914733111" for the entries 56, 57 and 58 and save them to the root of ./pdfs/.
	 *     $ wp gpdf bulk-save-pdf entries 56 57 58 --path=./pdfs/ --pdf-id=598914733111
	 *
	 *     # Generate the PDF "598914733111" and "598914734975" for the entries 56, 57 and 58 and save them to the root of ./pdfs/.
	 *     $ wp gpdf bulk-save-pdf entries 56 57 58 --path=./pdfs/ --pdf-id=598914734975
	 *
	 *     # Generate the PDF "598914733111" and "598914734975" for the entries 56, 57 and 58 and save PDF zip stream to sample.zip
	 *     $ wp gpdf bulk-save-pdf entries 56 57 58 --pdf-id=598914734975 > sample.zip
	 *
	 * @since 1.0
	 *
	 * @param array $args           The Entry ID, PDF ID and Path
	 * @param array $additionalArgs The additional arguments passed to the cli.
	 */
	public function entries( $args, $additionalArgs = [] ) {
		$savePdf = $this->savePdf;

		$entryIds = array_map( 'trim', explode( ' ', $args[0] ) );
		$savePath = ( isset( $additionalArgs['path'] ) ) ? trailingslashit( $additionalArgs['path'] ) : false;
		$group    = ( isset( $additionalArgs['group'] ) ) ? $additionalArgs['group'] : false;
		$pdfIds   = ( isset( $additionalArgs['pdf-id'] ) ) ? array_map( 'trim', explode( ',', $additionalArgs['pdf-id'] ) ) : [];

		$entries = $this->getEntriesByIds( $entryIds );
		$pdfs    = $this->getFormPdfs( $entries[0]['form_id'] );

		$this->cli->log( __( 'Please be patient. Bulk PDF generate takes time.', 'gravity-pdf-developer-toolkit' ) );
		$progressBar = $this->createProgressBar( $entries, $pdfs, $pdfIds );

		/* Loop through all entries and begin generating PDFs */
		foreach ( $entries as $entry ) {
			foreach ( $this->activePdfs[ $entry['id'] ] as $pdfId ) {
				/* Skip over PDF if requested */
				if ( count( $pdfIds ) > 0 && ! in_array( $pdfId, $pdfIds ) ) {
					continue;
				}

				$groupName   = $this->translateGroupName( $group, $entry );
				$newSavePath = ( $groupName ) ? $savePath . $groupName . '/' : $savePath;

				$savePdf(
					[
						$entry['id'],
						$pdfId,
						$newSavePath,
					],
					[
						'exists' => 'increment',
						'warn'   => true,
					]
				);

				$progressBar->tick();
			}
		}
	}

	/**
	 * Get the group directory name (if any)
	 *
	 * @param string $group The directory name format
	 * @param array  $entry
	 *
	 * @return string|false
	 *
	 * @since 1.0
	 */
	protected function translateGroupName( $group, $entry ) {
		if ( ! $group || strlen( trim( $group ) ) === 0 ) {
			return false;
		}

		if ( $group === 'id' ) {
			return $entry['id'];
		}

		/* Merge tag */
		if ( preg_match( '/{[^{]*?:(\d+(\.\d+)?)(:(.*?))?}/', $group ) ) {
			$form         = $this->gform->get_form( $entry['form_id'] );
			$processedTag = $this->gform->process_tags( $group, $form, $entry );

			return ( strlen( $processedTag ) > 0 ) ? $this->stripInvalidCharacters( $processedTag ) : false;
		}

		/* Assume it's a date format */
		$formattedDate = date( $group, strtotime( $entry['date_created'] ) );

		return ( strlen( $formattedDate ) > 0 ) ? $this->stripInvalidCharacters( $formattedDate ) : false;
	}

	/**
	 * @param $filename
	 *
	 * @return string
	 *
	 * @since 1.0
	 */
	protected function stripInvalidCharacters( $filename ) {
		$characters = [ '/', '\\', '"', '*', '?', '|', ':', '<', '>' ];
		return str_replace( $characters, '-', $filename );
	}

	/**
	 * Returns a CLI progress bar for the current entries
	 *
	 * @param array $entries The current entries being parsed
	 * @param array $pdfs    All PDFs for a form
	 * @param array $pdfIds  Contains the PDF IDs we only want to generate
	 *
	 * @return \cli\progress\Bar
	 *
	 * @since 1.0
	 */
	protected function createProgressBar( $entries, $pdfs, $pdfIds ) {
		$ticks = 0;

		foreach ( $entries as $entry ) {
			$activePdfs = $this->modelPdf->get_active_pdfs( $pdfs, $entry );

			foreach ( $activePdfs as $pdf ) {
				/* Skip over PDF if requested */
				if ( count( $pdfIds ) > 0 && ! in_array( $pdf['id'], $pdfIds ) ) {
					continue;
				}

				if ( ! isset( $this->activePdfs[ $entry['id'] ] ) ) {
					$this->activePdfs[ $entry['id'] ] = [];
				}

				$this->activePdfs[ $entry['id'] ][] = $pdf['id'];

				$ticks++;
			}
		}

		return $this->cli->createProgressBar( __( 'PDF Progress', 'gravity-pdf-developer-toolkit' ), $ticks );
	}

	/**
	 * Get PDF settings for current Gravity Form
	 *
	 * @param int $formId
	 *
	 * @return array|\WP_Error
	 *
	 * @since 1.0
	 */
	protected function getFormPdfs( $formId ) {
		$pdfs = \GPDFAPI::get_form_pdfs( $formId );
		if ( is_wp_error( $pdfs ) ) {
			$this->cli->error( __( 'Could not retrieve PDF settings.', 'gravity-pdf-developer-toolkit' ) );
		}

		return $pdfs;
	}

	/**
	 * Query Gravity Forms for matching entries
	 *
	 * @param int    $formId
	 * @param string $filter
	 * @param array  $dates
	 *
	 * @return array The matching Gravity Form entries
	 *
	 * @since 1.0
	 */
	protected function getEntriesBySearch( $formId, $filter, $dates ) {
		/* Prepare Gravity Forms Entry Search */
		$search = [ 'field_filters' => [] ];

		/* Add status */
		if ( $filter === 'starred' ) {
			$search['field_filters'][] = [
				'key'   => 'is_starred',
				'value' => 1,
			];
		}

		if ( $filter === 'unstarred' ) {
			$search['field_filters'][] = [
				'key'   => 'is_starred',
				'value' => 0,
			];
		}

		if ( $filter === 'read' ) {
			$search['field_filters'][] = [
				'key'   => 'is_read',
				'value' => 1,
			];
		}

		if ( $filter === 'unread' ) {
			$search['field_filters'][] = [
				'key'   => 'is_starred',
				'value' => 0,
			];
		}

		if ( in_array(
			$filter, [
				'paid',
				'processing',
				'failed',
				'active',
				'cancelled',
				'pending',
				'refunded',
				'voided',
			]
		) ) {
			$search['field_filters'][] = [
				'key'   => 'payment_status',
				'value' => $filter,
			];
		}

		$search['status'] = 'active';

		/* GravityView Filter */
		if ( class_exists( '\GravityView_Entry_Approval' ) ) {
			if ( $filter === 'gvapproved' ) {
				$search['field_filters'][] = [
					'key'   => \GravityView_Entry_Approval::meta_key,
					'value' => \GravityView_Entry_Approval_Status::APPROVED,
				];
			}

			if ( $filter === 'gvdisapproved' ) {
				$search['field_filters'][] = [
					'key'   => \GravityView_Entry_Approval::meta_key,
					'value' => \GravityView_Entry_Approval_Status::DISAPPROVED,
				];
			}
		}

		/* Add date restriction */
		if ( count( $dates ) === 2 ) {
			$search['start_date'] = $dates[0];
			$search['end_date']   = $dates[1];
		}

		$entries = \GFAPI::get_entries( $formId, $search );

		/* Throw error if failed lookup */
		if ( is_wp_error( $entries ) ) {
			$this->cli->error( __( 'Could not retrieve Gravity Forms entries.', 'gravity-pdf-developer-toolkit' ) );
		}

		/* Throw error if no results found */
		if ( count( $entries ) === 0 ) {
			$this->cli->error( __( 'No matching entries found for search criteria.', 'gravity-pdf-developer-toolkit' ) );
		}

		return $entries;
	}

	/**
	 * @param $entryIds
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	protected function getEntriesByIds( $entryIds ) {
		$formId  = 0;
		$entries = [];

		foreach ( $entryIds as $entryId ) {
			$entryId = (int) $entryId;
			$entry   = \GFAPI::get_entry( $entryId );

			/* If no error getting entry data from database and all entries from the same form */
			if ( ! is_wp_error( $entry ) ) {
				if ( $formId === 0 ) {
					$formId = (int) $entry['form_id'];
				}

				if ( (int) $entry['form_id'] !== $formId ) {
					$this->cli->error( __( 'All Gravity Forms <entry-ids> must be from a single Gravity Form.', 'gravity-pdf-developer-toolkit' ) );
				}

				$entries[] = $entry;
			}
		}

		if ( count( $entries ) === 0 ) {
			$this->cli->error( __( 'No valid entries found using using <entry-ids>.', 'gravity-pdf-developer-toolkit' ) );
		}

		return $entries;
	}

	/**
	 * Check if the date format passed in is valid
	 *
	 * @param string $range
	 *
	 * @return array An array with the start date and end date
	 *
	 * @since 1.0
	 */
	protected function validateDateRange( $range ) {
		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $range ) ) {
			$range .= '/' . $range;
		}

		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}\/\d{4}-\d{2}-\d{2}/', $range ) ) {
			$this->cli->error( __( 'The --dates=<value> format must be yyyy-mm-dd/yyy-mm-dd', 'gravity-pdf-developer-toolkit' ) );
		}

		return explode( '/', $range );
	}
}
