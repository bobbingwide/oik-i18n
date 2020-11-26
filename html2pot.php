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
print_r( $html );

if ( 0 === strlen( $html) ) {
	echo "Invalid file: " . $filename;

	gob();
}

require_once 'class-stringer.php';

/**
 * Use Gutenberg to parse the content into individual blocks.
 * I've got a block recreation routine in oik-clone.
 */
$parser = new WP_Block_Parser();
$blocks = $parser->parse( $html );

print_r( $blocks );

$stringer = new Stringer();
$count = 0;
foreach ( $blocks as $block) {
	echo PHP_EOL;
	echo "Block: " . $count;
	echo PHP_EOL;
	$stringer->get_strings( $block );
	//print_r( $block );

}
