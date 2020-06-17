<?php
/**
 * @package PermalinksCustomizer
 */

/**
 * show correct post/page, redirect if found.
 *
 * Show the correct page, make redirects if available and return correct permalink
 * for the different filters.
 *
 * @since 1.0.0
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
    add_filter( 'attachment_link', array( $this, 'customized_attachment_link' ), 10, 2 );
    add_filter( 'term_link', array( $this, 'customized_term_link' ), 10, 3 );
    add_filter( 'url_to_postid', array( $this, 'check_permalink' ), 10 );
    add_filter( 'user_trailingslashit', array( $this, 'apply_trailingslash' ), 10, 2 );

    // WPSEO Filters
    add_filter( 'wpseo_canonical', array( $this, 'fix_double_slash_canonical' ), 20, 1 );
  }

  /**
   * Filter to rewrite the query if we have a matching post.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $query Requested URL.
   *
   * @return string the URL which has to be parsed.
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
        if ( 'page' == $posts[0]->post_type ) {
          $original_link = $this->original_page_link( $posts[0]->ID );
        } elseif ( 'attachment' == $posts[0]->post_type ) {
          $original_link = $this->original_attachment_link( $posts[0]->ID );
        } else {
          $original_link = $this->original_post_link( $posts[0]->ID );
        }
        $original_url = preg_replace( '@/+@', '/', str_replace( trim( strtolower( $posts[0]->meta_value ), '/' ), $original_link, strtolower( $request_noslash ) ) );
      }
    }

    $term_checked = 0;
    if ( NULL !== $original_url && ( isset( $posts[0]->meta_value )
      && ( $request !== $posts[0]->meta_value
        && $request_noslash !== $posts[0]->meta_value
      )
    ) ) {
      $sql = $wpdb->prepare( "SELECT * FROM $wpdb->termmeta WHERE " .
        " meta_key = 'permalink_customizer' AND meta_value != '' AND " .
        " ( LOWER(meta_value) = LEFT(LOWER('%s'), LENGTH(meta_value)) OR " .
        " LOWER(meta_value) = LEFT(LOWER('%s'), LENGTH(meta_value)) ) " .
        " ORDER BY LENGTH(meta_value) DESC, $wpdb->termmeta.term_id ASC LIMIT 1",
        $request_noslash, $request_noslash . "/" );

      $taxonomy_term_data = $wpdb->get_results( $sql );

      if ( is_array( $taxonomy_term_data )
        && isset( $taxonomy_term_data[0]->meta_value )
        && ( $request === $taxonomy_term_data[0]->meta_value
          || $request_noslash === $taxonomy_term_data[0]->meta_value
        )
      ) {
        $original_url = NULL;
        $term_checked = 1;
      }
    }

    if ( NULL === $original_url ) {
      if ( 0 == $term_checked ) {
        $sql = $wpdb->prepare( "SELECT * FROM $wpdb->termmeta WHERE " .
          " meta_key = 'permalink_customizer' AND meta_value != '' AND " .
          " ( LOWER(meta_value) = LEFT(LOWER('%s'), LENGTH(meta_value)) OR " .
          " LOWER(meta_value) = LEFT(LOWER('%s'), LENGTH(meta_value)) ) " .
          " ORDER BY LENGTH(meta_value) DESC, $wpdb->termmeta.term_id ASC LIMIT 1",
          $request_noslash, $request_noslash . "/" );

        $taxonomy_term_data = $wpdb->get_results( $sql );
      }

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
      if ( isset( $wp->matched_rule ) ) {
        $wp->matched_rule = NULL;
      }
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
   * @since 1.0.0
   * @access public
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
      if ( 'page' == $post->post_type ) {
        $original_permalink = $this->original_page_link( $post->ID );
      } elseif ( 'attachment' == $post->post_type ) {
        $original_permalink = $this->original_attachment_link( $post->ID );
      } else {
        $original_permalink = $this->original_post_link( $post->ID );
      }
    } elseif ( is_tag() || is_category() || is_tax() ) {
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
   * @since 1.0.0
   * @access public
   *
   * @param string $permalink Default WordPress Permalink of Post.
   * @param object $post Post Details.
   *
   * @return string customized Post Permalink.
   */
  public function customized_post_link( $permalink, $post ) {
    $permalinks_customizer = get_post_meta( $post->ID, 'permalink_customizer', true );
    if ( $permalinks_customizer ) {
      $post_type = isset( $post->post_type ) ? $post->post_type : 'post';
      $language_code = apply_filters(
        'wpml_element_language_code', null,
        array( 'element_id' => $post->ID, 'element_type' => $post_type )
      );

      if ( $language_code ) {
        $permalink = apply_filters( 'wpml_permalink',
          trailingslashit( home_url() ) . $permalinks_customizer, $language_code
        );

        $site_url = site_url();
        $wpml_href = str_replace( $site_url, '', $permalink );
        if ( strpos( $wpml_href, '//' ) === 0 ) {
          if ( strpos( $wpml_href, '//' . $language_code  . '/' ) !== 0 ) {
            $permalink = $site_url . '/' . $language_code  . '/' . $permalinks_customizer;
          }
        }
      } else {
        $permalink = apply_filters( 'wpml_permalink',
          trailingslashit( home_url() ) . $permalinks_customizer
        );
      }

      $protocol = '';
      if ( 0 === strpos( $permalink, 'http://' ) ||
        0 === strpos( $permalink, 'https://' )
      ) {
        $split_protocol = explode( '://', $permalink );
        if ( 1 < count( $split_protocol ) ) {
          $protocol = $split_protocol[0] . '://';

          $permalink = str_replace( $protocol, '', $permalink );
        }
      }

      $permalink = str_replace( '//', '/', $permalink );
      $permalink = $protocol . $permalink;
    }

    return $permalink;
  }

  /**
   * Filter to replace the page permalink with the custom one.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $permalink Default WordPress Permalink of Page.
   * @param int $page Page ID.
   *
   * @return string customized Page Permalink.
   */
  public function customized_page_link( $permalink, $page ) {
    $permalinks_customizer = get_post_meta( $page, 'permalink_customizer', true );
    if ( $permalinks_customizer ) {
      $language_code = apply_filters(
        'wpml_element_language_code', null,
        array( 'element_id' => $page, 'element_type' => 'page' )
      );

      if ( $language_code ) {
        $permalink = apply_filters( 'wpml_permalink',
          trailingslashit( home_url() ) . $permalinks_customizer, $language_code
        );

        $site_url = site_url();
        $wpml_href = str_replace( $site_url, '', $permalink );
        if ( strpos( $wpml_href, '//' ) === 0 ) {
          if ( strpos( $wpml_href, '//' . $language_code  . '/' ) !== 0 ) {
            $permalink = $site_url . '/' . $language_code  . '/' . $permalinks_customizer;
          }
        }
      } else {
        $permalink = apply_filters( 'wpml_permalink',
          trailingslashit( home_url() ) . $permalinks_customizer
        );
      }

      $protocol = '';
      if ( 0 === strpos( $permalink, 'http://' ) ||
        0 === strpos( $permalink, 'https://' )
      ) {
        $split_protocol = explode( '://', $permalink );
        if ( 1 < count( $split_protocol ) ) {
          $protocol = $split_protocol[0] . '://';
          $permalink = str_replace( $protocol, '', $permalink );
        }
      }

      $permalink = str_replace( '//', '/', $permalink );
      $permalink = $protocol . $permalink;
    }

    return $permalink;
  }

  /**
   * Filter to replace the attachment permalink with the custom one.
   *
   * @since 2.2.0
   * @access public
   *
   * @param string $permalink Default WordPress Permalink of Attachment.
   * @param int $id Attachment ID.
   *
   * @return string customized Attachment Permalink.
   */
  public function customized_attachment_link( $permalink, $id ) {
    $permalinks_customizer = get_post_meta( $id, 'permalink_customizer', true );
    if ( $permalinks_customizer ) {
      $language_code = apply_filters(
        'wpml_element_language_code', null,
        array( 'element_id' => $id, 'element_type' => 'attachment' )
      );

      if ( $language_code ) {
        $permalink = apply_filters( 'wpml_permalink',
          trailingslashit( home_url() ) . $permalinks_customizer, $language_code
        );

        $site_url = site_url();
        $wpml_href = str_replace( $site_url, '', $permalink );
        if ( strpos( $wpml_href, '//' ) === 0 ) {
          if ( strpos( $wpml_href, '//' . $language_code  . '/' ) !== 0 ) {
            $permalink = $site_url . '/' . $language_code  . '/' . $permalinks_customizer;
          }
        }
      } else {
        $permalink = apply_filters( 'wpml_permalink',
          trailingslashit( home_url() ) . $permalinks_customizer
        );
      }

      $protocol = '';
      if ( 0 === strpos( $permalink, 'http://' ) ||
        0 === strpos( $permalink, 'https://' )
      ) {
        $split_protocol = explode( '://', $permalink );
        if ( 1 < count( $split_protocol ) ) {
          $protocol = $split_protocol[0] . '://';

          $permalink = str_replace( $protocol, '', $permalink );
        }
      }

      $permalink = str_replace( '//', '/', $permalink );
      $permalink = $protocol . $permalink;
    }

    return $permalink;
  }

  /**
   * Filter to replace the term permalink with the custom one.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $permalink Default WordPress Permalink of Term.
   * @param object $term Term Details.
   *
   * @return string customized Term Permalink.
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

        if ( $language_code ) {
          $permalink = apply_filters( 'wpml_permalink',
            trailingslashit( home_url() ) . $permalinks_customizer, $language_code
          );

          $site_url = site_url();
          $wpml_href = str_replace( $site_url, '', $permalink );
          if ( strpos( $wpml_href, '//' ) === 0 ) {
            if ( strpos( $wpml_href, '//' . $language_code  . '/' ) !== 0 ) {
              $permalink = $site_url . '/' . $language_code  . '/' . $permalinks_customizer;
            }
          }
        } else {
          $permalink = apply_filters( 'wpml_permalink',
            trailingslashit( home_url() ) . $permalinks_customizer
          );
        }
      } else {
        $permalink = apply_filters( 'wpml_permalink',
          trailingslashit( home_url() ) . $permalinks_customizer
        );
      }

      $protocol = '';
      if ( 0 === strpos( $permalink, 'http://' ) ||
        0 === strpos( $permalink, 'https://' )
      ) {
        $split_protocol = explode( '://', $permalink );
        if ( 1 < count( $split_protocol ) ) {
          $protocol = $split_protocol[0] . '://';

          $permalink = str_replace( $protocol, '', $permalink );
        }
      }

      $permalink = str_replace( '//', '/', $permalink );
      $permalink = $protocol . $permalink;
    }

    return $permalink;
  }

  /**
   * Find the Permalink for the provided term id.
   *
   * @since 1.0.0
   * @access public
   *
   * @param int $id Term ID.
   *
   * @return string or bool Term Link if found otherwise returns `false`.
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
   * @since 1.0.0
   * @access public
   *
   * @param int $post_id Post ID.
   *
   * @return string Original Permalink for Posts.
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
   * @since 1.0.0
   * @access public
   *
   * @param int $post_id Page ID.
   *
   * @return string Original Permalink for the Page.
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
   * Remove the attachment_link Filter for getting the original Permalink
   * of the Attachment Post and set it back.
   *
   * @since 2.2.0
   * @access public
   *
   * @param int $post_id Attachment ID.
   *
   * @return string Original Permalink for the Attachment.
   */
  public function original_attachment_link( $post_id ) {
    remove_filter( 'attachment_link',
      array( $this, 'customized_attachment_link' ), 10, 2
    );
    $wp_perm = get_permalink( $post_id );
    $original_permalink = ltrim(
      preg_replace( '|^(https?:)?//[^/]+(/.*)|i', '$2', $wp_perm ), '/'
    );
    add_filter( 'attachment_link',
      array( $this, 'customized_attachment_link' ), 10, 2
    );
    return $original_permalink;
  }

  /**
   * Remove the term_link and user_trailingslashit Filter for getting
   * the original Permalink of the Term and set it back.
   *
   * @since 1.0.0
   * @access public
   *
   * @param int $term_id Term ID.
   *
   * @return string Original Permalink for the Term.
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
   * Return default permalink against the customized permalink.
   *
   * @since 2.6.0
   * @access public
   *
   * @param string $permalink URL Permalink to check.
   *
   * @return string Default Permalink or the same permalink if not found.
   */
  public function check_permalink( $permalink ) {
    global $wpdb;

    if ( ! isset( $permalink ) || empty( $permalink ) ) {
      return $permalink;
    }

    $request = ltrim( $permalink, '/' );
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

    $post = $wpdb->get_row( $sql );

    if ( isset( $post ) && isset( $post->post_type ) ) {
      if ( 'page' === $post->post_type ) {
        $permalink = $this->original_page_link( $post->ID );
      } elseif ( 'attachment' === $post->post_type ) {
        $permalink = $this->original_attachment_link( $post->ID );
      } else {
        $permalink = $this->original_post_link( $post->ID );
      }
    }

    return $permalink;
  }

  /**
   * Use to Add Trailing Slash.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $string URL with or without a trailing slash.
   * @param int $type The type of URL being considered (e.g. single, category, etc) for use in the filter.
   *
   * @return string Adds/removes a trailing slash based on the permalink structure.
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
        return ( $string[0] == '/' ? '/' : '' ) . trailingslashit( $url['path'] ) . $_CPRegisteredURL;
      } else {
        return ( $string[0] == '/' ? '/' : '' ) . $_CPRegisteredURL;
      }
    }

    return $string;
  }

  /**
   * Check the requested URL has redirect if it has then return the redirect URL.
   *
   * @since 2.0.0
   * @access private
   *
   * @param string $url URL which is requested by the user.
   *
   * @return string URL on which it needs to be redirected or return empty string.
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

  /**
   * Fix double slash issue with canonical of Yoast SEO specially with WPML.
   *
   * @since 2.8.0
   * @access public
   *
   * @param string $canonical The canonical.
   *
   * @return string the canonical after removing double slash if exist.
   */
  public function fix_double_slash_canonical( $canonical ) {
    $protocol = '';
    if ( 0 === strpos( $canonical, 'http://' ) ||
      0 === strpos( $canonical, 'https://' )
    ) {
      $split_protocol = explode( '://', $canonical );
      if ( 1 < count( $split_protocol ) ) {
        $protocol = $split_protocol[0] . '://';
        $canonical = str_replace( $protocol, '', $canonical );
      }
    }

    $canonical = str_replace( '//', '/', $canonical );
    $canonical = $protocol . $canonical;

    return $canonical;
  }
}
