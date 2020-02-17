<?php

namespace GFPDF\Plugins\DeveloperToolkit\Legacy;

use WP_UnitTestCase;
use GPDFAPI;

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
 * Class TestAdvancedTemplate
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Legacy
 *
 * @group   legacy
 */
class TestAdvancedTemplate extends WP_UnitTestCase {

	/**
	 * @var AdvancedTemplate
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$this->class = new AdvancedTemplate( GPDFAPI::get_options_class() );

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function testModifyAdvancedTemplateField() {
		/* Test results remain unchanged */
		$fields = [ 'name' => 'Label' ];
		$this->assertSame( $fields, $this->class->modifyAdvancedTemplateField( $fields ) );

		/* Test advanced_template setting is removed */
		$fields['advanced_template'] = true;

		$results = $this->class->modifyAdvancedTemplateField( $fields );
		$this->assertArrayNotHasKey( 'advanced_template', $results );

		/* Test the advaned_template setting is not removed */
		$options = GPDFAPI::get_options_class();
		$options->update_option( 'advanced_templating', true );
		$this->assertSame( $fields, $this->class->modifyAdvancedTemplateField( $fields ) );
	}
}
