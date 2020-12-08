<?php
/**
 * Class DOM_string_updater
 * @package oik-i18n
 * @copyright (C) Copyright Bobbing Wide 2020
 */

class DOM_string_updater extends DOM_Stringer {

	private $locale;

	function __construct() {
		parent::__construct();
	}

	function set_locale( $locale ) {
		$this->locale = $locale;
	}

	function saveHTML() {
	    $this->narrator->narrate( 'Writing HTML', '');
		$html = $this->dom_doc->saveHTML();
		$html = trim( $html);
		return $html;
	}

	function translate() {
	    gob();
		$this->extract_strings( $this->dom_doc );

	}

	/**
	 * Translate the string.
	 *
	 * Should we maintain blanks?
	 *
	 * @param $string
	 *
	 * @return mixed|string|void
	 */
	function translate_string( $string ) {
	    $this->narrator->narrate( 'Locale', $this->locale );
        $this->narrator->narrate( 'String', $string );
       	$trimmed = $this->trim( $string );
        $this->narrator->narrate( 'Trimmed', $trimmed );
		$translated = __( $trimmed, $this->locale );
		if ( strlen( $trimmed ) < strlen( $string  ) ) {
			$translated = $this->detrim( $translated );
		}
		$this->narrator->narrate( 'Translated', $translated );
		return $translated;
	}

	/**
	 * Trims a string retaining left and right trimmed bits.
	 *
	 * @param $string
	 *
	 * @return string
	 */
	function trim( $string ) {
		$this->leftness = null;
		$this->rightness = null;
		$trimmed = trim( $string );
		$trimmed_length = strlen( $trimmed );
		$string_length = strlen( $string );
		$diff = $string_length - $trimmed_length;
		if ( $diff ) {
			$lpos = strpos( $string, $trimmed);
			if ( $lpos ) {
				$this->leftness=substr( $string, 0, $lpos );
			}
			$rlen = $diff - $lpos;
			if ( $rlen ) {
				$this->rightness=substr( $string, - $rlen );
			}
			/*
			echo "lpos:" . $lpos;
			echo "rlen:" . $rlen;
			echo "Trimmed left: '" . $this->leftness . "'";
			echo "Trimmed right: '" . $this->rightness . "'";
			*/
		}
		return $trimmed;

	}
	function detrim( $string ) {
		$detrimmed = $this->leftness;
		$detrimmed .= $string;
		$detrimmed .= $this->rightness;
		$this->leftness = null;
		$this->rightness = null;
		return $detrimmed;
	}

	/**
	 *
	 * @param $node
	 * @param $value
	 *
	 * DOMElement Object
	(
	[tagName] => p
	[schemaTypeInfo] =>
	[nodeName] => p
	[nodeValue] => Hello World
	[nodeType] => 1
	[parentNode] => (object value omitted)
	[childNodes] => (object value omitted)
	[firstChild] => (object value omitted)
	[lastChild] => (object value omitted)
	[previousSibling] =>
	[nextSibling] =>
	[attributes] => (object value omitted)
	[ownerDocument] => (object value omitted)
	[namespaceURI] =>
	[prefix] =>
	[localName] => p
	[baseURI] =>
	[textContent] => Hello World
	)
	 *
	 * nodeType | Value |  Translate
	 * -------- | ------ | --------
	 * XML_ELEMENT_NODE  | 1
	 * XML_TEXT_NODE | 3 | Yes
	 *               | 8 | Not necessary
	 */
	function add_string( $node, $value ) {
	    $this->narrator->narrate( 'Translating', $value );
		if ( XML_TEXT_NODE === $node->nodeType ) {
			$translated = $this->translate_string( $node->nodeValue );
			$this->narrator->narrate( 'Translated', $translated );
			$node->nodeValue= $translated;
			//$node->textContent = $translated;
		} else {
		    $this->narrator->narrate( 'Not translating', $node->nodeValue );
		    $this->narrator->narrate( 'NT', $node->nodeType );
		}
	}

	/**
	 * Updates the attribute string value for the selected locale.
	 *
	 * This is for the nodes.
	 * When dealing with blocks we have a different method.
	 */
	function add_attribute_string( $attr, $text ) {
		gob();
	}

	/**
	 * Overrides extract strings to translate them
	 * @param $node
	 * DOMNamedNodeMap Object
	(
	[length] => 1
	)
	1DOMAttr Object
	(
	[name] => class
	[specified] => 1
	[value] => fred
	[ownerElement] => (object value omitted)
	[schemaTypeInfo] =>
	[nodeName] => class
	[nodeValue] => fred
	[nodeType] => 2
	[parentNode] => (object value omitted)
	[childNodes] => (object value omitted)
	[firstChild] => (object value omitted)
	[lastChild] => (object value omitted)
	[previousSibling] =>
	[nextSibling] =>
	[attributes] =>
	[ownerDocument] => (object value omitted)
	[namespaceURI] =>
	[prefix] =>
	[localName] => class
	[baseURI] =>
	[textContent] => fred
	)
	 */
	function extract_strings_from_attributes( $node ) {
        if ( $node->hasAttributes() ) {
            $this->narrator->narrate( 'Translating attributes', $node->attributes->length );
            for ( $item = 0; $item < $node->attributes->length; $item++ ) {
                $attribute=$node->attributes->item( $item );
                $this->narrator->narrate( 'A#', $item );
                $this->narrator->narrate( 'AN', $attribute->name );
                $this->narrator->narrate( 'AV', $attribute->value );
                if ( $this->isAttrTranslatable( $attribute->name )) {
                    $translated=$this->translate_string( $attribute_value );
                    $node->setAttribute( $attribute->name, $translated );
                    $this->add_attribute_string( $attribute->name, $attribute->value );
                }
            }
        }

    }

