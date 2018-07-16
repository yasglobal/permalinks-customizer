<?php
/**
 * @package PermalinksCustomizer\Frontend
 */

final class Permalinks_Customizer_Frontend {

  /**
   * Initialize WordPress Hooks
   */
  public function init() {
    add_filter( 'request', array( $this, 'make_request' ), 10, 1 );

    add_action( 'template_redirect', array( $this, 'make_redirect' ), 5 );

    add_filter( 'post_link', array( $this, 'customized_post_link' ), 10, 2 );
    add_filter( 'post_type_link', array( $this, 'customized_post_link' ), 10, 2 );
    add_filter( 'page_link', array( $this, 'customized_page_link' ), 10, 2 );

    add_filter( 'term_link', array( $this, 'customized_term_link' ), 10, 3 );

    add_filter( 'user_trailingslashit', array( $this, 'apply_trailingslash' ), 10, 2 );
  }

  /**
   * Filter to rewrite the query if we have a matching post.
   *
   * @access public
   * @since 0.1
   *
   * @param string $query
   *   Requested URL
   *
   * @return string $query
   *   Return the URL which has to be parsed.
   */
  public function make_request( $query ) {
    global $wpdb;
    global $_CPRegisteredURL;
    $original_url = NULL;
    $url          = parse_url( get_bloginfo( 'url' ) );
    $url          = isset( $url['path'])  ? $url['path'] : '';
    $request      = ltrim( substr( $_SERVER['REQUEST_URI'], strlen( $url ) ), '/' );
    $pos          = strpos( $request, '?' );
    if ( $pos ) {
      $request = substr( $request, 0, $pos );
    }

    if ( ! $request ) {
      return $query;
    }

    $exclude_url = apply_filters(
      'permalinks_customizer_exclude_request', $request
    );

    if ( '__true' === $exclude_url ) {
      return $query;
    }

    $redirect        = $this->check_redirect( $request );
    $redirect_filter = apply_filters(
      'permalinks_customizer_disable_redirects', '__true'
    );
    if ( isset( $redirect ) && ! empty( $redirect )
      && '__true' === $redirect_filter ) {
      wp_redirect( trailingslashit( home_url() ) . $redirect, 301 );
      exit();
    }

    if ( defined( 'POLYLANG_VERSION' ) ) {
      require_once(
        PERMALINKS_CUSTOMIZER_PATH . 'frontend/class-permalinks-customizer-conflicts.php'
      );

      $conflicts = new Permalinks_Customizer_Conflicts();
      $request   = $conflicts->check_conflicts( $request );
    }

    $request_noslash = preg_replace( '@/+@', '/', trim( $request, '/' ) );

    $sql = $wpdb->prepare( "SELECT $wpdb->posts.ID, $wpdb->postmeta.meta_value, $wpdb->posts.post_type, $wpdb->posts.post_status FROM $wpdb->posts " .
        " LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) WHERE " .
        " meta_key = 'permalink_customizer' AND meta_value != '' AND " .
        " ( LOWER(meta_value) = LEFT(LOWER('%s'), LENGTH(meta_value)) OR " .
        "   LOWER(meta_value) = LEFT(LOWER('%s'), LENGTH(meta_value)) ) " .
        "  AND post_status != 'trash' AND post_type != 'nav_menu_item'" .
        " ORDER BY LENGTH(meta_value) DESC, " .
        " FIELD(post_status,'publish','private','draft','auto-draft','inherit')," .
        " FIELD(post_type,'post','page'), $wpdb->posts.ID ASC LIMIT 1",
        $request_noslash, $request_noslash . "/" );

    $posts = $wpdb->get_results( $sql );

    if ( $posts ) {
      if ( $request_noslash == trim( $posts[0]->meta_value, '/' ) ) {
        $_CPRegisteredURL = $request;
      }

      if ( 'draft' == $posts[0]->post_status ) {
        if ( 'page' == $posts[0]->post_type ) {
          $original_url = '?page_id=' . $posts[0]->ID;
        } elseif ( 'post' == $posts[0]->post_type ) {
          $original_url = '?p=' . $posts[0]->ID;
        } else {
          $original_url = '?post_type=' . $posts[0]->post_type . '&p=' . $posts[0]->ID;
        }
      } else {
        $original_url = preg_replace( '@/+@', '/', str_replace( trim( strtolower( $posts[0]->meta_value ), '/' ),
                  ( $posts[0]->post_type == 'page' ? $this->original_page_link( $posts[0]->ID ) : $this->original_post_link( $posts[0]->ID ) ), strtolower( $request_noslash ) ) );
      }
    }
    if ( NULL === $original_url ) {
      $sql = $wpdb->prepare( "SELECT * FROM $wpdb->termmeta WHERE " .
          " meta_key = 'permalink_customizer' AND meta_value != '' AND " .
          " ( LOWER(meta_value) = LEFT(LOWER('%s'), LENGTH(meta_value)) OR " .
          " LOWER(meta_value) = LEFT(LOWER('%s'), LENGTH(meta_value)) ) " .
          " ORDER BY LENGTH(meta_value) DESC, $wpdb->termmeta.term_id ASC LIMIT 1",
          $request_noslash, $request_noslash . "/" );

      $taxonomy_term_data = $wpdb->get_results( $sql );

      $get_term_permalink = NULL;
      if ( is_array( $taxonomy_term_data ) && isset( $taxonomy_term_data[0]->term_id ) ) {
        $get_term_permalink = $this->original_taxonomy_link( $taxonomy_term_data[0]->term_id );
        if ( isset( $get_term_permalink ) && '' != $get_term_permalink ) {
          if ( $request_noslash == trim( $taxonomy_term_data[0]->meta_value, '/' ) ) {
            $_CPRegisteredURL = $request;
          }

          $original_url = str_replace(
            trim( strtolower( $taxonomy_term_data[0]->meta_value ), '/' ),
            $get_term_permalink, trim( strtolower( $request ) )
          );
        }
      }

      if ( NULL === $original_url && NULL === $get_term_permalink ) {
        $table = get_option( 'permalinks_customizer_table' );
        if ( ! $table ) {
          return $query;
        }

        foreach ( array_keys( $table ) as $permalink ) {
          if ( $permalink == substr( $request_noslash, 0, strlen( $permalink ) )
            || $permalink == substr( $request_noslash . '/', 0, strlen( $permalink ) ) ) {
            $term = $table[$permalink];
            if ( $request_noslash == trim( $permalink, '/' ) ) {
              $_CPRegisteredURL = $request;
            }

            $original_url = str_replace(
              trim( $permalink, '/' ),
              $this->original_taxonomy_link( $term['id'] ),
              trim( $request, '/' )
            );
          }
        }
      }
    }

    if ( null !== $original_url ) {
      $original_url = str_replace( '//', '/', $original_url );
      $pos          = strpos( $_SERVER['REQUEST_URI'], '?' );
      if ( false !== $pos ) {
        $query_vars     = substr( $_SERVER['REQUEST_URI'], $pos + 1 );
        if ( false === strpos( $original_url, '?' ) ) {
          $original_url .= '?' . $query_vars;
        } else {
          $original_url .= '&' . $query_vars;
        }
      }
      $old_request_uri        = $_SERVER['REQUEST_URI'];
      $old_query_string       = $_SERVER['QUERY_STRING'];
      $_SERVER['REQUEST_URI'] = '/' . ltrim( $original_url, '/' );
      $pos = strpos( $original_url, '?' );
      if ( false !== $pos ) {
        $_SERVER['QUERY_STRING'] = substr( $original_url, $pos + 1 );
      } else {
        $_SERVER['QUERY_STRING'] = '';
      }
      parse_str( $_SERVER['QUERY_STRING'], $query_array );
      $old_values = array();
      if ( is_array( $query_array ) ) {
        foreach ( $query_array as $key => $value ) {
          $old_values[$key] = '';
          if ( isset( $_REQUEST[$key] ) ) {
            $old_values[$key] = $_REQUEST[$key];
          }
          $_REQUEST[$key] = $_GET[$key] = $value;
        }
      }
      remove_filter( 'request', array( $this, 'make_request' ), 10, 1 );
      global $wp;
      $wp->parse_request();
      $query = $wp->query_vars;
      add_filter( 'request', array( $this, 'make_request' ), 10, 1 );
      $_SERVER['REQUEST_URI']  = $old_request_uri;
      $_SERVER['QUERY_STRING'] = $old_query_string;
      foreach ( $old_values as $key => $value ) {
        $_REQUEST[$key] = $value;
      }
    }

    return $query;
  }

