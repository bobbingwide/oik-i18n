<?php

/** @copyright (C) Copyright Bobbing Wide 2020
 * @package oik-i18n
                                                   *
 * oik batch routine to Internationalize / localize HTML templates and template parts
 *
 * Syntax:
 * `
 * cd [path]/wp-content/plugins/oik-i18n
 * oikwp html2pot.php type filename.html locale
 *
 * where:
 * type is is template / part
 * filename.html is the file name
 * locale is the target locale: en_GB, bb_BB, fr_FR
 *
 * Output will be written to a languages folder
 */

/**
 * Stage 1. Find all the translatable strings in an .html file
 * Stage 2. Extract from all the theme's .html files to a .pot file
 * Stage 3. Translate into local language
 * Stage 4. Reparse, apply the target language and save in the new locale.
 *
 * This is the very beginning of Stage 1.
 *
 */

$filename = 'test.html';

$html = file_get_contents( $filename);
//print_r( $html );

if ( 0 === strlen( $html) ) {
	echo "Invalid file: " . $filename;
	exit();

}
require_once 'class-stringer.php';
require_once 'class-potter.php';

/**
 * Use Gutenberg to parse the content into individual blocks.
 * I've got a block recreation routine in oik-clone.
 */
$parser = new WP_Block_Parser();
$blocks = $parser->parse( $html );

//print_r( $blocks );

$stringer = new Stringer();
$count = 0;
$stringer->set_source_filename( $filename );
foreach ( $blocks as $block) {
	$count++;
	echo PHP_EOL;
	echo "Block: " . $count;
	echo PHP_EOL;
	print_r( $block );
	if ( !empty( $block['innerHTML'] ) ) {
		$strings = $stringer->get_strings( $block['innerHTML'] );
		//print_r( $block );
		//$strings =
		//$potter->write_strings( $strings );
	}
}

$strings = $stringer->get_all_strings();
$potter = new Potter();
$potter->set_pot_filename( 'fizzie.pot');
$potter->set_project( 'fizzie');
$output = $potter->write_header();
$output .= $potter->write_strings( $strings );
echo $output;

