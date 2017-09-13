<?php // (C) Copyright Bobbing Wide 2017

/**
 * Implement a class to automatically convert from US English to other variations of English.
 * 
 * Note: oik plugins are written in UK English ( en_GB ) but the translatable strings are supposed to be US English
 * so we need to perform a reverse mapping to US English if we want the UK English language version to be 'correct'.
 * There may not be that many instances where translation / conversion is actually required.
 * But we won't know until we've tried it. 
 * 
 */
class English_variants {

	/**
	 * File downloaded from https://docs.google.com/spreadsheets/d/1-Coz3zEHpPwsgcW0ZTe5SFeYqnMrPU6WxB2DyYD_HHg/edit#gid=280899954 
	 * Linked from https://en-gb.wordpress.org/translations/
	 */

	private $file = "WordPress.org Shared English Variants Translation Glossary - Variants.csv";
	
	private $locales = array( "en_US" => 0, "en_AU" => 1 , "en_CA" => 2 , "en_GB" => 3 , "en_NZ" => 4 , "en_ZA" => 5 );
	
	private $variants = array();
	
	private $source_locale = null;
	private $target_locale = null;
	
	private $map = array();
	private $reverse_map = array();
	
	private $context = null;
	
	/**
	 * @var dependencies_cache the true instance
	 */
	private static $instance;
	
