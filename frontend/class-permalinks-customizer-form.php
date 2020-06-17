<?php
/**
 * @package PermalinksCustomizer
 */

/**
 * Show Permalink edit form.
 *
 * Show edit form with the current permalink on PostTypes and Taxonomies.
 *
 * @since 1.0.0
 */
final class Permalinks_Customizer_Form {

  /**
   * To make decision either to show metabox or override the default permalink box.
   *
   * @since 2.3.0
   * @var int $permalink_metabox show metabox or override default permalink box.
   */
  private $permalink_metabox = 0;

  /**
   * Initialize WordPress Hooks.
   */
  public function init() {
    add_filter( 'get_sample_permalink_html',
      array( $this, 'post_edit_form' ), 10, 4
    );

    add_action( 'add_meta_boxes',  array( $this, 'permalink_edit_box' ) );

    add_filter( 'is_protected_meta',
      array( $this, 'make_meta_protected' ), 10, 3
    );

    add_action( 'save_post',
      array( $this, 'save_post_permalink' ), 10, 3
    );
    add_action( 'pmxi_saved_post',
      array( $this, 'pmxi_post_permalink' ), 10, 1
    );
    add_action( 'pc_generate_permalink',
      array( $this, 'pc_post_permalink' ), 10, 1
    );
    add_action( 'add_attachment',
      array( $this, 'create_attachment_post' ), 10, 1
    );
    add_action( 'edit_attachment',
      array( $this, 'update_attachment_post' ), 10, 1
    );

    add_action( 'delete_post', array( $this, 'delete_post_permalink' ), 10 );

    add_action( 'created_term',
      array( $this, 'create_term_permalink' ), 10, 3
    );
    add_action( 'edited_term',
      array( $this, 'update_term_permalink' ), 10, 3
    );
    add_action( 'delete_term',
      array( $this, 'delete_term_permalink' ), 10, 3
    );

    add_action( 'update_option_page_on_front',
      array( $this, 'static_front_page' ), 10, 2
    );
    add_action( 'init', array( $this, 'register_taxonomies_form' ) );

    add_action( 'admin_init', array( $this, 'add_bulk_option' ) );

    add_action( 'rest_api_init', array( $this, 'rest_edit_form' ) );

    add_action( 'admin_bar_menu',
      array( $this, 'add_toolbar_links' ), 999
    );
  }

  /**
   * Register meta box(es).
   *
   * @since 2.3.0
   * @access public
   */
  public function permalink_edit_box() {
    add_meta_box( 'permalinks-customizer-edit-box',
      __( 'Permalink', 'permalinks-customizer' ),
      array( $this, 'meta_edit_form' ), null, 'side', 'high',
      array(
        '__back_compat_meta_box' => false,
      )
    );
  }

  /**
   * Set the meta_keys to protected which is created by the plugin.
   *
   * @since 2.3.1
   * @access public
   *
   * @param bool $protected Whether the key is protected or not.
   * @param string $meta_key Meta key.
   * @param string $meta_type Meta type.
   *
   * @return bool `true` for the permalinks_customizer key.
   */
  public function make_meta_protected( $protected, $meta_key, $meta_type ) {
    if ( 'permalink_customizer' === $meta_key
      || 'permalink_customizer_regenerate_status' === $meta_key ) {
      $protected = true;
    }
    return $protected;
  }

  /**
   * Generate Form for editing the Permalinks for Post/Pages/Categories.
   *
   * @since 1.0.0
   * @access private
   *
   * @param string $permalink Permalink which is created by the plugin.
   * @param string $original Permalink which set by WordPress.
   * @param bool $renderContainers Shows Post/Term Edit.
   * @param string $postname Post Name.
   */
  private function get_form( $permalink, $original = '', $renderContainers = true, $postname = '' ) {
    $encoded_permalink = htmlspecialchars( urldecode( $permalink ) );
    echo '<input value="false" type="hidden" name="permalinks_customizer_regenerate_permalink" id="permalinks_customizer_regenerate_permalink" />' .
         '<input value="'. home_url() .'" type="hidden" name="permalinks_customizer_home_url" id="permalinks_customizer_home_url" />' .
         '<input value="' . $encoded_permalink . '" type="hidden" name="permalinks_customizer" id="permalinks_customizer" />';

    if ( $renderContainers ) {
      echo '<table class="form-table" id="permalinks_customizer_form">' .
           '<tr>' .
           '<th scope="row">' . __( 'Permalink', 'permalinks-customizer' ) . '</th>' .
           '<td>';
    }

    if ( '' == $permalink && defined( 'POLYLANG_VERSION' ) ) {
      require_once(
        PERMALINKS_CUSTOMIZER_PATH . 'frontend/class-permalinks-customizer-conflicts.php'
      );

      $conflicts = new Permalinks_Customizer_Conflicts();
      $original  = $conflicts->check_conflicts( $original );
    }

    $permalink_edit_value = htmlspecialchars( $permalink ? urldecode( $permalink ) : urldecode( $original ) );
    $original_permalink   = htmlspecialchars( urldecode( $original ) );

    $postname_html = '';
    if ( isset( $postname ) && '' != $postname ) {
      $postname_html = '<input type="hidden" id="new-post-slug" class="text" value="' . $postname . '" />';
    }

    $home_url   = trailingslashit( home_url() );
    $remove_url = apply_filters(
      'permalinks_customizer_remove_home_url', '__false'
    );
    if ( isset( $remove_url ) && ! empty( $remove_url )
      && '__false' !== $remove_url ) {
      $home_url = '/';
    }
    $permalink_edit_field = '';
    if ( isset( $this->permalink_metabox ) && 0 === $this->permalink_metabox ) {
      if ( '' !== $postname ) {
        $permalink_edit_field = '<label style="display:block;">URL</label>';
        $home_url = '';
      }
    }

    $permalink_edit_field .= $home_url .
                              '<span id="editable-post-name" title="Click to edit this part of the permalink">' .
                              $postname_html .
                              '<input type="text" id="permalinks-customizer-post-slug" class="text" value="' . $permalink_edit_value . '" style="width: 250px; color: #ddd;" />' .
                              '<input type="hidden" value="' . $original_permalink . '" id="original_permalink" />' .
                              '</span>';
    echo apply_filters( 'edit_permalink_field', $permalink_edit_field );

    if ( $renderContainers ) {
      wp_enqueue_script( 'permalink-customizer-admin', plugins_url( '/js/script-form.min.js', __FILE__ ), array(), false, true );
      if ( isset( $permalink ) && ! empty( $permalink ) ) {
        $view_url = trailingslashit( home_url() ) . $permalink;
        echo ' <span id="view-post-btn"><a href="' . $view_url . '" class="button button-small" target="_blank">View</a></span> <span id="regenerate_permalink"><a href="javascript:void(0);" class="button button-small">Regenerate Permalink</a></span>';
      } elseif ( isset( $original_permalink ) && ! empty( $original_permalink ) ) {
        $view_url = trailingslashit( home_url() ) . $original_permalink;
        echo ' <span id="view-post-btn"><a href="' . $view_url . '" class="button button-small" target="_blank">View</a></span> <span id="regenerate_permalink"><a href="javascript:void(0);" class="button button-small">Regenerate Permalink</a></span>';
      }
      echo '</td></tr></table>';
    }
  }

