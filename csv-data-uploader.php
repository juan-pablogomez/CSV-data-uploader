<?php

/**
 * 
 * Plugin Name: CSV data uploader
 * Plugin URI: https://github.com/juan-pablogomez
 * Description: Plugin for upload csv Data, 
 * Version: 1.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Juan Pablo Gómez
 * Author URI: https://curriculum-pablo.netlify.app/
 * License: GPL v2 or later  
 * @package csvplugin
 *  
 */

define("CDU_PLUGIT_DIR", plugin_dir_path(__FILE__));


add_shortcode("csv-data-uploader", "cdu_display_uploader_form");

function cdu_display_uploader_form()
{
  //Star PHP buffer
  ob_start();
  include_once CDU_PLUGIT_DIR . "/template/cdu-form.php";

  // Read buffer
  $template = ob_get_contents();

  // Clean buffer
  ob_end_clean();

  return $template;
}
