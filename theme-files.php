<?php

function get_theme_dir( $theme ) {
	$dir = get_stylesheet_directory();
	$theme_dir = dirname( $dir ) . '/' . $theme;
	echo $theme_dir;

	return $theme_dir;
}

function list_all_templates_and_parts( $theme ) {
	$theme_dir = get_theme_dir( $theme );
	$template_files = glob( $theme_dir . '/block-templates/*.html' );
	$template_parts = glob( $theme_dir. '/block-template-parts/*.html' );
	//print_r( $template_files );
	//print_r( $template_parts );
	$files = array_merge( $template_files, $template_parts);
	return $files;
}

/**
 * Processes all the theme files.
 *
 * @param $files
 * @param $stringer
 */
function process_theme_files( $files, $stringer ) {
	echo "Processing:" . count( $files);
	echo PHP_EOL;
	$count = 0;
	foreach ( $files as $filename ) {
		$count++;
		echo $count;
		echo ' ';
		echo $filename;
		echo PHP_EOL;
		process_file( $filename, $stringer );
	}
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
	$html = file_get_contents( $filename );
	$parser = new WP_Block_Parser();
	$blocks = $parser->parse( $html );
	$count   =0;
	$basename=basename( $filename );
	echo $basename;
	echo PHP_EOL;

	$stringer->set_source_filename( $basename );
	process_blocks( $blocks, $stringer );

}

/**
 * Recursively process inner blocks.
 *
 * Assume this doesn't go recursive since we're not loading other files.
 *
 * @param $block
 */
function process_blocks( $blocks, $stringer ) {
	static $count = 0;
	foreach ( $blocks as $block ) {
		//process_block( $block, $stringer );
		$count ++;
		echo PHP_EOL;
		echo "Block: " . $count;
		echo $block['blockName'];
		echo PHP_EOL;

		extract_strings_from_block_attributes( $block, $stringer );

		if ( ! empty( $block['innerHTML'] ) ) {
			$strings = $stringer->get_strings( $block['blockName'], $block['innerHTML'] );
		}
		if ( !empty( $block['innerBlocks'] ) ) {
			process_blocks( $block['innerBlocks'], $stringer );
		}
	}
}

/**
 * Extracts strings for translatable attributes.
 *
 * @TODO Implement recursion for nested attributes.
 * @param $block
 * @param $stringer
 */
function extract_strings_from_block_attributes( $block, $stringer ) {
	//print_r( $block );
	//print_r( $block['attrs'] );
	$stringer->set_blockName( $block['blockName'] );
	foreach ( $block['attrs'] as $key => $value ) {
		if ( $stringer->isAttrTranslatable( $key ) ) {
			$stringer->add_attribute_string( $key, $value );
		}
	}
}

/** See the class
function isAttrTranslatable( $key ) {
	$translatable = array_flip( [ 'label' ] );
	$not_translatable = array_flip( ['class', 'className', 'slug', 'ID', 'ref'] );
	$isTranslatable = isset( $translatable[ $key ] ) ? true : false;
	if ( isset( $not_translatable[ $key] ) ) {
		$isTranslatable = false;
	}
	return $isTranslatable;
}
 */

function write_pot_file( $theme, $stringer ) {
	$strings = $stringer->get_all_strings();
	$potter = new Potter();
	$potter->set_pot_filename( $theme . '.pot' );
	$potter->set_project( $theme );
	$output = $potter->write_header();
	$output .= $potter->write_strings( $strings );
	//echo $output;
	replace_pot_file( $theme, $output );

}

function replace_pot_file( $theme, $contents ) {
	$filename = get_theme_dir( $theme );
	$filename .= '/languages/' ;
	$filename .= $theme;
	$filename .= '-FSE';  // Append a suffix so we know these are FSE strings
	$filename .= '.pot';
	echo "Writing: " ;
	echo $filename;
	echo PHP_EOL;
	echo $contents;
	echo PHP_EOL;
	$written = file_put_contents(  $filename, $contents );
	if ( $written !== strlen( $contents ) ) {
		echo "File was badly written";
		echo "Wrote:" . $written;
		echo "Expected:" . strlen( $contents );
	}


}