  /**
   * This is the Main Function which gets the Permalink Edit form for the user
   * with validating the Post Types.
   *
   * @since 1.0.0
   * @access public
   *
   * @param string $html WP Post Permalink HTML.
   * @param int $id Post ID.
   * @param string $new_title Post Title.
   * @param string $new_slug Post Slug.
   *
   * @return string Edit Form string.
   */
  public function post_edit_form( $html, $id, $new_title, $new_slug ) {
    $post                    = get_post( $id );
    $this->permalink_metabox = 1;

    if ( $post->ID == get_option( 'page_on_front' ) ) {
      return $html;
    }

    $args = array(
      'public' => true
    );

    $post_types = get_post_types( $args, 'objects' );
    if ( ! isset( $post_types[$post->post_type] ) ) {
      return $html;
    }

    // Filter to excluding PostTypes to be worked on by the plugin
    $exclude_post_types = $post->post_type;
    $excluded           = apply_filters(
      'permalinks_customizer_exclude_post_type', $exclude_post_types
    );
    if ( '__true' === $excluded ) {
      return $html;
    }

    $permalink = get_post_meta( $id, 'permalink_customizer', true );

    ob_start();
    $pc_frontend = new Permalinks_Customizer_Frontend;

    if ( 'page' == $post->post_type ) {
      $original_permalink = $pc_frontend->original_page_link( $id );
      $view_post = __( 'View Page', 'permalinks-customizer' );
    } elseif ( 'attachment' == $post->post_type ) {
      $original_permalink = $pc_frontend->original_attachment_link( $id );
      $view_post = __( 'View Attachment', 'permalinks-customizer' );
    } else {
      $original_permalink = $pc_frontend->original_post_link( $id );
      $view_post = __( 'View ' . ucfirst( $post->post_type ), 'permalinks-customizer' );
    }
    $this->get_form( $permalink, $original_permalink, false, $post->post_name );

    $content = ob_get_contents();
    ob_end_clean();

    if ( 'trash' != $post->post_status ) {
      wp_enqueue_script( 'permalink-customizer-admin',
        plugins_url( '/js/script-form.min.js', __FILE__ ), array(), false, true
      );
      if ( isset( $permalink ) && ! empty( $permalink ) ) {
        $view_url = trailingslashit( home_url() ) . $permalink;
        $content .= ' <span id="view-post-btn">' .
                    '<a href="' . $view_url . '" class="button button-small" target="_blank">' . $view_post . '</a>' .
                    '</span>' .
                    ' <span id="regenerate_permalink">' .
                    '<a href="javascript:void(0);" class="button button-small">Regenerate Permalink</a>' .
                    '</span><br>';
      } else {
        $view_url = trailingslashit( home_url() ) . $original_permalink;
        $content .= ' <span id="view-post-btn">' .
                    '<a href="' . $view_url . '" class="button button-small" target="_blank">' . $view_post .' </a>' .
                    '</span>' .
                    ' <span id="regenerate_permalink">' .
                    '<a href="javascript:void(0);" class="button button-small">Regenerate Permalink</a>' .
                    '</span><br>';
      }
    }
    if ( preg_match( "@view-post-btn.*?href='([^']+)'@s", $html, $matches ) ) {
      $permalink = $matches[1];
    } else {
      list( $permalink, $post_name ) = get_sample_permalink( $post->ID, $new_title, $new_slug );
      if ( false !== strpos( $permalink, '%pagename%' ) ) {
        $permalink = str_replace( '%pagename%', $post_name, $permalink );
      }
      if ( false !== strpos( $permalink, '%postname%' ) ) {
        $permalink = str_replace( '%postname%', $post_name, $permalink );
      }
    }

    return '<strong>' . __( 'Permalink', 'permalinks-customizer' ) . '</strong>: ' . $content;
  }

  /**
   * This is the Main Function which adds the Permalink Edit Meta box
   * for the user with validating the Post Types to make
   * compatibility with Gutenberg.
   *
   * @since 1.0.0
   * @access public
   *
   * @param object $post WP Post Object.
   */
  public function meta_edit_form( $post ) {
    if ( isset( $this->permalink_metabox ) && 1 === $this->permalink_metabox ) {
      wp_enqueue_script( 'permalink-customizer-admin',
        plugins_url( '/js/script-form.min.js', __FILE__ ), array(), false, true
      );
      return;
    }

    if ( $post->ID == get_option( 'page_on_front' ) ) {
      wp_enqueue_script( 'permalink-customizer-admin',
        plugins_url( '/js/script-form.min.js', __FILE__ ), array(), false, true
      );
      return;
    }

    $args = array(
      'public' => true
    );

    $post_types = get_post_types( $args, 'objects' );
    if ( ! isset( $post_types[$post->post_type] ) ) {
      wp_enqueue_script( 'permalink-customizer-admin',
        plugins_url( '/js/script-form.min.js', __FILE__ ), array(), false, true
      );
      return;
    }

    // Filter to excluding PostTypes to be worked on by the plugin
    $exclude_post_types = $post->post_type;
    $excluded           = apply_filters(
      'permalinks_customizer_exclude_post_type', $exclude_post_types
    );
    if ( '__true' === $excluded ) {
      wp_enqueue_script( 'permalink-customizer-admin',
        plugins_url( '/js/script-form.min.js', __FILE__ ), array(), false, true
      );
      return;
    }

    $screen = get_current_screen();
    if ( 'add' === $screen->action ) {
      echo '<input value="add" type="hidden" name="permalinks_customizer_add" id="permalinks_customizer_add" />';
    }

    $permalink = get_post_meta( $post->ID, 'permalink_customizer', true );
    if ( false !== strpos( $permalink, '%pagename%' ) ) {
      $permalink = str_replace( '%pagename%', $post_name, $permalink );
    }
    if ( false !== strpos( $permalink, '%postname%' ) ) {
      $permalink = str_replace( '%postname%', $post_name, $permalink );
    }

    ob_start();
    $pc_frontend = new Permalinks_Customizer_Frontend;

    if ( 'page' == $post->post_type ) {
      $original_permalink = $pc_frontend->original_page_link( $post->ID );
      $view_post = __( 'View Page', 'permalinks-customizer' );
    } elseif ( 'attachment' == $post->post_type ) {
      $original_permalink = $pc_frontend->original_attachment_link( $post->ID );
      $view_post = __( 'View Attachment', 'permalinks-customizer' );
    } else {
      $original_permalink = $pc_frontend->original_post_link( $post->ID );
      $view_post = __( 'View ' . ucfirst( $post->post_type ), 'permalinks-customizer' );
    }
    $this->get_form( $permalink, $original_permalink, false, $post->post_name );

    $content = ob_get_contents();
    ob_end_clean();

    if ( 'trash' != $post->post_status ) {
      wp_enqueue_script( 'permalink-customizer-admin',
        plugins_url( '/js/script-form.min.js', __FILE__ ), array(), false, true
      );
      $content .= '<label style="display:block;">Actions</label>';
      if ( isset( $permalink ) && ! empty( $permalink ) ) {
        $view_url = trailingslashit( home_url() ) . $permalink;
        $content .= ' <span id="view-post-btn">' .
                    '<a href="' . $view_url . '" class="button button-small" target="_blank">' . $view_post . '</a>' .
                    '</span>' .
                    ' <span id="regenerate_permalink">' .
                    '<a href="javascript:void(0);" class="button button-small">Regenerate Permalink</a>' .
                    '</span><br>';
      } else {
        $view_url = trailingslashit( home_url() ) . $original_permalink;
        $content .= ' <span id="view-post-btn">' .
                    '<a href="' . $view_url . '" class="button button-small" target="_blank">' . $view_post .' </a>' .
                    '</span>' .
                    ' <span id="regenerate_permalink">' .
                    '<a href="javascript:void(0);" class="button button-small">Regenerate Permalink</a>' .
                    '</span><br>';
      }
      $content .= '<style>.editor-post-permalink,.pc-permalink-hidden{display:none;}</style>';
    }

    echo $content;
  }

