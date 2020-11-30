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
		echo 'Writing HTML';
		echo PHP_EOL;
		//print_r( $this );
		$html = $this->dom_doc->saveHTML();
		$html = trim( $html);
		//$html = $this->unwrap( $html );
		return $html;
	}

	function translate() {
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
		echo "Locale: ";
		echo $this->locale;
		echo PHP_EOL;
		echo "String: '";
		echo $string;
		echo "'";
		echo PHP_EOL;
		$trimmed = $this->trim( $string );
		echo "Trimmed: " . $trimmed . PHP_EOL;
		$translated = __( $trimmed, $this->locale );
		if ( strlen( $trimmed ) < strlen( $string  ) ) {
			$translated = $this->detrim( $translated );
		}
		echo "Translated: '" . $translated . "'";
		echo PHP_EOL;
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
			echo "lpos:" . $lpos;
			echo "rlen:" . $rlen;
			echo "Trimmed left: '" . $this->leftness . "'";
			echo "Trimmed right: '" . $this->rightness . "'";
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
		echo "Translating string:" . $value;
		echo PHP_EOL;
		//print_r( $node );
		if ( XML_TEXT_NODE === $node->nodeType ) {
			$translated = $this->translate_string( $node->nodeValue );
			echo $translated;
			echo PHP_EOL;
			$node->nodeValue= $translated;
			//$node->textContent = $translated;
		} else {

			echo "NO we're not!";
			echo "NV: " . $node->nodeValue;
			echo "NT: " . $node->nodeType;
			echo PHP_EOL;
		}
		echo PHP_EOL;

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
		echo "Translating attributes";
		echo PHP_EOL;
		if ( $node->hasAttributes() ) {
			//print_r( $node );
			//print_r( $node->attributes);
			echo $node->attributes->length;
			echo PHP_EOL;
			for ( $item=0; $item < $node->attributes->length; $item ++ ) {
				$attribute=$node->attributes->item( $item );
				echo 'A#:' . $item;
				echo 'AN:' . $attribute->name;
				echo 'AV:' . $attribute->value;
				echo PHP_EOL;
				if ( $this->isAttrTranslatable( $attribute->name ) ) {
					$translated=$this->translate_string( $attribute_value );
					$node->setAttribute( $attribute->name, $translated );
					//$this->add_attribute_string( $attribute->name, $attribute->value );
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
