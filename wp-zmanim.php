<?php

/**
 *
 * @link              https://adatosystems.com
 * @since             1.0.0
 * @package           Wp_Zmanim
 *
 * @wordpress-plugin
 * Plugin Name:       WP Zmanim
 * Plugin URI:        https://adatosystems.com/wp-zmanim
 * Description:       Choose from a variety of shitot (opinions), use shortcodes (with options) to display those times in posts, pages, and widgets. With gratitude to Eliyahu Hershfeld, Zachary Weixelbaum, and Elyahu Jacobi.
 * Version:           1.0.0
 * Author:            Leon Adato
 * Author URI:        https://adatosystems.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-zmanim
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}


require 'vendor/autoload.php';
use PhpZmanim\Zmanim;
use Carbon\Carbon;
use PhpZmanim\HebrewCalendar\JewishCalendar;
use PhpZmanim\HebrewCalendar\JewishDate;
use PhpZmanim\HebrewCalendar\TefilaRules;


// Call wp_zmanim_menu function to load plugin menu in dashboard
add_action( 'admin_menu', 'wp_zmanim_menu' );

// Create WordPress admin menu
function wp_zmanim_menu(){

  $page_title = 'WordPress Zmanim';
  $menu_title = 'WP Zmanim';
  $capability = 'manage_options';
  $menu_slug  = 'wp_zmanim';
  $function   = 'wp_zmanim_page';
  $icon_url   = 'dashicons-media-code';
  $position   = 4;

  add_menu_page( $page_title,
                 $menu_title,
                 $capability,
                 $menu_slug,
                 $function,
                 $icon_url,
                 $position );

  // Call wp_zmanim function to update database
  add_action( 'admin_init', 'update_wp_zmanim' );
}

// Create function to register plugin settings in the database
function update_wp_zmanim() {
  $settings = array(
    'wpkz_tzone', 'wpkz_lat', 'wpkz_long', 'wpkz_location',
    'wpkz_candles', 'wpkz_alot', 'wpkz_tzait', 'wpkz_shaah',
    'wpkz_misheyakir', 'wpkz_shema', 'wpkz_tefilla', 'wpkz_gedola',
    'wpkz_ketana', 'wpkz_plag', 'wpkz_bain', 'wpkz_molad', 'wpkz_chodesh'
  );

  foreach ($settings as $setting) {
    register_setting('wp_zmanim-settings', $setting);
  }
}

// Create pull drop-down selection from database (not needed for strings or single variables)
function wp_zmanim_page(){
  $selectionAlot = get_option('wpkz_alot');
  $selectionMish = get_option('wpkz_misheyakir');
  $selectionShaah = get_option('wpkz_shaah');
  $selectionShema = get_option('wpkz_shema');
  $selectionTefilla = get_option('wpkz_tefilla');
  $selectionGedola = get_option('wpkz_gedola');
  $selectionKetana = get_option('wpkz_ketana');
  $selectionPlag = get_option('wpkz_plag');
  $selectionBain = get_option('wpkz_bain');
  $selectionTzait = get_option('wpkz_tzait');
  $selectionMolad = get_option('wpkz_molad');
  $selectionChodesh = get_option('wpkz_chodesh');

// Create the actual Admin page
  ?>


  <h1>WordPress Zmanim</h1>
  <H2>Current Information</H2>
  <P>
    Date: <?php echo date('Y-m-d'); ?><br />
    Location (lat/long): <?php echo do_shortcode("[zman_location]"). ": " . do_shortcode("[zman_lat]") . ", " . do_shortcode("[zman_long]"); ?><br />
    Time Zone: <?php echo do_shortcode("[zman_tzone]"); ?><br />
    Sunrise: <?php echo do_shortcode('[zman_sunrise format="h:i:s a"]'); ?><br />
    Sunset: <?php echo do_shortcode('[zman_sunset format="h:i:s a"]'); ?><br />
  </P><hr />

  <form method="post" action="options.php">
    <?php settings_fields( 'wp_zmanim-settings' ); ?>
    <?php do_settings_sections( 'wp_zmanim-settings' ); ?>
    <table class="form-table">
       <tr><td>Enter your Time Zone: </td>
        <td><input name="wpkz_tzone" value="<?php echo do_shortcode("[zman_tzone]"); ?>"/>
        <br/>This must be in the correct <a href="https://en.wikipedia.org/wiki/List_of_tz_database_time_zones">IANA Time Zone format</a>
      </td></tr>

      <tr><td>Enter your location in latitude and longitude: </td>
        <td>LAT: <input name="wpkz_lat" value="<?php echo get_option('wpkz_lat'); ?>"/>
            LONG: <input name="wpkz_long" value="<?php echo get_option('wpkz_long'); ?>"/>
      </td></tr>

      <tr><td>Give this location a name: </td>
        <td>Location Name: <input name="wpkz_location" value="<?php echo get_option('wpkz_location'); ?>"/>
      </td></tr>

      <tr><td>Candle offset (in minutes): </td>
        <td><input type="number" name="wpkz_candles" pattern="[0-6][" maxlength="2" value="<?php echo get_option('wpkz_candles'); ?>"/>
          <br /> Sunset: <?php echo do_shortcode('[zman_sunset format="h:i:s a"]'); ?>
          <br/>Candle time: <?php echo do_shortcode("[zman_candles]"); ?>
        </td></tr>

      <tr><td>Molad Text: </td>
        <td>Give the text that will appear in front of the molad calculation. HTML styling is permitted.: <input name="wpkz_molad" value="<?php echo get_option('wpkz_molad'); ?>"/>
      </td></tr>

      <tr><td>Rosh Chodesh Text: </td>
        <td>Give the text that will appear in front of the Rosh Chodesh calculation. HTML styling is permitted.: <input name="wpkz_chodesh" value="<?php echo get_option('wpkz_chodesh'); ?>"/>
      </td></tr>

      <tr>
        <td>Alot Hashachar:</td>
        <td>
          <select id="wpkz_alot" name="wpkz_alot" value="<?php echo $selectionAlot; ?>">
            <?php
            $alotOptions = [
              "alos72", "alos60", "alos72Zmanis", "alos96", "alos90Zmanis",
              "alos96Zmanis", "alos90", "alos120", "alos120Zmanis", "alos26Degrees",
              "alos18Degrees", "alos19Degrees", "alos19Point8Degrees",
              "alos16Point1Degrees", "alosBaalHatanya"
            ];

            foreach ($alotOptions as $option) {
              $isSelected = ($option == $selectionAlot) ? "selected" : "";
              echo "<option value=\"$option\" $isSelected>$option</option>";
            }
            ?>
          </select>
          <br />Alot today based on your selection: <?php echo do_shortcode("[zman_alot]"); ?>
        </td>
      </tr>

      <tr><td>Misheyakir: </td>
        <td><select id="wpkz_misheyakir" name="wpkz_misheyakir" value="">
         <?php
      $mish_options = array(
        "misheyakir11Point5Degrees",
        "misheyakir11Degrees",
        "misheyakir10Point2Degrees",
        "misheyakir7Point65Degrees",
        "misheyakir9Point5Degrees"
      );
      foreach ($mish_options as $option) {
        ?>
          <option value="<?php echo esc_attr($option); ?>" <?php selected($option, $selectionMish); ?>><?php echo esc_html($option); ?></option>  
        <?php
      }
      ?>
    </select>
        <br />Misheyakir today based on your selection: <?php echo do_shortcode("[zman_misheyakir]"); ?>
      </td></tr>

      <tr>
      <td>Sha'ah Zmanim:</td>
      <td>
        <select id="wpkz_shaah" name="wpkz_shaah" value="<?php echo $selectionShaah; ?>">
          <?php
          $shaahOptions = [
            "shaahZmanis19Point8Degrees", "shaahZmanis18Degrees", "shaahZmanis26Degrees",
            "shaahZmanis16Point1Degrees", "shaahZmanis60Minutes", "shaahZmanis72Minutes",
            "shaahZmanis72MinutesZmanis", "shaahZmanis90Minutes", "shaahZmanis90MinutesZmanis",
            "shaahZmanis96MinutesZmanis", "shaahZmanisAteretTorah", "shaahZmanisAlos16Point1ToTzais3Point8",
            "shaahZmanisAlos16Point1ToTzais3Point7", "shaahZmanis96Minutes", "shaahZmanis120Minutes",
            "shaahZmanis120MinutesZmanis", "shaahZmanisBaalHatanya", "shaahZmanisGra", "shaahZmanisMGA"
          ];

          foreach ($shaahOptions as $option) {
            $isSelected = ($option == $selectionShaah) ? "selected" : "";
            echo "<option value=\"$option\" $isSelected>$option</option>";
          }
          ?>
        </select>
        <br />Sha'ah today based on your selection: <?php echo do_shortcode("[zman_shaah]"); ?>
      </td>
    </tr>

      <tr>
      <td>Sof Zman Kria Shema:</td>
      <td>
        <select id="wpkz_shema" name="wpkz_shema" value="<?php echo $selectionShema; ?>">
          <?php
          $shemaOptions = [
            "sofZmanShmaMGA", "sofZmanShmaGra", "sofZmanShmaMGA19Point8Degrees",
            "sofZmanShmaMGA16Point1Degrees", "sofZmanShmaMGA18Degrees",
            "sofZmanShmaMGA72Minutes", "sofZmanShmaMGA72MinutesZmanis",
            "sofZmanShmaMGA90Minutes", "sofZmanShmaMGA90MinutesZmanis",
            "sofZmanShmaMGA96Minutes", "sofZmanShmaMGA96MinutesZmanis",
            "sofZmanShma3HoursBeforeChatzos", "sofZmanShmaMGA120Minutes",
            "sofZmanShmaAlos16Point1ToSunset", "sofZmanShmaAlos16Point1ToTzaisGeonim7Point083Degrees",
            "sofZmanShmaKolEliyahu", "sofZmanShmaFixedLocal", "sofZmanShmaBaalHatanya",
            "sofZmanShmaMGA18DegreesToFixedLocalChatzos", "sofZmanShmaMGA16Point1DegreesToFixedLocalChatzos",
            "sofZmanShmaMGA90MinutesToFixedLocalChatzos", "sofZmanShmaMGA72MinutesToFixedLocalChatzos",
            "sofZmanShmaGRASunriseToFixedLocalChatzos"
          ];

          foreach ($shemaOptions as $option) {
            $isSelected = ($option == $selectionShema) ? "selected" : "";
            echo "<option value=\"$option\" $isSelected>$option</option>";
          }
          ?>
        </select>
        <br />Shema today based on your selection: <?php echo do_shortcode("[zman_shema]"); ?>
      </td>
    </tr>

    <tr>
      <td>Sof Zman Tefilla:</td>
      <td>
        <select id="wpkz_tefilla" name="wpkz_tefilla" value="<?php echo $selectionTefilla; ?>">
          <?php
          $tefillaOptions = [
            "sofZmanTfilaMGA", "sofZmanTfilaGra", "sofZmanTfilaMGA19Point8Degrees",
            "sofZmanTfilaMGA16Point1Degrees", "sofZmanTfilaMGA18Degrees",
            "sofZmanTfilaMGA72Minutes", "sofZmanTfilaMGA72MinutesZmanis",
            "sofZmanTfilaMGA90Minutes", "sofZmanTfilaMGA90MinutesZmanis",
            "sofZmanTfilaMGA96Minutes", "sofZmanTfilaMGA96MinutesZmanis",
            "sofZmanTfilaMGA120Minutes", "sofZmanTfila2HoursBeforeChatzos",
            "sofZmanTfilahAteretTorah", "sofZmanTfilaFixedLocal",
            "sofZmanTfilaBaalHatanya", "sofZmanTfilaGRASunriseToFixedLocalChatzos"
          ];

          foreach ($tefillaOptions as $option) {
            $isSelected = ($option == $selectionTefilla) ? "selected" : "";
            echo "<option value=\"$option\" $isSelected>$option</option>";
          }
          ?>
        </select>
        <br />Sof Zman Tefilla today based on your selection: <?php echo do_shortcode("[zman_tefilla]"); ?>
      </td>
    </tr>

    <tr>
      <td>Mincha Gedola:</td>
      <td>
        <select id="wpkz_gedola" name="wpkz_gedola" value="<?php echo $selectionGedola; ?>">
          <?php
          $gedolaOptions = [
            "minchaGedola", "minchaGedola30Minutes", "minchaGedola72Minutes",
            "minchaGedola16Point1Degrees", "minchaGedolaAhavatShalom",
            "minchaGedolaGreaterThan30", "minchaGedolaAteretTorah",
            "minchaGedolaBaalHatanya", "minchaGedolaBaalHatanyaGreaterThan30",
            "minchaGedolaGRAFixedLocalChatzos30Minutes"
          ];

          foreach ($gedolaOptions as $option) {
            $isSelected = ($option == $selectionGedola) ? "selected" : "";
            echo "<option value=\"$option\" $isSelected>$option</option>";
          }
          ?>
        </select>
        <br />Mincha Gedola today based on your selection: <?php echo do_shortcode("[zman_gedola]"); ?>
      </td>
    </tr>

    <tr>
      <td>Mincha Ketana:</td>
      <td>
        <select id="wpkz_ketana" name="wpkz_ketana" value="<?php echo $selectionKetana; ?>">
          <?php
          $ketanaOptions = [
            "minchaKetana", "minchaKetana16Point1Degrees", "minchaKetanaAhavatShalom",
            "minchaKetana72Minutes", "minchaKetanaAteretTorah", "minchaKetanaBaalHatanya",
            "minchaKetanaGRAFixedLocalChatzosToSunset", "samuchLeMinchaKetanaGRA",
            "samuchLeMinchaKetana16Point1Degrees", "samuchLeMinchaKetana72Minutes"
          ];

          foreach ($ketanaOptions as $option) {
            $isSelected = ($option == $selectionKetana) ? "selected" : "";
            echo "<option value=\"$option\" $isSelected>$option</option>";
          }
          ?>
        </select>
        <br />Mincha Ketana today based on your selection: <?php echo do_shortcode("[zman_ketana]"); ?>
      </td>
    </tr>

    <tr>
      <td>Plag HaMincha:</td>
      <td>
        <select id="wpkz_plag" name="wpkz_plag" value="<?php echo $selectionPlag; ?>">
          <?php
          $plagOptions = [
            "plagHamincha", "plagHamincha120MinutesZmanis", "plagHamincha120Minutes",
            "plagHamincha60Minutes", "plagHamincha72Minutes", "plagHamincha90Minutes",
            "plagHamincha96Minutes", "plagHamincha96MinutesZmanis", "plagHamincha90MinutesZmanis",
            "plagHamincha72MinutesZmanis", "plagHamincha16Point1Degrees", "plagHamincha19Point8Degrees",
            "plagHamincha26Degrees", "plagHamincha18Degrees", "plagAlosToSunset",
            "plagAlos16Point1ToTzaisGeonim7Point083Degrees", "plagAhavatShalom",
            "plagHaminchaAteretTorah", "plagHaminchaBaalHatanya",
            "plagHaminchaGRAFixedLocalChatzosToSunset"
          ];

          foreach ($plagOptions as $option) {
            $isSelected = ($option == $selectionPlag) ? "selected" : "";
            echo "<option value=\"$option\" $isSelected>$option</option>";
          }
          ?>
        </select>
        <br />Plag HaMincha today based on your selection: <?php echo do_shortcode("[zman_plag]"); ?>
      </td>
    </tr>

    <tr>
      <td>Start of Bain HaShmashot:</td>
      <td>
        <select id="wpkz_bain" name="wpkz_bain" value="<?php echo $selectionBain; ?>">
          <?php
          $bainOptions = [
            "bainHashmashosRT13Point24Degrees", "bainHashmashosRT58Point5Minutes",
            "bainHashmashosRT13Point5MinutesBefore7Point083Degrees", "bainHashmashosRT2Stars",
            "bainHashmashosYereim18Minutes", "bainHashmashosYereim3Point05Degrees",
            "bainHashmashosYereim16Point875Minutes", "bainHashmashosYereim2Point8Degrees",
            "bainHashmashosYereim13Point5Minutes", "bainHashmashosYereim2Point1Degrees",
          ];

          foreach ($bainOptions as $option) {
            $isSelected = ($option == $selectionBain) ? "selected" : "";
            echo "<option value=\"$option\" $isSelected>$option</option>";
          }
          ?>
        </select>
        <br />Bain HaShmashot today based on your selection: <?php echo do_shortcode("[zman_bain]"); ?>
      </td>
    </tr>

<tr>
  <td>Tzait HaKochavim:</td>
  <td>
    <select id="wpkz_tzait" name="wpkz_tzait" value="<?php echo $selectionTzait; ?>">
      <?php
      $tzaitOptions = [
        "tzais", "tzais72", "tzaisGeonim3Point7Degrees", "tzaisGeonim3Point8Degrees",
        "tzaisGeonim5Point95Degrees", "tzaisGeonim3Point65Degrees", "tzaisGeonim3Point676Degrees",
        "tzaisGeonim4Point61Degrees", "tzaisGeonim4Point37Degrees", "tzaisGeonim5Point88Degrees",
        "tzaisGeonim4Point8Degrees", "tzaisGeonim6Point45Degrees", "tzaisGeonim7Point083Degrees",
        "tzaisGeonim7Point67Degrees", "tzaisGeonim8Point5Degrees", "tzaisGeonim9Point3Degrees",
        "tzaisGeonim9Point75Degrees", "tzais60", "tzaisAteretTorah", "tzais72Zmanis", "tzais90Zmanis",
        "tzais96Zmanis", "tzais90", "tzais120", "tzais120Zmanis", "tzais16Point1Degrees", "tzais26Degrees",
        "tzais18Degrees", "tzais19Point8Degrees", "tzais96", "tzaisBaalHatanya", "tzais50",
      ];

      foreach ($tzaitOptions as $option) {
        $isSelected = ($option == $selectionTzait) ? "selected" : "";
        echo "<option value=\"$option\" $isSelected>$option</option>";
      }
      ?>
    </select>
    <br />Tzait HaKochavim today based on your selection: <?php echo do_shortcode("[zman_tzait]"); ?>
  </td>
</tr>

    </table>
  <?php submit_button(); ?>
  </form>
<?php
}

// Plugin logic for adding wp_zmanim to posts
if( !function_exists("wp_zmanim") )
{
  function wp_zmanim($content)
  {
    
    return $content;
  }

// Apply the wp_zmanim function on our content  
add_filter('the_content', 'wp_zmanim');
}

$shortcodes = [
    'tzone', 'location', 'lat', 'long', 'zmandate', 'sunrise', 'sunset', 'candles', 'alot', 'tzait',
    'shaah', 'misheyakir', 'shema', 'tefilla', 'gedola', 'ketana', 'plag', 'bain', 'parsha', 'chodesh',
    'molad'
];

foreach ($shortcodes as $shortcode) {
    add_shortcode('zman_' . $shortcode, $shortcode . '_handler');
}

//FUNCTION: Sanitize and validate inputs
  function sanizmanim($variable, $type) {
    if ($type == 'float') {
      $newvar = filter_var($variable, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION, FILTER_FLAG_ALLOW_THOUSAND);
      return $newvar;
    }
    if ($type == 'int') {
      $newvar = filter_var($variable, FILTER_SANITIZE_NUMBER_INT);
      return $newvar;
    }
    if ($type == 'string') {
      $newvar = filter_var($variable, FILTER_SANITIZE_STRING); 
      return $newvar;
    }
    if ($type == 'engdate') {
      $newvar = filter_var($variable, FILTER_SANITIZE_STRING); 
      return $newvar;
    }
  }

//FUNCTION: set up some global variables for the shortcodes
function getzmanim($dateinput, $offset, $dateformat, $lang, $option, $globalsonly) {
  $dateinput = sanizmanim($dateinput, 'engdate');
  $offset = sanizmanim($offset, "int");
  $dateformat = sanizmanim($dateformat, 'string');
  $lang = sanizmanim($lang, 'string');
  $option = sanizmanim($option, 'string');
  $globalsonly = sanizmanim($globalsonly, 'int');

  $tzone = get_option('wpkz_tzone');
  if (!$tzone) {$tzone = "America/New_York";}
  $lat = get_option('wpkz_lat');
  $long = get_option('wpkz_long');
  if (!$lat || !$long) {
    $lat = "41.4939407";
    $long = "-81.516709";
  }
  $location = get_option('wpkz_location');
  if (!$location) {$location = "BKCS Cleveland";}

  if (!$dateinput) {
    $dateinput == "today";
  }
  $thedatetype = gettype($dateinput);
  if (gettype($dateinput) == 'string') {
    $dateinput = strtolower($dateinput);
  }
  if (strtotime($dateinput) != '') { 
    $thedate = date('Y-m-d', strtotime($dateinput));
    } elseif ($dateinput == "today") { $thedate = date('Y-m-d');
    } elseif ($dateinput == "tomorrow") { $thedate = date('Y-m-d', strtotime('+1 day')); 
    } elseif ($dateinput == "sunday" || $dateinput == "monday" || $dateinput == "tuesday" || $dateinput == "wednesday" || $dateinput == "thursday" || $dateinput == "friday" || $dateinput == "saturday") {
      $setday = "next ".$dateinput;
      $thedate = date('Y-m-d', strtotime($setday));
    }
  $theyear = date('Y', strtotime($thedate));
  $themonth = date('m', strtotime($thedate));
  $theday = date('d', strtotime($thedate));
  if ($globalsonly) {
    $output = [$theyear, $themonth, $theday, $lat, $long, $tzone];
    return $output;
  }
  $zmanim = Zmanim::create($theyear, $themonth, $theday, "", $lat, $long, 0, $tzone);
  $output = $zmanim->$option;
  if ($offset <> 0 ) { 
    $output = date($dateformat, strtotime($output . " +" .$offset ." minutes"));
  } else { 
    $output = date($dateformat, strtotime($output)); 
  }
  return $output;
}

// set up shortcode handlers
function kosherzmanim_init() {
  function tzone_handler() {
    $tzone = get_option('wpkz_tzone');
    if (!$tzone) {$tzone = "America/New_York";}
    return $tzone;
  }
  function lat_handler() {
    $lat = get_option('wpkz_lat');
    if (!$lat) { $lat = "41.4939407"; }
    return $lat;
  }
  function long_handler() {
    $long = get_option('wpkz_long');
    if (!$long) { $long = "-81.516709"; }
    return $long;
  }
  function location_handler() {
    $location = get_option('wpkz_location');
    if (!$location) {$location = "BKCS Cleveland";}
    return $location;
  }
  function zmandate_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => 'today', "offset" => 0, "dateformat" => "h:i a", "lang" => "english") );
    $zmanargs = getzmanim($atts['date'], $atts['offset'], $atts['dateformat'], $atts['lang'], $option, 1);
    $theyear = intval($zmanargs[0]);
    $themonth = intval($zmanargs[1]);
    $theday = intval($zmanargs[2]);
    $jewishCalendar = Zmanim::jewishCalendar(Carbon::createFromDate($theyear, $themonth, $theday));
    $format = Zmanim::format();
    if (strtolower($atts['lang']) == 'hebrew') { $format->setHebrewFormat(true); }
    $output = json_decode('"' . $format->format($jewishCalendar) . '"');
    return $output;
    }
  function molad_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => '', "year" => '', "month" => '', "dateformat" => "h:i a", "lang" => "english") );
    $zmanargs = getzmanim($atts['date'], 0, $atts['dateformat'], $atts['lang'], "", 1);
    $moladtext = get_option('wpkz_molad');

    //$output = "THIS WOULD BE THE MOLAD";
    return $output;
    }
  
  function chodesh_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => '', "year" => '', "month" => '', "dateformat" => "h:i a", "lang" => "english") );
    $thedate = $atts['date']; //today, next (week), or a date
    $hebyear = $atts['year']; //this, next (year), or a specific hebrew year
    $hebmonth = $atts['month']; //this, next (month), or a specific month number (1 = Tishrei)
    $dateformat = $atts['dateformat'];
    $lang = $atts['lang'];
    $roshchodeshtext = get_option('wpkz_chodesh') . "<br/>";

    if (!$thedate && !$hebyear && !$hebmonth) {$thedate = "today";}
    if ($thedate) {
      if ($thedate == 'today') {
        $thedate = date('Y-m-d');
      }
      if ($thedate == 'next') {
        $thedate = date('Y-m-d', strtotime("next Sunday"));
      }
      $todaydow = date('w', strtotime($thedate));
      $todayheb = new JewishDate(Carbon::createFromDate(date('Y', strtotime($thedate)), date('m', strtotime($thedate)), date('d', strtotime($thedate))));
      $hebyear = $todayheb->getJewishYear();
      $todayhebmonth = $todayheb->getJewishMonth();
      $todayhebday = $todayheb->getJewishDayOfMonth();
      $nexthebmonth = $todayheb->addMonthsJewish('1');
      $hebmonth = $nexthebmonth->getJewishMonth();
      $nextrh = new JewishDate($hebyear, $hebmonth, 1);
      $rhstring = date('D m/d', strtotime($nextrh->getGregorianCalendar()));
      $nextrhstart = $nextrh->subDays(1);
      $nextrhdom = $nextrhstart->getJewishDayOfMonth();
      if ( $todaydow + ($nextrhdom - $todayhebday) <= 6) {
        if ($nextrhstart->getJewishDayOfMonth() == 30) { 
          $rhstart = date('D m/d', $nextrhstart->getGregorianCalendar());
          $rhstring = "$rhstart and " . $rhstring;
        }
      } else {$rhstring = ""; }
    } elseif ($hebyear || $hebmonth) {
      $jewishnow =  new JewishDate();
      if (!$hebmonth) {$hebmonth = $jewishnow->getJewishMonth();}
      if (!$hebyear) {$hebyear = $jewishnow->getJewishYear();}
      if ($hebyear == 'this') {
        $hebyear = $jewishnow->getJewishYear()+1;
      }
      if ($hebyear == 'next') {
        $hebyear = $jewishnow->getJewishYear()+1;
      }
      if ($hebmonth == 'this') {
        $hebmonth = $jewishnow->getJewishMonth();
      }
      if ($hebmonth == 'next') {
        $hebmonth = $jewishnow->addMonthsJewish(1);
      }
      $nextrh = new JewishDate($hebyear, $hebmonth, 1);
      $rhstring = date('D m/d', strtotime($nextrh->getGregorianCalendar()));
      $nextrhstart = $nextrh->subDays(1);
      if ($nextrhstart->getJewishDayOfMonth() == 30) {
        $rhstart = date('D m/d', strtotime($nextrhstart->getGregorianCalendar()));
        $rhstring = "$rhstart and " . $rhstring;
      }
    } else { $rhstring = ""; }

    if ($rhstring) {
      $jewishCalendar = Zmanim::jewishCalendar($hebyear, $hebmonth, 1);
      $format = Zmanim::format();
      if (strtolower($lang) == 'hebrew') { $format->setHebrewFormat(true); }
      $monthname = json_decode('"' . $format->formatMonth($jewishCalendar) . '"');

      //write entire string for RH ("Rosh Chodesh <month> will be dow mm/dd <and dow mm/dd>")
      $rhdates = "Rosh Chodesh $monthname will be $rhstring";
      $rhstring = $roshchodeshtext . $rhdates;  
    }

    return $rhstring;
  }

  function parsha_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => 'today', "offset" => 0, "dateformat" => "h:i a", "lang" => "english") );
    $zmanargs = getzmanim($atts['date'], $atts['offset'], $atts['dateformat'], $atts['lang'], $option, 1);
    $theyear = $zmanargs[0];
    $themonth = $zmanargs[1];
    $theday = $zmanargs[2];

    //blank = same as "this"
    //this = for current week (jump to day 7 and get parsha)
    //next = the following week
    //date = take day 7 of given date (if date given IS day 7, use it)
    //<DOW> = same as date
    //DON'T FORGET TO ADD INFORMATION FOR ISRAEL-BASED USERS!!

    $jewishCalendar = Zmanim::jewishCalendar(Carbon::createFromDate($theyear, $themonth, $theday));
    $format = Zmanim::format();
    if (strtolower($atts['lang']) == 'hebrew') { $format->setHebrewFormat(true); }
    $output = json_decode('"' . $format->formatParsha($jewishCalendar) . '"');
    return $output;
    }
  function sunrise_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => 'today', "offset" => 0, "dateformat" => "h:i a", "lang" => "english") );
    $zmanargs = getzmanim($atts['date'], $atts['offset'], $atts['dateformat'], $atts['lang'], $option, 1);
    $zmanim = Zmanim::create($zmanargs[0], $zmanargs[1], $zmanargs[2], "", $zmanargs[3], $zmanargs[4], 0, $zmanargs[5]);
    $output = $zmanim->sunrise;
    if ($atts['offset'] <> 0 ) { 
      $output = date($atts['format'], strtotime($output . " +" .$atts['offset'] ." minutes"));
    } else { 
      $output = date($atts['format'], strtotime($output)); }
    return $output;
  }
  function sunset_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => 'today', "offset" => 0, "dateformat" => "h:i a", "lang" => "english") );
    $zmanargs = getzmanim($atts['date'], $atts['offset'], $atts['dateformat'], $atts['lang'], $option, 1);
    $zmanim = Zmanim::create($zmanargs[0], $zmanargs[1], $zmanargs[2], "", $zmanargs[3], $zmanargs[4], 0, $zmanargs[5]);
    $output = $zmanim->sunset;
    if ($atts['offset'] <> 0 ) { 
      $output = date($atts['format'], strtotime($output . " +" .$atts['offset'] ." minutes"));
    } else { 
      $output = date($atts['format'], strtotime($output)); }
    return $output;
  }
  function candles_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => 'today', "offset" => 0, "dateformat" => "h:i a", "lang" => "english") );
    $option = get_option('wpkz_candles');
    $zmanargs = getzmanim($atts['date'], $atts['offset'], $atts['dateformat'], $atts['lang'], $option, 1);
    $zmanim = Zmanim::create($zmanargs[0], $zmanargs[1], $zmanargs[2], "", $zmanargs[3], $zmanargs[4], 0, $zmanargs[5]);
    $sunset = $zmanim->sunset;
    if ($atts['offset'] != 0 ) { $option = $option + $atts['offset'];}
    $output = date($atts['dateformat'], strtotime($sunset . " -" .$option ." minutes"));
    return $output;
  }
  function alot_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => 'today', "offset" => 0, "dateformat" => "h:i a", "lang" => "english") );
    $option = get_option('wpkz_alot');
    $zmanresult = getzmanim($atts['date'], $atts['offset'], $atts['dateformat'], $atts['lang'], $option, 0);
    return $zmanresult;
    }
  function tzait_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => 'today', "offset" => 0, "dateformat" => "h:i a", "lang" => "english") );
    $option = get_option('wpkz_tzait');
    $zmanresult = getzmanim($atts['date'], $atts['offset'], $atts['dateformat'], $atts['lang'], $option, 0);
    return $zmanresult;
    }
  function shaah_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => 'today', "offset" => 0, "dateformat" => "h:i a", "lang" => "english") );
    $option = get_option('wpkz_shaah');
    $zmanargs = getzmanim($atts['date'], $atts['offset'], $atts['dateformat'], $atts['lang'], $option, 1);
    $zmanim = Zmanim::create($zmanargs[0], $zmanargs[1], $zmanargs[2], "", $zmanargs[3], $zmanargs[4], 0, $zmanargs[5]);
    $output = number_format((float)$zmanim->$option/60000, 2, '.', '');
    return $output;
    }
  function misheyakir_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => 'today', "offset" => 0, "dateformat" => "h:i a", "lang" => "english") );
    $option = get_option('wpkz_misheyakir');
    $zmanresult = getzmanim($atts['date'], $atts['offset'], $atts['dateformat'], $atts['lang'], $option, 0);
    return $zmanresult;
    }
  function shema_handler($atts = []) {
    $atts = wp_parse_args($atts, array("date" => 'today', "offset" => 0, "dateformat" => "h:i a", "lang" => "english") );
    $option = get_option('wpkz_shema');
    $zmanresult = getzmanim($atts['date'], $atts['offset'], $atts['dateformat'], $atts['lang'], $option, 0);
    return $zmanresult;
  }
  function tefilla_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => 'today', "offset" => 0, "dateformat" => "h:i a", "lang" => "english") );
    $option = get_option('wpkz_tefilla');
    $zmanresult = getzmanim($atts['date'], $atts['offset'], $atts['dateformat'], $atts['lang'], $option, 0);
    return $zmanresult;
  }
  function gedola_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => 'today', "offset" => 0, "dateformat" => "h:i a", "lang" => "english") );
    $option = get_option('wpkz_gedola');
    $zmanresult = getzmanim($atts['date'], $atts['offset'], $atts['dateformat'], $atts['lang'], $option, 0);
    return $zmanresult;
  }
  function ketana_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => 'today', "offset" => 0, "dateformat" => "h:i a", "lang" => "english") );
    $option = get_option('wpkz_ketana');
    $zmanresult = getzmanim($atts['date'], $atts['offset'], $atts['dateformat'], $atts['lang'], $option, 0);
    return $zmanresult;
    }
  function plag_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => 'today', "offset" => 0, "dateformat" => "h:i a", "lang" => "english") );
    $option = get_option('wpkz_plag');
    $zmanresult = getzmanim($atts['date'], $atts['offset'], $atts['dateformat'], $atts['lang'], $option, 0);
    return $zmanresult;
    }
  function bain_handler($atts = []) {
    $atts = wp_parse_args( $atts, array("date" => 'today', "offset" => 0, "dateformat" => "h:i a", "lang" => "english") );
    $option = get_option('wpkz_bain');
    $zmanresult = getzmanim($atts['date'], $atts['offset'], $atts['dateformat'], $atts['lang'], $option, 0);
    return $zmanresult;
    }
  }

// only add shortcodes after WP starts
add_action('init', 'kosherzmanim_init');

?>