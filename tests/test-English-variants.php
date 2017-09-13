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
	
	function test_map_word() {
		$variants = English_variants::instance();
		$actual = $variants->map_word( "analyze" );
		$expected = "analyse";
		$this->assertEquals( $expected, $actual );
	}
	
	function test_map_mixed_case() {
		$variants = English_variants::instance();
		$actual = $variants->map( "Analyze your behavior." );
		$expected = "Analyse your behaviour.";
		$this->assertEquals( $expected, $actual );
	}
	
	function test_check_original_language() {
		$variants = English_variants::instance();
		$actual = $variants->check_original_language( "analyze" );
		$this->assertTrue( $actual ); 
	}
	
	function test_check_original_language_wrong() {
		$expectedOutputString = "Original text already in target locale." . PHP_EOL;
		$expectedOutputString .= "en_US: analyse" . PHP_EOL;
		$expectedOutputString .= "en_GB: analyze" . PHP_EOL;
		$this->expectOutputString( $expectedOutputString ); 
		$variants = English_variants::instance();
		$actual = $variants->check_original_language( "analyse" );
		$this->assertFalse( $actual ); 
	}
	
	function dont_test_tokenize() {
		$variants = English_variants::instance();
		$tokens = $variants->tokenize( "Analyze your behavior with <b>more</b> than one space (  )." );
		print_r( $tokens );
		$expected = array();
		$this->assertEquals( $expected, $tokens );
	}
	
	function test_get_tokens() {
		$variants = English_variants::instance();
		$tokens = $variants->get_tokens( "Analyze  your <b>behavior.</b>" );
		//print_r( $tokens );
		$expected = array();
		$expected[] = "Analyze";
		$expected[] = "  ";
		$expected[] = "your";
		$expected[] = " ";
		$expected[]	= "<";
		$expected[] = "b";
		$expected[] = ">";
		$expected[] = "behavior";
		$expected[] = ".</";
		$expected[] = "b";
		$expected[] = ">";
		$this->assertEquals( $expected, $tokens );
	}
	
	/**
	 * Test the use of context. 
	 * 
	 * Note: We can't directly test map() passing $context 
	 */
	function test_map_with_context() {
		$variants = English_variants::instance();
		$actual = $variants->map( "check", "bank" );
		$expected = "cheque";
		$this->assertEquals( $expected, $actual );
		
		$actual = $variants->map( "check" );
		$expected = "check";
		$this->assertEquals( $expected, $actual );
		
		$actual = $variants->map( "Check", "bank" );
		$expected = "Cheque";
		$this->assertEquals( $expected, $actual );
		
		$actual = $variants->map( "check", "examine" );
		$expected = "check";
		$this->assertEquals( $expected, $actual );
		
		$actual = $variants->map( "Check color" );
		$expected = "Check colour";
		$this->assertEquals( $expected, $actual );
		
		$actual = $variants->map( "Check color", "bank" );
		$expected = "Cheque colour";
		$this->assertEquals( $expected, $actual );
		
		$actual = $variants->map( "check color", "examine" );
		$expected = "check colour";
		$this->assertEquals( $expected, $actual );
		
		$actual = $variants->map( "colored checks", "bank" );
		$expected = "coloured cheques";
		$this->assertEquals( $expected, $actual );
		
		$actual = $variants->map( "colored checks" );
		$expected = "coloured checks";
		$this->assertEquals( $expected, $actual );
		
	}
		
	


}
