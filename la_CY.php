<?php // (C) Copyright Bobbing Wide 2015-2017

/**
 *
 * Library: la_CY
 * Provides: la_CY
 * Depends: l10n
 *
 *
 * Library functions to update a plugin-la_CY.po file to the latest version
 * given the new plugin.pot and the previous plugin-la_CY.po
 * 
 * 
 * 
 */


/**
 * Perform the conversions
 * 
 * @param string $plugin
 *  
 */ 
function la_CY( $plugin ) {
	static $c = 0;
	echo "Updating $plugin .po files for selected languages" . PHP_EOL;
	
	$locales = la_CY_get_locales( $plugin );
	$content = la_CY_load_pot( $plugin );
	la_CY_cd_working();
	//print_r ( $locales );
	//gob();
	//var_dump( debug_backtrace() );
	//bw_backtrace();
	if ( $c ) {
		//gob();
	}
	$c++;
	
	foreach ( $locales as $new_locale ) {
		global $locale;
		echo "$plugin $locale $new_locale" . PHP_EOL;
		$locale = $new_locale;
		la_CY_load_locale( $locale );
		la_CY_load_plugin_locale( $plugin, $locale );
		$outfile = la_CY_prepare_po( $plugin, $locale );
		
		la_CY_translate( $plugin, $locale, $content, $outfile );
		// Perform some test translations
		//_e( "About WordPress", $locale);
		//_e( "Powered by WordPress", $locale );
		//_e( "Weight/Country", $plugin );
		//la_CY_trace_l10n();
		//_e( '%1$s may not be fully functional.', $locale );
		//_e( '%1$s may not be fully functional.', $plugin );
		//_e( '%1$s may not be fully functional.', "oik" );
		
		
		la_CY_msgfmt( $plugin, $locale );
		la_CY_copytoplugin( $plugin, $locale );
		
	
	}
}

/**
 * Make our working folder the current directory
 *
 */
function la_CY_cd_working() {
	$dir = chdir( __DIR__ . '/working' );
	echo $dir . PHP_EOL;
	
} 

/**
 * Load the en_US .pot file
 *
 * We load the .pot file from the working directory
 * having assumed that we've just built it ourselves.
 * 
 * But this isn't where it's built by makepot is it?
 *
 */
function la_CY_load_pot( $plugin ) {
	$real_file = oik_path( "languages/$plugin.pot", $plugin );
  //$real_file = "$plugin.pot" ;
  echo __FUNCTION__ . ": Processing $real_file" . PHP_EOL;
  $content = file( $real_file );
	return( $content );
}

/**
 * Load the plugin's current language file
 *
 * We assume that the textdomain is the same as the plugin
 * AND that the languages files are stored in the plugin's languages folder
 * 
 * Note: We have to unload any previous language version first
 * 
 */
function la_CY_load_plugin_locale( $plugin, $locale ) {
	echo "Loading the translation file for the plugin" . PHP_EOL;
	unload_textdomain( $plugin );
	$result = load_plugin_textdomain( $plugin, false, "$plugin/languages" );
	print_r( $result );
}

function la_CY_trace_l10n() {
	global $l10n;
	bw_trace2( $l10n, "global l10n" );
}

/**
 * Get the Last translator and language team
 * `
 * "Last-Translator: Rémy Perona <remperona@gmail.com>\n"
 * "Language-Team: Rémy Perona <remperona@gmail.com>\n"
 * `
 * 
 * @TODO Develop logic using get_plugin_data() or whatever the API is
 */
function la_CY_last_translator( $outfile ) {
	$last_translator = null;
	if ( $last_translator ) {
		la_CY_outfile( $outfile, $last_translator );
	}
} 

/**
 * Run msgfmt to format the .mo file
 *
 * @TODO Check what happens for null target strings
 * 
 */
function la_CY_msgfmt( $plugin, $locale ) {
	if ( function_exists( "do_msgfmt" ) ) {
		do_msgfmt( $plugin, $locale );
	}	else {
		echo "You'll need to run msgfmt manually for $plugin $locale";
	}
}

/**
 * Copy the working files to the plugin
 */
function la_CY_copytoplugin( $plugin, $locale ) {
	if ( function_exists( "do_copytoplugin" ) ) {
		do_copytoplugin( $plugin, $locale );
	} else { 
		echo "You'll need to copy files manually for $plugin $locale";
	}
}


/**
 * Load the locale .mo files into one big translate table
 * 
 * admin-$locale.mo
 * admin-network-$locale.mo
 * continents-cities-$locale.mo
 * $locale.mo
 * plugins/
 * themes/
 *
 * @TODO and, in the future, the oik-i18n working directory's .mo files can also be loaded as 'default'
 *
 */
