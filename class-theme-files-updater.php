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
	function extract_strings_from_block_attributes( &$block, $stringer ) {
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
     *  Reconstitutes the file from the blocks and html.
     */
	function save_file( $filename, $stringer) {
		echo "Saving file:" . $filename;
		echo PHP_EOL;
		$blocks_reformer = new Blocks_Reformer();
		$output = $blocks_reformer->reform_blocks( $this->blocks, $stringer );
		echo $output;
		echo PHP_EOL;
		$this->write_locale_file( $filename, $output );
	}

	/**
	 * Returns the locale directory.
	 *
	 * Without a trailing slash.
	 * @return string
	 */
	function get_locale_dir() {
		$locale_dir = $this->get_theme_dir( $this->theme );
		$locale_dir .= '/languages/';
		$locale_dir .= $this->locale;
		return $locale_dir;
	}

	function get_locale_filepath( $filename ) {
		echo $filename . PHP_EOL;
		$filepath = $this->get_locale_dir();

		$filepath .= '/';
		$filepath .= basename( dirname( $filename ) );

		//if ( !file_exists( $filepath )) {
		//	echo "Creating locale directory: " . $filepath . PHP_EOL;
		wp_mkdir_p( $filepath );

		$filepath .= '/';
		$filepath .= basename( $filename );
		echo "Locale file: ";
		echo $filepath;
		echo PHP_EOL;
		return $filepath;
	}

	/**
	 * Writes the localized file.
	 *
	 * @param $filename
	 * @param $contents
	 */
	function write_locale_file( $filename, $contents ) {
		$filepath = $this->get_locale_filepath( $filename );
		$written = file_put_contents( $filepath, $contents );
		if ( $written !== strlen( $contents ) ) {
			echo "File was badly written";
			echo "Wrote:" . $written;
			echo "Expected:" . strlen( $contents );
			gob();
		}
	}

}