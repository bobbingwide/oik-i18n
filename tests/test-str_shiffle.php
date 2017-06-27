<?php // (C) Copyright Bobbing Wide 2017

/** 
 * 
 */

class Tests_str_shiffle extends BW_UnitTestCase {

	/** 
	 * set up logic
	 * 
	 * - ensure any database updates are rolled back
	 */
	function setUp() {
		parent::setUp();
		oik_require( "bb_BB.php", "oik-i18n" );
	}
	
	function str_shiffle_round_trip( $before, $after ) {
		$intermediate = str_shiffle( $before );
		$this->assertEquals( $after, $intermediate );
		$again = str_shiffle( $intermediate );
		$this->assertEquals( $before, $again );
	}
	
	
	function test_str_shiffle() {
		$this->str_shiffle_round_trip( "ar", "ra" );
		$this->str_shiffle_round_trip( "are", "rae" ) ;
		$this->str_shiffle_round_trip( "area", "raae" );
		$this->str_shiffle_round_trip( "areas", "raaes" );
		$this->str_shiffle_round_trip( "areas6", "raae6s" );
		$this->str_shiffle_round_trip( "be", "eb" );
		$this->str_shiffle_round_trip( "word", "owdr" );
		$this->str_shiffle_round_trip( "boing", "obnig" );
	}
	
	
}

