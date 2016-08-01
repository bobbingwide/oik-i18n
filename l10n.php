<?php // (C) Copyright Bobbing Wide 2013-2016

/** 
 * Return the list of internationalized plugins to localize 
 */
function l10n_plugin_list() {
  $plugins  = "oik,oik-nivo-slider,";
  $plugins .= "bbboing,cookie-cat,cookie-category,diy-oik,dtib-review,eedge,effort,kate,oik,oik-adr,";
  $plugins .= "oik-anything-slider,oik-batch,oik-batchmove,oik-bbpress,oik-blogger-redirect,oik-bob-bing-wide,oik-bp-signup-email,";
  $plugins .= "oik-business,oik-bwtrace,oik-child-theme,oik-clone,oik-content,oik-css,oik-css3,oik-debug-filters,oik-edd,oik-email-signature,";
  $plugins .= "oik-external-link-warning,oik-fields,oik-fum,oik-header,oik-html-importer,oik-i18n,oik-infusionsoft,oik-l10n,oik-money,";
  $plugins .= "oik-moreoptions,oik-ms,oik-mshot,oik-plugins,oik-post-type-support,oik-presentation,oik-privacy-policy,oik-rating,";
  $plugins .= "oik-responsive-menu,oik-rwd,oik-sc-help,oik-shortcodes,oik-sidebar,oik-signup-user-notification,oik-squeeze,oik-testimonials,";
  $plugins .= "oik-themes,oik-thesis-featurebox,oik-tos,oik-tunes,oik-types,oik-user,oik-video,oik-window-width,oik-woo,oik-working-feedback,";
  $plugins .= "setup,uk-tides,unserialize,us-tides,";
	$plugins .= "oik-weightcountry-shipping,oik-weightcountry-shipping-pro";
  return( bw_as_array( $plugins ) ); 
}

/**
 * main processing
 *
 * We perform the main processing in the working directory
 * and copy the generated files to the plugin's languages directory
 * This allows us to operate on oik-i18n as well
 *
 */ 
function do_main( $argc, $argv) {
  oik_require( "bobbcomp.inc" );
  
  echo getcwd();
  echo PHP_EOL;
  chdir( __DIR__ . "/working" );
  
  echo getcwd();
  echo PHP_EOL;

  //echo $argc;
  //print_r( $argv );
  if ( $argc > 1 ) {
    $plugins = bw_as_array( $argv[1] );
  } else {
    $plugins = l10n_plugin_list();
    echo "Processing plugin list";
    gobang();
  } 
	bw_trace2( $plugins, "plugins" ); 
  foreach ( $plugins as $plugin ) {
    do_plugin( $plugin );
  }
}


/**
 * Function to invoke when l10n is loaded
 *  
 */
function l10n_loaded() {
  //echo __FILE__;
  //echo __FUNCTION__;
  //echo $_SERVER['argc'];
  //print_r( $_SERVER['argv'] );
  //echo PHP_EOL; 
  $included_files = get_included_files();
  //print_r( $included_files );
  if ($included_files[0] == __FILE__) {
		// Actually this is no good now since we need to be invoked from oikwp
    do_main( $_SERVER['argc'], $_SERVER['argv'] );
  } else {
		if ( isset( $_SERVER['argc'] ) ) {
			echo "I'm not main";
			do_main( $_SERVER['argc'], $_SERVER['argv'] );
		}
  }   
} 

//l10n_loaded(); 

/**
 * Build a plugin's language files
 *
 * This is the logic of the original bat file
 * `
 * rem Build the bbboing language version of the oik-l10n plugin
 * cd c:\apache\htdocs\svn_tools\trunk 
 * php makeoik.php wp-plugin c:\apache\htdocs\wordpress\wp-content\plugins\oik-l10n
 * php c:\apache\htdocs\wordpress\wp-content\plugins\play\bb_BB.php oik-l10n > oik-l10n-bb_BB.po
 * msgfmt -c -v --statistics -o oik-l10n-bb_BB.mo oik-l10n-bb_BB.po
 * copy oik-l10n.pot c:\apache\htdocs\wordpress\wp-content\plugins\oik-l10n\languages 
 * copy oik-l10n-bb_BB.po c:\apache\htdocs\wordpress\wp-content\plugins\oik-l10n\languages
 * copy oik-l10n-bb_BB.mo c:\apache\htdocs\wordpress\wp-content\plugins\oik-l10n\languages
 *
 * rem build the bbboing language version of the oik base plugin
 * cd c:\apache\htdocs\svn_tools\trunk 
 * php makeoik.php wp-plugin c:\apache\htdocs\wordpress\wp-content\plugins\oik
 * php c:\apache\htdocs\wordpress\wp-content\plugins\play\bb_BB.php oik > oik-bb_BB.po
 * msgfmt -c -v --statistics -o oik-bb_BB.mo oik-bb_BB.po
 *
 * copy oik.pot c:\apache\htdocs\wordpress\wp-content\plugins\oik\languages 
 * copy oik-bb_BB.po c:\apache\htdocs\wordpress\wp-content\plugins\oik\languages
 * copy oik-bb_BB.mo c:\apache\htdocs\wordpress\wp-content\plugins\oik\languages
 * `
 * 
 * Notes:
 * - makeoik - is an extended makepot which deals with oik's extension APIs
 * - bb_BB - is the bbboing language version - used for testing
 * - we always build the bb_BB version
 *
 * @param string $plugin the plugin folder name
 * @param string $lang the language versions to create
 *
 
 */