function la_CY_load_locale( $locale ) {
  oik_require( "bobbcomp.inc" );
  wp_set_lang_dir();
  $files = "admin-$locale.mo,admin-network-$locale.mo,continents-cities-$locale.mo,$locale.mo";
  $files .= ",themes/twentyeleven-$locale.mo,themes/twentyten-$locale.mo,themes/twentythirteen-$locale.mo,themes/twentytwelve-$locale.mo,themes/twentyfourteen-$locale.mo";
	$files .= ",plugins/akismet-$locale.mo,plugins/buddypress-$locale.mo,plugins/woocommerce-$locale.mo,plugins/woocommerce-admin-$locale.mo";
  
  // $files = "themes/twentyfourteen-$locale.mo";
  $files = bw_as_array( $files );
  foreach ( $files as $file ) {
    echo "Loading: " . WP_LANG_DIR . "/" . $file . PHP_EOL;
    $result = load_textdomain( $locale, WP_LANG_DIR . "/" . $file ); 
    print_r( $result );
  }
	
	$plugins = array( "oik-weightcountry-shipping", "oik-weight-zone-shipping", "oik-weight-zone-shipping-pro", "oik-weightcountry-shipping-pro" 
									, "oik-types", "oik-fields", "oik" 
									);
	//$plugins = array( "oik" );
	
	//$locale = "oik"; 									
	foreach ( $plugins as $key => $plugin ) {
		echo "Loading $plugin into $locale" . PHP_EOL;
		//$result = load_plugin_textdomain( $locale, false, "$plugin/languages" );
		$path = oik_path( "languages/$plugin-$locale.mo", $plugin );
		$result = load_textdomain( $locale, $path );
    print_r( $result );
		if ( !$result ) {
			//gob();
		}
	}
	
	$actual_locale = get_locale();
	echo "Actual locale: $actual_locale" . PHP_EOL;
	return( $actual_locale );
}

/**
 * Prepare the plugin-locale.po file
 * 
 * 
 */
function la_CY_prepare_po( $plugin, $locale ) {
  $outfile = "$plugin-$locale.po";
  if ( is_file($outfile) ) {
    unlink( $outfile ); 
  }
  echo "Creating: $outfile " . PHP_EOL;
	return( $outfile );
}

/**
 * Return the locales for this plugin
 * 
 * @TODO Make it work with the locale files already present in the plugin's directory
 * In the mean time we just work with whatever's in the current directory.
 *
 * Note: oik-weightcountry-shipping and oik-weightcountry-shipping-pro have been translated into French.
 * oik-weight-zone-shipping and oik-weight-zone-shipping-pro will be similar.
 *
 * The French glossary at https://translate.wordpress.org/projects/wp/dev/fr/default/glossary
 * has been downloaded to wp-dev-fr-glossary.csv
 * 
 *
 *
 * No plugins have yet been translated into British English ( en_GB ) but that shouldn't be hard! 
 * There's a .csv file that contains mapping for words from US English to other English speaking countries
 *
 * @param string $plugin - plugin name future use 
 * @return array locales 
 */
function la_CY_get_locales( $plugin ) {

  $source_dir = getcwd();
	echo $source_dir;
	echo PHP_EOL;
	$locales = array();
	//$locales[] = "fr_FR";
	
	// This wastes a bit of time! 
	
	$locales[] = "en_GB";
	
	
	//$locales[] = "en_AU";
	//$locales[] = "en_CA";

	return( $locales ); 
}

/**
 * Attempt to translate the string
 *
 * @TODO Determine if a null string is acceptable in the .po file.  How does that get translated?
 *
 * @param string $text the string to be translated
 * @param string $plugin the plugin domain it's supposed to be in
 * @param string $locale the target locale
 * @param string $msgctxt context
 * @param string $translators_note
 * @return string a possibly translated string
 *
 */
function la_CY_translate_string( $text, $plugin, $locale, $msgctxt, $translators_note ) {
	echo "In : " . $text . PHP_EOL;
	$text = str_replace( '\"', '"', $text ); 
	
  //$text = trim( $text );
	//$text = substr( $text, 1, -1 );
	$la_CY_text = __( $text, $plugin );
	// Post plugin translate
	echo "PPT: " . $la_CY_text . PHP_EOL;
	if ( $la_CY_text == $text ) {
		$la_CY_text = __( $text, $locale );
		echo "$locale: " . $la_CY_text . PHP_EOL;
		
		$la_CY_oik_text = __( $text, "oik" );
		echo "oik: " . $la_CY_oik_text . PHP_EOL;
	}
	$la_CY_text = la_CY_check_utf8( $la_CY_text, $text ); 
	
	$la_CY_text = la_CY_variants( $text, $plugin, $locale, $msgctxt );
	echo "$locale:$msgctxt: $la_CY_text" . PHP_EOL;
	
	
	if ( $la_CY_text !== $text || $translators_note ) {
		$la_CY_text = la_CY_request_translation( $text, $plugin, $locale, $msgctxt, $translators_note );
	}
	$la_CY_text = str_replace( '"', '\"', $la_CY_text );
	echo "Out: " . $la_CY_text . PHP_EOL ;
	$la_CY_text = '"' . $la_CY_text . "\"\n";

	return( $la_CY_text );
}

