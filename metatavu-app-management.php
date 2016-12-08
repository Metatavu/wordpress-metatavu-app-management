<?php
/*
 * Created on Nov 7, 2016
 * Plugin Name: Metatavu App Management
 * Description: Metatavu App Management
 * Version: 0.1
 * Author: Heikki Kurhinen / Metatavu Oy
 */
defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );

define('METATAVU_APP_MANAGEMENT_I18N_DOMAIN', 'metatavu-app-management');

function metatavuAppManagementRenderTypeMetaBox($mobilepage) {
	$type = get_post_meta($mobilepage->ID, "metatavu-app-management-type", true);
	echo '<select name="metatavu-app-management-type" id="metatavu-app-management-type" style="width: 100%;" >';
  echo '<option '. selected( $type, 'event' ) .' value="event">Tapahtuma</option>' ;
  echo '<option '. selected( $type, 'info' ) .' value="info">Info - sivu</option>' ;
  echo '<option '. selected( $type, 'frontpage' ) .' value="frontpage">Etusivu</option></select>';
}

function metatavuAppManagementRenderLocationMetaBox($mobilepage) {
  $locations = [];

  $locations[] = [
    'name' => 'Kansalaisopisto',
    'value' => '62,23'
  ];

  $locations[] = [
    'name' => 'Musiikkiopisto',
    'value' => '61,24'
  ];

  $locations[] = [
    'name' => 'Suuri Näyttämö',
    'value' => '60.32,22.212'
  ];

  $locations[] = [
    'name' => 'Pieni Näyttämö',
    'value' => '60.2151,23.231'
  ];

  $locations[] = [
    'name' => 'Vanha Sotku',
    'value' => '62.12,23.23'
  ];

	$location = get_post_meta($mobilepage->ID, "metatavu-app-management-location", true);
  $value = $location['lat'] . ',' . $location['lng'];
	echo '<select name="metatavu-app-management-location" id="metatavu-app-management-location" style="width: 100%;" >';
  foreach ($locations as $loc) {
    echo '<option'. selected( $loc['value'], $value ) .' value="'. $loc['value'] .'">' . $loc['name'] . '</option>';
  }

  echo '</select>';
}

function metatavuAppManagementRenderOpenMetaBox($mobilepage) {
  $openDays = get_post_meta($mobilepage->ID, "metatavu-app-management-open", true);
  $openRow = '<p><span>pvm: <input class="metatavu-app-management-date uninitialized" type="text" name="metatavu-app-management-date[]" value="{{date}}"></input></span>' .
    '<span style="margin-left: 20px;">start: <input class="metatavu-app-management-time uninitialized" type="text" name="metatavu-app-management-start[]" value="{{start}}"></input></span>' .
    '<span style="margin-left: 20px;">end: <input class="metatavu-app-management-time uninitialized" type="text" name="metatavu-app-management-end[]" value="{{end}}"></input></span>' .
    '<span style="margin-left: 20px;"><a class="metatavu-app-management-remove-row-btn" href="#">poista</a></span></p>';

  echo '<div class="metatavu-app-management-open-container">';

  foreach ($openDays as $open) {
    $data = str_replace('{{date}}', $open['date'], $openRow);
    $data = str_replace('{{start}}', $open['opens'], $data);
    $data = str_replace('{{end}}', $open['closes'], $data);
    echo $data;
  }

  echo '</div>';
  echo '<p><a class="metatavu-app-management-add-row-btn" href="#">+ lisää</a></p>';
  echo '<script>var METATAVU_APP_MANAGEMENT_OPEN_ROW = \'' . str_replace(array('{{date}}', '{{start}}', '{{end}}'), '', $openRow) . '\';</script>';
}

function metatavuAppManagementSaveMobilePage($mobilepageId) {
  if (array_key_exists('metatavu-app-management-date', $_POST) && array_key_exists('metatavu-app-management-start', $_POST) && array_key_exists('metatavu-app-management-end', $_POST)) {
	  $open = [];
    foreach ($_POST['metatavu-app-management-date'] as $index => $date) {
      if(isset($_POST['metatavu-app-management-start'][$index]) && isset($_POST['metatavu-app-management-end'][$index])) {
        $row = [];
        $row['date'] = $date;
        $row['opens'] = $_POST['metatavu-app-management-start'][$index];
        $row['closes'] = $_POST['metatavu-app-management-end'][$index];
        $open[] = $row;
      }
    }

    update_post_meta($mobilepageId, 'metatavu-app-management-open', $open);
  }

  if(array_key_exists('metatavu-app-management-type', $_POST)) {
    update_post_meta($mobilepageId, 'metatavu-app-management-type', $_POST['metatavu-app-management-type']);
  }

  if(array_key_exists('metatavu-app-management-location', $_POST)) {
    $location = [];
    $parts = explode(',', $_POST['metatavu-app-management-location']);
    $location['lat'] = $parts[0];
    $location['lng'] = $parts[1];

    update_post_meta($mobilepageId, 'metatavu-app-management-location', $location);
  }

}

