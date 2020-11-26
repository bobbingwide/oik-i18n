<?php

/**
 * Class Stringer
 *
 * Implements simple_html_dom parsing for WordPress template and template part .html files
 * to find the translatable strings and make them available for translation
 *
 * The second phase is to replace the translatable strings with the translations
 * and create new locale specific versions of the theme's files
 *
 */


class Stringer {


	function __construct() {
		if ( ! function_exists( 'str_get_html' ) ) {
			require_once 'simple_html_dom.php';
		}

	}

	function get_strings( $content ) {
		$html = str_get_html( $content );
		print_r( $html );
	}

}