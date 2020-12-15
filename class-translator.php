<?php

/**
 * Class Translator
 *
 * Implements the base class to translate strings from US English ( en_US ) to other locales.
 *
 * Extended by:
 * - Translator_bb_BB
 * - Translator_en_GB
 * - etc
 *
 * @package oik-i18n
 * @copyright (C) Copyright Bobbing Wide 2020
 */


class Translator {

	protected $locale = null;

	private $component = null;

	protected $variants = null;

	protected $narrator = null;

	function __construct() {
		$this->narrator = Narrator::instance();
	}

	function set_locale( $locale ) {
		$this->locale=$locale;
	}

	function set_component( $component ) {
		$this->component=$component;
	}


	/**
	 * Attempts to translate the string.
	 *
	 * @TODO Determine if a null string is acceptable in the .po file.  How does that get translated?
	 *
	 * @param string $string the string to be translated.
	 * @param string $msgctxt context
	 * @param string $translators_note
	 * @return string a possibly translated string
	 */
	function translate_string( $string, $msgctxt, $translators_note ) {
		$translated = $string;
		$this->narrator->narrate( 'In', $string );
		$text = str_replace( '\"', '"', $string );

		$la_CY_text = __( $text, $this->component );
		// Post Plugin Translate
		$this->narrator->narrate( 'PPT', $la_CY_text );
		if ( $la_CY_text == $text ) {
			$la_CY_text = __( $text, $this->locale );
			$this->narrator->narrate( 'Locale', $la_CY_text );
			// What's the purpose of translating to the oik locale?
			// $la_CY_oik_text = __( $text, "oik" );
			// echo "oik: " . $la_CY_oik_text . PHP_EOL;
		}
		//$la_CY_text = la_CY_check_utf8( $la_CY_text, $text );

		$la_CY_text = $this->variants( $text, $msgctxt );
		$this->narrator->narrate( "variant $msgctxt", $la_CY_text );


		if ( ( $la_CY_text !== $text && $this->locale !== 'en_GB' ) || $translators_note ) {
			$la_CY_text = la_CY_request_translation( $text, $plugin, $locale, $msgctxt, $translators_note );
		}
		$la_CY_text = str_replace( '"', '\"', $la_CY_text );
		$this->narrator->narrate( 'Out', $la_CY_text );

		//$la_CY_text = '"' . $la_CY_text . "\"\n";

		return $la_CY_text;
	}

	function variants( $text, $msgctxt ) {
		$original_OK = $this->variants->check_original_language( $text, $msgctxt );
		if ( $original_OK ) {
			$text = $this->variants->map( $text, $msgctxt );
		}
		$this->narrator->narrate( 'Variant', $text );
		return $text;
	}


	

	/**
	* Loads the locale .mo files into one big translate table
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
	function load_translations() {
        //oik_require( "bobbcomp.inc" );
		$locale = $this->locale;
        wp_set_lang_dir();
        $files = "admin-$locale.mo,admin-network-$locale.mo,continents-cities-$locale.mo,$locale.mo";
        $files .= ",themes/twentyeleven-$locale.mo,themes/twentyten-$locale.mo,themes/twentythirteen-$locale.mo,themes/twentytwelve-$locale.mo,themes/twentyfourteen-$locale.mo";
		$files .= ",plugins/akismet-$locale.mo,plugins/buddypress-$locale.mo,plugins/woocommerce-$locale.mo,plugins/woocommerce-admin-$locale.mo";

        // $files = "themes/twentyfourteen-$locale.mo";
        $files = bw_as_array( $files );
        foreach ( $files as $file ) {
        	$mofile = WP_LANG_DIR . '/' . $file;
        	$this->narrator->narrate( "Loading", $mofile );
            $result = load_textdomain( $locale, $mofile );
            $this->narrator->narrate( 'result', $result );
        }

		$plugins = array( "oik-weightcountry-shipping", "oik-weight-zone-shipping", "oik-weight-zone-shipping-pro", "oik-weightcountry-shipping-pro"
									, "oik-types", "oik-fields", "oik"
									);
		//$plugins = array( "oik" );

		//$locale = "oik";
		foreach ( $plugins as $key => $plugin ) {
			$this->narrator->narrate( "Plugin", $plugin );
			//echo "Loading $plugin into $locale" . PHP_EOL;
			//$result = load_plugin_textdomain( $locale, false, "$plugin/languages" );
			$path = oik_path( "languages/$plugin-$locale.mo", $plugin );
			$result = load_textdomain( $locale, $path );
            $this->narrator->narrate( 'Result', $result );
		}

		//$actual_locale = get_locale();
		//echo "Actual locale: $actual_locale" . PHP_EOL;
		//return( $actual_locale );
	}

	/**
     *
     */
	function load_variants() {
	}
}