	/**
	 * Return a single instance of this class
	 *
	 * @return object 
	 */
	public static function instance() {
		if ( !isset( self::$instance ) && !( self::$instance instanceof self ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	/**
	 * Constructor
	 *
	 * Prepares for mapping from en_US to en_GB
	 */
	function __construct( $locale="en_GB" ) {
		$this->source_locale( "en_US" );
		$this->target_locale( $locale );
		$this->load_variants();
		$this->create_map();
		$this->update_map_with_context();
	}
	
	/**
	 * Creates maps and reverse maps
	 * 
	 * Each time you change the source and target then you need to create new maps.
	 * The reverse map is used to check that the original text is not already in the target locale's vocab.
	 */
	function create_map() {
		$this->map = array();
		$this->reverse_map = array();
		foreach ( $this->variants as $variant ) {
			$source = $variant[ $this->locales[ $this->source_locale ] ];
			$target = $variant[ $this->locales[ $this->target_locale ] ];
			$this->map[ $source ] = $target;
			$this->reverse_map[ $target ] = $source;
		}
	}
	
	/** 
	 * Sets the source locale
	 *
	 * @param string $locale 
	 */
	function source_locale( $locale="en_US" ) {
		$this->source_locale = $locale;
	}
	
	/**
	 * Sets the target locale
	 * 
	 * @param string $locale
	 */
	function target_locale( $locale ) {
		$this->target_locale = $locale;
	}
	
	/**
	 * 
	 * Note: You only need to load the variants once 
	 * 
	 */
	function load_variants() {
		$file_path = __DIR__ . '/' . $this->file;
		$file = file( $file_path, FILE_IGNORE_NEW_LINES );
		$line = array_shift( $file );
		$this->validate_locales( $line );
		foreach ( $file as $line ) {
			$variant = $this->get_variant( $line );
			$this->add_variant( $variant );
		}
	}
	
	/**
	 * Update map with context 
	 * 
	 */
	function update_map_with_context() {
		$file_path = __DIR__ . '/' . $this->source_locale . '-' . $this->target_locale . '.csv';
		if ( file_exists( $file_path ) ) {
			$file = file( $file_path, FILE_IGNORE_NEW_LINES );
		} else {
			echo "Missing file: $file_path" . PHP_EOL;
			//$file = array( "check,cheque,bank" );
			$file = array();
		}
		foreach ( $file as $line ) {
			$values = str_getcsv( $line );
			$source = $values[0];
			$target = $values[1];
			$context = bw_array_get( $values, 2, null );
			if ( $context ) {
				unset( $this->map[ $source ] );
				unset( $this->reverse_map[ $target ] );
				$this->map[ $context . ':' . $source ] = $target;
				$this->reverse_map[ $context . ':' . $target ] = $source;
			} else {
				$this->map[ $source ] = $target;
				$this->reverse_map[ $target ] = $source;
			}
				
		}
	}
	
	/** 
	 * Checks that the CSV file has the format we expect
	 * 
	 * If not, we carry on but it may not work.
	 * PHPUnit tests should fail when they see the echo's.
	 */
	function validate_locales( $line ) {
		if ( $line !== "en 🇺🇸,en-au 🇦🇺,en-ca 🇨🇦,en-gb 🇬🇧,en-nz 🇳🇿,en-za 🇿🇦,pos,description" ) {
			echo "Unexpected first line" . PHP_EOL;
			echo $line . PHP_EOL;
		}
	}
	
	/**
	 * Loads a variant
	 */
	function get_variant( $line ) {
		$variant = str_getcsv( $line );
		return $variant;
	}
	
	/**
	 * Adds a variant
	 */
	function add_variant( $variant ) {
		$this->variants[] = $variant;
	}
	
	/**
	 * Returns a mapped text
	 * 
	 * We need to be able to parse words in English language
	 *
	 * @paran string $text
	 * @param string|null $context
	 * @return string mapped text
	 */
	function map( $text, $context=null ) {
		$this->context = $context;
		//$words = explode( " ", $text );
		$words = $this->get_tokens( $text );
		$new_words = array_map( [ $this, "map_word" ], $words);
		$mapped = implode( "", $new_words );
		return $mapped;
	}
	
	/**
	 * Map a single word
	 * 
	 * @param string $word
	 * @return string the mapped result
	 */
	function map_word( $word ) {
		$lcword = strtolower( $word );
		$variant = null;
		if ( $this->context ) {
			$key = $this->context . ':' . $lcword;
			$variant = bw_array_get( $this->map, $key, null );
		}
		if ( null === $variant ) {
			$variant = bw_array_get( $this->map, $lcword, $lcword );
		}	
		if ( $lcword <> $word ) {
			$variant = $this->recapitalise( $variant, $word, $lcword );
		}
		return $variant;
	}
	
	/**
	 * Recapitalises the translated word
	 *
	 * @TODO Cater for more than just the first letter.
	 *
	 * @param string $variant
	 * @param string $word Original word with Capitals
	 * @param string $lcword lower case version of original word
	 */
	function recapitalise( $variant, $word, $lcword ) {
		$chars  = str_split( $word );
		$lcs = str_split( $lcword );
		$variants = str_split( $variant );
		for ( $i = 0; $i < count( $chars ); $i++ ) {
			if ( $chars[ $i] <> $lcs[ $i ] ) {
				if ( $variants[ $i  ] == $lcs[ $i ] ) {
					$variants[ $i ] = $chars[ $i ];
				}
			}
		}
		$variant = implode( "", $variants );
		return $variant;
	}
	
	/**
	 * Checks the original language
	 *
	 * @param string $text
	 * @param string|null $context
	 * @return bool true if OK 
	 */
	function check_original_language( $text, $context=null ) {
		$mapped = $this->reverse_map( $text, $context );
		if ( $mapped != $text ) {
			echo "Original text already in target locale." . PHP_EOL;
			echo $this->source_locale . ": " . $text . PHP_EOL;
			echo $this->target_locale . ": " . $mapped . PHP_EOL;
			return false;
		}
		return true;
	}
	
	/**
	 * Returns a reverse mapped text
	 * 
	 * @param string $text
	 * @param string|null $context
	 * @return string reverse mapped text
	 */
	function reverse_map( $text, $context=null ) {
		$words = $this->get_tokens( $text );
		$new_words = array_map( [ $this, "reverse_map_word" ], $words);
		$mapped = implode( "", $new_words );
		return $mapped;
	}
	
	/**
	 * Returns a reverse mapped word
	 * 
	 * @param string $word
	 * @return string reverse mapped word
	 */
	function reverse_map_word( $word ) {
		$lcword = strtolower( $word );
		$variant = null;
		if ( $this->context ) {
			$key = $this->context . ':' . $lcword;
			$variant = bw_array_get( $this->reverse_map, $key, null );
		}
		if ( null === $variant ) {
			$variant = bw_array_get( $this->reverse_map, $lcword, $lcword );
		}	
		if ( $lcword <> $word ) {
			$variant = $this->recapitalise( $variant, $word, $lcword );
		}
		return $variant;
	}
	
	/** 
	 * Splits into wordy tokens
	 * 
	 * In order to perform a simple lookup we need words without any punctuation and white space
	 * Checking ctype_alpha() should be OK for US/UK English source but not for any other language.
	 *
	 * We don't expect there to be any digits in words.
	 * 
	 * @param string $text text to tokenize into "words"
	 * @return array tokens - some of which are words
	 */
	public function get_tokens( $text ) {
		$chars = str_split( $text );
		$current = null;
		$current_type = "n";
		$tokens = array();
		$count = count( $chars );
		for ( $i = 0; $i < $count; $i++ ) {
			$char = $chars[ $i ];
			if ( ctype_alpha( $char ) ) {
				$this_type = 'a';
			} elseif ( ctype_space( $char ) ) {
				$this_type = " ";
			} else { 
				$this_type = "?";
			}
			if ( $this_type == $current_type ) {
				$current .= $char;
			} else {
				if ( $current ) {
					$tokens[] = $current;
				}
				$current = $char;
				$current_type = $this_type;
			}
		}
		if ( $current ) {
			$tokens[] = $current;
		}
		return $tokens;
	}
	
	
	
	
	



}