  /**
   * Action to redirect to the custom permalink.
   *
   * @access public
   * @since 0.1
   *
   * @return void
   */
  public function make_redirect() {
    $url     = parse_url( get_bloginfo( 'url' ) );
    $url     = isset( $url['path'] ) ? $url['path'] : '';
    $request = ltrim( substr( $_SERVER['REQUEST_URI'], strlen( $url ) ), '/' );

    if ( ( $pos= strpos( $request, '?' ) ) ) {
      $request = substr( $request, 0, $pos );
    }

    if ( defined( 'POLYLANG_VERSION' ) ) {
      require_once(
        PERMALINKS_CUSTOMIZER_PATH . 'frontend/class-permalinks-customizer-conflicts.php'
      );

      $conflicts = new Permalinks_Customizer_Conflicts();
      $request   = $conflicts->check_conflicts( $request );
    }
    global $wp_query;

    $permalinks_customizer = '';
    $original_permalink    = '';

    if ( is_single() || is_page() ) {
      $post = $wp_query->post;
      $permalinks_customizer = get_post_meta( $post->ID, 'permalink_customizer', true );
      $original_permalink    = ( $post->post_type == 'page' ? $this->original_page_link( $post->ID ) : $this->original_post_link( $post->ID ) );
    } else if ( is_tag() || is_category() || is_tax() ) {
      $theTerm                = $wp_query->get_queried_object();
      $permalinks_customizer  = $this->find_permalink_by_id( $theTerm->term_id );
      $original_permalink     = $this->original_taxonomy_link( $theTerm->term_id );
    }
    if ( $permalinks_customizer
      && ( substr( $request, 0, strlen( $permalinks_customizer ) ) != $permalinks_customizer
      || $request == $permalinks_customizer . '/' ) ) {
      $url = $permalinks_customizer;

      if ( substr( $request, 0, strlen( $original_permalink ) ) == $original_permalink
        && trim( $request, '/' ) != trim( $original_permalink, '/' ) ) {
        $url = preg_replace( '@//*@', '/', str_replace( trim( $original_permalink, '/' ), trim( $permalinks_customizer, '/' ), $request ) );
        $url = preg_replace( '@([^?]*)&@', '\1?', $url );
      }

      $url .= strstr( $_SERVER['REQUEST_URI'], '?' );

      wp_redirect( trailingslashit( home_url() ) . $url, 301 );
      exit();
    }
  }