  /**
   * This is the Main Function which gets the Permalink Edit form for the user
   * with validating the Taxonomy.
   *
   * @since 1.0.0
   * @access public
   *
   * @param object $object Term Object.
   */
  public function term_edit_form( $object ) {
    $permalink = '';
    $original_permalink = '';
    if ( isset( $object ) && isset( $object->term_id ) ) {
      $pc_frontend = new Permalinks_Customizer_Frontend;
      $permalink = $pc_frontend->find_permalink_by_id( $object->term_id );

      if ( $object->term_id ) {
        $original_permalink = $pc_frontend->original_taxonomy_link( $object->term_id );
      }
    }

    $this->get_form( $permalink, $original_permalink );

    wp_enqueue_script('jquery');
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function() {
      var button = jQuery('#permalinks_customizer_form').parent().find('.submit');
      button.remove().insertAfter(jQuery('#permalinks_customizer_form'));
    });
    </script>
   <?php
  }

  /**
   * This Function call when the Post/Page created by the WP All Import Plugin.
   *
   * @since 1.0.0
   * @access public
   *
   * @param int $post_id Post ID.
   */
  public function pmxi_post_permalink( $post_id ) {
    $post = get_post( $post_id );
    if ( is_object( $post ) && isset( $post->post_type ) ) {
      $this->save_post_permalink( $post_id, $post, false );
    }
  }

  /**
   * Generates the permalink for the provided post id.
   *
   * @since 2.7.0
   * @access public
   *
   * @param int $post_id Post ID.
   */
  public function pc_post_permalink( $post_id ) {
    if ( $post_id ) {
      $post = get_post( $post_id );
      if ( is_object( $post ) && isset( $post->post_type ) ) {
        $this->save_post_permalink( $post_id, $post, false );
      }
    }
  }

  /**
   * This Function call when the Post/Page has been Saved. This function
   * generates the Permalink according the PostType Permalink Structure and
   * also saves the permalink if it is updated manually by user.
   *
   * @since 1.0.0
   * @access public
   *
   * @param int $post_id Post ID.
   * @param object $post Post Object.
   * @param bool $update Whether this is an existing post being updated or not.
   */
  public function save_post_permalink( $post_id, $post, $update ) {

    if ( 'auto-draft' === sanitize_title( $post->post_title ) ) {
      return;
    }

    if ( $post_id == get_option( 'page_on_front' ) ) {
      $this->delete_post_permalink( $post_id );
      return;
    }

    $args = array(
      'public' => true
    );

    $post_types = get_post_types( $args, 'objects' );
    if ( ! isset( $post_types[$post->post_type] ) ) {
      return;
    }

    // Filter to excluding PostTypes to be worked on by the plugin
    $exclude_post_types = $post->post_type;
    $excluded           = apply_filters(
      'permalinks_customizer_exclude_post_type', $exclude_post_types
    );
    if ( '__true' === $excluded ) {
      return;
    }

    $post_status = $post->post_status;
    if ( 'inherit' == $post_status && 'attachment' != $post->post_type ) {
      $post_id = $post->post_parent;
      $post    = '';
      $post    = get_post( $post_id );
    }

    $url              = get_post_meta( $post_id, 'permalink_customizer', true );
    $permalink_status = get_post_meta( $post_id, 'permalink_customizer_regenerate_status', true );
    $prev_url         = $url;

    if ( empty( $prev_url ) ) {
      $wp_perm  = get_permalink( $post_id );
      $prev_url = ltrim(
        preg_replace( '|^(https?:)?//[^/]+(/.*)|i', '$2', $wp_perm ), '/'
      );
    }

    /*
     * Make sure that the post saved from quick edit form so, just make the
     * $_REQUEST['permalinks_customizer'] same as $url to regenerate permalink
     * if applicable.
     */
    if ( ! isset( $_REQUEST['permalinks_customizer'] ) ) {
      $_REQUEST['permalinks_customizer'] = $url;
    }

    if ( ( empty( $url ) && 'trash' != $post_status )
      || ( ! empty( $url ) && $url == $_REQUEST['permalinks_customizer']
        && isset( $permalink_status ) && '' != $permalink_status
        && 1 != $permalink_status && 'trash' != $post_status )
        || ( isset( $_REQUEST['permalinks_customizer_regenerate_permalink'] )
        && 'true' === $_REQUEST['permalinks_customizer_regenerate_permalink']
        && 'trash' != $post_status )
    ) {

      $get_permalink = esc_attr(
        get_option( 'permalinks_customizer_' . $post->post_type )
      );
      if ( empty( $get_permalink ) ) {
        $get_permalink = esc_attr( get_option('permalink_structure' ) );
      }

      /*
       * Permalink structure doesn't be defined in the Plugin Settings and
       * Permalink Settings of WordPress set to plain.
       */
      if ( empty( $get_permalink ) ) {
        return;
      }

      $set_permalink = $this->replace_posttype_tags(
        $post_id, $post, $get_permalink
      );

      global $wpdb;
      $trailing_slash = substr( $set_permalink, -1 );
      if ( '/' == $trailing_slash ) {
        $set_permalink = rtrim( $set_permalink, '/' );
      }
      $permalink = $set_permalink;
      $qry       = "SELECT * FROM $wpdb->postmeta WHERE meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "' AND post_id != " . $post_id . " OR meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "/' AND post_id != " . $post_id . " LIMIT 1";
      $check_exist_url = $wpdb->get_results( $qry );
      if ( ! empty( $check_exist_url ) ) {
        $i = 2;
        while (1) {
          $permalink       = $set_permalink . '-' . $i;
          $qry             = "SELECT * FROM $wpdb->postmeta WHERE meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "' AND post_id != " . $post_id . " OR meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "/' AND post_id != " . $post_id . " LIMIT 1";
          $check_exist_url = $wpdb->get_results( $qry );
          if ( empty( $check_exist_url ) ) {
            break;
          }
          $i++;
        }
      }

      if ( '/' == $trailing_slash ) {
        $permalink = $permalink . '/';
      }

      if ( 0 === strpos( $permalink, '/' ) ) {
        $permalink = substr( $permalink, 1 );
      }

      $permalink = preg_replace( '/(\/+)/', '/', $permalink );
      $permalink = preg_replace( '/(\-+)/', '-', $permalink );
      update_post_meta( $post_id, 'permalink_customizer', $permalink );
      $_REQUEST['permalinks_customizer'] = $permalink;
      if ( 'publish' == $post_status ) {
        /*
         * permalink_customizer_regenerate_status = 1 means Permalink won't be
         * generated again on updating the post.
         */
        update_post_meta( $post_id, 'permalink_customizer_regenerate_status', 1 );
      } else {
        /*
         * permalink_customizer_regenerate_status = 0 means Permalink will be
         * generated again on updating the post.
         */
        update_post_meta( $post_id, 'permalink_customizer_regenerate_status', 0 );
      }

      // Add Redirect on regenrating the permalink
      if ( $update && 'trash' != $post_status && ( empty( $url )
        || ( isset( $_REQUEST['permalinks_customizer_regenerate_permalink'] )
        && 'true' === $_REQUEST['permalinks_customizer_regenerate_permalink'] ) )
      ) {
        $post_type = 'post';
        if ( isset( $post->post_type ) && ! empty( $post->post_type ) ) {
          $post_type = $post->post_type;
        }

        $this->add_auto_redirect( $prev_url, $permalink, $post_type, $post_id );
      }
    } elseif ( isset( $_REQUEST['permalinks_customizer'] )
      && ! empty( $_REQUEST['permalinks_customizer'] )
      && $url != $_REQUEST['permalinks_customizer']
    ) {
      $permalink = $_REQUEST['permalinks_customizer'];
      $permalink = preg_replace( '/(\/+)/', '/', $permalink );
      $permalink = preg_replace( '/(\-+)/', '-', $permalink );
      update_post_meta( $post_id, 'permalink_customizer', $permalink );
      /*
       * Permalink_customizer_regenerate_status = 1 means Permalink won't be
       * generated again on updating the post (Once, user changed it).
       */
      update_post_meta( $post_id, 'permalink_customizer_regenerate_status', 1 );

      $post_type = 'post';
      if ( isset( $post->post_type ) && ! empty( $post->post_type ) ) {
        $post_type = $post->post_type;
      }

      // Add Redirect on manually updating the post
      $this->add_auto_redirect( $prev_url, $permalink, $post_type, $post_id );
    }
  }

  /**
   * This Function call when the Attachment Post is created.
   *
   * @since 2.2.0
   * @access public
   *
   * @param int $post_id Post ID.
   */
  public function create_attachment_post( $post_id ) {
    $post = get_post( $post_id );
    if ( is_object( $post ) && isset( $post->post_type ) ) {
      $this->save_post_permalink( $post_id, $post, false );
    }
  }

  /**
   * This Function call when the Attachment Post is updated.
   *
   * @since 2.2.0
   * @access public
   *
   * @param int $post_id Post ID.
   */
  public function update_attachment_post( $post_id ) {
    $post = get_post( $post_id );
    if ( is_object( $post ) && isset( $post->post_type ) ) {
      $this->save_post_permalink( $post_id, $post, true );
    }
  }

  /**
   * Replace the tags with the respective value on generating the Permalink
   * for the Post types.
   *
   * @since 1.0.0
   * @access private
   *
   * @param int $post_id Post ID.
   * @param object $post The post object.
   * @param string $replace_tag Structure which is used to create permalink.
   *
   * @return string permalink after replacing the appropriate tags with their values.
   */
  private function replace_posttype_tags( $post_id, $post, $replace_tag ) {

    $date = new DateTime( $post->post_date );

    // Replace %title% with the respective sanitize value of the post title.
    if ( false !== strpos( $replace_tag, '%title%' ) ) {
      $title       = sanitize_title( $post->post_title );
      $replace_tag = str_replace( '%title%', $title, $replace_tag );
    }

    /*
     * Replace %parent_title% with the respective sanitize value of the post
     * title and its parent post title if parent post is selected.
     */
    if ( false !== strpos( $replace_tag, '%parent_title%' ) ) {
      $parents     = get_ancestors( $post_id, $post->post_type, 'post_type' );
      $post_titles = '';
      if ( $parents && is_array( $parents ) && ! empty( $parents )
        && count( $parents ) >= 1
      ) {
        $parent      = get_post( $parents[0] );
        $post_titles = sanitize_title( $parent->post_title ) . '/';
      }
      $post_titles .= sanitize_title( $post->post_title );

      $replace_tag = str_replace( '%parent_title%', $post_titles, $replace_tag );
    }

    /*
     * Replace %all_parents_title% with the respective sanitize value of the
     * post title and its parents post title if parent post is selected.
     */
    if ( false !== strpos( $replace_tag, '%all_parents_title%' ) ) {
      $parents     = get_ancestors( $post_id, $post->post_type, 'post_type' );
      $post_titles = '';
      if ( $parents && is_array( $parents ) && ! empty( $parents )
        && count( $parents ) >= 1
      ) {
        $i = count( $parents ) - 1;
        for ( $i; $i >= 0; $i-- ) {
          $parent       = get_post( $parents[$i] );
          $post_titles .= sanitize_title( $parent->post_title ) . '/';
        }
      }
      $post_titles .= sanitize_title( $post->post_title );

      $replace_tag = str_replace( '%all_parents_title%', $post_titles, $replace_tag );
    }

    // Replace %year% with the respective post publish date year.
    if ( false !== strpos( $replace_tag, '%year%' ) ) {
      $year        = $date->format( 'Y' );
      $replace_tag = str_replace( '%year%', $year, $replace_tag );
    }

    // Replace %monthnum% with the respective post publish date month number.
    if ( false !== strpos( $replace_tag, '%monthnum%' ) ) {
      $month       = $date->format( 'm' );
      $replace_tag = str_replace( '%monthnum%', $month, $replace_tag );
    }

    // Replace %day% with the respective post publish date day.
    if ( false !== strpos( $replace_tag, '%day%' ) ) {
      $day         = $date->format( 'd' );
      $replace_tag = str_replace( '%day%', $day, $replace_tag );
    }

    // Replace %hour% with the respective post publish date hour.
    if ( false !== strpos( $replace_tag, '%hour%' ) ) {
      $hour        = $date->format( 'H' );
      $replace_tag = str_replace( '%hour%', $hour, $replace_tag );
    }

    // Replace %minute% with the respective post publish date minute.
    if ( false !== strpos( $replace_tag, '%minute%' ) ) {
      $minute      = $date->format( 'i' );
      $replace_tag = str_replace( '%minute%', $minute, $replace_tag );
    }

    // Replace %second% with the respective post publish date second.
    if ( false !== strpos( $replace_tag, '%second%' ) ) {
      $second      = $date->format( 's' );
      $replace_tag = str_replace( '%second%', $second, $replace_tag );
    }

    // Replace %post_id% with the respective post id.
    if ( false !== strpos( $replace_tag, '%post_id%' ) ) {
      $replace_tag = str_replace( '%post_id%', $post_id, $replace_tag );
    }

    // Replace %postname% with the respective post name.
    if ( false !== strpos( $replace_tag, '%postname%' ) ) {
      if ( ! empty( $post->post_name ) ) {
        $replace_tag = str_replace( '%postname%', $post->post_name, $replace_tag );
      } else {
        $title       = sanitize_title( $post->post_title );
        $replace_tag = str_replace( '%postname%', $title, $replace_tag );
        if ( ! empty( $title ) ) {
          $this->update_post_name($post_id, $title);
        }
      }
    }

    /*
     * Replace %parent_postname% with the respective post name and its
     * parent post name if parent post is selected.
     */
    if ( false !== strpos( $replace_tag, '%parent_postname%' ) ) {
      $parents   = get_ancestors( $post_id, $post->post_type, 'post_type' );
      $postnames = '';
      if ( $parents && is_array( $parents ) && ! empty( $parents )
        && count( $parents ) >= 1 ) {
        $parent    = get_post( $parents[0] );
        $postnames = $parent->post_name . '/';
      }

      if ( ! empty( $post->post_name ) ) {
        $postnames .= $post->post_name;
      } else {
        $title      = sanitize_title( $post->post_title );
        $postnames .=  $title;
        if ( ! empty( $title ) ) {
          $this->update_post_name($post_id, $title);
        }
      }

      $replace_tag = str_replace( '%parent_postname%', $postnames, $replace_tag );
    }

    /*
     * Replace %all_parents_postname% with the respective post name and its
     * parents post name if parent post is selected.
     */
    if ( false !== strpos( $replace_tag, '%all_parents_postname%' ) ) {
      $parents   = get_ancestors( $post_id, $post->post_type, 'post_type' );
      $postnames = '';
      if ( $parents && is_array( $parents ) && ! empty( $parents )
        && count( $parents ) >= 1 ) {
        $i = count( $parents ) - 1;
        for ( $i; $i >= 0; $i-- ) {
          $parent     = get_post( $parents[$i] );
          $postnames .= $parent->post_name . '/';
        }
      }

      if ( ! empty( $post->post_name ) ) {
        $postnames .= $post->post_name;
      } else {
        $title      = sanitize_title( $post->post_title );
        $postnames .=  $title;
        if ( ! empty( $title ) ) {
          $this->update_post_name($post_id, $title);
        }
      }

      $replace_tag = str_replace( '%all_parents_postname%', $postnames, $replace_tag );
    }

    /*
     * Replace %category% with the respective post category with their
     * parent categories.
     */
    if (strpos( $replace_tag, '%category%' ) !== false ) {
      $categories = get_the_category( $post_id );
      $total_cat  = count( $categories );
      $tid = 1;
      if ( $total_cat > 0 && is_array( $categories ) ) {
        $tid = '';
        foreach ( $categories as $cat ) {
          if ( $cat->term_id < $tid || empty( $tid ) ) {
            $tid = $cat->term_id;
            $pid = '';
            if ( ! empty( $cat->parent ) ) {
              $pid = $cat->parent;
            }
          }
        }
      }
      $term_category = get_term( $tid );
      $category      = is_object( $term_category ) ? $term_category->slug : '';
      if ( ! empty( $pid ) ) {
        $parent_category = get_term( $pid );
        $category        = is_object( $parent_category ) ? $parent_category->slug . '/' . $category : '';
      }
      $replace_tag = str_replace( '%category%', $category, $replace_tag );
    }

    // Replace %child-category% with the respective post category.
    if ( false !== strpos( $replace_tag, '%child-category%' ) ) {
      $categories = get_the_category( $post_id );
      $total_cat  = count( $categories );
      $tid        = 1;
      if ( $total_cat > 0 && is_array( $categories ) ) {
        $tid = '';
        foreach( $categories as $cat ) {
          if ( $cat->term_id < $tid || empty( $tid ) ) {
            $tid = $cat->term_id;
          }
        }
      }
      $term_category = get_term( $tid );
      $category      = is_object( $term_category ) ? $term_category->slug : '';
      $replace_tag   = str_replace( '%child-category%', $category, $replace_tag );
    }

    /*
     * Replace %product_cat% with the respective post (Product Category).
     * Used with WooCommerce.
     */
    if ( false !== strpos( $replace_tag, '%product_cat%' ) ) {
      $categories = get_the_terms( $post_id, 'product_cat' );
      $total_cat  = count( $categories );
      $tid = 1;
      if ( $total_cat > 0 && is_array( $categories ) ) {
        $tid = '';
        foreach ( $categories as $cat ) {
          if ( $cat->term_id < $tid || empty( $tid ) ) {
            $tid = $cat->term_id;
            $pid = '';
            if ( ! empty( $cat->parent ) ) {
              $pid = $cat->parent;
            }
          }
        }
      }
      $term_category = get_term( $tid );
      $category      = is_object( $term_category ) ? $term_category->slug : '';
      if ( ! empty( $pid ) ) {
        $parent_category = get_term( $pid );
        $category        = is_object( $parent_category ) ? $parent_category->slug . '/' . $category : $category;
      }
      $replace_tag = str_replace( '%product_cat%', $category, $replace_tag );
    }

    // Replace <%ctax_custom_taxonomy%> with it's appropriate selected category.
    if ( false !== strpos( $replace_tag, '&lt;%ctax_' )
      && false !== strpos( $replace_tag, '%&gt;' ) ) {
      preg_match_all('/&lt;%ctax_(.*?)%&gt;/s', $replace_tag, $matches);
      foreach ( $matches[1] as $row ) {
        $ctax_name    = $row;
        $category     = '';
        $primary_term = '';
        if ( false !== strpos( $row, '??' ) ) {
          $split_ctax = explode( '??', $row );
          if ( isset( $split_ctax[0] ) && isset( $split_ctax[1] ) ) {
            $ctax_name = $split_ctax[0];
            $category  = $split_ctax[1];
          }
        }

        $ctax_tag = '&lt;%ctax_' . $row . '%&gt;';

        if ( class_exists('WPSEO_Primary_Term') ) {
          $wpseo_primary_term = new WPSEO_Primary_Term( $ctax_name, $post_id );
          $primary_term       = $wpseo_primary_term->get_primary_term();
        }

        $categories = get_the_terms( $post_id, $ctax_name );
        if ( ! is_wp_error( $categories ) && false !== $categories ) {
          if ( count( $categories ) > 0 && is_array( $categories ) ) {
            $tid = '';
            foreach ( $categories as $cat ) {
              if ( $cat->term_id < $tid || empty( $tid )
                || $cat->term_id == $primary_term ) {
                $tid = $cat->term_id;
                $pid = '';
                if ( ! empty( $cat->parent ) ) {
                  $pid = $cat->parent;
                }
                if ( $tid == $primary_term ) {
                  break;
                }
              }
            }
            $term_category = get_term( $tid );
            $category      = is_object( $term_category ) ? $term_category->slug : '';
            if ( ! empty( $pid ) ) {
              $parent_category = get_term( $pid );
              $category        = is_object( $parent_category ) ? $parent_category->slug . '/' . $category : $category;
            }
          }
        }
        $replace_tag = str_replace( $ctax_tag, $category, $replace_tag );
      }
    }

    // Replace <%ctaxparents_custom_taxonomy%> with it's appropriate selected category.
    if ( false !== strpos( $replace_tag, '&lt;%ctaxparents_' )
      && false !== strpos( $replace_tag, '%&gt;' ) ) {
      preg_match_all('/&lt;%ctaxparents_(.*?)%&gt;/s', $replace_tag, $matches);
      foreach ( $matches[1] as $row ) {
        $ctax_name    = $row;
        $category     = '';
        $primary_term = '';
        if ( false !== strpos( $row, '??' ) ) {
          $split_ctax = explode( '??', $row );
          if ( isset( $split_ctax[0] ) && isset( $split_ctax[1] ) ) {
            $ctax_name = $split_ctax[0];
            $category  = $split_ctax[1];
          }
        }

        $ctax_tag = '&lt;%ctaxparents_' . $row . '%&gt;';

        if ( class_exists('WPSEO_Primary_Term') ) {
          $wpseo_primary_term = new WPSEO_Primary_Term( $ctax_name, $post_id );
          $primary_term       = $wpseo_primary_term->get_primary_term();
        }

        $categories = get_the_terms( $post_id, $ctax_name );
        if ( ! is_wp_error( $categories ) && false !== $categories ) {
          if ( count( $categories ) > 0 && is_array( $categories ) ) {
            $tid = '';
            foreach ( $categories as $cat ) {
              if ( $cat->term_id < $tid || empty( $tid )
                || $cat->term_id == $primary_term ) {
                $tid = $cat->term_id;
                $pid = '';
                if ( ! empty( $cat->parent ) ) {
                  $pid = $cat->parent;
                }
                if ( $tid == $primary_term ) {
                  break;
                }
              }
            }
            $term_category = get_term( $tid );
            $category      = is_object( $term_category ) ? $term_category->slug : '';
            if ( ! empty( $pid ) ) {
              $parents      = get_ancestors( $tid, $ctax_name, 'taxonomy' );
              $parent_slugs = '';
              if ( $parents && ! empty( $parents ) && 1 <= count( $parents ) ) {
                $i            = count( $parents ) - 1;
                $parent_slugs = '';
                for ( $i; $i >= 0; $i-- ) {
                  $parent        = get_term( $parents[$i] );
                  $parent_slugs .= $parent->slug . '/';
                }
              }

              if ( ! empty( $parent_slugs ) ) {
                $category = $parent_slugs . '/' . $category;
              }
            }
          }
        }
        $replace_tag = str_replace( $ctax_tag, $category, $replace_tag );
      }
    }

    // Replace %author% with the author of the respective post.
    if ( false !== strpos( $replace_tag, '%author%' ) ) {
      $author      = get_the_author_meta( 'user_login', $post->post_author );
      $replace_tag = str_replace( '%author%', $author, $replace_tag );
    }

    // Replace %author_firstname% with the first name of author of the respective post.
    if ( false !== strpos( $replace_tag, '%author_firstname%' ) ) {
      $author_firstname = get_the_author_meta( 'first_name', $post->post_author );
      if ( $author_firstname && ! empty( $author_firstname ) ) {
        $author_firstname = strtolower( $author_firstname );
        $author_firstname = preg_replace( "/[\s]/", "-", $author_firstname );
        $replace_tag      = str_replace( '%author_firstname%', $author_firstname, $replace_tag );
      } else {
        $author      = get_the_author_meta( 'user_login', $post->post_author );
        $replace_tag = str_replace( '%author_firstname%', $author, $replace_tag );
      }
    }

    // Replace %author_lastname% with the lastname of author of the respective post.
    if ( false !== strpos( $replace_tag, '%author_lastname%' ) ) {
      $author_lastname = get_the_author_meta( 'last_name', $post->post_author );
      if ( $author_lastname && ! empty( $author_lastname ) ) {
        $author_lastname = strtolower( $author_lastname );
        $author_lastname = preg_replace( '/[\s]/', '-', $author_lastname );
        $replace_tag     = str_replace( '%author_lastname%', $author_lastname, $replace_tag );
      } else {
        $author      = get_the_author_meta( 'user_login', $post->post_author );
        $replace_tag = str_replace( '%author_lastname%', $author, $replace_tag );
      }
    }

    /*
     * Replace custom tag with the provided value by plugin/theme.
     */
    if ( false !== strpos( $replace_tag, '%pc_custom_posttype_tag%' ) ) {
      $custom_tag = apply_filters( 'pc_custom_posttype_tag', $post );

      $custom_tag  = strip_tags( $custom_tag );
      $replace_tag = str_replace(
        '%pc_custom_posttype_tag%', $custom_tag, $replace_tag
      );
    }

    return $replace_tag;
  }

  /**
   * Set `post_name` for the posts who doesn't have that.
   *
   * @since 2.1.1
   * @access private
   *
   * @param int $id Post ID.
   * @param string $post_name Post name which needs to be set.
   */
  private function update_post_name($id, $post_name) {
    global $wpdb;
    $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_name = %s " .
      " WHERE id = %d", $post_name, $id
    ) );
  }

  /**
   * Delete Permalink when the Post is deleted or when the saving Post is
   * selected as Front Page.
   *
   * @since 1.0.0
   * @access public
   *
   * @param int $id Post ID.
   */
  public function delete_post_permalink( $id ) {
    global $wpdb;
    $wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key = 'permalink_customizer' AND post_id = %d", $id ) );
  }

  /**
   * Call the function which generates the permalink on `created_term` action.
   *
   * @since 2.8.0
   * @access public
   *
   * @param int $id Term ID.
   */
  public function create_term_permalink( $id ) {
    $this->generate_term_permalink( $id, false );
  }

  /**
   * Call the Function which updates the permalink on `edited_term` action.
   *
   * @since 2.8.0
   * @access public
   *
   * @param int $id Term ID.
   */
  public function update_term_permalink( $id ) {
    $this->generate_term_permalink( $id, true );
  }

  /**
   * Generates the Permalink according to the Taxonomy settings and also
   * saves the permalink if it is updated manually by the user.
   *
   * @since 1.0.0
   * @access public
   *
   * @param int $id Term ID.
   * @param bool $term_updated `true` when called from the`edited_term` hook.
   */
  public function generate_term_permalink( $id, $term_updated ) {
    $new_permalink = '';
    if ( isset( $_REQUEST['permalinks_customizer'] ) ) {
      $new_permalink = ltrim( stripcslashes( $_REQUEST['permalinks_customizer'] ), '/' );
    }

    $term = get_term( $id );
    if ( empty( $new_permalink )
      || ( isset( $_REQUEST['permalinks_customizer_regenerate_permalink'] )
      && 'true' === $_REQUEST['permalinks_customizer_regenerate_permalink'] )
    ) {
      $permalinks_customizer_settings = get_option( 'permalinks_customizer_taxonomy_settings', '' );
      if ( is_string( $permalinks_customizer_settings ) ) {
        $permalinks_customizer_settings = unserialize( $permalinks_customizer_settings );
      }
      if ( isset( $permalinks_customizer_settings[$term->taxonomy . '_settings'] )
        && isset( $permalinks_customizer_settings[$term->taxonomy . '_settings']['structure'] )
        && ! empty( $permalinks_customizer_settings[$term->taxonomy . '_settings']['structure'] )
      ) {
        $new_permalink = $this->replace_term_tags( $term, $permalinks_customizer_settings[$term->taxonomy . '_settings']['structure'] );
      }
    }

    if ( empty( $new_permalink ) || '' == $new_permalink ) {
      return;
    }

    $pc_frontend   = new Permalinks_Customizer_Frontend;
    $old_permalink = $pc_frontend->original_taxonomy_link( $id );

    $regenerate = 0;
    if ( isset( $_REQUEST['permalinks_customizer_regenerate_permalink'] )
      && 'true' === $_REQUEST['permalinks_customizer_regenerate_permalink'] ) {
      $regenerate = 1;
    }

    if ( $new_permalink == $old_permalink && 0 == $regenerate) {
      return;
    }

    $this->save_term_permalink(
      $term, str_replace( '%2F', '/', urlencode( $new_permalink ) ),
      $old_permalink, $regenerate, $term_updated
    );
  }

  /**
   * Replace the tags with the respective value on generating the Permalink
   * for the Taxonmoies.
   *
   * @since 1.0.0
   * @access private
   *
   * @param object $term contains the list of saved values.
   * @param string $replace_tag Structure which is used to create permalink.
   *
   * @return string permalink after replacing the appropriate tags with their values.
   */
  private function replace_term_tags( $term, $replace_tag ) {

    if ( false !== strpos( $replace_tag, '%name%' ) ) {
      $name        = sanitize_title( $term->name );
      $replace_tag = str_replace( '%name%', $name, $replace_tag );
    }

    if ( false !== strpos( $replace_tag, '%term_id%' ) ) {
      $replace_tag = str_replace( '%term_id%', $term->term_id, $replace_tag );
    }

    if ( false !== strpos( $replace_tag, '%slug%' ) ) {
      if ( ! empty( $term->slug ) ) {
         $replace_tag = str_replace( '%slug%', $term->slug, $replace_tag );
      } else {
         $name        = sanitize_title( $term->name );
         $replace_tag = str_replace( '%slug%', $name, $replace_tag );
      }
    }

    if ( false !== strpos( $replace_tag, '%parent_slug%' ) ) {
      $parents    = get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' );
      $term_names = '';
      if ( $parents && ! empty( $parents ) && count( $parents ) >= 1 ) {
        $parent     = get_term( $parents[0] );
        $term_names = $parent->slug . '/';
      }

      if ( ! empty( $term->slug ) ) {
         $term_names .= $term->slug;
      } else {
         $title       = sanitize_title( $term->name );
         $term_names .=  $title;
      }

      $replace_tag = str_replace( '%parent_slug%', $term_names, $replace_tag );
    }

    if ( false !== strpos( $replace_tag, '%all_parents_slug%' ) ) {
      $parents    = get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' );
      $term_names = '';
      if ( $parents && ! empty( $parents ) && count( $parents ) >= 1 ) {
        $i = count( $parents ) - 1;
        for ( $i; $i >= 0; $i-- ) {
          $parent      = get_term( $parents[$i] );
          $term_names .= $parent->slug . '/';
        }
      }

      if ( ! empty( $term->slug ) ) {
         $term_names .= $term->slug;
      } else {
         $title       = sanitize_title( $term->name );
         $term_names .=  $title;
      }

      $replace_tag = str_replace( '%all_parents_slug%', $term_names, $replace_tag );
    }

    /*
     * Replace custom tag with the provided value by plugin/theme.
     */
    if ( false !== strpos( $replace_tag, '%pc_custom_taxonomy_tag%' ) ) {
      $custom_tag = apply_filters( 'pc_custom_taxonomy_tag', $term );

      $custom_tag  = strip_tags( $custom_tag );
      $replace_tag = str_replace(
        '%pc_custom_taxonomy_tag%', $custom_tag, $replace_tag
      );
    }

    return $replace_tag;
  }

  /**
   * Save Permalink for the Term.
   *
   * @since 1.3.0
   * @access private
   *
   * @param object $term Term Object.
   * @param string $permalink New permalink which needs to be saved.
   * @param string $prev Previously saved permalink.
   * @param string $regenerate `1` for Permaink Regenerating else for creating permalink.
   * @param string $update Whether this is an existing term being updated or not.
   */
  private function save_term_permalink( $term, $permalink, $prev, $regenerate, $update ) {
    $url = get_term_meta( $term->term_id, 'permalink_customizer' );
    if ( empty( $url ) || 1 == $regenerate ) {
      global $wpdb;
      $trailing_slash = substr( $permalink, -1 );
      if ( '/' == $trailing_slash ) {
        $permalink = rtrim( $permalink, '/' );
      }
      $set_permalink = $permalink;
      $qry = "SELECT * FROM $wpdb->termmeta WHERE meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "' AND term_id != " . $term->term_id . " OR meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "/' AND term_id != " . $term->term_id . " LIMIT 1";
      $check_exist_url = $wpdb->get_results( $qry );
      if ( ! empty( $check_exist_url ) ) {
        $i = 2;
        while (1) {
          $permalink = $set_permalink . '-' . $i;
          $qry = "SELECT * FROM $wpdb->termmeta WHERE meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "' AND term_id != " . $term->term_id . " OR meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "/' AND term_id != " . $term->term_id . " LIMIT 1";
          $check_exist_url = $wpdb->get_results( $qry );
          if ( empty( $check_exist_url ) ) {
            break;
          }
          $i++;
        }
      }

      if ( '/' == $trailing_slash ) {
        $permalink = $permalink . '/';
      }

      if ( 0 === strpos( $permalink, '/' ) ) {
        $permalink = substr( $permalink, 1 );
      }
    }

    update_term_meta( $term->term_id, 'permalink_customizer', $permalink );

    $taxonomy = 'category';
    if ( isset( $term->taxonomy ) && ! empty( $term->taxonomy ) ) {
      $taxonomy = $term->taxonomy;
    }

    if ( ! $update ) {
      return;
    }

    if ( ! empty( $permalink ) && ! empty( $prev ) && $permalink != $prev  ) {
      $this->add_auto_redirect( $prev, $permalink, $taxonomy, $term->term_id );
    }
  }

  /**
   * Delete Permalink when the Term is deleted.
   *
   * @since 1.0.0
   * @access public
   *
   * @param int $id Term ID.
   */
  public function delete_term_permalink( $id ) {
    global $wpdb;
    $wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->termmeta WHERE meta_key = 'permalink_customizer' AND term_id = %d", $id ) );

    $table = get_option( 'permalinks_customizer_table' );
    if ( $table ) {
      foreach ( $table as $link => $info ) {
        if ( $info['id'] == $id ) {
          unset( $table[$link] );
          break;
        }
      }
    }

    update_option( 'permalinks_customizer_table', $table );
  }

  /**
   * Delete the Permalink for the Page selected as the Front Page.
   *
   * @since 1.0.0
   * @access public
   *
   * @param int $prev_front_page_id Page ID of previously set Front Page.
   * @param int $new_front_page_id Page ID of current Front Page.
   */
  public function static_front_page( $prev_front_page_id, $new_front_page_id ) {
    $this->delete_post_permalink( $new_front_page_id );
  }

  /**
   * Register Taxonomy to show Permalink Add/Edit Form.
   *
   * @since 1.3.0
   * @access public
   */
  public function register_taxonomies_form() {
    $args = array(
      'public' => true
    );
    $taxonomies = get_taxonomies( $args, 'names' );
    foreach ( $taxonomies as $taxonomy ) {
      if ( 'nav_menu' == $taxonomy ) {
        continue;
      }
      add_action( $taxonomy . '_add_form', array( $this, 'term_edit_form' ) );
      add_action( $taxonomy . '_edit_form', array( $this, 'term_edit_form' ) );
    }
  }

  /**
   * Add Redirect on regenerating or manual updating the permalink
   *
   * @since 2.0.0
   * @access private
   *
   * @param string $redirect_from Previous permalink or url.
   * @param string $redirect_to Current permalink or url.
   * @param string $type Post Name or Term Name.
   * @param int $id Post ID or Term ID.
   */
  private function add_auto_redirect( $redirect_from, $redirect_to, $type, $id ) {
    $redirect_filter = apply_filters(
      'permalinks_customizer_auto_created_redirects', '__true'
    );
    if ( $redirect_from !== $redirect_to && '__true' === $redirect_filter ) {
      global $wpdb;

      $table_name = "{$wpdb->prefix}permalinks_customizer_redirects";

      $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET enable = 0 " .
        " WHERE redirect_from = %s", $redirect_to
      ) );

      $post_perm = 'p=' . $id;
      $page_perm = 'page_id=' . $id;
      if ( 0 === strpos( $redirect_from, '?' ) ) {
        if ( false !== strpos( $redirect_from, $post_perm )
          || false !== strpos( $redirect_from, $page_perm ) ) {
          return;
        }
      }

      $find_red = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name " .
        " WHERE redirect_from = %s AND redirect_to = %s", $redirect_from,
        $redirect_to
      ) );

      if ( isset( $find_red ) && is_object( $find_red )
        && isset( $find_red->id ) ) {

        if ( isset( $find_red->enable ) && 0 == $find_red->enable ) {
          $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET enable = 1 " .
            " WHERE id = %d", $find_red->id
          ) );
        }
      } else {
        $redirect_added = $wpdb->insert( $table_name, array(
          'redirect_from'   => $redirect_from,
          'redirect_to'     => $redirect_to,
          'type'            => $type,
          'redirect_status' => 'auto',
          'enable'          => 1,
        ));
      }
    }
  }

  /**
   * Add `Regenerate Permalink` option in Bulk Action.
   *
   * @since 2.0.0
   * @access public
   */
  public function add_bulk_option() {
    $args = array(
      'public' => true
    );

    $post_types = get_post_types( $args, 'objects' );
    foreach ( $post_types as $post_type ) {
      if ( 'attachment' == $post_type->name ) {
        add_filter( 'bulk_actions-upload', array( $this, 'bulk_option' ) );
        add_filter( 'handle_bulk_actions-upload',
          array( $this, 'bulk_posttype_regenerate' ), 10, 3
        );
      }
      add_filter( 'bulk_actions-edit-' . $post_type->name,
        array( $this, 'bulk_option' )
      );
      add_filter( 'handle_bulk_actions-edit-' . $post_type->name,
        array( $this, 'bulk_posttype_regenerate' ), 10, 3
      );
    }

    $taxonomies = get_taxonomies( $args, 'objects' );
    foreach ( $taxonomies as $taxonomy ) {
      add_filter( 'bulk_actions-edit-' . $taxonomy->name,
        array( $this, 'bulk_option' )
      );
      add_filter( 'handle_bulk_actions-edit-' . $taxonomy->name,
        array( $this, 'bulk_term_regenerate' ), 10, 3
      );
    }
  }

  /**
   * Add Regenerate Permalink option in bulk action.
   *
   * @since 2.0.0
   * @access public
   *
   * @param array $actions Contains the list of actions.
   *
   * @return array the bulk actions with adding the Regenerate Permalink.
   */
  public function bulk_option( $actions ) {
    $action           = 'permalinks_customizer_regenerate';
    $actions[$action] = __( 'Regenerate Permalinks', 'permalinks-customizer' );
    return $actions;
  }

  /**
   * Regenerate Permalink only or with adding redirect against the old permalink
   * of the selected posts/taxonomies.
   *
   * @since 2.0.0
   * @access public
   *
   * @param string $redirect_to URL on which needs to be redirected.
   * @param string $doaction Action that has been requested.
   * @param array $post_ids List of Term IDs.
   *
   * @return string Redirect URI or Redirect URI with adding query argument.
   */
  public function bulk_posttype_regenerate( $redirect_to, $doaction, $post_ids ) {
    if ( 'permalinks_customizer_regenerate' !== $doaction ) {
      return $redirect_to;
    }
    $post_struct = '';
    $generated   = 0;
    $error       = 0;

    global $wpdb;
    foreach ( $post_ids as $id ) {
      $post = get_post( $id );
      if ( '' == $post_struct ) {
        $post_struct = esc_attr(
          get_option( 'permalinks_customizer_' . $post->post_type )
        );
        if ( empty( $post_struct ) ) {
          $post_struct = esc_attr( get_option('permalink_structure' ) );
        }
        if ( empty( $post_struct ) ) {
          $error = 2;
          break;
        }
      }

      $prev_url = get_post_meta( $id, 'permalink_customizer', true );
      if ( empty( $prev_url ) ) {
        $wp_perm  = get_permalink( $id );
        $prev_url = ltrim(
          preg_replace( '|^(https?:)?//[^/]+(/.*)|i', '$2', $wp_perm ), '/'
        );
      }

      $set_permalink = $this->replace_posttype_tags( $id, $post, $post_struct );

      $trailing_slash = substr( $set_permalink, -1 );
      if ( '/' == $trailing_slash ) {
        $set_permalink = rtrim( $set_permalink, '/' );
      }
      $permalink = $set_permalink;
      $qry       = "SELECT * FROM $wpdb->postmeta WHERE meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "' AND post_id != " . $id . " OR meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "/' AND post_id != " . $id . " LIMIT 1";
      $check_exist_url = $wpdb->get_results( $qry );
      if ( ! empty( $check_exist_url ) ) {
        $i = 2;
        while (1) {
          $permalink       = $set_permalink . '-' . $i;
          $qry             = "SELECT * FROM $wpdb->postmeta WHERE meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "' AND post_id != " . $id . " OR meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "/' AND post_id != " . $id . " LIMIT 1";
          $check_exist_url = $wpdb->get_results( $qry );
          if ( empty( $check_exist_url ) ) {
            break;
          }
          $i++;
        }
      }

      if ( '/' == $trailing_slash ) {
        $permalink = $permalink . '/';
      }

      if ( 0 === strpos( $permalink, '/' ) ) {
        $permalink = substr( $permalink, 1 );
      }

      $permalink = preg_replace( '/(\/+)/', '/', $permalink );
      $permalink = preg_replace( '/(\-+)/', '-', $permalink );
      if ( $prev_url == $permalink ) {
        continue;
      }
      update_post_meta( $id, 'permalink_customizer', $permalink );
      if ( 'publish' == $post->post_status ) {
        /*
         * permalink_customizer_regenerate_status = 1 means Permalink won't be
         * generated again on updating the post.
         */
        update_post_meta( $id, 'permalink_customizer_regenerate_status', 1 );
      } else {
        /*
         * permalink_customizer_regenerate_status = 0 means Permalink will be
         * generated again on updating the post.
         */
        update_post_meta( $id, 'permalink_customizer_regenerate_status', 0 );
      }

      $post_type = 'post';
      if ( isset( $post->post_type ) && ! empty( $post->post_type ) ) {
        $post_type = $post->post_type;
      }

      $this->add_auto_redirect( $prev_url, $permalink, $post_type, $id );

      $generated++;
    }

    if ( 2 === $error ) {
      $redirect_to = remove_query_arg( 'regenerated_permalink', $redirect_to );
      $redirect_to = add_query_arg(
        'regenerated_permalink_error', $error, $redirect_to
      );
    } else {
      $redirect_to = remove_query_arg(
        'regenerated_permalink_error', $redirect_to
      );
      $redirect_to = add_query_arg(
        'regenerated_permalink', $generated, $redirect_to
      );
    }
    return $redirect_to;
  }

  /**
   * Regenerate Permalink only or with adding redirect against the old permalink
   * of the selected posts/taxonomies.
   *
   * @since 2.0.0
   * @access public
   *
   * @param string $redirect_to URL on which needs to be redirected.
   * @param string $doaction Action that has been requested.
   * @param array $term_ids List of Term IDs.
   *
   * @return string Redirect URI or Redirect URI with adding query argument.
   */
  public function bulk_term_regenerate( $redirect_to, $doaction, $term_ids ) {
    if ( 'permalinks_customizer_regenerate' !== $doaction ) {
      return redirect_to;
    }
    $settings = get_option( 'permalinks_customizer_taxonomy_settings', '' );
    if ( is_string( $settings ) ) {
      $settings = unserialize( $settings );
    }
    $term_struct = '';
    $error       = 0;
    $generated   = 0;
    foreach ( $term_ids as $id ) {
      $new_permalink = '';
      $term          = get_term( $id );
      if ( '' == $term_struct ) {
        if ( isset( $settings[$term->taxonomy . '_settings'] )
          && isset( $settings[$term->taxonomy . '_settings']['structure'] )
          && ! empty( $settings[$term->taxonomy . '_settings']['structure'] ) ) {
          $term_struct = $settings[$term->taxonomy . '_settings']['structure'];
        } else {
          $error = 1;
          break;
        }
      }

      $new_permalink = $this->replace_term_tags( $term, $term_struct );

      if ( '' == $new_permalink ) {
        continue;
      }

      $pc_frontend   = new Permalinks_Customizer_Frontend;
      $old_permalink = $pc_frontend->original_taxonomy_link( $id );

      if ( $new_permalink == $old_permalink ) {
        continue;
      }

      $this->save_term_permalink(
        $term, str_replace( '%2F', '/', urlencode( $new_permalink ) ),
        $old_permalink, 1, true
      );
      $generated++;
    }
    if ( 1 === $error ) {
      $redirect_to = remove_query_arg( 'regenerated_permalink' );
      $redirect_to = add_query_arg(
        'regenerated_permalink_error', $error, $redirect_to
      );
    } else {
      $redirect_to = remove_query_arg( 'regenerated_permalink_error' );
      $redirect_to = add_query_arg(
        'regenerated_permalink', $generated, $redirect_to
      );
    }
    return $redirect_to;
  }

  /**
   * Added Custom Endpoints for refreshing the permalink.
   *
   * @since 2.4.0
   * @access public
   */
  public function rest_edit_form() {
    register_rest_route( 'permalinks-customizer/v1',
      '/get-permalink/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => array( $this, 'refresh_meta_form' )
      )
    );
  }

  /**
   * Refresh Permalink using AJAX Call.
   *
   * @since 2.4.0
   * @access public
   *
   * @param object $data Contains post id with some default REST Values.
   */
  public function refresh_meta_form( $data ) {
    if ( isset( $data['id'] ) && is_numeric( $data['id'] ) ) {
      $post = get_post( $data['id'] );
      $all_permalinks = array();
      $all_permalinks['permalink_customizer'] = get_post_meta( $data['id'], 'permalink_customizer', true );
      $pc_frontend = new Permalinks_Customizer_Frontend;
      if ( 'page' == $post->post_type ) {
        $all_permalinks['original_permalink'] = $pc_frontend->original_page_link( $data['id'] );
      } elseif ( 'attachment' == $post->post_type ) {
        $all_permalinks['original_permalink'] = $pc_frontend->original_attachment_link( $data['id'] );
      } else {
        $all_permalinks['original_permalink'] = $pc_frontend->original_post_link( $data['id'] );
      }
      echo json_encode( $all_permalinks );
      exit;
    }
  }

  /**
   * Add `Flush Permalinks Cache` link in Admin Toolbar.
   *
   * @since 2.4.0
   * @access public
   *
   * @param object $wp_admin_bar Contain Toolbar links.
   */
  public function add_toolbar_links( $wp_admin_bar ) {
    $child_args = array();
    $post_args  = array();
    $tax_args   = array();

    if ( current_user_can( 'pc_manage_permalink_settings' ) ) {
      $set1 = 'wp-admin/admin.php?page=permalinks-customizer-posts-settings';
      $set2 = 'wp-admin/admin.php?page=permalinks-customizer-taxonomies-settings';

      $post_args[] = array(
        'id'     => 'permalinks-customizer-posts-settings',
        'title'  => __( 'Settings', 'permalinks-customizer' ),
        'parent' => 'permalinks-customizer-posttypes',
        'href'   => trailingslashit( home_url() ) . $set1,
        'meta'   => array(
          'title' => __( 'PostTypes Settings', 'permalinks-customizer' ),
        ),
      );
      $tax_args[] = array(
        'id'     => 'permalinks-customizer-taxonomies-settings',
        'title'  => __( 'Settings', 'permalinks-customizer' ),
        'parent' => 'permalinks-customizer-taxonomies',
        'href'   => trailingslashit( home_url() ) . $set2,
        'meta'   => array(
          'title' => __( 'Taxonomies Settings', 'permalinks-customizer' ),
        ),
      );
    }

    if ( current_user_can( 'pc_manage_permalinks' ) ) {
      $perm1 = 'wp-admin/admin.php?page=permalinks-customizer-post-permalinks';
      $perm2 = 'wp-admin/admin.php?page=permalinks-customizer-taxonomy-permalinks';

      $post_args[] =  array(
        'id'     => 'permalinks-customizer-posts-permalinks',
        'title'  => __( 'Permalinks', 'permalinks-customizer' ),
        'parent' => 'permalinks-customizer-posttypes',
        'href'   => trailingslashit( home_url() ) . $perm1,
        'meta'   => array(
          'title' => __( 'PostTypes Permalinks', 'permalinks-customizer' ),
        ),
      );

      $tax_args[] = array(
        'id'     => 'permalinks-customizer-taxonomies-permalinks',
        'title'  => __( 'Permalinks', 'permalinks-customizer' ),
        'parent' => 'permalinks-customizer-taxonomies',
        'href'   => trailingslashit( home_url() ) . $perm2,
        'meta'   => array(
          'title' => __( 'Taxonomies Permalinks', 'permalinks-customizer' ),
        ),
      );
    }

    if ( ! empty( $post_args ) ) {
      $child_args[] = array(
        'id'     => 'permalinks-customizer-posttypes',
        'title'  => __( 'PostTypes', 'permalinks-customizer' ),
        'parent' => 'permalinks-customizer',
      );

      $child_args = array_merge( $child_args, $post_args );
    }

    if ( ! empty( $tax_args ) ) {
      $child_args[] = array(
        'id'     => 'permalinks-customizer-taxonomies',
        'title'  => __( 'Taxonomies', 'permalinks-customizer' ),
        'parent' => 'permalinks-customizer',
      );

      $child_args = array_merge( $child_args, $tax_args );
    }

    if ( current_user_can( 'pc_manage_permalink_redirects' ) ) {
      $redirects = 'wp-admin/admin.php?page=permalinks-customizer-redirects';

      $child_args[] = array(
        'id'     => 'permalinks-customizer-redirects',
        'title'  => __( 'Redirects', 'permalinks-customizer' ),
        'parent' => 'permalinks-customizer',
        'href'   => trailingslashit( home_url() ) . $redirects,
        'meta'   => array(
          'title' => __( 'Redirects', 'permalinks-customizer' ),
        )
      );
    }

    if ( current_user_can( 'pc_manage_permalinks' ) ) {
      $cache = 'wp-admin/admin.php?page=permalinks-customizer-posts-settings&cache=1';

      $child_args[] = array(
        'id'     => 'permalinks-customizer-flush-cache',
        'title'  => __( 'Flush Permalinks Cache', 'permalinks-customizer' ),
        'parent' => 'permalinks-customizer',
        'href'   => trailingslashit( home_url() ) . $cache,
        'meta'   => array(
          'title'  => __( 'Flush Permalinks Cache', 'permalinks-customizer' ),
        ),
      );
    }

    if ( ! empty( $child_args ) ) {
      $parent_args = array(
        'id'    => 'permalinks-customizer',
        'title' => __( 'Permalinks Customizer', 'permalinks-customizer' ),
      );
      $wp_admin_bar->add_node( $parent_args );

      foreach( $child_args as $each_arg )  {
        $wp_admin_bar->add_node( $each_arg );
      }
    }
  }
}
