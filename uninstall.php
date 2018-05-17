<?php
/**
 * PermalinksCustomizer Uninstall
 *
 * Deletes Settings, Post Permalinks and Taxonomies Permalinks
 * on uninstalling the Plugin.
 *
 * @package PermalinksCustomizer/Uninstaller
 * @since 2.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  exit;
}

$args = array(
  'public'   => true
);

// Delete PostType Settings
$post_types = get_post_types( $args, 'objects' );
foreach ( $post_types as $post_type ) {
  if ( 'attachment' == $post_type->name ) {
    continue;
  }
  delete_option( 'permalinks_customizer_' . $post_type->name );
}

// Delete Taxonomy Settings
delete_option( 'permalinks_customizer_taxonomy_settings' );

// Delete meta_keys for posts/pages
delete_post_meta_by_key( 'permalink_customizer' );
delete_post_meta_by_key( 'permalink_customizer_regenerate_status' );

// Delete Category/Tags with Older version style
delete_option( 'permalinks_customizer_table' );
// Delete all terms with latest version style
global $wpdb;
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->termmeta WHERE meta_key = 'permalink_customizer'" ) );

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

// Clear any cached data that has been removed
wp_cache_flush();
