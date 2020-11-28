<?php
/**
 * @package oik-i18n
 * @copyright (C) Copyright Bobbing Wide 2013-2020
 *
 * Syntax: oikwp l10n.php component langs
 *
 * from oik-i18n directory
 *
 * where:
 * - component is the plugin / theme name eg oik ( plugin ), fizzie ( theme )
 * - langs are the target locale(s) eg eb_GB or bb_BB
 *
 * Invoke l10n directly to perform localization of the selected plugin/theme for the given lang(s)
 *
 * 
 * Note: If the plugin is hosted on wordpress.org and has been translated to a selected language
 * then we can download the .po and .mo files and rename them.
 * 
 * e.g. For oik-weight-zone-shipping
 * https://translate.wordpress.org/projects/wp-plugins/oik-weight-zone-shipping/dev/fr/default
 * 
 * file  | rename/copy to
 * ----- | -------------
 * wp-plugins-oik-weight-zone-shipping-dev-fr.mo | oik-weight-zone-shipping-fr_FR.mo
 * wp-plugins-oik-weight-zone-shipping-dev-fr.po | oik-weight-zone-shipping-fr_FR.po
 
 * wp-plugins-oik-weight-zone-shipping-dev-en-gb.po	| oik-weight-zone-shipping-en_GB.po
 * 
 * We can download the file by visiting: 
 * https://translate.wordpress.org/projects/wp-plugins/oik-weight-zone-shipping/dev/fr/default/export-translations?format=mo
 */
 
/** 
 * Returns the list of internationalized plugins to localize 
 * 
 * @return array 
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
	$plugins .= "oik-weightcountry-shipping,oik-weightcountry-shipping-pro,";
	$plugins .= "oik-weight-zone-shipping,oik-weight-zone-shipping-pro,";
  return( bw_as_array( $plugins ) ); 
}

/**
 * Return the list of locales to process
 * 
 * @param string $component the component to process
 * @return array array of locales possibly including bb_BB and most likely fr_FR and en_GB
 */
function l10n_list_locales( $component ) {
	gob();
	return( "fr_FR" );
}

/**
 * main processing
 *
 * We perform the main processing in the working directory
 * and copy the generated files to the component's languages directory
 * This allows us to operate on oik-i18n as well
 *
 */ 
function l10n_run_l10n() {
  //oik_require( "bobbcomp.inc" );
  
  echo getcwd();
  echo PHP_EOL;
  chdir( __DIR__ . "/working" );
  
  echo getcwd();
  echo PHP_EOL;

  //echo $argc;
  //print_r( $argv );
	$components = oik_batch_query_value_from_argv( 1, null );
  if ( $components ) {
    $components = bw_as_array( $components );
  } else {
    $components = l10n_component_list();
    echo "Processing component list";
	echo "Not yet supported. Please specify the component to process.";
    gobang();
  }
	$lang = oik_batch_query_value_from_argv( 2, "en_GB" );
	 
	bw_trace2( $components, "components" );
  foreach ( $components as $component ) {
		
		if ( !$lang ) {
			$lang = l10n_list_locales( $component );
		}	
    do_component( $component, $lang );
  }
}


/**
 * Function to invoke when l10n is loaded
 * 
 * The original logic tested to see if we were the first file.
 * Now we need to respond either to run_l10n.php or have a function such as  invoked directly
 * 
 */
function l10n_loaded() {
	add_action( "run_l10n.php", "l10n_run_l10n" );
  //echo __FILE__;
  //echo __FUNCTION__;
  //echo $_SERVER['argc'];
  //print_r( $_SERVER['argv'] );
  //echo PHP_EOL; 
  //$included_files = get_included_files();
  //print_r( $included_files );
  //if ($included_files[0] == __FILE__) {
		// Actually this is no good now since we need to be invoked from oikwp
    //do_main( $_SERVER['argc'], $_SERVER['argv'] );
  //} else {
	//	if ( isset( $_SERVER['argc'] ) ) {
	 // 	echo "I'm not main";
	 // 	do_main( $_SERVER['argc'], $_SERVER['argv'] );
	 // }
  //}   
} 

l10n_loaded(); 

/**
 * Build a component's language files
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
 * @param string $component the component folder name
 * @param string $lang the language versions to create
 *
 
 */

function do_component( $component, $lang="en_GB" ) {
	echo "Processing: " . $component . PHP_EOL;

	list( $component_type, $component_path ) = l10n_locate_component( $component );
	echo "Component type: " . $component_type . PHP_EOL;
	echo "Component path: " . $component_path . PHP_EOL;
	echo PHP_EOL;
	$res = true;
	if ( "plugin" === $component_type ) {
		$res=maybe_do_makeoik( $component, $component_path );
		if ( $res ) {
			echo "do_makeoik worked";
		} else {
			echo "do_makeoik failed";
		}
	} elseif ( "theme" === $component_type ) {
		copytoworking( $component, $component_path );
	} else {
		echo "Invalid component. Path not found: " . $component;
		$res = false;
	}
	if ( $res ) {
		$res=do_bb_BB( $component, $component_type );

		$res=do_msgfmt( $component );
		$res=do_copytocomponent( $component, 'bb_BB', $component_path );
		$res=do_otherlangs( $component, $lang, $component_path );
	}
}

/**
 * Locates the languages folder for the component.
 *
 * Assume it's a plugin first of all.
 * Then try for theme.
 *
 * @param $component
 *
 * @return string
 */
function l10n_locate_component( $component ) {
	$path = oik_path( 'languages', $component );
	if ( file_exists( $path )) {
		return [ 'plugin', $path ];
	}
	$theme_dir = get_stylesheet_directory();
	$theme_dir = dirname( $theme_dir ) . '/' . $component . '/languages';
	if ( file_exists( $theme_dir ) ) {
		return ['theme', $theme_dir ];
	}
	return [null, null];
}


