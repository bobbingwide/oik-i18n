<?php
/*
 * This is an incomplete test for extracting strings
 *
 * Code copied from the early prototype of html2la_CY.php
 * /*
global $l10n;
//print_r( $l10n );
print_r( $l10n[$locale] );

foreach ( [ 'en_US', 'en_GB', 'bb_BB'] as $locale) {
	//$switched=switch_to_locale( $locale );
	//if ( ! $switched ) {
	//	echo "Didn't switch";
	//}
	echo $locale;

	$hw=__( 'index.html', $locale );
	echo $hw;
	echo PHP_EOL;
}
	gob();
*/



*/

/**
 * @package
 *
 * Tests for logic in shortcodes/oik-countdown.php
 */
class Tests_extract_strings extends BW_UnitTestCase {

	function setUp(): void {
		parent::setUp();

		//oik_require_lib( "oik-sc-help" );
		//oik_require( "shortcodes/oik-countdown.php" );
	}

	function incomplete_test_extract() {
		// $html = '<p class="fred">index.html<b>!</b></p>';

		$theme = 'fizzie';
		$locale = 'bb_BB';
	}

	function incomplete_text_translate() {
		$stringer=new DOM_string_updater();
		$stringer->loadHTML( $html );
		$stringer->set_locale( $locale );
		$stringer->translate();
		$html_after=$stringer->saveHTML();
		echo 'HTML:';
		echo PHP_EOL;
		echo $html_after;
		echo '!';
	}
}