<?php // (C) Copyright Bobbing Wide 2014
//namespace oiki18n\fr_FR;

/**

Source:

#: wp-admin/about.php:32 wp-admin/credits.php:65 wp-admin/freedoms.php:12
#: wp-admin/freedoms.php:32
msgid "Freedoms"
msgstr ""

Expected output:

#: wp-admin/about.php:32 wp-admin/credits.php:65 wp-admin/freedoms.php:12
#: wp-admin/freedoms.php:32
msgid "Freedoms"
msgstr "Foedmers"

Source:

#: wp-admin/about.php:37 wp-admin/about.php:163
msgid "Maintenance and Security Release"
msgid_plural "Maintenance and Security Releases"
msgstr[0] ""
msgstr[1] ""

Expected: output

#: wp-admin/about.php:37 wp-admin/about.php:163
msgid "Maintenance and Security Release"
msgid_plural "Maintenance and Security Releases"
msgstr[0] "Mnntaeicnae And Scriuety Realese"
msgstr[1] "Mnntancaeie And Sctuiery Reeeasls"


Source: 

#: wp-admin/about.php:50
msgid ""
"The new default theme puts focus on your content with a colorful, single-"
"column design made for media-rich blogging."
msgstr ""

Expected output:

#: wp-admin/about.php:50
msgid ""
"The new default theme puts focus on your content with a colorful, single-"
"column design made for media-rich blogging."
msgstr ""
"The new default theme puts focus on your content with a colorful, single-"  - but fr_FRed
"column design made for media-rich blogging."                                - but fr_FRed 



Input        Saved as                     Outputted when
-------      ---------                    --------------
msgid        msgstr                       immediately
msgid_plural reply                        immediately
msgstr       n/a 
""           appended to repl/repl_plural immediately and then next blank line
msgstr[0]    repl                         next blank line 
msgstr[1]    repl_plural                  next blank line

*/ 


 
/**
 * Write some output to the $file
 */
