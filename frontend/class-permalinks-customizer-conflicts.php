<?php
/**
 * @package PermalinksCustomizer\Frontend\Conflicts
 */

class Permalinks_Customizer_Conflicts {

  /**
   * Check Conflicts and resolve it (e.g: Polylang).
   *
   * @access public
   * @since 1.3.3
   *
   * @param string $requested_url
   *   URL requested by User
   *
   * @return string $requested_url
   *   Return the requested URL by removing the language/ from it if exist.
   */
  public function check_conflicts ( $requested_url = '' ) {
    if ( '' == $requested_url ) {
      return;
    }

    // Check if the Polylang Plugin is installed so, make changes in the URL
    if ( defined( 'POLYLANG_VERSION' ) ) {
      $polylang_config = get_option( 'polylang' );
      if ( 1 == $polylang_config['force_lang'] ) {

        if ( false !== strpos( $requested_url, 'language/' ) ) {
          $requested_url = str_replace( 'language/', '', $requested_url );
        }

        $remove_lang = ltrim( strstr( $requested_url, '/' ), '/' );
        if ( '' != $remove_lang ) {
          return $remove_lang;
        }
      }
    }

    return $requested_url;
  }
}
