<?php
//namespace oiki18n\bb_BB;


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
"The new default theme puts focus on your content with a colorful, single-"  - but bbboinged
"column design made for media-rich blogging."                                - but bbboinged 



Input        Saved as                     Outputted when
-------      ---------                    --------------
msgid        msgstr                       immediately
msgid_plural reply                        immediately
msgstr       n/a 
""           appended to repl/repl_plural immediately and then next blank line
msgstr[0]    repl                         next blank line 
msgstr[1]    repl_plural                  next blank line



/**
 * Determine the real file name given the plugin name
 * 
 * @TODO Check if this is needed.
 * 
 */
function bb_BB_real_file( $argc, $argv ) {
  if ( $argc > 1 ) {
    $file = $argv[1];
    switch ( $file ) {
      case "wordpress":
      case "admin":
        $real_file = "C:\\apache\\htdocs\\svn_tools\\trunk\\$file.pot";
        break;
      
      default: 
        $real_file = "C:\\apache\\htdocs\\svn_tools\\trunk\\$file.pot"; 
        break;
    }  
  } else {
    $real_file = "C:\\apache\\htdocs\\svn_tools\\trunk\\admin.pot";
    $real_file = "C:\\apache\\htdocs\\svn_tools\\trunk\\oik-i18n.pot";
    $real_file = "C:\\apache\\htdocs\\wordpress\\wp-content\\plugins\\play\\oik-i18n.pot" ;
  }
  return( $real_file );
} 
 
/**
 * Write some output to the $file
 */
function bb_BB_outfile( $file, $line ) {
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
 * Create a bbboing language .po file for a given .pot file
 * Purpose: Localize a plugin's .pot file into the "bbboing language" ( bb ), country code ( BB )
 * This can be used to test two things:
 * 1. Whether or not the code is fully Internationalized - missed strings will not be "bbboinged"
 * 2. Whether or not the code is properly Internationalized - detects "poorly" i18n'ed can be detected.
 *  
 * Package: bb_BB  
 * Syntax: php bb_BB.php plugin.pot > plugin-bb_BB.po 
 * 
 * then use:
 * msgfmt  
 * 
 */
function bb_BB( $plugin ) {
	switch ( $plugin ) {
		case "admin-":
		case "admin-network-":
		case "":
			$real_file = $plugin . "en_GB.po";
			$outfile = $plugin . "bb_BB.po";
			chdir( WP_LANG_DIR );
			echo WP_LANG_DIR . PHP_EOL;
			//gob();
			break;
			
		default:
      $real_file = "$plugin.pot" ;
			$outfile = "$plugin-bb_BB.po";
	}
  
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
      bb_BB_outfile( $outfile, "$line\n" );   
      $repl_plural = str_replace( "msgid_plural ", "", $line );
      $repl_plural = substr( $line, 13 );
      $repl_plural = do_bbboing( $repl_plural );
      //echo "^$repl_plural^";
      $plural = true;
      
    } elseif ( "msgid " == substr( $line. "      ", 0, 6 ) ) {
      //echo __LINE__ . " " ;
      //echo "$line\n";
      bb_BB_outfile( $outfile, "$line\n" );   
      $repl = str_replace( "msgid ", "", $line );
      $repl = do_bbboing( $repl );
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
        
        bb_BB_outfile( $outfile, "$line" );   
      } else { 
        //echo "$line\n";  
        bb_BB_outfile( $outfile, "$line\n" );   
      }  
      $plural = false;
      
    } elseif ( '"' == substr( $line." ", 0, 1 ) ) {
      //echo "$line\n";
      
      bb_BB_outfile( $outfile, "$line\n" );   
      if ( $first  && substr( $line, 0, 18 ) == "\"MIME-Version: 1.0" ) {
        //echo "\"Plural-Forms: nplurals=2; plural=n == 1 ? 0 : 1;\\n\"\n";
        
        bb_BB_outfile( $outfile, "\"Plural-Forms: nplurals=2; plural=n == 1 ? 0 : 1;\\n\"\n" );   
        bb_BB_outfile( $outfile, "\"Language: bb_BB\\n\"\n" );
      } else {  
        $line = do_bbboing( $line );
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
          bb_BB_outfile( $outfile, "msgstr[0] $repl" );
          bb_BB_outfile( $outfile, "msgstr[1] $repl_plural" );
          //echo $repl;
          
          if ( $line !== "" ) {
            bb_BB_outfile( $outfile, "\n" );
          } else {
            $last_blank = $count; 
          }   
          bb_BB_outfile( $outfile, "$line\n" );
        } else {
          //echo $repl;
        } 
      }
      $plural = false;
      $first = false;   
      
    } else {  
    //echo "$count($line)";
      bb_BB_outfile( $outfile, "$line\n" ); 
    } 
    
  }

  //echo "end";
}  
  

/**
 * Translate the International language text to the native "bbboing" language
 */
function do_bbboing( $text ) { 
  if ( !function_exists( "bbboing" ) ) {
  
    require_once( "../oik/oik_boot.inc" );
    oik_require( "bbboing.inc", "bbboing" );
  }  
  $text = trim( $text );
  // It's not safe to trim quotes as this could remove double quotes at the end leaving a single '\' rather than a '\"'
  //$text = trim( $text, '"' );
  //echo "!$text!";
  $text = bbboing( $text );
  //echo "%$text%";
  //$text = '"' . $text . "\"\n";
  $text = $text . "\n";
  return( $text );
}

/** 
 * bbboing some text
 * 
 * Note: In its first version this routine would transform "http://www.bobbingwide.com" to something pretty nasty
 * similarly it could ruin any HTML tags or anything with %1$s
 * To overcome this we're going to improve the bboing() function... replacing it with the new function bboing2()
 * 
 * @param string - the text to be bbboing'ed
 * @param string - the bbboing'ed text
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
      // Convert a to decimal 132 hex 84 - which is  �  - a umlaut 
      // Convert e to decimal 130 hex 82 - which is  �  - e acute
      // Convert i to decimal 140 hex 8C - which is  �  - i caret
      // Convert o to decimal 149 hex 95 - which is  �  - o grave
      // Convert u to decimal 129 hex 81 - which is  �  - u umlaut
      $wrod = str_replace( array( "a", "e", "i", "o", "u" ), array( "A", "E", "I", "O", "U" ), $word );
      // $wrod = str_replace( array( "a", "e", "i", "o", "u" ), array( "�", "�", "�", "�", "�" ), $word );
      
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


