<?php


/**
 * Prototype converting html to the language country specific locale.
 */
require_once 'class-dom-stringer.php';
require_once 'class-dom-string-updater.php';
require_once 'class-theme-files.php';

$html = '<p class="fred">index.html<b>!</b></p>';

$theme = 'fizzie';
$locale = 'bb_BB';

$theme_files = new Theme_Files();
$theme_files->load_text_domain( $theme, $locale );
/*
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



$stringer = new DOM_string_updater();
$stringer->loadHTML( $html );
$stringer->set_locale( $locale );
$stringer->translate();
$html_after = $stringer->saveHTML();
echo 'HTML:';
echo PHP_EOL;
echo $html_after;
echo '!';


 