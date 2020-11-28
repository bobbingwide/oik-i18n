<?php
/*
$contents = file_get_contents( 'test.html');

//$html = '<html><body>';
$html = $contents;
//$html .= '</body></html>';

$stringer = new DOM_Stringer();
$stringer->get_strings( $html );
*/


/**
 * Class DOM_Stringer
 *
 * Need to cater for:
 *
Warning: DOMDocument::loadHTML(): Tag section invalid in Entity, line: 31 in C:\apache\htdocs\wordpress\wp-content\plugins\oik-i18n\DOM_Stringer.php on line 25

Warning: DOMDocument::loadHTML(): Tag svg invalid in Entity, line: 32 in C:\apache\htdocs\wordpress\wp-content\plugins\oik-i18n\DOM_Stringer.php on line 25
 */


class DOM_Stringer  {


	protected $dom_doc = null;

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
	 * Stack of nodes.
	 *
	 * @var array
	 */
	public $nodeName = [];

	/**
	 *
	 */

	function __construct() {
		$this->dom_doc = new DOMDocument();
		$this->setUpNotNeeded();
	}

	function set_source_filename( $filename ) {
		$this->source_filename = $filename;
	}

	function set_blockName( $blockName ) {
		$this->blockName = $blockName;
	}

	/**
	 * Pushes a node onto the tree.
	 * @param $nodeName
	 */
	function set_nodeName ( $nodeName ) {
		$this->nodeName[] = $nodeName;
	}

	function pop_nodeName() {
		array_pop( $this->nodeName );
	}