/**
 * Check the UTF-8 ness of the content
 * 
 * This was an attempt to correct the bad encoding of the content
 * It's now got a lot worse. 
 * So we'll have to try again I suppose
 */
function la_CY_check_utf8( $utf8, $text ) {
	$decoded = utf8_decode( $utf8 );
	$cp850 = iconv( "UTF-8", "850", $utf8 );
	if ( strlen( $decoded ) == strlen( $utf8 ) ) {
		$encoded = $utf8;
	} else {
		$encoded = utf8_encode( $decoded );
		if ( $utf8 != $encoded ) {
			$encoded = $text;
		} 
	}
	echo $utf8 . PHP_EOL;
	echo $decoded . PHP_EOL;
	echo $encoded . PHP_EOL;
	echo $cp850 . PHP_EOL;
	echo $text . PHP_EOL;
	return( $encoded );
}

/**
 * Request translation of a 'new' string
 * 
 * @param string $text the string to be translated
 * @param string $plugin the plugin's text domain
 * @param string $locale the target locale ( la_CY )
 * @param string|null $msgctxt context
 * @param string|null $translators_note
 * @return string the translated string
 *
 */
function la_CY_request_translation( $text, $plugin, $locale, $msgctxt, $translators_note ) {
	if ( $text != "" ) {
		if ( !(strpos( $text, "http" ) === 0 ) ) {
			echo $translators_note;
			echo "Translation required: $locale: $msgctxt: $text" . PHP_EOL;
			$response = docontinue( "Type translation, or =,  or just press Enter." );
			switch ( $response ) {
				case '=':
					break;
					
				default:
					if ( $response ) {
						$text = la_CY_utf8( $response );
					} elseif ( $locale == 'en_GB' ) {
						// Accept the original as UK English	
					}	else {
						 
						echo "No translation is OK by me" . PHP_EOL;
						$text = null;
					}
			}		
		}
	}
	return( $text );
}

function la_CY_utf8( $response ) {
	echo "Response: $response" . PHP_EOL;
	if ( false ) {
		$text = utf8_decode( $response );
		if ( strlen( $text) == strlen( $response ) ) {
			$text = $response;
		}
		$text = utf8_encode( $text );
	} else {
		//$text = iconv( "Windows-1252", "UTF-8", $response );
		
		$text = $response;
		
		$text = iconv( "850", "UTF-8", $text );
	}
		
	echo "I'll use: $text" . PHP_EOL;
	return( $text );
}
	 


/** 
 * Re-translate into la_CY locale
 *
 * Note: in bb_BB.php we can 'translate' every source line immediately
 * Here we need to concatenate the lines in repl or repl_plural before translation
 * since this is how translators do it using poedit or similar. 
 * 
 */
