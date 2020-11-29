<?php

/**
 * Class Theme_Files_Updater
 * @package oik-i18n
 * @copyright (C) Copyright Bobbing Wide 2020
 */


class Theme_Files_Updater extends Theme_Files {

	function __construct() {
		parent::__construct();
	}

	/**
	 * Implements the translate logic for block attributes
	 *
	 * @TODO Implement recursion for nested attributes.
	 *
	 * @param $block
	 * @param $stringer
	 */
	function extract_strings_from_block_attributes( $block, $stringer ) {
		//print_r( $block );
		//print_r( $block['attrs'] );
		$stringer->set_blockName( $block['blockName'] );
		foreach ( $block['attrs'] as $key=>$value ) {
			if ( $stringer->isAttrTranslatable( $key ) ) {
				//$stringer->add_attribute_string( $key, $value );
				$block['attrs'][$key] = $stringer->translate_string( $value );
			}
		}
	}

	/**
	 * Processes a single theme file.
	 *
	 */
	function process_file( $filename, $stringer ) {
		parent::process_file( $filename, $stringer );
		$this->save_file( $filename, $stringer );
	}

/**
 *  Reconstitute the file from the blocks and html
 */
	function save_file( $filename, $stringer) {
		echo "Saving file:" . $filename;
		echo PHP_EOL;
		$blocks_reformer = new Blocks_Reformer();
		$output = $blocks_reformer->reform_blocks( $this->blocks, $stringer );
		echo $output;


	}

}