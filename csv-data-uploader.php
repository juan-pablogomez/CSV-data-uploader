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

defined('ABSPATH') or die("You can't access this file");

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


//DB Table create on plugin Activation

register_activation_hook(__FILE__, "cdu_create_table");

function cdu_create_table()
{
  global $wpdb;
  $table_prefix = $wpdb->prefix;
  $table_name = $table_prefix . "students_data";

  $table_collate = $wpdb->get_charset_collate();
  $sql_command = " CREATE TABLE `" . $table_name . "` (
    `id` int NOT NULL AUTO_INCREMENT,
    `name` varchar(50) DEFAULT NULL,
    `email` varchar(50) DEFAULT NULL,
    `age` int DEFAULT NULL,
    `phone` varchar(30) DEFAULT NULL,
    `photo` varchar(150) DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) " . $table_collate . " 
  ";

  require_once ABSPATH . "/wp-admin/includes/upgrade.php";

  dbDelta($sql_command, true);
}

// Add Script File

add_action("wp_enqueue_scripts", "cdu_add_script_file");

function cdu_add_script_file() {
  wp_enqueue_script("cdu-script-js", plugin_dir_url(__FILE__) . "/assets/script.js", ["jquery"]);
  wp_localize_script("cdu-script-js", "cdu_object", [
    "ajax_url" => admin_url("admin-ajax.php")
  ]);
}

// Capture ajax Request

add_action("wp_ajax_cdu_submit_form_data", "cdu_ajax_handler"); // When user is logged in
add_action("wp_ajax_nopriv_cdu_submit_form_data", "cdu_ajax_handler"); // When user is logged out

function cdu_ajax_handler() {
  if($_FILES["csv_data_file"]) {
    $csvFile = $_FILES["csv_data_file"]["tmp_name"];
    $handle = fopen($csvFile, 'r');
    global $wpdb;
    $table_name = $wpdb->prefix . "students_data";
    if($handle) {
      $row = 0;
      while( ($data = fgetcsv($handle, 1000, ',')) !== FALSE ) {
        if($row == 0) {
          $row++;
          continue;
        }
        // Insert data into table
        $wpdb->insert($table_name, [
          "name" => $data[1],
          "email" => $data[2],
          "age" => $data[3],
          "phone" => $data[4],
          "photo" => $data[5]
        ]);
      }
      fclose($handle);
      echo json_encode([
        "status" => 1,
        "message" => "Data uploaded successfully"
      ]);
    }
  } else {

    echo json_encode([
      "status" => 1,
      "message" => "No File Found",
    ]);
  }

  exit;
}