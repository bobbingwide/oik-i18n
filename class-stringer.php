<?php

/**
 * Class Stringer
 *
 * Implements simple_html_dom parsing for WordPress template and template part .html files
 * to find the translatable strings and make them available for translation.
 *
 * The second phase is to replace the translatable strings with the translations
 * and create new locale specific versions of the theme's files.
 *
 */


class Stringer {

	private $notNeededTags = [];

	public $strings = [];

	public $source_filename = null;

	function __construct() {
		if ( ! function_exists( 'str_get_html' ) ) {
			require_once 'simple_html_dom.php';
		}
		$this->setUpNotNeeded();
	}

	function set_source_filename( $filename ) {
		$this->source_filename = $filename;
	}

	/**
	 * Gets translatable strings from the inner HTML for a block.
	 *
	 */
	function get_strings( $innerHTML ) {
		$html = str_get_html( $innerHTML );
		$this->recurse( $html->root );
		return $this->strings;
	}

	/**
	 * Lists the HTML tags where we don't need to extract text.
	 *
	 * The HTML parser can duplicate text from inner tags
	 * such as ul, ol and others yet to be discovered.
	 *
	 */
	function setUpNotNeeded() {
		$notNeededTags = [ 'ul', 'ol'];
		$this->notNeededTags  = array_flip( $notNeededTags );
	}

	/**
	 * Checks if we need the text for this tag.
	 */
	function isInnertextNeeded( $tag ) {
		if ( isset( $this->notNeededTags[$tag ] ) ) {
			return false;
		}
		return true;

	}

	/**
	 * Extracts translatable strings from the parsed HTML.
	 *
	 * @param $node
	 */
	function recurse( $node ) {
		static $nest = 0;
		$nest++;

		echo PHP_EOL;
		echo str_repeat( '  ', $nest);
		echo $node->tag;
		echo ' ';
		echo $node->nodetype;
		echo ' ';

		if ( $node->nodetype !== HDOM_TYPE_ROOT) {
			if ( $this->isInnertextNeeded( $node->tag )) {
				$text=$node->innertext();
				$text=trim( $text );
				echo $text;
				$this->add_string( $text );
			}
		}
		echo implode( ' ', $node->getAllAttributes() );

		if ( count( $node->children ) ) {
			foreach ( $node->children as $child ) {
				$this->recurse( $child );
			}
		}


		//echo $node->innertext();

		//print_r(  $node->_ );


			//$node->dump( $node );
			//echo $node->text();
			//echo $node->__tostring();
			//if( isset( $node->text )) {
			//echo "IT:";
			//echo $node->innertext();
			//echo ":TI";
			//echo $node->text();


		$nest--;
		echo PHP_EOL;
	}

	/**
	 * Add a translatable string.
	 * @param $text
	 */
	function add_string( $text ) {
		if ( !isset( $this->strings[ $text ] ) ) {
			$this->strings[$text] = $this->source_filename;
		}
	}

	/**
	 * Dumps the strings to a .pot file
	 *
	 */
	function get_all_strings() {
		return $this->strings;
	}

}