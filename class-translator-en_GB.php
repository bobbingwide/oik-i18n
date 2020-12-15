<?php

/**
 * Class Translator_en_GB
 * @package oik-i18n
 * @copyright (C) Copyright Bobbing Wide 2020
 */


class Translator_en_GB extends Translator {


	function __construct() {
		parent::__construct();
	}

	function translate_string( $string, $msgctxt, $translators_note ) {
		$translated = parent::translate_string( $string, $msgctxt, $translators_note );
		$this->narrator->narrate( 'en_GB', $translated );
		return $translated;
	}

	/**
	 * Loads the English variants.
	 */
	function load_variants() {
		$this->variants = null;
		oik_require( 'class-English-variants.php', 'oik-i18n');
		$this->variants = new English_variants( $this->locale );
	}



}