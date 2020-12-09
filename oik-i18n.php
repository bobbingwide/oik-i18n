<?php
/*
Plugin Name: oik internationalization 
Plugin URI: https://www.oik-plugins.com/oik-i18n
Description: Internationalization (or is it localization) for oik and related plugins
Version: 0.3.0
Author: bobbingwide
Author URI: http://www.oik-plugins.com/author/bobbingwide
Text Domain: oik-i18n
Domain Path: /languages/
Author URI: http://www.bobbingwide.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

    Copyright 2013-2020 Bobbing Wide (email : herb@bobbingwide.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/

/**
 * Implement "oik_loaded" for oik-i18n
 * 
 */
function oiki18n_oik_loaded() {
  oiki18n_load_plugin_textdomain();
  //add_action( "shutdown", "oiki18n_merged_filters" );
  // add_action( "shutdown", "oiki18n_global_l10n" );
  add_action( "shutdown", "oiki18n_report_global_gettexts" );
}

/**
 * Implement "oik_login_only" for oik-i18n
 * 
 */
function oiki18n_login_only() {
  oiki18n_load_plugin_textdomain();
  //add_action( "shutdown", "oiki18n_merged_filters" );
  add_action( "shutdown", "oiki18n_global_l10n" );
  // oik_require( "includes/oik-filters.inc" );
  //bw_replace_filter( "gettext" );
}

/**
 * Trace the $merged_filters and $wp_filter global variables
 *
 * Just to see if we need to do anything with merged_filters **?**
 */
function oiki18n_merged_filters() {
  global $merged_filters, $wp_filter;
  bw_trace2( $merged_filters, "merged_filters", false );
  bw_trace2( $wp_filter, "wp_filter", false );
  //print_r( $wp_filter );
}

function oiki18n_global_l10n() {
  global $l10n;
  //bw_trace2( $l10n, "localization" );
  foreach ( $l10n as  $key => $mo ) {
    bw_trace2( $key, "MOkey", false );
    if ( $key == "oik-i18n" ) {
       bw_trace2( $mo, "MO", false );
    } else {
      $count = count( $mo->entries );
      bw_trace2( $count, "MO entries: $l10n" );
    }  
  }
  // oiki18n_report_global_gettexts();
} 

function oiki18n_report_global_gettexts() { 
  global $l10n_gettexts;
  bw_trace2( $l10n_gettexts );
}
/**
 * Just a MO
   [%d Theme Update] => Translation_Entry Object
                (
                    [is_plural] => 1
                    [context] => 
                    [singular] => %d Theme Update
                    [plural] => %d Theme Updates
                    [translations] => Array
                        (
                            [0] => %d Tmhee Utadpe
                            [1] => %d Thmee Uepdtas
                        )

                    [translator_comments] => 
                    [extracted_comments] => 
                    [references] => Array
                        (
                        )

                    [flags] => Array
                        (
                        )

                )
 */


/** 
 * Let's ask some questions
 * 
 * - Do we really need a whole load of textdomains for each of the oik plugins or can we get away with one?
 * - How many installations actually need language versions anyway? 40% ?
 * - What's the difference between load_textdomain() and load_plugin_textdomain()
 */ 
function oiki18n_load_plugin_textdomain( ) {
  $files = bw_as_array( "oik,oik-nivo-slider,bbboing,oik-privacy-policy" );
  foreach ( $files as $file ) { 
    //bw_trace2( $file, "file" );   
    load_plugin_textdomain( $file, false, dirname( plugin_basename(__FILE__)).'/languages/' );
    //gobang();
  }

} 

/**  This code is copied from wp-includes/l10n.php
function load_textdomain( $domain, $mofile ) {
	global $l10n;

	$plugin_override = apply_filters( 'override_load_textdomain', false, $domain, $mofile );

	if ( true == $plugin_override ) {
		return true;
	}

	do_action( 'load_textdomain', $domain, $mofile );

	$mofile = apply_filters( 'load_textdomain_mofile', $mofile, $domain );

	if ( !is_readable( $mofile ) ) return false;

	$mo = new MO();
	if ( !$mo->import_from_file( $mofile ) ) return false;

	if ( isset( $l10n[$domain] ) )
		$mo->merge_with( $l10n[$domain] );

	$l10n[$domain] = &$mo;

	return true;
}
*/

