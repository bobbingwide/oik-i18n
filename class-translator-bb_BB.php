<?php

/**
 * Class Translator_bb_BB
 * @package oik-i18n
 * @copyright (C) Copyright Bobbing Wide 2020
 */


class Translator_bb_BB extends Translator {

	function __construct() {
		parent::__construct();
	}

	function load_translations() {
		// No need to do this for bb_BB since every string can be automatically translated.
	}

	function translate_string( $string ) {
		$translated = $this->do_bbboing( $string );
		$this->narrator->narrate( 'bb_BB', $translated ) ;
		return $translated;
	}


	/**
	 * Translate the International language text to the native "bbboing" language
	 */
	function do_bbboing( $text ) {
		$text = trim( $text );
		// It's not safe to trim quotes as this could remove double quotes at the end leaving a single '\' rather than a '\"'
		//$text = trim( $text, '"' );
		//echo "!$text!";
		$text = $this->bbboing( $text );
		//echo "%$text%";
		//$text = '"' . $text . "\"\n";

		// @TODO - do we need this new line or not?
		//$text = $text . "\n";
		return $text;
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
			$bbtext.= $this->bboing2($word );
			$bbtext.= " ";
		}
		$bbtext = trim( $bbtext );
		return( $bbtext );
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
		if ( $this->bboing_process_word( $word ) ) {
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
						$this->boingsubword( $chars, $subword, $first );
					}
					$count=0;
					$first=null;
					$subword = null;
				}
			}
			if ( $count > 0 ) {
				$this->boingsubword( $chars, $subword, $first );
			}
			$wrod = implode( $chars );
		} else {
			$wrod = $word;
		}
		return( $wrod );
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
	 * Perform boing() against a part of the word replacing the characters in the chars array
	 */
	function boingsubword( &$chars, $subword, $first ) {
		$wrod = $this->boing( $subword );
		$crahs = str_split( $wrod );
		foreach ( $crahs as $i => $char ) {
			$chars[$i+$first] = $char;
		}
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

				$dim = $this->str_shiffle( $mid );
				if ( $dim == $mid ) {
					$dim = $this->str_shiffle( $mid );
				}
				$wrod = $l . $dim . $r;
				break;
		}
		return( $wrod );
	}

	/**
	 * shiffle rather than shuffle
	 *
	 * Swaps odd and even letters leaving any odd letter at the end
	 *
	 *
	 * Examples
	 * original word | shiffled word
	 * ------------- | -------------
	 * be            | eb
	 * are           | rae
	 * word          | owdr
	 * boing         | obnig
	 *
	 *
	 * @param string $mid	- string to be shiffled
	 * @return string $dim
	 */
	function str_shiffle( $mid ) {
		$dim = null;
		$chars = str_split( $mid );
		$len = count( $chars );
		//print_r( $chars );
		//echo $len;
		for ( $i = 0; $i < $len-1; $i += 2 ) {

			$dim .= $chars[ $i+1 ];
			$dim .= $chars[ $i ];
		}
		if ( $len & 1 ) {
			$dim .= $chars[ $len -1 ];
		}
		return $dim;
	}









}