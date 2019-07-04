<?php
/**
 * PermalinksCustomizer Uninstall
 *
 * Deletes Settings, Post Permalinks and Taxonomies Permalinks
 * on uninstalling the Plugin.
 *
 * @package PermalinksCustomizer
 * @since 2.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  exit;
}

$args = array(
  'public' => true
);

// Delete Plugin Version
delete_option( 'permalinks_customizer_plugin_version' );

// Delete PostType Settings
$post_types = get_post_types( $args, 'objects' );
foreach ( $post_types as $post_type ) {
  delete_option( 'permalinks_customizer_' . $post_type->name );
}

// Delete Taxonomy Settings
delete_option( 'permalinks_customizer_taxonomy_settings' );

// Delete meta_keys for posts/pages
delete_post_meta_by_key( 'permalink_customizer' );
delete_post_meta_by_key( 'permalink_customizer_regenerate_status' );

// Delete Category/Tags with Older version style
delete_option( 'permalinks_customizer_table' );

global $wpdb;

// Delete all terms with latest version style
$wpdb->query( "DELETE FROM $wpdb->termmeta WHERE meta_key = 'permalink_customizer'" );

// Drop Redirects Table if Exist
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}permalinks_customizer_redirects" );

$get_capability = get_option( 'permalinks_customizer_capabilities', -1 );
if ( -1 !== $get_capability ) {
  $capabilities = unserialize( $get_capability );
  foreach ( $capabilities as $pc_role => $capability ) {
    $role = get_role( $pc_role );
    foreach ( $capability as $row ) {
      $role->remove_cap( $row );
    }
  }
}

$role = get_role( 'permalinks_customizer_manager' );
if ( ! empty( $role ) ) {
  $role->remove_cap( 'pc_manage_permalinks' );
  $role->remove_cap( 'pc_manage_permalink_settings' );
  $role->remove_cap( 'pc_manage_permalink_redirects' );

  remove_role( 'permalinks_customizer_manager' );
}

// Clear any cached data that has been removed
wp_cache_flush();