  /**
   * Filter to replace the post permalink with the custom one.
   *
   * @access public
   * @since 0.1
   *
   * @param string $permalink
   *   Default WordPress Permalink of Post
   * @param object $post
   *   Post Details
   *
   * @return string $permalink
   *   Returns the customized Post Permalink
   */
  public function customized_post_link( $permalink, $post ) {
    $permalinks_customizer = get_post_meta( $post->ID, 'permalink_customizer', true );
    if ( $permalinks_customizer ) {
      $post_type = isset( $post->post_type ) ? $post->post_type : 'post';
      $language_code = apply_filters(
        'wpml_element_language_code', null,
        array( 'element_id' => $post->ID, 'element_type' => $post_type )
      );
      if ( $language_code )
        return apply_filters(
          'wpml_permalink', trailingslashit( home_url() ) . $permalinks_customizer, $language_code
        );
      else
        return apply_filters(
          'wpml_permalink', trailingslashit( home_url() ) . $permalinks_customizer
        );
    }
    return $permalink;
  }

  /**
   * Filter to replace the page permalink with the custom one.
   *
   * @access public
   * @since 0.1
   *
   * @param integer $permalink
   *   Default WordPress Permalink of Page
   * @param integer $page
   *   Page Details
   *
   * @return string $permalink
   *   Returns the customized Page Permalink
   */
  public function customized_page_link( $permalink, $page ) {
    $permalinks_customizer = get_post_meta( $page, 'permalink_customizer', true );
    if ( $permalinks_customizer ) {
      $language_code = apply_filters(
        'wpml_element_language_code', null,
        array( 'element_id' => $page, 'element_type' => 'page' )
      );
      if ( $language_code )
        return apply_filters(
          'wpml_permalink', trailingslashit( home_url() ) . $permalinks_customizer, $language_code
        );
      else
        return apply_filters(
          'wpml_permalink', trailingslashit( home_url() ) . $permalinks_customizer
        );
    }
    return $permalink;
  }

