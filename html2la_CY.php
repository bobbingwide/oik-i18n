<?php
/**
 * oik batch routine to Internationalize / localize HTML templates and template parts
 *                                                                              *
 * Syntax:
 * `
 * cd [path]/wp-content/plugins/oik-i18n
 * oikwp html2la_CY.php theme locale
 *
 * where:
 * - theme - is the theme to process. default fizzie
 * - locale is the target locale: en_GB, bb_BB, fr_FR - default bb_BB
 *
 * Output will be written to the /languages/locale folder in the selected theme
 */

/**
 * Stage 1. Find all the translatable strings in an .html file
 * Stage 2. Extract translatable strings from all of the theme's `.html` files to a `.pot` file.
 * Stage 3. Translate into local language
 * Stage 4. Reparse, apply the target language, and save in the new locale.
 * Stage 5. WordPress loads the theme's files from the languages/la_CY folder.
 *
 * This is the first prototype of Stage 4.
 *
 */

if ( PHP_SAPI !== "cli" ) {
	die();
}
require_once 'class-dom-stringer.php';
require_once 'class-dom-string-updater.php';
require_once 'class-theme-files.php';
require_once 'class-theme-files-updater.php';
require_once 'class-blocks-reformer.php';

$theme = oik_batch_query_value_from_argv( 1, 'fizzie' );
$locale = oik_batch_query_value_from_argv( 2, 'bb_BB' );

$theme_files = new Theme_Files_Updater();
$theme_files->set_locale( $locale );
$theme_files->load_text_domain( $theme );

$translated = __( '404.html', $locale );
echo $translated;
echo PHP_EOL;


$stringer = new DOM_string_updater();
$stringer->set_locale( $locale );
//$stringer->set_theme( $theme );
$files = $theme_files->list_all_templates_and_parts( $theme );
$theme_files->process_theme_files( $files, $stringer );



 