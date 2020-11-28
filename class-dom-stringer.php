<?php

$contents = file_get_contents( 'test.html');

//$html = '<html><body>';
$html = $contents;
//$html .= '</body></html>';

$stringer = new DOM_Stringer();
$stringer->get_strings( $html );


/**
 * Class DOM_Stringer
 *
 * Need to cater for:
 *
Warning: DOMDocument::loadHTML(): Tag section invalid in Entity, line: 31 in C:\apache\htdocs\wordpress\wp-content\plugins\oik-i18n\DOM_Stringer.php on line 25

Warning: DOMDocument::loadHTML(): Tag svg invalid in Entity, line: 32 in C:\apache\htdocs\wordpress\wp-content\plugins\oik-i18n\DOM_Stringer.php on line 25
 */


class DOM_Stringer {


	private $dom_doc = null;

	function __construct() {
		$this->dom_doc = new DOMDocument();
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
		$this->dom_doc->loadHTML( $html );
		libxml_use_internal_errors( false );
		print_r( $this->dom_doc );
		echo $this->dom_doc->textContent;
		echo PHP_EOL;
	}

	function get_strings( $html ) {
		$this->loadHTML( $html );
		echo $this->dom_doc->textContent;
		echo PHP_EOL;
		$this->showDOMNode( $this->dom_doc );
		echo PHP_EOL;

	}

	function showDOMNode(DOMNode $domNode) {
		static $nested;
		$nested++;
		foreach ($domNode->childNodes as $node) {

			//echo PHP_EOL;
			if ( $node->haschildNodes() ) {
				echo PHP_EOL;
				echo str_repeat( '   ', $nested );
				echo 'PN:' . $node->nodeName . ': ';
				echo 'PT:' . $node->nodeType;
				$this->showDOMNode( $node );

			} else {
				$value = trim( $node->nodeValue);
				if ( !empty( $value ) ) {
					echo PHP_EOL;
					echo str_repeat( '   ', $nested );
					echo 'N:' . $node->nodeName . ': ';
					echo 'V:' . $node->nodeValue;
					echo 'T:' . $node->nodeType;
					if ( !empty( $node->wholeText ) ) {
					echo 'W:' . $node->wholeText;
					}
					echo 'C:' . $node->textContent;
				} else {
					echo PHP_EOL;
					echo str_repeat( '   ', $nested );
					echo 'N:' . $node->nodeName . ': ';

				}
				//print_r( $node);
			}
		}
		$nested--;
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