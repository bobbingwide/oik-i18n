<?php // (C) Copyright Bobbing Wide 2016

/** 
 * Syntax oikwp bb_king.php
 * run from the oik-i18n directory
 * 
 * Create the bb_BB.po and .mo files from the English language version files for WordPress
 *
 * Process
 * - Update WordPress to the latest level
 * - Update the translations for the English language version
 * - Run bb_king.php
 
 * Note: bb_BB.php needs to work off .po files as well as .pot files
 *
 
 

cd \apache\htdocs\wpms\wp-content\languages

php \apache\htdocs\wordpress\wp-content\plugins\oik-i18n\bb_BB.php admin-en_GB.po 



php bb_BB.php admin-network-en_GB.po
php bb_BB.php en_GB.po


 Directory of c:\apache\htdocs\wpms\wp-content\languages

01/08/2016  21:52           332,749 admin-en_GB.mo
01/08/2016  21:52           464,074 admin-en_GB.po
01/08/2016  21:52            46,044 admin-network-en_GB.mo
01/08/2016  21:52            62,558 admin-network-en_GB.po
01/08/2016  21:52           158,141 en_GB.mo
01/08/2016  21:52           267,930 en_GB.po

*/

function bb_king_loaded() {

	oik_require( "bb_BB.php", "oik-i18n" );
	//bb_BB( "admin-en_GB" );
	//bb_BB( "admin-network" );
	
	bb_BB( "admin-" );
	bb_BB( "admin-network-" );
	bb_BB( "" );
	
	do_msgfmt( "admin-" );
	do_msgfmt( "admin-network-" );
	do_msgfmt( "" );
	
	

}


/**
 * Create a .mo file from a .po file 
 * 
 * Invoke the msgfmt program to convert the locale's .po file to the .mo file
 * 
 * @param string $plugin - the plugin slug e.g. oik-privacy-policy
 * @param string $locale - the target locale
 * 
 */
function do_msgfmt( $plugin, $locale="bb_BB" ) {
  $cmd = "msgfmt -c -v --statistics -o $plugin$locale.mo $plugin$locale.po";  
  echo $cmd . PHP_EOL;
  $text = system( $cmd, $res );
  echo "$res $text" . PHP_EOL;
  return( $res );
} 


bb_king_loaded();
