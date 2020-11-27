<?php

/** @copyright (C) Copyright Bobbing Wide 2020
 * @package oik-i18n
                                                   *
 * oik batch routine to Internationalize / localize HTML templates and template parts
 *
 * Syntax:
 * `
 * cd [path]/wp-content/plugins/oik-i18n
 * oikwp html2pot.php theme locale
 *
 * where:
 * - theme - is the theme to process. default fizzie
 * - locale is the target locale: en_GB, bb_BB, fr_FR
 *
 * Output will be written to a /languages folder in the selected theme
 */

/**
 * Stage 1. Find all the translatable strings in an .html file
 * Stage 2. Extract translatable strings from all of the theme's `.html` files to a `.pot` file.
 * Stage 3. Translate into local language
 * Stage 4. Reparse, apply the target language, and save in the new locale.
 *
 * This is the first prototype of Stages 1 and 2.
 *
 */

if ( PHP_SAPI !== "cli" ) {
	die();
}

require_once 'class-stringer.php';
require_once 'class-potter.php';
require_once 'theme-files.php';

$theme = oik_batch_query_value_from_argv( 1, 'fizzie' );
$files = list_all_templates_and_parts( $theme );
$stringer = new Stringer();
process_theme_files( $files, $stringer );
write_pot_file( $theme, $stringer );

