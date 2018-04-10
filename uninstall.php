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

// Delete PostType Settings
$post_types = get_post_types( '', 'objects' );
foreach ( $post_types as $post_type ) {
  if ( $post_type->name == 'revision' || $post_type->name == 'nav_menu_item' || $post_type->name == 'attachment' ) {
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

// Clear any cached data that has been removed
wp_cache_flush();
