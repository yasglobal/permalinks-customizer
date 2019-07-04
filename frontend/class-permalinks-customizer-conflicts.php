<?php
/**
 * @package PermalinksCustomizer
 */

/**
 * Class which checks conflict.
 *
 * Check the Polylang plugin is enabled if so, then try to make that fix.
 *
 * @since 1.0.0
 */
class Permalinks_Customizer_Conflicts {

  /**
   * Check Conflicts and resolve it (e.g: Polylang).
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $requested_url Optional. URL requested by User. ''.
   *
   * @return string requested URL by removing the language/ from it if exist.
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

        /*
         * Check if `hide_default` is true and the current language is not
         * the default. If `true` the remove the lang code from the url.
         */
        if ( 1 == $polylang_config['hide_default'] ) {
          $current_language = '';
          if ( function_exists( 'pll_current_language' ) ) {
            // get current language
            $current_language = pll_current_language();
          }
          // get default language
          $default_language = $polylang_config['default_lang'];
          if ( $current_language !== $default_language ) {
            $remove_lang = ltrim( strstr( $requested_url, '/' ), '/' );
            if ( '' != $remove_lang ) {
              return $remove_lang;
            }
          }
        } else {
          $remove_lang = ltrim( strstr( $requested_url, '/' ), '/' );
          if ( '' != $remove_lang ) {
            return $remove_lang;
          }
        }
      }
    }

    return $requested_url;
  }
}
