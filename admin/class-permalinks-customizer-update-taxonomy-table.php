<?php
/**
 * @package PermalinksCustomizer
 */

/**
 * Create PostTypes Permalinks page.
 *
 * Create available PostTypes Permalinks HTML and display the page.
 *
 * @since 1.3.3
 */
class Permalinks_Customizer_Update_Taxonomy {

  /**
   * Call Update Taxonomy Terms Function.
   */
  function __construct() {
    $this->update_taxonomy_terms();
  }

  /**
   * Add the Permalinks of the Taxonomy to the term table.
   *
   * @since 1.3.3
   * @access private
   */
  private function update_taxonomy_terms() {
    $taxonomy_table = get_option( 'permalinks_customizer_table', -1 );
    if ( isset( $taxonomy_table ) && ! empty( $taxonomy_table )
      && is_array( $taxonomy_table ) ) {
      global $wpdb;
      $i = 1;
      foreach ( $taxonomy_table as $link => $info ) {

        if ( $i > 10 ) {
          break;
        }

        $query = $wpdb->prepare( "SELECT * FROM $wpdb->termmeta AS tm
                  WHERE tm.meta_key = 'permalink_customizer'
                  AND tm.term_id = %d LIMIT 1", $info["id"] );
        $check_term = $wpdb->get_row( $query );
        if( isset( $check_term->meta_value )
          && ! empty( $check_term->meta_value ) ) {
          unset( $taxonomy_table[$link] );
        } else {
          $check_update = update_term_meta(
            $info["id"], 'permalink_customizer', $link
          );
          if ( ! is_wp_error( $check_update ) && true !== $check_update ) {
            unset( $taxonomy_table[$link] );
          }
            }
        $i++;
      }
      update_option( 'permalinks_customizer_table', $taxonomy_table );
    } elseif ( isset( $taxonomy_table ) && empty( $taxonomy_table ) ) {
      delete_option( 'permalinks_customizer_table' );
      if ( defined( 'PERMALINKS_CUSTOMIZER_PLUGIN_VERSION' ) ) {
        update_option( 'permalinks_customizer_plugin_version',
          PERMALINKS_CUSTOMIZER_PLUGIN_VERSION
        );
      }
    }
  }
}
