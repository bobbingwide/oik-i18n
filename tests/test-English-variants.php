<?php // (C) Copyright Bobbing Wide 2017

class tests_English_variants extends BW_UnitTestCase {

	function setUp() {
		//oik_require_lib( "oik-autoload" );
		//oik_autoload();
		//oik_require_class( "English_variants" );
		oik_require( "class-English-variants.php", "oik-i18n" );
	}

	function tests_constructor() {
		$variants = English_variants::instance();
		$this->assertInstanceOf( "English_variants", $variants );
	}
	
	function test_map() {
		$variants = English_variants::instance();
		$actual = $variants->map( "analyze" );
		$expected = "analyse";
		$this->assertEquals( $expected, $actual );
	} 


}
