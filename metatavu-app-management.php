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

function metatavuAppManagementRenderMetaBox($tile) {
	$link = get_post_meta($tile->ID, "tile-link", true);
	echo '<input name="tile-link" id="tile-link" type="url" style="width: 100%;" value="' . $link . '"></input>';
}

add_action ('add_meta_boxes', function() {
  add_meta_box( 
    'page-type-meta-box',
    __( 'Page Type', METATAVU_APP_MANAGEMENT_I18N_DOMAIN ),
    'metatavuAppManagementRenderMetaBox',
    'type',
    'side',
    'default'
  );
});

add_action ('init', function () {
  register_post_type ( 'announcement', array (
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

?>