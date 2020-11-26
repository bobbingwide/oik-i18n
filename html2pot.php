<?php

/**
 * Convert .html files to .pot files
 */

$html = file_get_contents( 'test.html');
//print_r( $html );

/*
// Let's grab all image tags from the HTML.
$dom_doc = new DOMDocument();

// The @ is not enough to suppress errors when dealing with libxml,
// we have to tell it directly how we want to handle errors.
libxml_use_internal_errors( true );
$dom_doc->loadHTML(  );
libxml_use_internal_errors( false );

$image_tags = $dom_doc->getElementsByTagName( 'img' );

// For each image Tag, make sure it can be added to the $images array, and add it.
foreach ( $image_tags as $image_tag ) {
	$img_src = $image_tag->getAttribute( 'src' );

	if ( empty( $img_src ) ) {
		continue;
	}
*/

$parser = new WP_Block_Parser();
$blocks = $parser->parse( $html);
//print_r( $blocks );
$stringer = new Stringer();
$count = 0;
foreach ( $blocks as $block) {
	echo PHP_EOL;
	echo "Block: " . $count;
	echo PHP_EOL;
	$stringer->get_strings( $block );
	//print_r( $block );

}

class Stringer {
	private $dom_doc = null;
	function __construct() {
		$this->dom_doc = new DOMDocument();

	}

	function get_strings( $block ) {
		//echo $block['innerHTML'];
		$this->dom_doc->loadHTML( $block['innerHTML']);
		//print_r( $this->dom_doc );
		echo $this->dom_doc->textContent;
		echo PHP_EOL;
		$this->showDOMNode( $this->dom_doc);
		echo PHP_EOL;
		/*

		echo PHP_EOL;
		if ( $this->dom_doc->hasChildNodes() ) {
			echo "this has child nodes" . PHP_EOL;
			foreach ( $this->dom_doc->childNodes as $childNode ) {
				print_r( $childNode );
				//gob();
				//$this->get_child_node_strings( $childNode);
			}

		}
		*/

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
		            echo 'W:' . $node->wholeText;
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
