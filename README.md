# oik-i18n 
![banner](https://raw.githubusercontent.com/bobbingwide/oik-i18n/master/assets/oik-i18n-banner-772x250.jpg)
* Contributors: bobbingwide
* Donate link: http://www.oik-plugins.com/oik/oik-donate/
* Tags: i18n, internationalization, localization, l10n, oik, bb_BB, bbboing
* Requires at least: 4.2
* Tested up to: 5.5.1
* Stable tag: 0.3.0
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html

## Description 
Internationalization for the oik suite of plugins

The base domain.pot file(s) are named to match the plugin. e.g. oik.pot, oik-i18n.pot

Translators build Machine Object (.mo) files where the filenames are expected to be in the form:


domain-la_CY.mo

where
domain is the plugin domain ( e.g. "oik" or "oik-i18n" )
la is the 2 digit abbreviation of the language (e.g. en, fr, de, it )
CY - is the 2 digit code for the country ( e.g. GB, FR, DE, IT, CH, CA, US )

So the French version for the "oik" plugin/domain would be oik-fr_FR.mo


POT files
After the strings are marked in the source files, a utility routine ( sometimes called xgettext ) is used to extract the original strings and to build a template translation POT file.
Here is an example POT file entry:

* #: wp-admin/admin-header.php:49
msgid "Sign Out"
msgstr ""

Here is another for singular and plural


Here is another where there's more than one line in the string

to be completed.



## Installation 
This is not really necessary, as the logic is currently intended for use in a 'batch build' environment.

1. Upload the contents of the oik-i18n plugin to the `/wp-content/plugins/oik-i18n' directory
1. Activate the oik-i18n plugin through the 'Plugins' menu in WordPress

## Frequently Asked Questions 
# Who's written about i18n? 

http://ottopress.com/2012/internationalization-youre-probably-doing-it-wrong/

# What are the APIs 

load_textdomain
http://codex.wordpress.org/Function_Reference/load_textdomain

load_plugin_textdomain
load_theme_textdomain

# What is bb_BB? 
We use bb_BB for a fictitious test language and country

bb = bbboing language - an automatic translation of the existing language with character movement and vowel replacement
BB = Bbboingland - the country where bbboing is spoken

# What is la_CY? 
This is the library that attempts to update the language files for the plugin.

la = language
CY = CountrY

It's invoked by the oik-zip plugin as part of the l10n processing.

# What do the .php files do? 

oik-i18n.php - plugin file
l10n.php -
la_CY.php -
bb_BB.php -


# What is makeoik.php? 
makeoik.php is a PHP routine based on WordPress's makepot.php

It extends the list of functions that accept Internationalized strings
so that they can be found and included in the master .pot file for a plugin.
This .pot file is then passed to the translators ( including the automated routines )
for creation of the .po files... from which the .mo files are built.




# How do we run it? 

Here we use oik-batch to invoke the l10n routine - localize a plugin
```
php c:\apache\htdocs\wordpress\wp-content\plugins\oik-batch\oik-batch.php c:\apache\htdocs\wordpress\wp-content\plugins\oik-i18n\l10n.php %*
```
if you have a 'batch' file then you should be able to use

batch oik-i18n\l10n

# What programs are needed? 

msgfmt - to convert .po to .mo files


# How do I Internationalize my code? 

For simple text translation
1. Find the text
2. Change the API to an i18n version
3. OR wrap the text in an i18n version
If there are variables then use sprintf and %n$s  e.g. %1$s to replace them
Remembers to wrap the text in single quotes when you do this.

Examples:
e( "Hello" ); -> bwt( "Hello" );
e( "<br />" ); -> unchanged
e( "Hello $fred" ); -> e( sprintf( __( 'Hello %1$s', "textdomain"), $fred ) );
OR possibly:
bwt( "Hello" );
e( " $fred" );
it depends on the usage.


# What locales does WordPress support? 

See [Translation Teams](https://make.wordpress.org/polyglots/teams)


Notes:
* The PHP locale is normally set as "la-CY" e.g. fr-FR.
* The country is sometimes omitted where the language the home country.
* PHP uses a hyphen
* WordPress uses an underscore.
* GlotPress uses a hyphen and all lower case (e.g. la-cy ),

Don't get confused by the WordPress method, which effectively ignores the PHP locale.



# Can I get support? 
Yes - see above

## Screenshots 
1. oik-i18n in action

## Upgrade Notice 
# 0.3.0 
Now supports plugins which deliver blocks built with wp-scripts

# 0.2 
* Still not for general release.

# 0.1 
* Not for general release.

## Changelog 
# 0.3.0 
* Changed: Added support for Internationalization and localization of block plugins,https://github.com/bobbingwide/oik-i18n/issues/6
* Tested: With WordPress 5.5.1
* Tested: With PHP 7.4


# 0.2 
* Updated l10n.php to create the bb_BB locale version as part of oik-zip.php
* Other changes will be discovered next time round

# 0.1 

* First version


## Further reading 
If you want to read more about the oik plugins then please visit the
[oik plugin](https://www.oik-plugins.com/oik)
**"the oik plugin - for often included key-information"**