  /**
   * Filter to replace the term permalink with the custom one.
   *
   * @access public
   * @since 1.0
   *
   * @param string $permalink
   *   Default WordPress Permalink of Term
   * @param object $term
   *   Term Details
   *
   * @return string $permalink
   *   Returns the customized Term Permalink.
   */
  public function customized_term_link( $permalink, $term ) {
    if ( is_object( $term ) ) {
      $term = $term->term_id;
    }
    $permalinks_customizer = $this->find_permalink_by_id( $term );

    if ( $permalinks_customizer ) {
      $taxonomy = get_term( $term );
      if ( isset( $taxonomy ) && isset( $taxonomy->term_taxonomy_id ) ) {
        $term_type = isset( $taxonomy->taxonomy ) ? $taxonomy->taxonomy : 'category';
        $language_code = apply_filters(
          'wpml_element_language_code', null,
          array( 'element_id' => $taxonomy->term_taxonomy_id, 'element_type' => $term_type )
        );
        return apply_filters(
          'wpml_permalink', trailingslashit( home_url() ) . $permalinks_customizer, $language_code
        );
      } else {
        return apply_filters(
          'wpml_permalink', trailingslashit( home_url() ) . $permalinks_customizer
        );
      }
    }
    return $permalink;
  }

  /**
   * Find the Permalink for the provided term id.
   *
   * @access public
   * @since 0.1
   *
   * @param integer $id
   *   Term ID
   *
   * @return string $link or boolean
   *   Returns Term Link if found otherwise returns false
   */
  public function find_permalink_by_id( $id ) {

    $term_link = get_term_meta( $id, 'permalink_customizer', true );

    if ( $term_link ) {
      return $term_link;
    }

    $table = get_option( 'permalinks_customizer_table' );
    if ( $table ) {
      foreach ( $table as $link => $info ) {
        if ( $info['id'] == $id ) {
          return $link;
        }
      }
    }
    return false;
  }

  /**
   * Remove the post_link and post_type_link Filter for getting the
   * original Permalink of the Posts and set it back.
   *
   * @access public
   * @since 0.1
   *
   * @param integer $post_id
   *   Post ID
   *
   * @return string $original_permalink
   *   Returns the Original Permalink rather than the Permalink
   *   generated by this plugin for Posts.
   */
  public function original_post_link( $post_id ) {
    remove_filter( 'post_link', array( $this, 'customized_post_link' ), 10, 2 );
    remove_filter( 'post_type_link', array( $this, 'customized_post_link' ), 10, 2 );
    $wp_perm = get_permalink( $post_id );
    $original_permalink = ltrim(
      preg_replace( '|^(https?:)?//[^/]+(/.*)|i', '$2', $wp_perm ), '/'
    );
    add_filter( 'post_link', array( $this, 'customized_post_link' ), 10, 2 );
    add_filter( 'post_type_link', array( $this, 'customized_post_link' ), 10, 2 );
    return $original_permalink;
  }