/**
 * Makeoik is required when we can't use makepot within npm.
 *
 * This is the case when the source files are in the same folder as the build files;
 * as in the original versions of oik and oik-blocks.
 *
 * Until these components are converted to use wp-scripts then we won't have internationalised content in the editor.
 * Newer components such as sb-children-block uses the modern method.
 *
 * node_modules | src | Process to use
 * ------------ | ---- | ------------
 * n            | n    | makeoik
 * n            | y    | makeoik -
 * y            | n    | makeoik - but JavaScipt blocks are not internationalised
 * y            | y    | npm run makepot & npm run l10n & npm run makejson
 *
 * @param $component
 * @param string $component_path -
 * @return bool - always true?
 *
 */
function maybe_do_makeoik( $plugin, $component_path ) {
	$node_modules = oik_path( 'node_modules', $plugin );
	$src = oik_path( 'src', $plugin );
	if ( file_exists( $node_modules ) && file_exists( $src ) ) {
		echo "Not running makeoik - use npm run makepot";
		echo "I assume you've already done that!";
		echo PHP_EOL;
		copytoworking( $plugin, $component_path );
	} else {
		echo "Running makeoik";
		echo PHP_EOL;
		$res=do_makeoik( $plugin );
		echo $res;
		echo PHP_EOL;
		copyfromworking( $plugin );
	}
	$res=true;
	return $res;
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
  echo "makeoik: " . $plugin_path;
  echo PHP_EOL;
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
  echo "result of makeoik: " . $res;
  echo PHP_EOL;
  return( $res );
}



/**
 * Perform the first step - extract the translatable strings
 *
 * Here we use makepot.php 
 *
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

/**
 * Perform the second step in the creation of a bb_BB language file
 *  
 * php c:\apache\htdocs\wordpress\wp-content\plugins\play\bb_BB.php oik > oik-bb_BB.po
 */
function do_bb_BB( $component, $component_type ) {
  oik_require( "bb_BB.php", "oik-i18n" );
  //php c:\apache\htdocs\wordpress\wp-content\plugins\play\bb_BB.php oik > oik-bb_BB.po
  bb_BB( $component, $component_type );
  return( true ); 
}

/**
 * Translate the component into other languages
 *
 * @param string $component the component folder
 * @param string $lang the required language(s)
 * @param string $component_path
 * @return bool result of the translations
 */ 
function do_otherlangs( $component, $lang, $component_path ) {
	oik_require( "la_CY.php", "oik-i18n" );
	la_CY( $component, $lang, $component_path );
	return( true ); 
} 

/**
 * Create a .mo file from a .po file 
 * 
 * Invoke the msgfmt program to convert the locale's .po file to the .mo file
 * 
 * @param string $component - the component slug e.g. oik-privacy-policy
 * @param string $locale - the target locale
 * 
 */
function do_msgfmt( $component, $locale="bb_BB" ) {
  $cmd = "msgfmt -c -v --statistics -o $component-$locale.mo $component-$locale.po";
  echo $cmd . PHP_EOL;
  $text = system( $cmd, $res );
  echo "$res $text" . PHP_EOL;
  return( $res );
} 

/**
 * Copy the locale files to the language directory of the translated component
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
function do_copytocomponent( $plugin, $locale="bb_BB", $component_path ) {
  $source_dir = getcwd();
  $target_dir = $component_path;
  oik_require( "admin/oik-relocate.inc" );
  // Note: bw_mkdir expects a full file name but doesn't use the filename bit
  bw_mkdir( "$target_dir/$plugin.pot" );
	
	if ( $source_dir != $target_dir ) {
		if ( file_exists( "$source_dir/$plugin.pot" ) ) { 
			copy( "$source_dir/$plugin.pot", "$target_dir/$plugin.pot" );
		}
		copy( "$source_dir/$plugin-$locale.po", "$target_dir/$plugin-$locale.po" );
		copy( "$source_dir/$plugin-$locale.mo", "$target_dir/$plugin-$locale.mo" );
		echo "Copied files from source: $source_dir" . PHP_EOL; 
		echo "Copied files to target: $target_dir" . PHP_EOL;
	} else {
		echo "Not copied from source to target" . PHP_EOL;
		echo "Source: $source_dir" . PHP_EOL;
		echo "Target: $target_dir" . PHP_EOL;
	}
}

/**
 * Copies the component.pot file from $component/languages to oik-i18n/working
 *
 * Note: The current working directory is expected to be oik-i18n/working
 *
 * @param $component
 * @param string $component_path - the languages folder for the component ( plugin or theme )
 */
function copytoworking( $component, $component_path ) {
	$target_dir = getcwd();

	$source_dir = $component_path;
	$result = copy( "$source_dir/$component.pot", "$target_dir/$component.pot");
	echo "Copied $component.pot to working?";
	echo $result ? "OK" : "Failed";
	echo PHP_EOL;
}

/**
 * Copies the plugin.pot file from oik-i18n/working to $plugin/languages
 *
 * Note: The current working directory is expected to be oik-i18n/working
 *
 * @param $plugin
 */
function copyfromworking( $plugin ) {
	$source_dir = getcwd();
	$target_dir = dirname( dirname( $source_dir) );
	$target_dir .= "/$plugin/languages";
	echo "Source dir: " . $source_dir;
	echo "Target dir: " . $target_dir;
	$result = copy( "$source_dir/$plugin.pot", "$target_dir/$plugin.pot");
	echo "Copied $plugin.pot from working? ";
	echo $result ? "OK" : "Failed";
	echo PHP_EOL;
}