/**
 * Implement "load_textdomain" action for oik-i18n
 *
 * Perform any preliminary logic before the mofile for the domain is loaded
 * e.g. Fetch the file from somewhere, rebuild it or something else
 * 
 *
 * @param string $domain - the domain to load
 * @param string $mofile - the name of the MO file to load
 */
function oiki18n_load_textdomain( $domain, $mofile ) {
}

/** 
 * Implement "load_textdomain_mofile" filter for oik-i18n 
 * 
 * @param string $mofile - the name of the MO file    
 * @param string $domain - the text domain name
 * @return string $mofile - the new name of the MO file
 */
function oiki18n_load_textdomain_mofile( $mofile, $domain ) {
  //bw_trace2();
  // bw_backtrace();
  return( $mofile );
} 

/**
 * Decide whether or not we're going to override the processing for "load_textdomain"
 *
 * There seems to be no reason to do this... yet! 
 *  
 * @param bool - false
 * @param string $domain - the name of the domain being loaded
 * @param string $mofile - the name of the MO file being loaded
 * @return bool - true if you want to override
 * 
	$plugin_override = apply_filters( 'override_load_textdomain', false, $domain, $mofile );
 */
function oiki18n_override_load_textdomain( $override=false, $domain, $mofile  ) {
  //global $l10n;
  //bw_trace2( $l10n, "localization" );
  return( $override );
}

/** 
 *
 0. bw_lazy_backtrace C:\apache\htdocs\wordpress\wp-content\plugins\oik\bwtrace.inc:55 0
1. bw_backtrace C:\apache\htdocs\wordpress\wp-content\plugins\oik-i18n\oik-i18n.php:197 0
2. oiki18n_gettext(Spelling and Grammar,Spelling and Grammar,jetpack) C:\apache\htdocs\wordpress\wp-content\plugins\oik-i18n\oik-i18n.php:0 3
3. call_user_func_array(oiki18n_gettext,Array) C:\apache\htdocs\wordpress\wp-includes\plugin.php:198 2
4. apply_filters(gettext,Spelling and Grammar,Spelling and Grammar,jetpack) C:\apache\htdocs\wordpress\wp-includes\l10n.php:69 4
5. translate(Spelling and Grammar,jetpack) C:\apache\htdocs\wordpress\wp-content\plugins\jetpack\class.jetpack.php:805 2
6. get_module(C:\apache\htdocs\wordpress\wp-content\plugins\jetpack/modules/after-the-deadline.php) C:\apache\htdocs\wordpress\wp-content\plugins\jetpack\class.jetpack.php:702 1
7. get_available_modules C:\apache\htdocs\wordpress\wp-content\plugins\jetpack\class.jetpack.php:843 0
8. is_module(vaultpress) C:\apache\htdocs\wordpress\wp-content\plugins\jetpack\class.jetpack.php:0 1
9. array_filter(Array,Array) C:\apache\htdocs\wordpress\wp-content\plugins\jetpack\class.jetpack.php:399 2
10. load_modules() C:\apache\htdocs\wordpress\wp-content\plugins\jetpack\class.jetpack.php:0 1
11. call_user_func_array(Array,Array) C:\apache\htdocs\wordpress\wp-includes\plugin.php:438 2
12. do_action(plugins_loaded) C:\apache\htdocs\wordpress\wp-settings.php:209 1
13. require_once(C:\apache\htdocs\wordpress\wp-settings.php) C:\apache\htdocs\wordpress\wp-config.php:162 1
14. require_once(C:\apache\htdocs\wordpress\wp-config.php) C:\apache\htdocs\wordpress\wp-load.php:29 1
15. require_once(C:\apache\htdocs\wordpress\wp-load.php) C:\apache\htdocs\wordpress\wp-admin\admin.php:30 1
16. require_once(C:\apache\htdocs\wordpress\wp-admin\admin.php) C:\apache\htdocs\wordpress\wp-admin\options-general.php:10 1

 */
function oiki18n_gettext( $translated, $text, $domain ) {
  global $l10n_gettexts;
  if ( is_null( $l10n_gettexts )) {
    $l10n_gettexts = array();
    // bw_backtrace();
  }
  if ( !isset( $l10n_gettexts[ $domain ] ) ) {
    $l10n_gettexts[ $domain ] = 1;
  } else {
    $l10n_gettexts[ $domain ] += 1; 
  }
  
  // $caller = bw_callerof( "translate" );
  
  return( $translated );  
}
/**
 * Start up processing for oik-i18n
 *
 * DON'T perform internationalization for Ajax requests
 *
 */
