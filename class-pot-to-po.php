<?php

/**
 * Class Pot_To_Po
 * @package oik-i18n
 * @copyright (C) Copyright Bobbing Wide 2020
 */


/**
 * Class Pot_To_Po
 * Reimplements functionality in la_CY.php
 * as object oriented code
 * supporting Google Cloud Translate API to perform the translation of strings
 * from "msgid" to "msgstr"
 */

class Pot_To_Po {

	private $narrator = null;

	private $component = null;
	private $component_type = null;
	private $component_path = null;

	private $pot = null; // The content of the .pot file

	private $repl;
	private $repl_plural;

	private $first;
	private $msgctxt;
	private $translators_note;

	private $po_filename = null; // The content of the locale .po file

	function __construct() {
		$this->narrator = Narrator::instance();
	}

	function setComponent( $component ) {
		$this->component = $component;
	}

	/**
	 * Locates the languages folder for the component.
	 *
	 * Assume it's a plugin first of all.
	 * Then try for theme.
	 *
	 * @param $component
	 * @return array [ $component_type, $component_path ]
	 */
	function locate_component( $component ) {
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

	function getPotFilename() {
		$pot_filename = null;
		list( $this->component_type, $this->component_path) = $this->locate_component( $this->component );
		if ( $this->component_type && $this->component_path ) {
			$pot_filename = $this->component_path;
			$pot_filename .= '/';
			$pot_filename .= $this->component;
			$pot_filename .= '.pot';
		} else {
			$this->narrator->narrate( "Invalid component?", $this->component );
			gob();
		}
		return $pot_filename;
	}

	function setLocale( $locale ) {
		$this->locale = $locale;
	}

	function getPoFilename() {
		$po_filename = $this->component_path;
		$po_filename .= '/';
		$po_filename .= $this->component;
		$po_filename .= '-';
		$po_filename .= $this->locale;
		$po_filename .= '.po';
		$this->narrator->narrate( '.po', $po_filename );

		return $po_filename;
	}

	function preparePo() {
		$this->po_filename = $this->getPoFilename();
		if ( is_file( $this->po_filename ) ) {
			unlink( $this->po_filename );
		}
	}

	/**
	 * Loads the en_US .pot file into $this->pot.
	 *
	 * Replaces la_CY_load_pot
	 */
	function load_pot() {
		$pot_filename = $this->getPotFilename();
		$this->pot = file( $pot_filename );
		$size = filesize( $pot_filename );
		$this->narrator->narrate( 'Procesing', $pot_filename );
		$this->narrator->narrate( 'Size (bytes)', $size );
		$this->narrator->narrate( 'Lines', count( $this->pot ) );

	}

	/**
	 * Counts the total translatable bytes.
	 *
	 */
	function count_translatable_bytes() {
		$count = 0;
		$total_translatable = 0;
		$count_lines = count( $this->pot );
		while ( $count < $count_lines ) {
			$line = $this->pot[ $count ];
			$count++;
			$line = trim( $line );
			$type = $this->determine_line_type( $line );
			$translatable_text = $this->get_translatable_text( $type, $line );
			$total_translatable += strlen( $translatable_text );

		}
		$this->narrator->narrate( 'Translatable (bytes)', $total_translatable );
	}

	/**
	 * Determines the line type.
	 *
	 * Looks for a particular value to use as the line type.
	 *
	 * We could use constants but this method should also work.
	 *
	 * @param $line
	 * @return string|null
	 */
	function determine_line_type( $line ) {
		// Ensure we can match with "msgid_plural"
		$line .= "            ";
		if ( "msgid_plural" === substr( $line, 0, 12 ) ) {
			$type = "msgid_plural";
		} elseif ( "msgid " ===  substr( $line, 0,6 ) ) {
			$type = "msgid ";
		} elseif ( "msgstr[0]" === substr( $line, 0, 9 ) ) {
			$type = "msgstr[0]";
		} elseif ( "msgstr[1]" === substr( $line, 0, 9 ) ) {
			$type = "msgstr[1]";
		} elseif ( "msgstr " === substr( $line, 0, 7 ) ) {
			$type = "msgstr ";
		} elseif ( '"' === substr( $line, 0, 1 ) ) {
			$type = '"';  // Start of more translatable text
		} elseif ( '' === trim( $line ) ) {
			$type = '';
		} elseif ( '#.' === substr( $line, 0, 2 ) ) {
			$type = '#.';
		} elseif ( "msgctxt " === substr( $line, 0, 8 ) ) {
			$type = 'msgctxt ';
		} else {
			$type = 'default';
		}

		$this->narrator->narrate( "type", $type );
		return $type;
	}

	/**
	 * Returns the translatable text for the line type.
	 *
	 * @param $type
	 * @param $line
	 * @return string|null
	 */
	function get_translatable_text( $type, $line ) {
		$text = null;
		switch ( $type ) {
			case "msgid_plural":
				$text = substr( $line, 14, -1 );
				break;
			case "msgid ":
				$text = substr( $line, 7, -1 );
				break;
			case '"':
				$text = substr( $line, 1, -1);
				break;
		}
		return $text;
	}

	/**
	 * Controls the translation from .pot to .po.
	 *
	 * Replaces la_CY_translate.
	 */
	function control_translation() {
		$count = 0;
		$this->repl = null;
		$this->plural = false;
		$this->first = true;
		$this->msgctxt = null;
		$this->translators_note = null;
		$this->last_blank = 0;
		$count_lines = count( $this->pot );
		while ( $count < $count_lines ) {
			$line = $this->pot[ $count ];
			$count++;
			$line = trim( $line );
			$type = $this->determine_line_type( $line );
			$this->narrator->narrate( 'line', $line );

			$translatable_text = $this->get_translatable_text( $type, $line );
			switch ( $type ) {
				case 'msgid_plural':
					$this->outfile( "$line\n");
					$this->repl_plural = $translatable_text;
					$this->plural = true;
					break;
				case 'msgid ':
					$this->outfile( "$line\n");
					$this->repl = $translatable_text;
					$this->plural = false;
					break;

				case 'msgstr[0]':
					$this->plural = false;
					break;

				case 'msgstr[1]':
					$this->plural = true;
					break;

				case 'msgstr ':
					$translated = $this->translate_string( $this->repl );
					$line = str_replace( '""', $translated, $line );
					if ( $this->first ) {
						$this->outfile( $line );
					} else {

						$this->outfile("$line\n" );
					}
					$this->plural = false;
					break;

				case '"':
					$this->outfile( "$line\n" );
					if ( $this->maybe_plural_forms( $line ) ) {
						//
					} else {
						if ( $this->plural ) {
							$this->repl_plural .= $translatable_text;
						} else {
							$this->repl .= $translatable_text;
						}
					}
					break;

				case '':
					$this->output_plural_translation( $line );
					break;

				case '#.':
					if ( $this->last_blank++ == $count ) {
						$this->output_plural_translation( $line );
					}
					$this->translators_note = $line;
					break;

				case 'msgctxt':
					$this->msgctxt = substr( $line, 9, -1);
					$this->outfile( "$line\n" );
					break;

				default:
					$this->outfile( "$line\n" );
			}
		}

	}

	function output_plural_translation( $line ) {
		if ( !$this->first ) {
			if ( $this->plural ) {
				$repl = $this->translate_string( $this->repl );
				$repl_plural = $this->translate_string( $this->repl_plural );
				$this->outfile("msgstr[0] $repl" );
				$this->outfile("msgstr[1] $repl_plural" );

				if ( $line !== "" ) {
					$this->outfile("\n" );
				} else {
					$this->last_blank = $count;
				}
				$this->outfile("$line\n" );
			} else {
				//echo $repl;
			}
		}
		$this->plural = false;
		$this->first = false;
		$this->msgctxt = null;
		// Set translator's note - which
		// #. translators: 1: month name, 2: 4-digit year
		$this->translators_note = null;
		if ( "#." == substr( $line, 0, 2 ) ) {
			$this->translators_note = $line;
		}
	}

	/**
	 * Writes out Plural-Forms after the MIME-Version record
	 *
	 * @param $line
	 * @return bool
	 */
	function maybe_plural_forms( $line ) {
		if ( $this->first && substr( $line, 0, 18 ) == "\"MIME-Version: 1.0" ) {
			//echo "\"Plural-Forms: nplurals=2; plural=n == 1 ? 0 : 1;\\n\"\n";
			$this->outfile( "\"Plural-Forms: nplurals=2; plural=n == 1 ? 0 : 1;\\n\"\n" );
			$this->outfile( "\"Language: {$this->locale}\\n\"\n" );
			// @TODO add Last Translator and Language team
			//$this->last_translator();
			return true;
		}
		return false;
	}

	/**
	 * Writes a line of output to the .po file.
	 *
	 * @param $output
	 *
	 * @return string
	 */
	function outfile( $output) {
		$this->narrator->narrate(null, $output );
		$handle = fopen( $this->po_filename, "a" );
		if ( false === $handle ) {
			$ret = "fopen failed";
			$this->narrator->narrate( "fopen failed", $this->po_filename );
		} else {
			$bytes = fwrite( $handle, $output );
			$ret = fclose( $handle );
			$ret .= " $bytes {$this->po_filename} $output";
		}
		return $ret;
	}

	/**
	 * Translates the string.
	 *
	 * Uses:
	 * - locale - to determine the target language and country
	 * - msgctxt - Context provided by the code.
	 * - translator's note - provided in comments before the code
	 *
	 * @param $string
	 *
	 * @return mixed
	 */
	function translate_string( $string ) {
		$this->narrator->narrate( "translate", $string );
		if ( !empty( $string ) ) {

		}
		return '"' . $string . '"' . "\n";
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


}