function fr_FR_outfile( $file, $line ) {
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
 * Attempt to create a French language .po file for a given .pot file
 * 
 *
 * Purpose: Localize a plugin's .pot file into French ( language fr ) for France ( country code FR )
 *  
 * Package: fr_FR  
 * Syntax: php fr_FR.php plugin.pot > plugin-fr_FR.po 
 * 
 * then use:
 * msgfmt ... see l10n.php for examples with "bb_BB"  
 * 
 *
 * Whereas bb_BB.php generates a plugin-bb_BB.po file from plugin.pot 
 * this is intended to create a French version ( locale fr_FR )
 * by looking up the strings from other plugin's .po files
 * 
 * I imagine glotpress does something similar.
 */
function fr_FR( $plugin ) {
  $real_file = "$plugin.pot" ;
  $outfile = "$plugin-fr_FR.po";
  
  if ( is_file($outfile) ) {
    unlink( $outfile ); 
  }
  echo __FUNCTION__ . ": Processing $real_file" . PHP_EOL;
  echo "Creating: $outfile " . PHP_EOL;
  $content = file( $real_file );
  //echo $content;
  $count = 0;
  $repl = null;
  $plural = false;
  $first = true;
  $last_blank = 0;
  foreach ( $content as $line ) {
    $count++;
    $line = trim( $line );
    //echo $count . $line;
    
    if ( "msgid_plural" == substr( $line. "              ", 0, 12 ) ) {
      //echo __LINE__ . " " ;
      fr_FR_outfile( $outfile, "$line\n" );   
      $repl_plural = str_replace( "msgid_plural ", "", $line );
      $repl_plural = substr( $line, 13 );
      $repl_plural = do_fr_FR( $repl_plural, true );
      //echo "^$repl_plural^";
      $plural = true;
      
    } elseif ( "msgid " == substr( $line. "      ", 0, 6 ) ) {
      //echo __LINE__ . " " ;
      //echo "$line\n";
      fr_FR_outfile( $outfile, "$line\n" );   
      $repl = str_replace( "msgid ", "", $line );
      $repl = do_fr_FR( $repl );
      $plural = false;
      
    } elseif ( "msgstr[0]" == substr( $line. "      ", 0, 9 ) ) { 
      $line = str_replace( '""', $repl, $line );
      $plural = false;
      
    } elseif ( "msgstr[1]" == substr( $line. "      ", 0, 9 ) ) { 
      $line = str_replace( '""', $repl_plural, $line );
      $plural = true;
      
    } elseif ( "msgstr " == substr( $line. "      ", 0, 7 ) ) {
      $line = str_replace( '""', $repl, $line );
      //echo __LINE__ . " ";
      if ($first ) {
        // echo "$line";
        
        fr_FR_outfile( $outfile, "$line" );   
      } else { 
        //echo "$line\n";  
        fr_FR_outfile( $outfile, "$line\n" );   
      }  
      $plural = false;
      
    } elseif ( '"' == substr( $line." ", 0, 1 ) ) {
      //echo "$line\n";
      
      fr_FR_outfile( $outfile, "$line\n" );   
      if ( $first  && substr( $line, 0, 18 ) == "\"MIME-Version: 1.0" ) {
        //echo "\"Plural-Forms: nplurals=2; plural=n == 1 ? 0 : 1;\\n\"\n";
        
        fr_FR_outfile( $outfile, "\"Plural-Forms: nplurals=2; plural=n == 1 ? 0 : 1;\\n\"\n" );   
        fr_FR_outfile( $outfile, "\"Language: fr_FR\\n\"\n" );
      } else {  
        $line = do_fr_FR( $line );
        if ( $plural ) {
          $repl_plural .= $line;
        } else {
          $repl .= $line;
        }
      }   
      
    } elseif ( $line == "" || ( "#." == substr( $line, 0, 2 ) &&  $last_blank++ == $count) ) {
      if ( !$first ) {
        if ( $plural ) {
          //echo __LINE__ . " " ;
          fr_FR_outfile( $outfile, "msgstr[0] $repl" );
          fr_FR_outfile( $outfile, "msgstr[1] $repl_plural" );
          //echo $repl;
          
          if ( $line !== "" ) {
            fr_FR_outfile( $outfile, "\n" );
          } else {
            $last_blank = $count; 
          }   
          fr_FR_outfile( $outfile, "$line\n" );
        } else {
          //echo $repl;
        } 
      }
      $plural = false;
      $first = false;   
      
    } else {  
    //echo "$count($line)";
      fr_FR_outfile( $outfile, "$line\n" ); 
    } 
    
  }

  //echo "end";
} 

/**
 * The string that we pass to translate must not have \" but should have \n
 * so stripslashes() won't do.
 */
function fr_FR_prepare_text( $text ) { 
  $text = trim( $text );
  $text = substr( $text, 1, -1 );
  //$text = stripslashes( $text );
  $text = str_replace( '\"', '"', $text );
  return( $text );
}  
  

/**
 * Translate the International language text to the native "fr_FR" language
 * 
 * Note: The incoming string is expected to be wrapped in a double quote at either end.
 * It's not safe to trim quotes as this could remove double quotes at the end leaving a single '\' rather than a '\"'
 * We have to remove these using substr( 
 * And then put them back on again later
 * 
 */
function do_fr_FR( $text, $plural=false ) { 
  static $single;
  $text = fr_FR_prepare_text( $text ); 
  if ( $plural ) {
    $text = fr_FR_translate_plural( $text, $single ); 
  } else {
    $single = $text;
    $text = fr_FR_translate( $text );
  } 
  $text = '"' . $text . '"'; 
  //echo "%$text%";
  //$text = '"' . $text . "\"\n";
  $text = $text . "\n";
  return( $text );
}

/** 
 * Translate some text to "fr_FR" 
 *
 * Attempt to find a French translation of the text from pre-existing .po files
 * If not found then try to do it word by word or farm it out to the polyglot solution.
 *
 * The current solution doesn't appear to work for strings with plurals!
 * 
 
 * 
 * @param string - the text to be fr_FR'ed
 * @param string - the fr_FR'ed text
 */
function fr_FR_translate( $text ) {
  $fr_text = translate( $text );
  $result = fr_FR_evaluate( $text, $fr_text );
  fr_FR_count( $text, $fr_text, $result );
  return( $fr_text );
}


/** 
 * Translate some plural text to "fr_FR" 
 *
 * Attempt to find a French translation of the plural text from pre-existing .po files
 * If not found then try to do it word by word or farm it out to the polyglot solution.
 
  0 %1$s thoughts on &ldquo;%2$s&rdquo; %1$s thoughts on &ldquo;%2$s&rdquo;
 

#: comments.php:27
msgid "One thought on &ldquo;%2$s&rdquo;"
msgid_plural "%1$s thoughts on &ldquo;%2$s&rdquo;"
msgstr[0] "Un r√©flexion sur &ldquo;&nbsp;%2$s&nbsp;&rdquo;"
msgstr[1] "Quelques r√©flexions sur &ldquo;&nbsp;%2$s&nbsp;&rdquo;"

 * 
 * @param string - the text to be fr_FR'ed
 * @param string - the fr_FR'ed text
 */
function fr_FR_translate_plural( $text, $single ) {
  $fr_text = _n( $single , $text, 2 );
  $result = fr_FR_evaluate( $text, $fr_text );
  fr_FR_count( $text, $fr_text, $result );
  return( $fr_text );
}

/**
 * Evaluate the results
 * 
 * If the results match then some sort of failure occurred
 * Set the value to -1 if it's acceptable
 * - when the string is less than 3 characters
 * - when it's a particular key field... @TODO 
 *
 */  
function fr_FR_evaluate( $text, $fr_text ) {
  if ( $text === $fr_text ) {
    $result = 0;
    if ( strlen( $text ) < 3 ) {
      $result = -1;
    } elseif ( false === strpos( $text, " ") ) {
      $result = -1;
    } elseif ( substr( $text, -2, 2 ) == '\n' ) {
      $result = -1;
    }
    echo "$result $text $fr_text" . PHP_EOL;  
    pause(); 
  } else {
    $result = 1;
  }  
  return( $result );
}

/**
 * Count the results
 */  
function fr_FR_count( $text, $fr_text, $result ) {

  global $fr_FR_total, $fr_fr_translated, $fr_fr_todo, $fr_fr_allowed;
  if ( !$fr_FR_total ) {
    $fr_FR_total = 0;
    $fr_fr_translated = 0;
    $fr_fr_todo = 0;
    $fr_fr_allowed = 0;
  }
  $fr_FR_total++;
  switch ( $result ) {
    case 0: 
      $fr_fr_todo++;
      break;
    case -1:
      $fr_fr_allowed++;
      break;
    default:  
      $fr_fr_translated++;
  } 
  return( $fr_FR_total );
}

function fr_FR_report() {

  global $fr_FR_total, $fr_fr_translated, $fr_fr_todo, $fr_fr_allowed;

  echo "Translations attempted $fr_FR_total" . PHP_EOL;
  echo "Success: $fr_fr_translated" . PHP_EOL;
  echo "TODO: $fr_fr_todo" . PHP_EOL;
  echo "Allowed: $fr_fr_allowed" . PHP_EOL;

}   
 
/**
 * Load the French .mo files into one big translate table
 * 
 * admin-fr_FR.mo
 * admin-network-fr_FR.mo
 * continents-cities-fr_FR.mo
 * fr_FR.mo
 
 
	load_textdomain( 'default', WP_LANG_DIR . "/$locale.mo" );
 */
function fr_FR_load() {
  oik_require( "bobbcomp.inc" );
  wp_set_lang_dir();
  $files = "admin-fr_FR.mo,admin-network-fr_FR.mo,continents-cities-fr_FR.mo,fr_FR.mo";
  $files .= ",themes/twentyeleven-fr_FR.mo,themes/twentyten-fr_FR.mo,themes/twentythirteen-fr_FR.mo,themes/twentytwelve-fr_FR.mo,themes/twentyfourteen-fr_FR.mo";
  
  // $files = "themes/twentyfourteen-fr_FR.mo";
  $files = bw_as_array( $files );
  foreach ( $files as $file ) {
    echo "Loading $file " . PHP_EOL;
    load_textdomain( 'default', WP_LANG_DIR . "/" . $file ); 
  }
}

  
/**
 * The next comment is associated with the original bboing() function
 
 * Note: In its first version this routine would transform "http://www.bobbingwide.com" to something pretty nasty
 * similarly it could ruin any HTML tags or anything with %1$s
 * To overcome this we're going to improve the bboing() function... replacing it with the new function bboing2()
 */ 
function bbboing( $text ) {  
  
  $bbtext = '';
  $words = explode( ' ', $text );
  foreach ( $words as $word ) {
    $bbtext.= bboing2($word );   
    $bbtext.= " ";
  }
  $bbtext = trim( $bbtext );
  return( $bbtext );
}



/**
 * bbboing a particular (potentially complex) word
 *
 * If the word itself did not start with a "<" then the original logic attempted to ignore any final punctuation character
 * So "word." would become "wrod."
 * 
 * But we need to be smarter than that. 
 * 
 * e.g. 
 * http://www.bobbingwide.com could become
 * http://www.bbbidiwonge.com      
 * 
 *
 * @param string $word - a particular word
 * @return string - the bbboing'ed word
 *
 */
function bboing( $word ) {
  if ( ctype_alpha( $word ) ) {
    // echo "c";
    $word = boing( $word ); 
  } else {
    $count = strlen( $word );
    $l = substr( $word, 0, 1 );
    if ( $l <> "<" ) {
      $r = substr( $word, $count-1 ); 
      $pos = strpos( ".?:-", $r );
      if ($pos === FALSE ) {
         $word = boing( $word ); 
      } else { 
        $word = boing( substr( $word, 0, $count-1 )) . $r;
      }   
    }
  } 
  return( $word );
}

/**
 * Shuffle the embedded letters in a word of 4 or more characters
 * 
 * The first and last letters remain the same, all the others are randomised.
 * e.g.
 * bobbing could become bbboing or bibbong or bnibbog
 *
 * @param string $word
 * @return string $wrod
 */ 
function boing( $word ) {
  // echo $word . "\n";
  $count = strlen( $word );
  switch ( $count )
  {
    case 0:
    case 1:
    case 2:
    case 3:
      // Can't really do anything with this length word
      // Convert all vowels to uppercase
      // Convert a to decimal 132 hex 84 - which is  Ñ  - a umlaut 
      // Convert e to decimal 130 hex 82 - which is  Ç  - e acute
      // Convert i to decimal 140 hex 8C - which is  å  - i caret
      // Convert o to decimal 149 hex 95 - which is  ï  - o grave
      // Convert u to decimal 129 hex 81 - which is  Å  - u umlaut
      $wrod = str_replace( array( "a", "e", "i", "o", "u" ), array( "A", "E", "I", "O", "U" ), $word );
      // $wrod = str_replace( array( "a", "e", "i", "o", "u" ), array( "Ñ", "Ç", "å", "ï", "Å" ), $word );
      
    break;

    default:
      $l = substr( $word, 0, 1 );
      $r = substr( $word, $count-1 ); 
      $mid = substr( $word, 1, $count-2 ); 
    
      $dim = str_shuffle( $mid );
      if ( $dim == $mid ) {
        $dim = str_shuffle( $mid );
      }  
      $wrod = $l . $dim . $r;
    break;
  }    
  return( $wrod );
}

/**
 * Perform boing() against a part of the word replacing the characters in the chars array
 */
function boingsubword( &$chars, $subword, $first ) {
  $wrod = boing( $subword );
  $crahs = str_split( $wrod );
  foreach ( $crahs as $i => $char ) {
    $chars[$i+$first] = $char;
  }  
}

/**
 * Return true if we think we should process the word, false otherwise
 * 
 * Note: We expect the words to be split on blank characters
 * 
 * Strings we should not auto translate contain:
 * :// -> URLs  http://,  https://, ftp://, ftps://, mailto://
 * @ -> email addresses
 * % -> symbolic substitution variables e.g. %1$s 
 * = -> keyword="value" ... but this could have problems if within HTML tags...
 * & -> symbolic html such as &lasquo;
 * so we cater for < and > separately
 * 
 */
function bboing_process_word( $word ) {
  $process = true;
  $ignores = array( "://", "@", "%", "=", "&", "[", "]" );
  foreach ( $ignores as $ignore ) {
    if ( strpos( $word, $ignore ) !== false ) {
      $process = false;
      break;
    }  
  }
  $reportable = array( "<", ">" );
  foreach ( $reportable as $report ) {
    if ( strpos( $word, $report ) !== false ) {
      $process = false;
      // echo "#. Possible embedded HTML in string. Consider changing code\n" ;
      break;
    }  
  }
  return( $process );
}  

/** 
 * Only bbboing characters inside a complex string
 *
 * bboing only the character parts of a continuous string of characters in a word
 * with some sanity checking on what we're messing with                   
 * 
 * Punctuation characters used to delimit the bbboinging are:
 * -.:;?' - 
 * well anything else really except characters
 * we don't even accept digits
 * 
 * @param string $word
 * @return string $wrod
 */
function bboing2( $word ) {
  if ( bboing_process_word( $word ) ) {
    $chars = str_split( $word );
    $count = 0;
    $first = null;
    $subword = null;
    foreach ( $chars as $index => $char ) {
      if ( ctype_alpha( $char ) ) {
        $count++;
        if ( $first === null ) {
          $first = $index;
        }
        $subword .= $char;
      } else {
        if ( $count > 0 ) {
          boingsubword( $chars, $subword, $first );
        }         
        $count=0;
        $first=null; 
        $subword = null;
      }  
    } 
    if ( $count > 0 ) {
      boingsubword( $chars, $subword, $first );
    }
    $wrod = implode( $chars );
  } else {
    $wrod = $word; 
  }  
  return( $wrod );
}

//fr_FR( "fr_FR" );

function pause() {

  //oik_require( "oik-login.inc", "oik-batch" );
  //  oikb_get_response( null, true );
}    



/**
 * Load the French .mo files for all the plugins into one big translate table
 *
 * wp-content/plugins/jetpack/languages/ 
 
 
	load_textdomain( 'default', WP_LANG_DIR . "/$locale.mo" );
 */
function fr_FR_load_plugins() {
  oik_require( "bobbcomp.inc" );
  wp_set_lang_dir();
  $files = "jetpack/languages/jetpack-fr_FR.mo";
  
  foreach ( $files as $file ) {
    echo "Loading $file " . PHP_EOL;
    load_textdomain( 'default', WP_LANG_DIR . "/" . $file ); 
  }
}

/**
 * Routine to test the translation look up
 
 
From twentyfourteen.pot


#: content-aside.php:46 content-audio.php:46 content-gallery.php:46
#: content-image.php:46 content-link.php:46 content-quote.php:46
#: content-video.php:46 content.php:54 inc/widgets.php:113 inc/widgets.php:158
msgid "Continue reading <span class=\"meta-nav\">&rarr;</span>"
msgstr "Continue la lecture <span class=\"meta-nav\">&rarr;</span>"

From twentyfourteen-fr_FR.po

#: content-aside.php:46 content-audio.php:46 content-gallery.php:46
#: content-image.php:46 content-link.php:46 content-quote.php:46
#: content-video.php:46 content.php:54 inc/widgets.php:113 inc/widgets.php:158
msgid "Continue reading <span class=\"meta-nav\">&rarr;</span>"
msgstr "Continue la lecture <span class=\"meta-nav\">&rarr;</span>"

*/
function fr_FR_unit_test() {
  $text = do_fr_fr( '"' ."Powered by WordPress" . '"' );
  echo $text . PHP_EOL;
  fr_fr_report();
  
  $text = do_fr_fr( '"'. "Continue reading <span class=\"meta-nav\">&rarr;</span>" . '"' );
  
  echo $text . PHP_EOL;
  $text = do_fr_fr( '"Continue reading <span class=\"meta-nav\">&rarr;</span>"' );
  
  echo $text . PHP_EOL;
  fr_fr_report();
  
}

   
chdir( dirname( __FILE__ ) );
echo getcwd();
echo WP_PLUGIN_DIR;
fr_FR_load();
//fr_FR_load_plugins();
 
   
fr_FR( "oik" );
//fr_FR_unit_test();
//fr_FR( "twentyfourteen" );
fr_fr_report();





