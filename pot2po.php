<?php

/**
 * @package oik-i18n
 * @copyright (C) Copyright Bobbing Wide 2020
 *
 * Syntax:
 *  php pot2po.php component locale
 *  oikwp pot2po.php component locale
 *  wp i18n
 *
 * where:
 * component is the plugin/theme to be translated eg fizzie / oik
 * locale is the target locale eg fr_FR for French in France
 *
 * How to support WP-CLI?
 *
 * Replaces la_CY.php for languages other than en_GB and bb_BB.
 *
 */


if ( PHP_SAPI !== "cli" ) {
	die();
}

require_once 'class-potter.php';
require_once 'class-narrator.php';
require_once 'class-pot-to-po.php';
require_once 'class-translator.php';


$component =  oik_batch_query_value_from_argv( 1, 'fizzie' );
$locale = oik_batch_query_value_from_argv( 2, 'bb_BB' );

if ( 'bb_BB' === $locale ) {
	require_once 'class-translator-bb_BB.php';
}

if ( 'en_GB' === $locale ) {
	require_once 'class-translator-en_GB.php';
}

$pot2po = new Pot_To_Po();
$pot2po->setComponent( $component );
$pot2po->load_pot();
$pot2po->count_translatable_bytes();
$pot2po->setLocale( $locale );
$pot2po->preparePo();
$pot2po->load_component_locale();
$pot2po->load_translator();
$pot2po->control_translation();
$pot2po->do_msgfmt();
