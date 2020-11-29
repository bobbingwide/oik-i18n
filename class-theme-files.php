<?php

/**
 * Class Theme_Files
 * @package oik-i18n
 * @copyright (C) Copyright Bobbing Wide 2020
 *
 * Implements the base class to process all the template and template part theme files for a Full Site Editing theme
 * in order to translate the text in the HTML files and blocks into another locale ( la_CY ).
 *
 * This is extended by Theme_Files_Updater which
 * applies the translations when processing:
 * - add_string
 * - add_attribute_string
 *
 * After translation the new theme files are written to the theme's languages folder
 * in a subdirectory for the chosen locale - la_CY
 * where la is the language code eg en
 * and CY is the country code eg GB
 *
 * For the bb_BB test language the folders would be:
 *
 * - theme/bb_BB/block-templates
 * - theme/bb_BB/block-template-parts
 *
 */

class Theme_Files {
	/**
	 * @var string $theme
	 */
	protected $theme;

	protected $blocks = null;

	protected $locale = null;

	function __construct() {
	}

	function set_locale( $locale) {
		$this->locale = $locale;
	}

	function get_theme_dir( $theme ) {
		$dir      =get_stylesheet_directory();
		$theme_dir=dirname( $dir ) . '/' . $theme;
		echo "Theme dir: " .  $theme_dir . PHP_EOL;
		return $theme_dir;
	}

	/**
	 * Loads the text domain for the FSE theme's HTML files.
	 *
	 * @param $theme
	 */
	function load_text_domain( $theme ) {
		$path = $this->get_theme_dir( $theme );
		$path .= '/languages/';
		$path .= $theme;
		$path .= '-';
		$path .= $this->locale;
		$path .= '.mo';  // For the time being I don't need the -FSE suffix.
		//$path = oik_path( "languages/$plugin-$locale.mo", $plugin );
		$result = load_textdomain( $this->locale, $path );
		echo "Result:" . $result;
		if ( false === $result ) {
			echo "Failed to load: " . $path;
			gob();
		}
	}

	function list_all_templates_and_parts( $theme ) {
		$this->theme = $theme;
		$theme_dir     = $this->get_theme_dir( $theme );
		$template_files=glob( $theme_dir . '/block-templates/*.html' );
		$template_parts=glob( $theme_dir . '/block-template-parts/*.html' );
		//print_r( $template_files );
		//print_r( $template_parts );
		$files=array_merge( $template_files, $template_parts );

		return $files;
	}

	/**
	 * Processes all the theme files.
	 *
	 * @param $files
	 * @param $stringer
	 */
	function process_theme_files( $files, $stringer ) {
		echo "Processing:" . count( $files );
		echo PHP_EOL;
		$count=0;
		foreach ( $files as $filename ) {
			$count ++;
			echo $count;
			echo ' ';
			echo $filename;
			echo PHP_EOL;
			$this->process_file( $filename, $stringer );
		}
	}

	function get_blocks( $filename ) {
		$html    =file_get_contents( $filename );
		$parser  =new WP_Block_Parser();
		$this->blocks  =$parser->parse( $html );
	}

	/**
	 * Process a single .html file to extract the translatable strings.
	 *
	 * Uses Gutenberg to parse the content into individual blocks.
	 * PS. I've got a block recreation routine in oik-clone.
	 *
	 * @param $filename
	 */
	function process_file( $filename, $stringer ) {
		$this->get_blocks( $filename );
		$count   =0;
		$basename=basename( $filename );
		echo $basename;
		echo PHP_EOL;

		$stringer->set_source_filename( $basename );
		$this->process_blocks( $this->blocks, $stringer );
		print_r( $this->blocks);



	}

	/**
	 * Recursively process inner blocks.
	 *
	 * Assume this doesn't go infinitely recursive since we're not loading other files.
	 *
	 * @param $block
	 */
	function process_blocks( &$blocks, $stringer ) {
		static $count=0;
		foreach ( $blocks as $key =>  $block ) {
			//process_block( $block, $stringer );
			$count ++;
			echo PHP_EOL;
			echo "Block: " . $count;
			echo $block['blockName'];
			echo PHP_EOL;

			$this->extract_strings_from_block_attributes( $block, $stringer );

			if ( ! empty( $block['innerBlocks'] ) ) {
				$this->process_blocks( $block['innerBlocks'], $stringer );
			} else {

				if ( ! empty( $block['innerHTML'] ) ) {
					$innerHTML=$stringer->get_strings( $block['blockName'], $block['innerHTML'] );
					echo $innerHTML;
					if ( count( $block['innerContent'] ) > 1 ) {
						print_r( $block );

						//print_r( $block['innerContent']);
						gob();
					}
					$blocks[ $key ]['innerContent'][0]=$innerHTML;
				}
			}
			//if ( ! empty( $block['innerBlocks'] ) ) {
			//	$this->process_blocks( $block['innerBlocks'], $stringer );
			//}
		}
	}

	/**
	 * Extracts strings for translatable attributes.
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
				$stringer->add_attribute_string( $key, $value );
			}
		}
	}

	/** See the class
	 * function isAttrTranslatable( $key ) {
	 * $translatable = array_flip( [ 'label' ] );
	 * $not_translatable = array_flip( ['class', 'className', 'slug', 'ID', 'ref'] );
	 * $isTranslatable = isset( $translatable[ $key ] ) ? true : false;
	 * if ( isset( $not_translatable[ $key] ) ) {
	 * $isTranslatable = false;
	 * }
	 * return $isTranslatable;
	 * }
	 */

	function write_pot_file( $theme, $stringer ) {
		$strings=$stringer->get_all_strings();
		$potter =new Potter();
		$potter->set_pot_filename( $theme . '.pot' );
		$potter->set_project( $theme );
		$output=$potter->write_header();
		$output.=$potter->write_strings( $strings );
		//echo $output;
		$this->replace_pot_file( $theme, $output );

	}

	function replace_pot_file( $theme, $contents ) {
		$filename= $this->get_theme_dir( $theme );
		$filename.='/languages/';
		$filename.=$theme;
		//$filename.='-FSE';  // Append a suffix so we know these are FSE strings
		$filename.='.pot';
		echo "Writing: ";
		echo $filename;
		echo PHP_EOL;
		echo $contents;
		echo PHP_EOL;
		$written=file_put_contents( $filename, $contents );
		if ( $written !== strlen( $contents ) ) {
			echo "File was badly written";
			echo "Wrote:" . $written;
			echo "Expected:" . strlen( $contents );
		}
	}
}

