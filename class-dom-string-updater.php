<?php
/**
 * Class DOM_string_updater
 * @package oik-i18n
 * @copyright (C) Copyright Bobbing Wide 2020
 */

class DOM_string_updater extends DOM_Stringer {

	function __construct() {
		parent::__construct();
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
	 */
	function add_string( $node, $value ) {
		echo "Translating string:" . $value;
		echo PHP_EOL;
		print_r( $node );
		if ( XML_TEXT_NODE === $node->nodeType ) {
			$node->nodeValue="Hlelo Wrold";
		}

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
			$attribute = $node->attributes->item(0);
			echo $attribute->name;
			echo $attribute->value;
			// $attribute->value = "derf";
			//print_r( $attribute );
			$node->setAttribute ( $attribute->name , "derf" );
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
