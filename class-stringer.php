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

	private $notNeededTags = [];

	function __construct() {
		if ( ! function_exists( 'str_get_html' ) ) {
			require_once 'simple_html_dom.php';
		}
		$this->setUpNotNeeded();
	}

	function get_strings( $block ) {
		$html = str_get_html( $block['innerHTML'] );
		// print_r( $html );
		//$html->dump();
		//foreach ( $html->root->children as $node ) {
		//print_r( $node );
		$this->recurse( $html->root );
	}

	function setUpNotNeeded() {
		$notNeededTags = [ 'ul', 'ol'];
		$this->notNeededTags  = array_flip( $notNeededTags );
	}

	function isInnertextNeeded( $tag ) {
		if ( isset( $this->notNeededTags[$tag ] ) ) {
			return false;
		}
		return true;

	}

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

}