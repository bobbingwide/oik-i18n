<?php



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
     * @var array Tags allowed in rich text
     */
    private $acceptableTags = [];


    /**
	 * @var array Translatable strings and their context.
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
     * Nested level
     * @var integer;
     *
     */
    private $nested = 0;

	/**
	 * DOM_Stringer constructor.
	 */
	function __construct() {
		$this->dom_doc = new DOMDocument();
		$this->setAcceptableTags();
		$this->setTranslatableAttrs();
		$this->setNotTranslatableAttrs();
		$this->narrator = Narrator::instance();
	}

	/**
	 * Sets the source filename for reporting in the .pot file
	 * @param $filename
	 */
	function set_source_filename( $filename ) {
		$this->source_filename = $filename;
	}

	/**
	 * Sets the block name ( eg core/template-part ).
	 *
	 * @param $blockName
	 */
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

	/**
	 * Pops a node off the tree.
	 */
	function pop_nodeName() {
		array_pop( $this->nodeName );
	}

	/**
	 * Returns a string representation of the node tree.
	 *
	 * @return string|string[]
	 */
	function get_nodeNameTree() {
		$tree = implode( '>',  $this->nodeName );
		$tree = str_replace( '#document>', '', $tree );
		return $tree;
	}

	/**
	 * Lists the HTML tags that are acceptable in rich text.
	 *
     * Rich text tags include: p, li, hn, dt, dd, th, td, figcaption, cite.
     * Acceptable tags are those that we'll extract as part of the rich text
     * and which the translator will have to decide whether or not to use.
     *
     * #text is the DOM document name for a text node.
     *
     * @return array of tags keyed by the tag.
	 */
	function setAcceptableTags() {
		$acceptableTags = [ 'br', 'b', 'em', 'i', 'a', 'img', 'span', 'code', 'kbd', '#text' ];
		$this->acceptableTags = array_flip( $acceptableTags );
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
        $this->narrator->narrate( "Text", $this->dom_doc->textContent );
	}

	/**
	 * Wraps the HTML in a standard structure.
     *
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
		return $this->dom_doc->saveHTML();
	}

	/**
	 * Extracts strings from the nodes.
	 *
	 * Recursive processing to extract / update strings.	 *
	 * Update is performed by the extending class DOM_String_Updater.
	 *
	 * @param DOMNode $node
	 */
	function extract_strings( DOMNode $node) {
		$this->narrator->nest();
		$this->set_nodeName( $node->nodeName );
		$this->narrator->narrate( "nodeName", $node->nodeName );
		$this->narrator->narrate( 'nodeType', $node->nodeType );
		$this->narrator->narrate( 'nodeValue', $node->nodeValue );

		$this->extract_strings_from_attributes( $node );
		// Trim the nodeValue. It may have leading or trailing blanks but we don't
		// want to include these in the string to be translated.
		// Are there languages where we shouldn't maintain leading or trailing blanks?
		$value = trim( $node->nodeValue );
		if ( !empty( $value ) ) {
			$this->narrator->narrate( 'String', $value );
			$this->add_string( $node, $value );
			//echo 'T:' . $node->nodeType;
			if ( !empty( $node->wholeText ) ) {
				$this->narrator->narrate( 'wholeText' , $node->wholeText );
			}
			$this->narrator->narrate( 'textContent', $node->textContent );

		} else {
		    $this->narrator->narrate( 'No string', '' );
			//echo PHP_EOL;
			//echo str_repeat( '   ', $nested );
			//echo 'N:' . $node->nodeName . ': ';
			//echo 'T:' . $node->nodeType;

		}
		if ( $node->haschildNodes() ) {
            $rich_text = $this->isRichText( $node );
            if ( $rich_text ) {
                $this->extractRichText( $node );
            } else {
			    foreach ( $node->childNodes as $child_node ) {
                    $this->narrator->narrate('child name', $child_node->nodeName);
                    $this->narrator->narrate('child type', $child_node->nodeType);
                    $this->extract_strings( $child_node );
                }
			}
		}

		$this->pop_nodeName();
		$this->narrator->denest();
	}

	function extract_strings_from_attributes( $node ) {

		if ( $node->hasAttributes() ) {
            $this->narrator->narrate( 'Attributes', $node->attributes->length );

			for ( $item = 0; $item < $node->attributes->length; $item++ ) {
				$attribute=$node->attributes->item( $item );
				$this->narrator->narrate( 'A#', $item );
				$this->narrator->narrate( 'AN', $attribute->name );
				$this->narrator->narrate( 'AV', $attribute->value );
				if ( $this->isAttrTranslatable( $attribute->name )) {
					$this->add_attribute_string( $attribute->name, $attribute->value );
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
	function setTranslatableAttrs() {
		$this->translatable = array_flip( [ 'label', 'alt', 'title' ] );
	}

	/**
	 * Gets the names of attributes which are not translatable.
	 *
	 * @TODO - decide which is better to list - translatable or not translatable.
	 * Is there any harm in having lots of terms that are not actually translatable
	 * in the .pot file?
	 */
	function setNotTranslatableAttrs() {
		$this->not_translatable = array_flip( ['class', 'className', 'slug', 'ID', 'ref', 'href', 'style',
			'aria-hidden', 'translate', 'theme'] );
	}

	/**
	 * Adds a translatable attr.
	 *
	 * Attributes are different from Text strings.
	 * We don't need to check the node type
	 * It'd be nice to record the attribute name
	 *
	 */
	function add_attribute_string( $attr, $text ) {
		if ( ! isset( $this->strings[ $text ] ) ) {
			$this->strings[ $text ]=$this->source_filename . ' ' . $this->blockName . ' ' . $this->get_nodeNameTree() . ' ' . $attr;
		}
	}

	/**
	 * Add a translatable string.
	 *
	 * Only extract the string if it's part of a text node
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

    /**
     * Determines if this node contains rich text.
     *
     * Rich text can be translated in one lump. The translator gets the context
     * to the inner tags, such as `<b>` and `<em>`. There may be more complex markup including
     * links. Here the translator will have to translate the attributes as well.
     * Let's hope the translator's tool is up to the job.
     *
     * If there's only one childNode and it's a #text node then it's not rich text.
     *
     * @param DOMnode $node
     * @return bool
     */
	function isRichText( DOMnode $node ) {
	    $rich_text = true;
	    $count = 0;
	    foreach ( $node->childNodes as $childNode ) {
	        if ( '#text' !== $childNode->nodeName ) {
                $acceptable = $this->isAcceptableInRichText($childNode);
                $count++;
                $rich_text &= $acceptable;
            }
        }
	    if ( 0 === $count ) {
	        $rich_text = false;
        }
	    $this->narrator->narrate( $node->nodeName, $rich_text);
	    return $rich_text;
    }


    /**
     * Returns true if the tag is acceptable in rich text.
     *
     * @param $childNode
     * @return bool
     */
    function isAcceptableInRichText( $childNode ) {
        $this->narrator->narrate( 'Tag?', $childNode->nodeName );
	    return isset( $this->acceptableTags[ $childNode->nodeName]);
    }

    /**
     * Extracts rich text as a single string.
     *
     * @param DOMnode $node
     * @return
     */
    function extractRichText( $node ) {
        $html = $this->getRichTextHTML( $node );
        $this->add_rich_text_string( $node, $html );
    }

    function getRichTextHTML( $node ) {
        $rtdoc = new DOMDocument();
        $new_node = $rtdoc->importNode( $node, true );
        $this->removeAttributes( $new_node );
        $rtdoc->appendChild( $new_node );
        $html = $rtdoc->saveHTML( $rtdoc->firstChild );
        $html = $this->trimOuterTag( $html, $node );
        $html = str_replace( "\n", "", $html );
        return $html;
    }

    /**
     * Adds rich text string for translation.
     *
     * @param DOMnode $node The node to be translated
     * @param string $html The rich text HTML
     */

    function add_rich_text_string( $node, $html ) {
        //$html = str_replace( "\n", "", $html );
        $this->add_string( null, $html );
    }

    /**
     * Trims the outer tag of the HTML.
     *
     * @param string $html The saved HTML for the rich text node.
     * @param DOMnode $node The rich text node.
     * @return string
     */
    function trimOuterTag( $html, $node ) {
        $startTag = '<'. $node->nodeName . '>';
        $tagLen = strlen( $startTag );
        $start = strpos( $html, $startTag );
        if ( 0 === $start ) {
            $html = substr( $html, $tagLen, - ( $tagLen+1 ) );
        }
        return $html;
    }

    /**
     * Removes the attributes from the outer tag.
     *
     * @param $node
     */
    function removeAttributes( $node ) {
        while ($node->hasAttributes()) {
            $attribute = $node->attributes->item(0);
            $node->removeAttributeNode( $attribute );
        }
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