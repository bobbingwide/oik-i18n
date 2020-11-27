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

	/**
	 * @var array Tags from which the innertText string is not needed.
	 */
	private $notNeededTags = [];

	/**
	 * @var array Translateable strings and their context.
	 */
	public $strings = [];

	/**
	 * @var string Source filename where the string was first detected.
	 */
	public $source_filename = null;

	/**
	 * @var string block type where the string was first detected.
	 */
	public $blockName = null;

	/**
	 * Stringer constructor.
	 * Ensures simple HTML DOM parser is loaded
	 */

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
	 */
	function get_strings( $blockName, $innerHTML ) {
		$this->set_blockName( $blockName );
		$html = str_get_html( $innerHTML );
		$this->recurse( $html->root );
		return $this->strings;
	}

	function set_blockName( $blockName ) {
		$this->blockName = $blockName;
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
		// @TODO - Extract strings from attributes
		echo implode( ' ', $node->getAllAttributes() );

		if ( count( $node->children ) ) {
			foreach ( $node->children as $child ) {
				$this->recurse( $child );
			}
		}
		$nest--;
		echo PHP_EOL;
	}

	/**
	 * Add a translatable string.
	 * @param $text
	 */
	function add_string( $text ) {
		if ( !isset( $this->strings[ $text ] ) ) {
			$this->strings[$text] = $this->source_filename . ' ' . $this->blockName;
		}
	}

	/**
	 * Returns all the strings.
	 *
	 * @return array All the translatable strings found.
	 */
	function get_all_strings() {
		return $this->strings;
	}

}