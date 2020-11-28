<?php


/**
 * Prototype converting html to the language country specific locale.
 */
require_once 'class-dom-stringer.php';
require_once 'class-dom-string-updater.php';
require_once 'theme-files.php';

$html = '<p class="fred">Hello World<b>!</b></p>';


$stringer = new DOM_string_updater();
$stringer->loadHTML( $html );
$stringer->translate();
$html_after = $stringer->saveHTML();
echo 'HTML:';
echo PHP_EOL;
echo $html_after;
echo '!';


 