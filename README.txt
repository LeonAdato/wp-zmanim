=== WP_Zmanim ===
Contributors: @adatosystems
Donate link: https://ko-fi.com/leonadato
Tags: Jewish, Time, Zmanim, Holidays, Shabbat, Prayer, Tefillot, Davening
Requires at least: 3.0.1
Tested up to: 6.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Choose from a variety of shitot (opinions), use shortcodes (with options) to display those times in posts, pages, and widgets. With gratitude to Eliyahu Hershfeld, Zachary Weixelbaum, and Elyahu Jacobi.

== Description ==
This plugin presents an admin panel with dropdowns to select from a variety of shitot (opinions) on time calculation, and provides shortcodes (with options) to display those times in posts, pages, and widgets.

It would not be possible without the amazing [kosher java](https://github.com/KosherJava/zmanim) library by [Eliyahu Hershveld](https://kosherjava.com/), which has been [ported to PHP](https://github.com/zachweix/PhpZmanim) by the incredible [Zachary Weixelbaum](https://www.linkedin.com/in/zachweix/).  

## Introduction
As mentioned, this plugin allows the site administrator to select from a range of shitot (halachic opinions) which will use the [kosher java](https://github.com/KosherJava/zmanim) library by [Eliyahu Hershveld](https://kosherjava.com/) to determine the correct time calculations. 

There are two primary areas of use:
 - the Admin panel, where you set various options
 - Shortcodes, which you can use on pages, posts, or even widgets to display various times. 

 Both of those areas are described in detail below.

## Usage - the Admin Panel
The admin panel lets you set some basic information about the website - location, name, etc - and also select from a list of pre-set time calculation systems.

For the drop-down selections (Alot Hashachar, Misheyakir, etc), there is a detailed explanation of each option at the bottom of this README file. 

Details information on each setting is:

 - Time Zone - The [IANA Time Zone](https://en.wikipedia.org/wiki/List_of_tz_database_time_zones) formatted string for the time zone of the website. All time calculations will take this time zone into consideration. 
 - Latitude/Longitude - The standard latitude and longitude for this location.
 - Location Name - Any name you want to give.
 - Candle Offset - Time, in minues, before shkia that candles will be lit for Shabbat
 - Molad Text - The text which will be used as the title for the section announcing the Molad. This can include HTML formatting.
 - Rosh Chodesh Text - The text which will be used as the title for the section announcing Rosh Chodesh. This can include HTML formatting.
 - Alot Hashachar - "daybreak", or the time when some light is visible. 
 - Misheyakir - the time when one can put on Tallit and Tefillin.
 - Sha'ah Zmanim - A halachic hour, or 1/12 of the available daylight time.
 - Sof Zman Kria Shema - the latest time one can say Shema.
 - Sof Zman Tefilla - the latest time one can pray Shacharit.
 - Mincha Gedola - The earliest time one can pray Mincha
 - Mincha Ketana - by some authorities, the preferable time to pray Mincha
 - Plag HaMincha - the mid-point between Mincha Ketana and sunset, and and used for various decisions about when to light candles, pray on Friday, etc.
 - Start of Bain HaShmashot - "Twilight", or the time between Shkia (sunset) and Tzait haKochavim (nightfall)
 - Tzait haKochavim - "Nightfall", or the starting time for varios mitzvot that are to be performed at night.

## Usage - the shortcodes
Shortcodes can be used in several locations of the WordPress system including Posts, Pages, and Widgets. Broadly speaking, they consist of a keyword surrounded by square brackets:

`[zman_alot]`

For this plugin, all shortcodes begin with the `zman_` prefix. 

Most of the shortcodes in this plugin also take optional parameters after the main keyword. For example, if one wanted to display the Parsha in Hebrew rather than English, one could use the `lang=` shortcode:

`[zman_parsha lang="hebrew"]` 

### Shortcode Options

The parameters which are available across shortcodes are:
 - date: Indicates relative or specific date you want. Valid options include:
     - today - show the time for the current date (when the page / post is being viewed)
     - tomorrow - show the time for the day after the current date
     - sunday, monday, tuesday, etc - show the time for the next upcoming weekday indicated.
     - (actual date) - Any reasonable date format (2023-01-20, Jan 20, 2023, 1/20/23, etc.) will show the time for that specific date.

Example: `[zman_alot date="Wednesday"]`
Example: `[zman_alot date="2023-01-20"]`

Notes: Leaving this option blank defaults to "today". 

 - offset: A number of minutes (including fractions - i.e. 10.5), either positive or negative, to adjust the time by.

Example: `[zman_alot offset=-10]`
Example: `[zman_alot offset=20]`
Example: `[zman_alot offset=+20]`

Notes: Leaving this option blank defaults to 0. If no sign (+ or -) is given, the number is assumed to be positive. 

 - dateformat: this takes PHP codes to format the output. [Here's a nice tutorial](https://www.tutorialrepublic.com/php-tutorial/php-date-and-time.php) on those codes. And [here is the full PHP page](https://www.php.net/manual/en/datetime.formats.php) on the topic.

Example: `[zman_alot dateformat="m/d/Y h:i:s a"]`

Notes: Leaving this option blank defaults to "h:i a", or hh:mm am/pm (i.e.: 10:32 pm). 

- lang - Displays the information English (the default) or Hebrew. 

Example: `[zman_parsha lang="hebrew"]`

Notes: Default is "English.". This option only works for Rosh Chosdesh, the Molad, and the Parsha.

- month - Indicates a specific Hebrew month. Valid options include:
     - this - show Rosh Chodesh for the end of this month, beginning of next month. 
     - next - show Rosh Chodesh for the end of the month after this. 
     - You can indicate which month by number (With Nissan = 1)

Example: `[zman_chodesh month=1 ]`

Notes: This option only works for Rosh Chodesh and the Molad.

Because Rosh Chodesh is intrinsically interested in knowing when the UPCOMING date(s) will be, "this" and "next" operate counter-intuitively for some. The way to think about it is that you either want to know "the end of THIS/NEXT month", rather than the date when Rosh Chodesh WAS (previously) for this month.

Also note that if you specify a year, you may get Rosh Chodesh for a date which has already passed (i.e.: If you ask for month=4 (Tammuz), and it's already Elul (month 6))

- year - indicates the Hebrew year you'd like for this calculation. It uses the same options as "month", described above. 

Example: `[zman_chodesh year="next"]`

Notes: As with "month", this option is only valid for Rosh Chodesh and the Molad. 

### Shortcodes
Here is a list of available shortcodes, with description if needed, along *(with valid parameters in italics)*

 - zman_location - Displays the location, as defined on the admin page. *(No options)*
 - zman_lat - Shows the latitude, as defined on the admin page. *(No options)*
 - zman_long - Shows the longitude, as defined on the admin page. *(No options)*
 - zman_tzone - Shows the Time Zone, as defined on the admin page. *(No options)*
 - zman_sunrise - Shows the time of nautical sunrise, without elevation, for the given location. *(date, offset, dateformat, lang)*
 - zman_sunset - Shows the time of shkia (nautical sunset, without elevation) for the given location. *(date, offset, dateformat, lang)*
 - zman_candles - Shows the time for Shabbat candles, which is sunset/shkia with the number of minutes indicated on the admin page subtracted. *(date, offset, dateformat, lang)*
 - zman_alot - *(date, offset, dateformat, lang)*
 - zman_misheyakir - *(date, offset, dateformat, lang)*
 - zman_shaah - A halachic hour, or 1/12 of the available daylight, as calculated based on the shita selected in the drop-down on the admin page. *(date, dateformat, lang)*
 - zman_shema - *(date, offset, dateformat, lang)*
 - zman_tefilla - *(date, offset, dateformat, lang)*
 - zman_gedola - *(date, offset, dateformat, lang)*
 - zman_ketana - *(date, offset, dateformat, lang)*
 - zman_plag - *(date, offset, dateformat, lang)*
 - zman_bain - *(date, offset, dateformat, lang)*
 - zman_tzait - *(date, offset, dateformat, lang)*
 - zman_chodesh - The day(s) for the indicated Rosh Chodesh. If the date indicated is "today" or "next", text ONLY be visible if this week/next week is Rosh Chodesh. *(date, month, year, dateformat, lang)*
 - zman_molad - The day/time for the indicated Molad. If the date indicated is "today" or "next", text ONLY be visible if this week/next week is the Molad. *(date, month, year, dateformat, lang)*

## Zman Calculation Details
 - Alot Hashachar
 - Misheyakir
 - Sha'ah Zmanim
 - Sof Zman Kria Shema
 - Sof Zman Tefilla
 - Mincha Gedola
 - Mincha Ketana
 - Plat HaMincha
 - Start of Bain HaShmashot
 - Tzait haKochavim




== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= A question that someone might have =

An answer to that question.

= What about foo bar? =

Answer to foo bar dilemma.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the /assets directory or the directory that contains the stable readme.txt (tags or trunk). Screenshots in the /assets
directory take precedence. For example, `/assets/screenshot-1.png` would win over `/tags/4.3/screenshot-1.png`
(or jpg, jpeg, gif).
2. This is the second screen shot

== Changelog ==

= 1.0 =
* A change since the previous version.


== Upgrade Notice ==

= 1.0 =
Upgrade notices describe the reason a user should upgrade.  No more than 300 characters.



== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.
