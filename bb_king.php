<?php
/**
 * @package oik-i18n
 * @copyright (C) Copyright Bobbing Wide 2016-2020

/** 
 * Syntax: oikwp bb_king.php
 * run from the oik-i18n directory
 * 
 * Create the bb_BB.po and .mo files from the UK English language version files for WordPress
 *
 * Process
 * - Update WordPress to the latest level
 * - Update the translations for the UK English language version
 * - Run bb_king.php
 *
 * The new language files are created in the site's wp-content/languages folder.
 *
 * Note: bb_BB.php needs to work off .po files as well as .pot files, which means
 * - bb_BB has to handle msgstr values which have already been set.
 * - bb_BB has to know which files are la_CY.po files (with locale) and which are .pot ( without )
 * - bb_BB has to change directory to WP_LANG_DIR to locate these files
 */
 
/**
 * Function to invoke when loaded 
 */ 
function bb_king_loaded() {
	oik_require( "bb_BB.php", "oik-i18n" );
	bb_BB( "admin-", 'plugin' );
	bb_BB( "admin-network-", 'plugin' );
	bb_BB( "", 'plugin' );
	do_msgfmt( "admin-" );
	do_msgfmt( "admin-network-" );
	do_msgfmt( "" );
}

/**
 * Create a .mo file from a .po file 
 * 
 * Invoke the msgfmt program to convert the locale's .po file to the .mo file
 * Note: this is a local version that caters for WordPress's global files
 * 
 * @param string $plugin - the plugin slug - may be blank
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