function oiki18n_plugin_loaded() {
  //add_filter( "override_load_textdomain", "oiki18n_override_load_textdomain", 10, 3 );
  //add_action( "load_textdomain", "oiki18n_load_textdomain", 10, 2 );
  
  if ( defined('DOING_AJAX') && DOING_AJAX ) { 
    // Perhaps we should be testing the client's language! 
  } else {
    add_action( "oik_loaded", "oiki18n_oik_loaded", 9 );   
    add_action( "oik_login_only", "oiki18n_login_only" ); 
    add_filter( "load_textdomain_mofile", "oiki18n_load_textdomain_mofile", 10, 2 );
    add_filter( "gettext", "oiki18n_gettext", 10, 3 );
  }
}

oiki18n_plugin_loaded();


/**
 * Examples of each of the different APIs that can be used to create entries in the POT file
   This code extracted from makepot.php 
   
	var $rules = array(

		'_' => array('string'),
		'__' => array('string'),
		'_e' => array('string'),
		'_c' => array('string'),
		'_n' => array('singular', 'plural'),
		'_n_noop' => array('singular', 'plural'),
		'_nc' => array('singular', 'plural'),
		'__ngettext' => array('singular', 'plural'),
		'__ngettext_noop' => array('singular', 'plural'),
		'_x' => array('string', 'context'),
		'_ex' => array('string', 'context'),
		'_nx' => array('singular', 'plural', null, 'context'),
		'_nx_noop' => array('singular', 'plural', 'context'),
		'_n_js' => array('singular', 'plural'),
		'_nx_js' => array('singular', 'plural', 'context'),
		'esc_attr__' => array('string'),
		'esc_html__' => array('string'),
		'esc_attr_e' => array('string'),
		'esc_html_e' => array('string'),
		'esc_attr_x' => array('string', 'context'),
		'esc_html_x' => array('string', 'context'),
		'comments_number_link' => array('string', 'singular', 'plural'),
                
	);
 */

/**
 * Some examples of WordPress i18n functions where text strings will get translated
 * 
 * run makepot.php wp-plugin oik-i18n to see these generated in oik-i18n.pot
 
 */
function oiki18n_examples() {

  $_ = _( "underscore is an alias of gettext", "oik-i18n" );
  
  $__ = __( "WordPress uses double underscore instead of gettext.", "oik-i18n" ); 
  
  $_e = _e( "Displays the returned translated text", "oik-i18n" );
  
  $_c = _c( "Deprecated - use _x", "oik-i18n" );
  
  $_n = _n( "Retrieve the single or plural form based on the amount. %s " 
          , "Retrieve the plural or single form based on the amount. %s"
          , $amount
          , "oik-i18n" 
          );
  echo $_n;        
          
  $n_noop = _n_noop( "Register plural string in POT but don't translate" 
                   , "Register plural string in POT but don't translate"
                   , "oik-i18n" 
                   ); 
   
  $nc = _nc( "deprecated - use _ nx - singular", "plural" );
  $__ngettext = __ngettext( "deprecated - use _n", "deprecated - plural - use _n" );
  $__ngettext_noop = __ngettext__noop( "deprecated - use _n_noop", "deprecated - plural use_n_noop" );
    
  
           
  $_x = _x( "Translate this", "context", "oik-i18n" );
  $_ex = _ex ( "Display the translated text", "context", "oik-i18n" );
  

  $_nx = _nx( "Retrieve the <i>single</i> or <i>plural</i> form based on the amount. %s " 
          , "Retrieve the plural or single form based on the amount. %s"
          , $amount
          , "context"
          , "oik-i18n" 
          );
  
//		'_nx' => array('singular', 'plural', null, 'context'),
//		'_nx_noop' => array('singular', 'plural', 'context'),
//		'_n_js' => array('singular', 'plural'),
//		'_nx_js' => array('singular', 'plural', 'context'),
//		'esc_attr__' => array('string'),
//		'esc_html__' => array('string'),
//		'esc_attr_e' => array('string'),
//		'esc_html_e' => array('string'),
//		'esc_attr_x' => array('string', 'context'),
//		'esc_html_x' => array('string', 'context'),
//		'comments_number_link' => array('string', 'singular', 'plural'),
 
  
}
   
  


