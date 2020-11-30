<?php

/**
 * Class Potter
 * @package oik-i18n
 * @copyright (C) Bobbing Wide 2020
 */

class Potter {
	public $pot_filename;
	public $source_filename;
	public $project;

	function __construct() {
	}

	function set_pot_filename( $filename ) {
		$this->pot_filename = $filename;
	}

	function set_project( $project ) {
		$this->project = $project;
	}

	function write_strings( $strings ) {
		$output = '';
		foreach ( $strings as $string => $filename ) {
			$output .= $this->write_fileline( $filename );
			$output .= $this->write_string( $string );
			$output .= $this->write_blank();
		}
		return $output;
	}

	function write_fileline( $filename ) {
		$output = '#: ' . $filename;
		$output .= PHP_EOL;
		return $output;
	}

	/**
	 * Writes the string.
	 *
	 * Double quotes have to be escaped but not single.
	 * What about new lines in the middle of the content?
	 *
	 * @param $string
	 * @return string
	 */
	function write_string( $string ) {
		$string = str_replace( '"', '\"', $string );
		$output = 'msgid "' . $string . '"';
		$output .= PHP_EOL;
		$output .= 'msgstr ""';
		return $output;
	}

	function write_blank() {
		$output = PHP_EOL;
		$output .= '';
		$output .= PHP_EOL;
		return $output;
	}



	function write_header() {
		$output = [];
		$output[] = '# Copyright (C) 2020';
		$output[] = '# This file is distributed under the same licence as WordPress';
		$output[] = $this->write_string('');
		$output[] = "\"Project-Id-Version: {$this->project}\\n\"";
		$output[] = "\"Report-Msgid-Bugs-To: http://wordpress.org/tag/{$this->project}\\n\"";
		$output[] = "\"POT-Creation-Date: ".  date('Y-m-d h:i:s') ."+00:00\\n\"";
		$output[] = "\"MIME-Version: 1.0\\n\"";
		$output[] = "\"Content-Type: text/plain; charset=UTF-8\\n\"";
		$output[] = "\"Content-Transfer-Encoding: 8bit\\n\"";
		$output[] = "\"PO-Revision-Date: 2020-MO-DA HO:MI+ZONE\\n\"";
		$output[] = "\"Last-Translator: FULL NAME <EMAIL@ADDRESS>\\n\"";
		$output[] = "\"Language-Team: LANGUAGE <LL@li.org>\\n\"";
		$output[] = '';
		$output = implode( PHP_EOL, $output);
		$output .= PHP_EOL;
		return $output;
	}
}

/*
# Copyright (C) 2020 oik
# This file is distributed under the same license as the oik package.
msgid ""
msgstr ""
"Project-Id-Version: oik 4.1.0\n"
"Report-Msgid-Bugs-To: http://wordpress.org/tag/oik\n"
"POT-Creation-Date: 2020-09-04 13:46:15+00:00\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"PO-Revision-Date: 2020-MO-DA HO:MI+ZONE\n"
"Last-Translator: FULL NAME <EMAIL@ADDRESS>\n"
"Language-Team: LANGUAGE <LL@li.org>\n"

#: admin/class-bw-list-table.php:153
msgid "List View"
msgstr ""

#: admin/class-bw-list-table.php:154
msgid "Excerpt View"
msgstr ""

*/