	function get_nodeNameTree() {
		$tree = implode( '>',  $this->nodeName );
		$tree = str_replace( '#document>', '', $tree );
		return $tree;
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
	 * Loads the HTML.
	 *
	 * Attempts to cater for:
	 * `Warning DOMDocument::loadHTML(): Tag section invalid in Entity, ...`
	 * messages
	 *
	 * @param $html
	 */
	function loadHTML( $html ) {
		libxml_use_internal_errors( true );
		$this->dom_doc->loadHTML( $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		libxml_use_internal_errors( false );
		//print_r( $this->dom_doc );
		echo $this->dom_doc->textContent;
		echo PHP_EOL;
	}

	/**
	 * Wraps the HTML in a standard srtucture
	 * which we strip off again after save
	 * <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
	<html><body><p>Hello World</p></body></html>
	 * @param $html
	 *
	 * @return string
	 *
	 */

	function wrap_html( $html ) {
		$wrapped = '<!DOCTYPE html>';
		$wrapped .= '<html><body>';
		$wrapped .= $html;
		$wrapped .= '</body></html>';
		return $wrapped;
	}

	/**
	 * Gets translatable strings from the inner HTML for a block.
    */
	function get_strings( $blockName, $html ) {
		//$wrapped = $this->wrap_html( $html );
		$this->set_blockName( $blockName );
		$this->loadHTML( $html );
		$this->extract_strings( $this->dom_doc );
		echo PHP_EOL;
	}

	function extract_strings( DOMNode $node) {
		static $nested;
		$nested++;
		echo PHP_EOL;
		echo str_repeat( '   ', $nested );
		$this->set_nodeName( $node->nodeName );
		echo 'N:' . $node->nodeName . ': ';
		echo 'T:' . $node->nodeType;
		echo 'V:' . $node->nodeValue;
		$this->extract_strings_from_attributes( $node );
		$value = trim( $node->nodeValue);
		if ( !empty( $value ) ) {
			echo PHP_EOL;
			echo str_repeat( '   ', $nested );
			echo 'N:' . $node->nodeName . ': ';
			echo 'V:' . $node->nodeValue;
			$this->add_string( $node, $value );
			echo 'T:' . $node->nodeType;
			if ( !empty( $node->wholeText ) ) {
				echo 'W:' . $node->wholeText;
			}
			echo 'C:' . $node->textContent;
		} else {
			echo PHP_EOL;
			echo str_repeat( '   ', $nested );
			echo 'N:' . $node->nodeName . ': ';
			echo 'T:' . $node->nodeType;

		}
		if ( $node->haschildNodes() ) {

			foreach ( $node->childNodes as $node ) {

				echo PHP_EOL;
				echo str_repeat( '   ', $nested );
				echo 'PN:' . $node->nodeName . ': ';
				echo 'PT:' . $node->nodeType;
				$this->extract_strings( $node );

			}
		}

		$this->pop_nodeName();


		$nested--;
	}

	function extract_strings_from_attributes( $node ) {
		//echo $node->getAttributes();
		echo 'A: getting attributes';
		echo PHP_EOL;
		//print_r( $node);

		if ( $node->hasAttributes() ) {
			//print_r( $node );
			//print_r( $node->attributes);
			echo $node->attributes->length;
			echo PHP_EOL;
			for ( $item = 0; $item < $node->attributes->length; $item++ ) {
				$attribute=$node->attributes->item( $item );
				echo 'A#:' . $item;
				echo 'AN:' . $attribute->name;
				echo 'AV:' . $attribute->value;
				echo PHP_EOL;
				// $attribute->value = "derf";
				//print_r( $attribute );
				//$node->setAttribute( $attribute->name, "derf" );
				if ( $this->isAttrTranslatable( $attribute->name )) {
					$this->add_string( null, $attribute->value );
				}
			}
		}

	}

	/**
	 * Checks if the attribute is translatable.
	 *
	 * Note: $translatable should be a bigger array.
	 * and $not_translatable is only really necessary when the default
	 * if the key is not in $translatable is true. Currently it's false.
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	function isAttrTranslatable( $key ) {
		//$translatable = array_flip( [ 'label', 'alt', 'title' ] );
		//$not_translatable = array_flip( ['class', 'className', 'slug', 'ID', 'ref', 'href', 'style', 'aria-hidden', 'translate'] );
		$isTranslatable = isset( $this->translatable[ $key ] ) ? true : false;
		if ( isset( $this->not_translatable[ $key] ) ) {
			$isTranslatable = false;
		}
		return $isTranslatable;
	}

	/**
	 * Gets the names of translatable attributes.
	 *
	 * @TODO Extract the names of translatable attributes from each block's attributes.
	 *
	 */
	function getTranslatableAttrs() {
		$this->translatable = array_flip( [ 'label', 'alt', 'title' ] );
	}

	/**
	 * Gets the names of attributes which are not translatable.
	 *
	 * @TODO - decide which is better to list - translatable or not translatable.
	 * Is there any harm in having lots of terms that are not actually translatable
	 * in the .pot file?
	 */
	function getNotTranslatableAttrs() {
		$this->not_translatable = array_flip( ['class', 'className', 'slug', 'ID', 'ref', 'href', 'style',
			'aria-hidden', 'translate', 'theme'] );
	}

	/**
	 * Adds a translatable attr.
	 */
	function add_attribute_string( $attr, $text ) {
		
	}

	/**
	 * Add a translatable string.
	 *
	 * Only only extract the string if it's part of a text node
	 * or the node is null - for Gutenberg blocks.
	 *
	 * @param null|DOMNode $node
	 * @param string $text Translatable string
	 */
	function add_string( $node, $text ) {
		if ( null === $node || XML_TEXT_NODE === $node->nodeType ) {
			if ( ! isset( $this->strings[ $text ] ) ) {
				$this->strings[ $text ]=$this->source_filename . ' ' . $this->blockName . ' ' . $this->get_nodeNameTree();
			}
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

/*
 * DOMText Object
(
    [wholeText] =>
    [data] =>
    [length] => 2
    [nodeName] => #text
    [nodeValue] =>
    [nodeType] => 3
    [parentNode] => (object value omitted)
    [childNodes] =>
    [firstChild] =>
    [lastChild] =>
    [previousSibling] => (object value omitted)
    [nextSibling] =>
    [attributes] =>
    [ownerDocument] => (object value omitted)
    [namespaceURI] =>
    [prefix] =>
    [localName] =>
    [baseURI] =>
    [textContent] =>
)
 */