function do_plugin( $plugin, $lang ) {
  echo "processing $plugin";
  echo PHP_EOL;
  $res = do_makeoik( $plugin );
  if ( $res ) {
    $res = do_bb_BB( $plugin ); 
    $res = do_msgfmt( $plugin );
    $res = do_copytoplugin( $plugin );
		$res = do_otherlangs( $plugin, $lang );
  } else {
    echo "do_makeoik failed";
  }  
}

/**
 * Perform the first step - extract the translatable strings
 *
 * Here we use makeoik.php rather than makepot.php since we have additional functions from which we extract the 
 * translatable strings. 
 *
 * @param string $plugin
 */
function do_makeoik( $plugin ) {
  oik_require( "makeoik.php", "oik-i18n" );
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

/**
 * Perform the second step in the creation of a bb_BB language file
 *  
 * php c:\apache\htdocs\wordpress\wp-content\plugins\play\bb_BB.php oik > oik-bb_BB.po
 */
function do_bb_BB( $plugin ) {
  oik_require( "bb_BB.php", "oik-i18n" );
  //php c:\apache\htdocs\wordpress\wp-content\plugins\play\bb_BB.php oik > oik-bb_BB.po
  bb_BB( $plugin );
  return( true ); 
}

/**
 * Translate the plugin into other languages
 *
 * @param string $plugin the plugin folder
 * @param string $lang the required language(s)
 * @return bool result of the translations
 */ 
function do_otherlangs( $plugin, $lang ) {
	oik_require( "la_CY.php", "oik-i18n" );
	la_CY( $plugin, $lang );
	return( true ); 
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
  $cmd = "msgfmt -c -v --statistics -o $plugin-$locale.mo $plugin-$locale.po";  
  echo $cmd . PHP_EOL;
  $text = system( $cmd, $res );
  echo "$res $text" . PHP_EOL;
  return( $res );
} 

/**
 * Copy the locale files to the language directory of the translated plugin
 * 
 * The files should have been generated in oik-i18n\working
 * We need to copy them to $plugin\languages
 * e.g. for the "oik" base plugin and locale "bb_BB"
 *  copy oik.pot c:\apache\htdocs\wordpress\wp-content\plugins\oik\languages 
 *  copy oik-bb_BB.po c:\apache\htdocs\wordpress\wp-content\plugins\oik\languages
 *  copy oik-bb_BB.mo c:\apache\htdocs\wordpress\wp-content\plugins\oik\languages
 *
 * Note: This copies the .pot file for each locale. But that's not a problem yet. 
 *  
 * @param string $plugin - the slug of the plugin
 * @param string $locale - the target locale
 */
function do_copytoplugin( $plugin, $locale="bb_BB" ) {
  $source_dir = getcwd();
  $target_dir = dirname( dirname( $source_dir) );
  $target_dir .= "/$plugin/languages/";
  oik_require( "admin/oik-relocate.inc" );
  // Note: bw_mkdir expects a full file name
  bw_mkdir( "$target_dir/$plugin.pot" );
	
	if ( $source_dir != $target_dir ) {
		if ( file_exists( "$source_dir/$plugin.pot" ) ) { 
			copy( "$source_dir/$plugin.pot", "$target_dir/$plugin.pot" );
		}
		copy( "$source_dir/$plugin-$locale.po", "$target_dir/$plugin-$locale.po" );
		copy( "$source_dir/$plugin-$locale.mo", "$target_dir/$plugin-$locale.mo" );
		echo "Copied files from source: $source_dir" . PHP_EOL; 
		echo "Copied files to target: $target_dir" . PHP_EOL;
	}
}   
