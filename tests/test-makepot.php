<?php // (C) Copyright Bobbing Wide 2017

/** 
 * Unit tests for the bobbcomp.inc file
 */

class Tests_makepot extends BW_UnitTestCase {

	/** 
	 * set up logic
	 * 
	 * - ensure any database updates are rolled back
	 */
	function setUp() {
		parent::setUp();
	}

	/**
	 * Perform the first step - extract the translatable strings
	 *
	 * Here we use the WordPress standard routine ( makepot.php ) which only recognises a subset of the
	 * functions that the oik routine can handle. 
	 * This test is primarily to generate another version of the oik.pot file in the oik-i18n main directory.
	 * We're changing the source code of the oik plugin so that it's compatible with makepot.
	 * Overtime the oik.pot produced by this test should become the same as the one generated by makeoik.php
	 * @param string $plugin
	 */
	function do_makepot( $plugin ) {
		oik_require( "makepot.php", "oik-i18n" );
		$plugin_path = oik_path( null, $plugin );
		$makepot = new MakePOT;
		$res = call_user_func( array( &$makepot, "wp_plugin" )
												 , $plugin_path
												 , null 
												 );
		//f ((3 == count($argv) || 4 == count($argv)) && in_array($method = str_replace('-', '_', $argv[1]), get_class_methods($makepot))) {
		// $res = call_user_func(array(&$makepot, $method), realpath($argv[2]), isset($argv[3])? $argv[3] : null);
		if (false === $res) {
		 fwrite(STDERR, "Couldn't generate POT file!\n");
		}
		return( $res );
	}
	
	function test_makepot() {
		$res = $this->do_makepot( "oik" );
		$this->assertEquals( $res, true );
	}
}	
