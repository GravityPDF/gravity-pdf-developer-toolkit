<?php

namespace GFPDF\Plugins\DeveloperToolkit\Loader;

use GFPDF\Helper\Helper_Misc;
use GFPDF\Helper\Helper_Templates;
use WP_UnitTestCase;

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
 * Class TestJavascript
 *
 * @package GFPDF\Plugins\DeveloperToolkit\Loader
 *
 * @group   loader
 */
class TestJavascript extends WP_UnitTestCase {

	/**
	 * @var Javascript
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {
		$misc = $this->getMockBuilder( Helper_Misc::class )
					->setConstructorArgs(
						[
							\GPDFAPI::get_log_class(),
							\GPDFAPI::get_form_class(),
							\GPDFAPI::get_data_class(),
						]
					)
					 ->setMethods( [ 'is_gfpdf_page' ] )
					 ->getMock();

		$misc->method( 'is_gfpdf_page' )
			->will(
				$this->onConsecutiveCalls(
					true
				)
			);

		$template = $this->getMockBuilder( Helper_Templates::class )
						->setConstructorArgs(
							[
								\GPDFAPI::get_log_class(),
								\GPDFAPI::get_data_class(),
								\GPDFAPI::get_form_class(),
							]
						)
						 ->setMethods( [ 'get_template_path_by_id' ] )
						 ->getMock();

		$template->method( 'get_template_path_by_id' )
				->will(
					$this->onConsecutiveCalls(
						__DIR__ . '/../pdfs/sample1.php',
						__DIR__ . '/../pdfs/sample2.php'
					)
				);

		$this->class = new Javascript(
			$misc,
			$template
		);

		$this->class->init();

		parent::setUp();
	}

	/**
	 * @since 1.0
	 */
	public function testRegisterAssets() {
		global $wp_scripts;

		$wp_scripts->remove( 'gfpdf_js_dev_toolbox_settings' );
		$this->class->registerAssets();
		$this->assertTrue( isset( $wp_scripts->registered['gfpdf_js_dev_toolbox_settings'] ) );
	}

	/**
	 * @since 1.0
	 */
	public function testEnqueueScripts() {
		global $wp_scripts;

		$this->assertCount( 0, $wp_scripts->queue );
		$this->class->enqueueScripts();
		$this->assertCount( 1, $wp_scripts->queue );
	}

	/**
	 * @since    1.0
	 * @Internal Have tested function directly instead of using WP_Ajax_UnitTestCase for speed
	 */
	public function testGetTemplateHeader() {
		$this->setRole( 'administrator' );
		$_POST['nonce'] = wp_create_nonce( 'gfpdf_ajax_nonce' );

		/* Mock will produce a failure */
		try {
			ob_start();
			$this->class->getTemplateHeader();
		} catch ( \WPDieException $e ) {
		}
		$results = json_decode( ob_get_clean(), true );

		$this->assertFalse( $results['toolkit'] == 'true' );

		/* Mock will produce a success */
		try {
			ob_start();
			$this->class->getTemplateHeader();
		} catch ( \WPDieException $e ) {
		}
		$results = json_decode( ob_get_clean(), true );

		$this->assertTrue( $results['toolkit'] == 'true' );
	}

	/**
	 * @param $role
	 *
	 * @since    1.0
	 * @Internal copied from WP_Ajax_UnitTestCase
	 */
	protected function setRole( $role ) {
		$post    = $_POST;
		$user_id = self::factory()->user->create( [ 'role' => $role ] );
		wp_set_current_user( $user_id );
		$_POST = array_merge( $_POST, $post );
	}

}