	/**
	 * Unwraps the html and body from tags from the saved HTML
	 * ```
	 * !DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
	 * <html><body><p>Hello World</p></body></html>
	 * ```
	 */
	function unwrap( $html ) {
		//$doctype= '!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">';
		//$doctype .= PHP_EOL;
		//$unwrapped = substr( $html, strlen( $doctype ) );
		$unwrapped = trim( $html );
		//$unwrapped = substr( $unwrapped, strlen( '<html><body>') );
		//$unwrapped = substr( $unwrapped, 0 , - strlen( '</body></html>' ) );
		return $unwrapped;
	}

    /**
     * Translates the rich text string.
     *
     * - Translates the string using the normal lookup
     * - Loads the HTML as a new DOM document
     * - Replaces the current node with the new node including attributes and children
     *
     * @param DOMnode $node
     * @param string $html
     */
	function add_rich_text_string( $node, $html ) {
	    $translated = $this->translate_string( $html );
	    $this->narrator->narrate( 'Rich text', $translated );
        $this->replace_node( $node, $translated );
    }

    /**
     * Converts the translated HTML into a DOMnode.
     *
     * @param DOMnode $node The original node.
     * @param string $html The translated rich text HTML.
     * @return DOMNode
     */
    function getHTMLasNode( $node, $html ) {
	    $html = $this->wrap_html( $node, $html );
	    $this->narrator->narrate( "HTML?", $html );

	    $domdoc = new DOMdocument();
        libxml_use_internal_errors( true );
        $domdoc->loadHTML( $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
        libxml_use_internal_errors( false );
        //print_r( $domdoc );
        return $domdoc->firstChild;
    }

    /**
     * Wraps the translated HTML in the node's tag.
     *
     * @param DOMnode $node The rich text node being translated.
     * @param string $html The translation of the rich text.
     * @return string
     */
    function wrap_html( $node, $html )   {
	    $wrapped = $this->tag( $node->nodeName);
	    $wrapped .= $html;
	    $wrapped .= $this->tag( $node->nodeName, true);
	    return $wrapped;
    }

    /**
     * Returns a start or end tag.
     *
     * @param string $tagname Tag name without chevrons eg p
     * @param bool $end_tag True if you want the end tag.
     * @return string
     */
    function tag( $tagname, $end_tag = false ) {
	    $tag = '<';
	    $tag .= $end_tag ? '/' : '';
	    $tag .= $tagname;
	    $tag .= '>';
	    return $tag;
    }

    /**
     * Replaces the original node with the translated version.
     *
     * @param DOMnode $node The node being replaced
     * @param string $html Translated rich text HTML.
     */
    function replace_node( $node, $html ) {
	    $this->narrator->narrate( "Node", $node->nodeName);
        //print_r( $node );
	    $replacement_node = $this->getHTMLasNode( $node, $html );
	    $this->copy_attributes( $node, $replacement_node );
	    $this->narrator->narrate( "Replacement", $replacement_node->nodeName );
	    //print_r($replacement_node );
	    $new_node = $this->dom_doc->importNode( $replacement_node, true );
	    $this->narrator->narrate( "New node", $new_node->nodeName );
	    //print_r( $new_node );
        $node->parentNode->replaceChild( $new_node, $node );
    }

    /**
     * Copies attributes from the source node to the target node.
     *
     *
     * @param $source_node
     * @param $target_node
     */
    function copy_attributes( $source_node, $target_node ) {
        if ( $source_node->hasAttributes() ) {
            for ( $item = 0; $item < $source_node->attributes->length; $item++ ) {
                $attribute = $source_node->attributes->item($item);
                $target_node->setAttribute($attribute->name, $attribute->value);
            }
        }
    }
}

/*
 * DOMDocument Object
(
    [doctype] =>
    [implementation] => (object value omitted)
    [documentElement] => (object value omitted)
    [actualEncoding] =>
    [encoding] =>
    [xmlEncoding] =>
    [standalone] => 1
    [xmlStandalone] => 1
    [version] =>
    [xmlVersion] =>
    [strictErrorChecking] => 1
    [documentURI] =>
    [config] =>
    [formatOutput] =>
    [validateOnParse] =>
    [resolveExternals] =>
    [preserveWhiteSpace] => 1
    [recover] =>
    [substituteEntities] =>
    [nodeName] => #document
    [nodeValue] =>
    [nodeType] => 13
    [parentNode] =>
    [childNodes] => (object value omitted)
    [firstChild] => (object value omitted)
    [lastChild] => (object value omitted)
    [previousSibling] =>
    [nextSibling] =>
    [attributes] =>
    [ownerDocument] =>
    [namespaceURI] =>
    [prefix] =>
    [localName] =>
    [baseURI] =>
    [textContent] => Hello World
)
 */