  /**
   * Remove the page_link Filter for getting the original Permalink
   * of the Page and set it back.
   *
   * @access public
   * @since 0.1
   *
   * @param integer $post_id
   *   Page ID
   *
   * @return string $original_permalink
   *   Returns the Original Permalink rather than the Permalink
   *   generated by this plugin for the Page.
   */
  public function original_page_link( $post_id ) {
    remove_filter( 'page_link', array( $this, 'customized_page_link' ), 10, 2 );
    $wp_perm = get_permalink( $post_id );
    $original_permalink = ltrim(
      preg_replace( '|^(https?:)?//[^/]+(/.*)|i', '$2', $wp_perm ), '/'
    );
    add_filter( 'page_link', array( $this, 'customized_page_link' ), 10, 2 );
    return $original_permalink;
  }

  /**
   * Remove the term_link and user_trailingslashit Filter for getting
   * the original Permalink of the Term and set it back.
   *
   * @access public
   * @since 1.0
   *
   * @param integer $term_id
   *   Term ID
   *
   * @return string $original_permalink
   *   Returns the Original Permalink rather than the Permalink
   *   generated by this plugin for the Term.
   */
  public function original_taxonomy_link( $term_id ) {

    remove_filter( 'term_link', array( $this, 'customized_term_link' ), 10, 2 );
    remove_filter( 'user_trailingslashit',
      array( $this, 'apply_trailingslash' ), 10, 2
    );

    $term      = get_term( $term_id );
    $term_link = get_term_link( $term );

    add_filter( 'user_trailingslashit',
      array( $this, 'apply_trailingslash' ), 10, 2
    );
    add_filter( 'term_link', array( $this, 'customized_term_link' ), 10, 2 );

    if ( is_wp_error( $term_link ) ) {
      return '';
    }

    $original_permalink = ltrim(
      preg_replace( '|^(https?:)?//[^/]+(/.*)|i', '$2', $term_link ), '/'
    );

    return $original_permalink;
  }

  /**
   * Use to Add Trailing Slash.
   *
   * @access public
   * @since 0.1
   *
   * @param string $string
   *   String on trailingslash needs needs to be applied
   * @param integer $type
   *
   * @return string $string
   */
  public function apply_trailingslash( $string, $type ) {
    global $_CPRegisteredURL;

    remove_filter( 'user_trailingslashit',
      array( $this, 'apply_trailingslash' ), 10, 2
    );
    $url = parse_url( get_bloginfo( 'url' ) );
    $request = ltrim( isset( $url['path'] ) ? substr( $string, strlen( $url['path'] ) ) : $string, '/' );
    add_filter( 'user_trailingslashit',
      array( $this, 'apply_trailingslash' ), 10, 2
    );

    if ( ! trim( $request ) ) {
      return $string;
    }

    if ( trim( $_CPRegisteredURL, '/' ) == trim( $request, '/' ) ) {
      if ( isset( $url['path'] ) ) {
        return ( $string{0} == '/' ? '/' : '' ) . trailingslashit( $url['path'] ) . $_CPRegisteredURL;
      } else {
        return ( $string{0} == '/' ? '/' : '' ) . $_CPRegisteredURL;
      }
    }
    return $string;
  }

  /**
   * Check the requested URL has redirect if it has
   * then return the redirect URL.
   *
   * @access private
   * @since 2.0.0
   *
   * @param string $url
   *   URL which is requested by user
   *
   * @return string $return_uri
   *   Return URL on which it needs to be redirected or return empty string.
   */
  private function check_redirect( $url ) {
    $return_uri = '';
    if ( isset( $url ) && ! empty( $url ) ) {
      global $wpdb;

      $table_name = "{$wpdb->prefix}permalinks_customizer_redirects";

      $find_red = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name " .
        " WHERE redirect_from = %s AND enable = 1", $url
      ) );

      if ( isset( $find_red ) && is_object( $find_red )
        && isset( $find_red->redirect_to )
        && ! empty( $find_red->redirect_to ) ) {
        $return_uri = $find_red->redirect_to;
        $date       = date('Y-m-d h:i:s');
        $count      = $find_red->count + 1;
        $wpdb->query( "UPDATE $table_name SET count = " . $count . ", " .
          " last_accessed = '" . $date ."' WHERE id = " . $find_red->id ."" );
      }
    }
    return $return_uri;
  }
}
