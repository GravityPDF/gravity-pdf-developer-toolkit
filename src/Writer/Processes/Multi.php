<?php

namespace GFPDF\Plugins\DeveloperToolkit\Writer\Processes;

use GFPDF\Plugins\DeveloperToolkit\Writer\AbstractWriter;

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


class Multi extends AbstractWriter {
	protected $font_size = 10;
	protected $line_height = 14;
	protected $strip_br = false;

	public function configMulti( $config ) {
		foreach ( $config as $name => $value ) {
			switch ( $name ) {
				case 'font-size':
					$this->font_size = (int) $value;
				break;

				case 'line-height':
					$this->line_height = (int) $value;
				break;

				case 'strip-br':
					$this->strip_br = (bool) $value;
				break;
			}
		}
	}

	public function addMulti( $html, $position = [], $overflow = 'auto', $config = [] ) {
		if ( count( $position ) !== 4 ) {
			throw new \BadMethodCallException( '$position needs to include an array with four elements: $x, $y, $width, $height' );
		}

		if ( ! in_array( $overflow, [ 'auto', 'visible', 'hidden' ] ) ) {
			throw new \BadMethodCallException( '$overflow can only be "auto", "visible" or "hidden".' );
		}

		$font_size   = ( isset( $config['font-size'] ) ) ? (int) $config['font-size'] : $this->font_size;
		$line_height = ( isset( $config['line-height'] ) ) ? (int) $config['line-height'] : $this->line_height;
		$strip_br    = ( isset( $config['strip-br'] ) ) ? (int) $config['strip-br'] : $this->strip_br;

		if ( $strip_br ) {
			$html = str_replace( [ '<br>', '<br/>', '<br />' ], ' &nbsp; ', $html );
		}

		$output = sprintf(
			'<div class="multi" style="font_size: %s; line-height: %s">%s</div>',
			$font_size . 'pt',
			$line_height . 'pt',
			$html
		);

		$this->mpdf->WriteFixedPosHTML( $output, $position[0], $position[1], $position[2], $position[3], $overflow );
	}
}