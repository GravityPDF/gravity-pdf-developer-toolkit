&#x3C;?php

namespace GFPDF\Templates\Config;

use GFPDF\Helper\Helper_Interface_Config;
use GFPDF\Helper\Helper_Interface_Setup_TearDown;
use GFPDF\Helper\Helper_Abstract_Config_Settings;

use GPDFAPI;

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class <?php echo $data['name'] . "\n"; ?>
 *
 * @package  GFPDF\Templates\Config
 *
 * @Internal See https://docs.gravitypdf.com/v6/install-template-via-template-manager for more information about this class
 */
class <?php echo $data['name']; ?> extends Helper_Abstract_Config_Settings implements Helper_Interface_Config, Helper_Interface_Setup_TearDown {

	/**
	 * Runs when the template is initially installed via the PDF Template Manager
	 *
	 * @Internal Great for installing custom fonts you've shipped with your template.
	 * @Internal Recommend creating the directory structure /install/<?php echo $data['name']; ?>/ for bundled fonts
	 *
	 * @since    1.0
	 */
	public function setUp() {
//		$font_data = [
//			'font_name'   => 'Font Name',
//			'regular'     => __DIR__ . '/../install/<?php echo $data['name']; ?>/font-name/regular.ttf',
//			'italics'     => __DIR__ . '/../install/<?php echo $data['name']; ?>/font-name/italics.ttf',
//			'bold'        => __DIR__ . '/../install/<?php echo $data['name']; ?>/font-name/bold.ttf',
//			'bolditalics' => __DIR__ . '/../install/<?php echo $data['name']; ?>/font-name/bold-italics.ttf',
//		];
//
//		GPDFAPI::add_pdf_font( $font_data );
	}

	/**
	 * Runs when the template is deleted via the PDF Template Manager
	 *
	 * @Internal Great for cleaning up any additional directories
	 *
	 * @since    1.0
	 */
	public function tearDown() {
//		$misc = GPDFAPI::get_misc_class();
//		$misc->rmdir( __DIR__ . '/../install/<?php echo $data['name']; ?>/' );
	}

	/**
	 * Return the templates configuration structure which control what extra fields will be shown in the "Template" tab when configuring a form's PDF.
	 *
	 * @return array The array, split into core components and custom fields
	 *
	 * @since 1.0
	 */
	public function configuration() {
		return [
//			/* Enable core fields */
//			'core'   => [
//				'show_form_title'      => true,
//				'show_page_names'      => true,
//				'show_html'            => true,
//				'show_section_content' => true,
//				'enable_conditional'   => true,
//				'show_empty'           => true,
//				'header'               => true,
//				'first_header'         => true,
//				'footer'               => true,
//				'first_footer'         => true,
//				'background_color'     => true,
//				'background_image'     => true,
//			],
//
//			/* Create custom fields to control the look and feel of a template */
//			'fields' => [
//				'my_text_field' => [
//					'id'   => 'my_text_field',
//					'name' => 'Label',
//					'type' => 'text',
//					'desc' => 'Description about this field',
//				],
//			],
		];
	}
}