function la_CY_translate( $plugin, $locale, $content, $outfile ) {
  //echo $content;
  $count = 0;
  $repl = null;
  $plural = false;
  $first = true;
	$msgctxt = null;
	$translators_note = null;
  $last_blank = 0;
	$count_lines = count( $content );
	while ( $count < $count_lines ) { 
  //foreach ( $content as $line ) {
		$line = $content[ $count ];
    $count++;
    $line = trim( $line );
    //echo $count . $line;
		
    
    if ( "msgid_plural" == substr( $line. "              ", 0, 12 ) ) {
      //echo __LINE__ . " " ;
      la_CY_outfile( $outfile, "$line\n" );   
      $repl_plural = substr( $line, 14, -1 );
      $plural = true;
      
    } elseif ( "msgid " == substr( $line. "      ", 0, 6 ) ) {
      //echo __LINE__ . " " ;
      //echo "$line\n";
      la_CY_outfile( $outfile, "$line\n" );   
      $repl = str_replace( "msgid ", "", $line );
			$repl = substr( $line, 7, -1 );
			// Move this to later
      $plural = false;
      
    } elseif ( "msgstr[0]" == substr( $line. "      ", 0, 9 ) ) { 
      //$line = str_replace( '""', $repl, $line );
      $plural = false;
      
    } elseif ( "msgstr[1]" == substr( $line. "      ", 0, 9 ) ) { 
      //$line = str_replace( '""', $repl_plural, $line );
      $plural = true;
      
    } elseif ( "msgstr " == substr( $line. "      ", 0, 7 ) ) {
      $repl = la_CY_translate_string( $repl, $plugin, $locale, $msgctxt, $translators_note );
      $line = str_replace( '""', $repl, $line );
      //echo __LINE__ . " ";
      if ($first ) {
        // echo "$line";
        
        la_CY_outfile( $outfile, "$line" );   
      } else { 
        //echo "$line\n";  
        la_CY_outfile( $outfile, "$line\n" );   
      }  
      $plural = false;
      
    } elseif ( '"' == substr( $line." ", 0, 1 ) ) {
      //echo "$line\n";
      
      la_CY_outfile( $outfile, "$line\n" );   
      if ( $first  && substr( $line, 0, 18 ) == "\"MIME-Version: 1.0" ) {
        //echo "\"Plural-Forms: nplurals=2; plural=n == 1 ? 0 : 1;\\n\"\n";
        
        la_CY_outfile( $outfile, "\"Plural-Forms: nplurals=2; plural=n == 1 ? 0 : 1;\\n\"\n" );   
        la_CY_outfile( $outfile, "\"Language: $locale\\n\"\n" );
				// @TODO add Last Translator and Language team
				la_CY_last_translator( $outfile ); 
      } else {  
        if ( $plural ) {
          $repl_plural .= substr( $line, 1, -1 );
        } else {
          $repl .= substr( $line, 1, -1 );
        }
      }   
      
    } elseif ( $line == "" || ( "#." == substr( $line, 0, 2 ) &&  $last_blank++ == $count) ) {
		
      if ( !$first ) {
        if ( $plural ) {
          //echo __LINE__ . " " ;
					$repl = la_CY_translate_string( $repl, $plugin, $locale, $msgctxt, $translators_note );
					$repl_plural = la_CY_translate_string( $repl_plural, $plugin, $locale, $msgctxt, $translators_note );
          la_CY_outfile( $outfile, "msgstr[0] $repl" );
          la_CY_outfile( $outfile, "msgstr[1] $repl_plural" );
          //echo $repl;
          
          if ( $line !== "" ) {
            la_CY_outfile( $outfile, "\n" );
          } else {
            $last_blank = $count; 
          }   
          la_CY_outfile( $outfile, "$line\n" );
        } else {
          //echo $repl;
        } 
      }
      $plural = false;
      $first = false;  
			$msgctxt = null; 
			// Set translator's note - which  
			// #. translators: 1: month name, 2: 4-digit year
			$translators_note = null;
			if ( "#." == substr( $line, 0, 2 ) ) {
				$translators_note = $line;
			} 
    } elseif ( "msgctxt " == substr( $line, 0, 8 ) ) {
			$msgctxt = str_replace( "msgctxt ", "", $line );
			la_CY_outfile( $outfile, "$line\n" );
    } else {  
    //echo "$count($line)";
      la_CY_outfile( $outfile, "$line\n" ); 
    } 
    
  }

  //echo "end";
} 

 
/**
 * Write some output to the $file
 *
 * @param string $file output file name ( written in current directory )
 * @param string $line the line to write
 */
function la_CY_outfile( $file, $line ) {
  $handle = fopen( $file, "a" );
  // echo "<!-- $handle $file $line-->";
  if ( $handle === FALSE ) {
     //bw_trace_off();
     // It would be nice to let them know... 
     $ret = "fopen failed"; 
  }
  else {
    $bytes = fwrite( $handle, $line );
    $ret = fclose( $handle );
    $ret .= " $bytes $file $line";
  }
  return( $ret );
}


/**
 * Load the English variants
 * 
 */
function la_CY_load_variants( $locale ) {
	static $variants = null;
	if ( substr( $locale, 0, 2 ) == "en" ) {
	
		oik_require( "class-English-variants.php", "oik-i18n" );
		//oik_require_class( "English_variants" );
		$variants = new English_variants( $locale );
	}
	return $variants;
}

/**
 * Returns the target locale's variant
 * 
 * 
 */
function la_CY_variants( $text, $plugin, $locale, $msgctxt ) {
	$la_CY_text = $text;
	$variants = la_CY_load_variants( $locale );
	if ( $variants ) {
		$original_OK = $variants->check_original_language( $text, $msgctxt );
		if ( $original_OK ) { 
			$la_CY_text = $variants->map( $text, $msgctxt );
		}
	}
	return $la_CY_text;
}	




/**
 *  
 */
function la_CY_loaded() {
  $included_files = get_included_files();
  if ( $included_files[0] == __FILE__ ) {
    la_CY( "oik-i18n" );
  } else {
    echo "I'm not main - this is just a test" . PHP_EOL;
		echo PHP_EOL;
    //do_main( $_SERVER['argc'], $_SERVER['argv'] );
		//la_CY( "oik-i18n" );
  }   
} 

la_CY_loaded(); 
 
 