function metatavuAppManagementRestGet( $object, $field_name, $request) {
  return get_post_meta( $object[ 'id' ], $field_name, true);
}

add_action ('add_meta_boxes', function() {
  add_meta_box( 
    'page-type-meta-box',
    __( 'Page Type', METATAVU_APP_MANAGEMENT_I18N_DOMAIN ),
    'metatavuAppManagementRenderTypeMetaBox',
    'mobilepage',
    'side',
    'default'
  );
  add_meta_box( 
    'page-location-meta-box',
    __( 'Location', METATAVU_APP_MANAGEMENT_I18N_DOMAIN ),
    'metatavuAppManagementRenderLocationMetaBox',
    'mobilepage',
    'side',
    'default'
  );
  add_meta_box( 
    'page-open-meta-box',
    __( 'Open', METATAVU_APP_MANAGEMENT_I18N_DOMAIN ),
    'metatavuAppManagementRenderOpenMetaBox',
    'mobilepage',
    'normal',
    'default'
  );
}, 9999, 3 );

add_action ('init', function () {

  wp_enqueue_script( 'metatavu-app-management-flatpickr', plugin_dir_url( __FILE__ ) . 'scripts/flatpickr.2.2.4.js' );
  wp_enqueue_script( 'metatavu-app-management-timepicker', plugin_dir_url( __FILE__ ) . 'scripts/jquery.timepicker.min.js' );
  wp_enqueue_script( 'metatavu-app-management-init', plugin_dir_url( __FILE__ ) . 'scripts/init.js' );

  wp_enqueue_style('metatavu-app-management-flatpickr-style', plugin_dir_url( __FILE__ ) . 'styles/flatpickr.min.css', true);
  wp_enqueue_style('metatavu-app-management-timepicker-style', plugin_dir_url( __FILE__ ) . 'styles/jquery.timepicker.css', true);

  register_post_type ( 'mobilepage', array (
    'labels' => array (
      'name'               => __( 'Mobile Pages', METATAVU_APP_MANAGEMENT_I18N_DOMAIN ),
      'singular_name'      => __( 'Mobile Page', METATAVU_APP_MANAGEMENT_I18N_DOMAIN ),
      'add_new'            => __( 'Add a Mobile Page', METATAVU_APP_MANAGEMENT_I18N_DOMAIN ),
      'add_new_item'       => __( 'Add New Mobile Page', METATAVU_APP_MANAGEMENT_I18N_DOMAIN ),
      'edit_item'          => __( 'Edit Mobile Page', METATAVU_APP_MANAGEMENT_I18N_DOMAIN ),
      'new_item'           => __( 'New Mobile Page', KUNTA_API_ANNOUNEMENTS_I18N_DOMAIN ),
      'view_item'          => __( 'View Mobile Page', METATAVU_APP_MANAGEMENT_I18N_DOMAIN ),
      'search_items'       => __( 'Search Mobile Pages', METATAVU_APP_MANAGEMENT_I18N_DOMAIN ),
      'not_found'          => __( 'No mobile pages found', METATAVU_APP_MANAGEMENT_I18N_DOMAIN ),
      'not_found_in_trash' => __( 'No mobile pages in trash', METATAVU_APP_MANAGEMENT_I18N_DOMAIN ),
      'menu_name'          => __( 'Mobile Pages', METATAVU_APP_MANAGEMENT_I18N_DOMAIN ),
      'all_items'          => __( 'Mobile Pages', METATAVU_APP_MANAGEMENT_I18N_DOMAIN )
    ),
    'public' => true,
    'has_archive' => true,
    'show_in_rest' => true,
    'supports' => array (
      'title',
      'editor'
     )
   ));
});

add_action ('plugins_loaded', function () {
  load_plugin_textdomain(METATAVU_APP_MANAGEMENT_I18N_DOMAIN);
});

add_action ('save_post', 'metatavuAppManagementSaveMobilePage');

add_action('rest_api_init', function () {

  register_rest_field( 'mobilepage', 'metatavu-app-management-location', array(
    'get_callback' => 'metatavuAppManagementRestGet',
    'update_callback' => null,
    'schema' => null
  ));

  register_rest_field( 'mobilepage', 'metatavu-app-management-type', array(
    'get_callback' => 'metatavuAppManagementRestGet',
    'update_callback' => null,
    'schema' => null
  ));

  register_rest_field( 'mobilepage', 'metatavu-app-management-open', array(
    'get_callback' => 'metatavuAppManagementRestGet',
    'update_callback' => null,
    'schema' => null
  ));
  
});

?>