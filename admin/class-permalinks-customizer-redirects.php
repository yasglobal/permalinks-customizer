<?php
/**
 * @package PermalinksCustomizer
 */

/**
 * Create Redirects page.
 *
 * Create Redirects page HTML and display the page.
 *
 * @since 2.0.0
 */
class Permalinks_Customizer_Redirects {

  /**
   * Call Redirect Function.
   */
  function __construct() {
    $this->show_redirects();
  }

  /**
   * Shows all the Redirects created by using this Plugin with Pager
   * and Search Functionality.
   *
   * @since 2.0.0
   * @access private
   */
  private function show_redirects() {
    global $wpdb;

    $filter_options   = '';
    $search_permalink = '';
    $page_html        = '';

    if ( isset( $_GET['_wpnonce'] )
      && wp_verify_nonce( $_GET['_wpnonce'], 'permalinks-customizer_redirects' )
    ) {

      // Handle Bulk Operations
      if ( isset( $_GET['permalink'] ) && ! empty( $_GET['permalink'] )
        && ( isset( $_GET['action'] ) || isset( $_GET['action2'] ) )
      ) {
        $rids = implode( ',', $_GET['permalink'] );
        if ( 'delete' === $_GET['action'] || 'delete' === $_GET['action2'] ) {
          if ( preg_match( '/^\d+(?:,\d+)*$/', $rids ) ) {
            $wpdb->query( "DELETE FROM {$wpdb->prefix}permalinks_customizer_redirects WHERE id IN ($rids)" );

            $action_comp = array(
              'action' => wp_kses( 'deleted', array() ),
              'total'  => wp_kses( count( $_GET['permalink'] ), array() )
            );

            update_option( 'permalinks_customizer_redirects_action',
              $action_comp
            );
          }
        } elseif ( 'disable' === $_GET['action'] || 'disable' === $_GET['action2'] ) {
          if ( preg_match( '/^\d+(?:,\d+)*$/', $rids ) ) {
            $wpdb->query( "UPDATE {$wpdb->prefix}permalinks_customizer_redirects SET enable = 0 WHERE id IN ($rids)" );

            $action_comp = array(
              'action' => wp_kses( 'disabled', array() ),
              'total'  => wp_kses( count( $_GET['permalink'] ), array() )
            );

            update_option( 'permalinks_customizer_redirects_action',
              $action_comp
            );
          }
        } elseif ( 'enable' === $_GET['action'] || 'enable' === $_GET['action2'] ) {
          if ( preg_match( '/^\d+(?:,\d+)*$/', $rids ) ) {
            $wpdb->query( "UPDATE {$wpdb->prefix}permalinks_customizer_redirects SET enable = 1 WHERE id IN ($rids)" );

            $action_comp = array(
              'action' => wp_kses( 'enabled', array() ),
              'total'  => wp_kses( count( $_GET['permalink'] ), array() )
            );

            update_option( 'permalinks_customizer_redirects_action',
              $action_comp
            );
          }
        }
      }
    }

    $flag_redirect   = 0;
    $orderby_options = array(
      'redirect_from',
      'redirect_to',
      'type',
      'enable',
      'count',
      'last_accessed'
    );
    if ( isset( $_GET ) ) {
      foreach ( $_GET as $key => $value ) {
        if ( 'page' !== $key && 'paged' !== $key ) {
          if ( 's' === $key && '' !== $value ) {
            continue;
          } elseif ( 'redirect_type' === $key ) {
            continue;
          } elseif ( 'orderby' === $key && in_array( $value, $orderby_options ) ) {
            continue;
          } elseif ( 'order' === $key ) {
            continue;
          }
          unset( $_GET[$key] );
          if ( '_wpnonce' === $key || '_wp_http_referer' === $key) {
            $flag_redirect = 1;
          }
        }
      }
    }

    if ( 1 === $flag_redirect ) {
      $rebuild_query = '/wp-admin/admin.php?' . http_build_query( $_GET );
      header( 'Location: ' . $rebuild_query, 301 );
      exit();
    }

    $message        = '';
    $applied_action = get_option( 'permalinks_customizer_redirects_action', '' );
    if ( ! empty( $applied_action ) ) {
      delete_option( 'permalinks_customizer_redirects_action' );
      if ( isset( $applied_action['action'] )
        && isset( $applied_action['total'] )
        && is_numeric( $applied_action['total'] ) && 0 < $applied_action['total']
      ) {
        $del_items   = $applied_action['total'];
        $action_type = $applied_action['action'];
        $message     = sprintf( '<div id="message" class="updated notice notice-success is-dismissible"><p>' .
          _n( '%s Redirect is ' . $action_type .'.',
            '%s Redirects are ' . $action_type . '.',
            $del_items,
            'permalinks-customizer'
          ) . '</p></div>', $del_items );
      }
    }

    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-common-functions.php'
    );
    $common_functions = new Permalinks_Customizer_Common_Functions();

    $plugin_url = plugins_url( '/admin', PERMALINKS_CUSTOMIZER_FILE );
    wp_enqueue_style( 'style', $plugin_url . '/css/style.min.css' );

    $enabled_query  = "SELECT count(id) as total FROM {$wpdb->prefix}permalinks_customizer_redirects " .
        " WHERE enable = 1";
    $disabled_query = "SELECT count(id) as total FROM {$wpdb->prefix}permalinks_customizer_redirects " .
        " WHERE enable = 0";

    $enabled_redirects  = $wpdb->get_row( $enabled_query );
    $disabled_redirects = $wpdb->get_row( $disabled_query );

    $all_redirects = 0;
    if ( isset( $enabled_redirects->total )
      && isset( $disabled_redirects->total )
      && ( 0 < $enabled_redirects->total || 0 < $enabled_redirects->total )
    ) {
      $all_redirects = $enabled_redirects->total + $disabled_redirects->total;
    }

    $type_list        = '';
    $redirect_filter  = '';
    $set_hidden_field = '';
    if ( 0 < $all_redirects ) {
      $page_uri       = 'wp-admin/admin.php?page=permalinks-customizer-redirects';
      $page_path      = trailingslashit( home_url() ) . $page_uri;
      $all_link       = $page_path . '&redirect_type=all';
      $enabled_link   = $page_path . '&redirect_type=enabled';
      $disabled_link  = $page_path . '&redirect_type=disabled';

      $all_class        = '';
      $enabled_class    = '';
      $disabled_class   = '';
      if ( isset( $_GET['redirect_type'] ) ) {
        if ( 'all' === $_GET['redirect_type'] ) {
          $all_class        = ' class="current"';
          $set_hidden_field = '<input type="hidden" name="redirect_type" value="all" />';
        } elseif ( 'enabled' === $_GET['redirect_type'] ) {
          $redirect_filter  = 1;
          $enabled_class    = ' class="current"';
          $set_hidden_field = '<input type="hidden" name="redirect_type" value="enabled" />';
        } elseif ( 'disabled' === $_GET['redirect_type'] ) {
          $redirect_filter  = 0;
          $disabled_class   = ' class="current"';
          $set_hidden_field = '<input type="hidden" name="redirect_type" value="disabled" />';
        }
      }
      $type_list .= '<ul class="subsubsub">' .
                      '<li class="all">' .
                        '<a href="' . $all_link . '" ' . $all_class . '>' .
                          'All <span class="count">(' . $all_redirects . ')</span>' .
                        '</a>';

      if ( 0 < $enabled_redirects->total ) {
        $type_list .= '| </li>' .
                      '<li class="enabled">' .
                        '<a href="' . $enabled_link . '" ' . $enabled_class . '>' .
                          'Enabled <span class="count">(' . $enabled_redirects->total . ')</span>' .
                        '</a>';
      }

      if ( 0 < $disabled_redirects->total ) {
        $type_list .= '| </li>' .
                      '<li class="disabled">' .
                        '<a href="' . $disabled_link . '" '. $disabled_class .'>' .
                          'Disabled <span class="count">(' . $disabled_redirects->total . ')</span>' .
                        '</a>';
      }
      $type_list .= '</li>';
      $type_list .= '</ul>';
    }

    $page_html .= '<div class="wrap">' .
                    '<h1 class="wp-heading-inline">' .
                      __( 'Redirects', 'permalinks-customizer' ) .
                    '</h1>';

    $search_value     = '';
    $filter_permalink = '';
    $page_limit       = 'LIMIT 0, 20';
    $current_page     = 1;

    if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
      $search_value     = htmlspecialchars( ltrim( $_GET['s'], '/' ) );
      $filter_permalink = 'WHERE redirect_from LIKE "%' . $search_value . '%" OR redirect_to LIKE "%' . $search_value . '%"';
      $search_permalink = '&s=' . $search_value . '';
      $page_html       .= '<span class="subtitle">Search results for "' . $search_value . '"</span>';
    }

    if ( 0 === $redirect_filter || 1 === $redirect_filter ) {
      if ( '' === $filter_permalink ) {
        $filter_permalink = 'WHERE enable = ' . $redirect_filter . '';
      } else {
        $filter_permalink .= ' AND enable = ' . $redirect_filter . '';
      }
    }
    $page_html .= '<hr class="wp-header-end">';
    $page_html .= $message;

    if ( isset( $_GET['paged'] ) && is_numeric( $_GET['paged'] ) ) {
      $current_page = $_GET['paged'];
    }

    if ( 1 < $current_page ) {
      $pager      = 20 * ( $current_page - 1 );
      $page_limit = 'LIMIT ' . $pager . ', 20';
    }
    $sorting_by       = 'ORDER By id DESC';
    $order_by         = 'asc';
    $order_by_class   = 'desc';
    if ( isset( $_GET['orderby'] )
      && in_array( $_GET['orderby'], $orderby_options )
    ) {
      $set_orderby     = $_GET['orderby'];
      $filter_options .= '<input type="hidden" name="orderby" value="' . $set_orderby . '" />';
      if ( isset( $_GET['order'] ) && 'desc' === $_GET['order'] ) {
        $sorting_by      = 'ORDER By ' . $set_orderby . ' DESC';
        $order_by        = 'asc';
        $order_by_class  = 'desc';
        $filter_options .= '<input type="hidden" name="order" value="desc" />';
      } else {
        $sorting_by      = 'ORDER By ' . $set_orderby;
        $order_by        = 'desc';
        $order_by_class  = 'asc';
        $filter_options .= '<input type="hidden" name="order" value="asc" />';
      }
    }
    $count_query = "SELECT COUNT(id) AS total_rids FROM {$wpdb->prefix}permalinks_customizer_redirects $filter_permalink";
    $count_posts = $wpdb->get_row( $count_query );

    if ( '' !== $type_list ) {
      $page_html .= '<h2 class="screen-reader-text">' .
                      __( "Filter posts list", "permalinks-customizer" ) .
                    '</h2>' .
                    $type_list;
    }
    $page_html .= '<form id="permalinks-filter" method="get">' .
                    '<p class="search-box">' .
                      '<label class="screen-reader-text" for="permalinks-customizer-search-input">' .
                        __( "Search Redirects:", "permalinks-customizer" ) .
                      '</label>' .
                      '<input type="search" id="permalinks-customizer-search-input" name="s" value="' . $search_value . '">' .
                      '<input type="submit" id="search-submit" class="button" value="' . __( "Search Redirects", "permalinks-customizer" ) . '">' .
                    '</p>' .
                    '<input type="hidden" name="page" value="permalinks-customizer-redirects" />' .
                    $set_hidden_field .
                    $filter_options .
                    wp_nonce_field( 'permalinks-customizer_redirects' ) .
                    '<div class="tablenav top">' .
                    '<div class="alignleft actions bulkactions">' .
                      '<label for="bulk-action-selector-top" class="screen-reader-text">' .
                        __( "Select bulk action", "permalinks-customizer" ) .
                      '</label>' .
                      '<select name="action" id="bulk-action-selector-top">' .
                        '<option value="-1">' .
                          __( "Bulk Actions", "permalinks-customizer" ) .
                        '</option>' .
                        '<option value="delete">' .
                          __( "Delete", "permalinks-customizer" ) .
                        '</option>' .
                        '<option value="disable">' .
                          __( "Disable", "permalinks-customizer" ) .
                        '</option>' .
                        '<option value="enable">' .
                          __( "Enable", "permalinks-customizer" ) .
                        '</option>' .
                      '</select>' .
                      '<input type="submit" id="doaction" class="button action" value="' . __( "Apply", "permalinks-customizer" ) . '">' .
                    '</div>';

    $redirects         = 0;
    $top_pagination    = '';
    $bottom_pagination = '';
    if ( isset( $count_posts->total_rids ) && 0 < $count_posts->total_rids ) {
      $page_html .= '<h2 class="screen-reader-text">' .
                      __( "Permalinks Customizer navigation", "permalinks-customizer" ) .
                    '</h2>';
      $query = "SELECT * FROM {$wpdb->prefix}permalinks_customizer_redirects " .
        " $filter_permalink $sorting_by $page_limit";

      $redirects      = $wpdb->get_results( $query );
      $total_pages    = ceil( $count_posts->total_rids / 20 );
      $top_pagination = $common_functions->get_pager(
        $count_posts->total_rids, $current_page, $total_pages, 'top'
      );
      $bottom_pagination = $common_functions->get_pager(
        $count_posts->total_rids, $current_page, $total_pages, 'bottom'
      );

      if ( $current_page > $total_pages ) {
        $redirect_uri = explode(
          '&paged=' . $current_page, $_SERVER['REQUEST_URI']
        );
        header( 'Location: ' . $redirect_uri[0], 301 );
        exit();
      }

      $page_html .= $top_pagination;
    }
    $table_navigation = $this->redirect_nav(
      $order_by_class, $order_by, $search_permalink, $_GET['page'], 'top'
    );
    $table_navigation = $this->redirect_nav(
      $order_by_class, $order_by, $search_permalink, $_GET['page'], 'bottom'
    );

    $page_html .= '</div>';
    $page_html .= '<table class="wp-list-table widefat fixed striped redirects">' .
                    '<thead>' . $table_navigation . '</thead>' .
                    '<tbody>';
    if ( 0 != $redirects && ! empty( $redirects ) ) {
      foreach ( $redirects as $row ) {
        $f_red = home_url() . '/' . $row->redirect_from;
        $t_red = home_url() . '/' . $row->redirect_to;

        $page_html .= '<tr valign="top">' .
                        '<th scope="row" class="check-column">' .
                          '<input type="checkbox" name="permalink[]" value="' . $row->id . '" />' .
                        '</th>' .
                        '<td>' .
                          '<strong>' .
                            '<a class="row-title" href="' . $f_red . '">/' . $row->redirect_from . '</a>' .
                          '</strong>' .
                        '</td>' .
                        '<td>' .
                          '<strong>' .
                            '<a class="row-title" href="' . $t_red . '">/' . $row->redirect_to . '</a>' .
                          '</strong>' .
                        '</td>' .
                        '<td class="type">' . ucwords( $row->type ) . '</td>';
        if ( 1 == $row->enable) {
          $page_html .= '<td class="status enabled"> ' .
                          __( "Enabled", "permalinks-customizer" ) .
                        ' </td>';
        } else {
          $page_html .= '<td class="status disabled"> ' .
                          __( "Disabled", "permalinks-customizer" ) .
                        ' </td>';
        }
        $page_html .= '<td class="count">' . $row->count . '</td>';
        if ( '' == $row->last_accessed) {
          $page_html .= '<td class="accessed">Never</td>';
        } else {
          $page_html .= '<td class="accessed">' . $row->last_accessed . '</td>';
        }
        $page_html .= '</tr>';
      }
    } else {
      $page_html .= '<tr class="no-items">' .
                      '<td class="colspanchange" colspan="7">' .
                        __( "No redirects found.", "permalinks-customizer" ) .
                      '</td>' .
                    '</tr>';
    }
    $page_html .= '</tbody>' .
                  '<tfoot>' . $table_navigation . '</tfoot>' .
                  '</table>';

    $page_html .= '<div class="tablenav bottom">' .
                    '<div class="alignleft actions bulkactions">' .
                      '<label for="bulk-action-selector-bottom" class="screen-reader-text">' .
                        __( "Select bulk action", "permalinks-customizer" ) .
                      '</label>' .
                      '<select name="action2" id="bulk-action-selector-bottom">' .
                        '<option value="-1">' .
                          __( "Bulk Actions", "permalinks-customizer" ) .
                        '</option>' .
                        '<option value="delete">' .
                          __( "Delete", "permalinks-customizer" ) .
                        '</option> ' .
                        '<option value="disable">' .
                          __( "Disable", "permalinks-customizer" ) .
                        '</option>' .
                        '<option value="enable">' .
                          __( "Enable", "permalinks-customizer" ) .
                        '</option>' .
                      '</select>' .
                      '<input type="submit" id="doaction2" class="button action" value="' . __( "Apply", "permalinks-customizer" ) . '">' .
                      '</div>' .
                      $bottom_pagination .
                    '</div>';
    $page_html .= '</form></div>';

    echo $page_html;
  }

  /**
   * Return the Navigation row HTML for Redirects Page.
   *
   * @access private
   * @since 2.0.0
   *
   * @param string $order_by_class Class either asc or desc.
   * @param string $order_by set orderby for sorting.
   * @param string $search_permalink Permalink which has been searched or an empty string.
   * @param string $page_url Page Slug set by the Plugin.
   * @param string $position Define the position header/footer row.
   *
   * @return string table row according to the provided params.
   */
  private function redirect_nav( $order_by_class, $order_by, $search_permalink, $page_url, $position ) {
    if ( isset ( $_GET['redirect_type'] ) && ! empty( $_GET['redirect_type'] ) ) {
      $page_url = $page_url . '&redirect_type=' . $_GET['redirect_type'];
    }
    if ( 'top' === $position ) {
      $nav = '<tr>' .
                '<td id="cb" class="column-cb check-column">' .
                  '<label class="screen-reader-text" for="cb-select-all-1">Select All</label>' .
                  '<input id="cb-select-all-1" type="checkbox">' .
                '</td>' .
                '<th scope="col" id="redirect_from" class="manage-column sortable ' . $order_by_class . '">' .
                  '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=redirect_from&order=' .  $order_by . $search_permalink . '">' .
                    '<span>' . __( "Redirect From", "permalinks-customizer" ) . '</span>' .
                    '<span class="sorting-indicator"></span>' .
                  '</a>' .
                '</th>' .
                '<th scope="col" id="redirect_to" class="manage-column sortable ' . $order_by_class . '">' .
                  '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=redirect_to&order=' .  $order_by . $search_permalink . '">' .
                    '<span>' . __( "Redirect To", "permalinks-customizer" ) . '</span>' .
                    '<span class="sorting-indicator"></span>' .
                  '</a>' .
                '</th>' .
                '<th scope="col" id="type" class="manage-column sortable ' . $order_by_class . ' type">' .
                  '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=type&order=' .  $order_by . $search_permalink . '">' .
                    '<span>' . __( "Type", "permalinks-customizer" ) . '</span>' .
                    '<span class="sorting-indicator"></span>' .
                  '</a>' .
                '</th>' .
                '<th scope="col" id="enable" class="manage-column sortable ' . $order_by_class . ' status">' .
                  '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=enable&order=' .  $order_by . $search_permalink . '">' .
                    '<span>' . __( "Status", "permalinks-customizer" ) . '</span>' .
                    '<span class="sorting-indicator"></span>' .
                  '</a>' .
                '</th>' .
                '<th scope="col" id="count" class="manage-column sortable ' . $order_by_class . ' count">' .
                  '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=count&order=' .  $order_by . $search_permalink . '">' .
                    '<span>' . __( "Count", "permalinks-customizer" ) . '</span>' .
                    '<span class="sorting-indicator"></span>' .
                  '</a>' .
                '</th>' .
                '<th scope="col" id="last_accessed" class="manage-column sortable ' . $order_by_class . ' accessed">' .
                  '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=last_accessed&order=' .  $order_by . $search_permalink . '">' .
                    '<span>' . __( "Last Accessed", "permalinks-customizer" ) . '</span>' .
                    '<span class="sorting-indicator"></span>' .
                  '</a>' .
                '</th>' .
              '</tr>';
    } else {
      $nav = '<tr>' .
                '<td class="column-cb check-column">' .
                  '<label class="screen-reader-text" for="cb-select-all-1">Select All</label>' .
                  '<input id="cb-select-all-2" type="checkbox">' .
                '</td>' .
                '<th scope="col" class="manage-column sortable ' . $order_by_class . '">' .
                  '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=redirect_from&order=' .  $order_by . $search_permalink . '">' .
                    '<span>' . __( "Redirect From", "permalinks-customizer" ) . '</span>' .
                    '<span class="sorting-indicator"></span>' .
                  '</a>' .
                '</th>' .
                '<th scope="col" class="manage-column sortable ' . $order_by_class . '">' .
                  '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=redirect_to&order=' .  $order_by . $search_permalink . '">' .
                    '<span>' . __( "Redirect To", "permalinks-customizer" ) . '</span>' .
                    '<span class="sorting-indicator"></span>' .
                  '</a>' .
                '</th>' .
                '<th scope="col" class="manage-column sortable ' . $order_by_class . ' type">' .
                  '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=type&order=' .  $order_by . $search_permalink . '">' .
                    '<span>' . __( "Type", "permalinks-customizer" ) . '</span>' .
                    '<span class="sorting-indicator"></span>' .
                  '</a>' .
                '</th>' .
                '<th scope="col" class="manage-column sortable ' . $order_by_class . ' status">' .
                  '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=enable&order=' .  $order_by . $search_permalink . '">' .
                    '<span>' . __( "Status", "permalinks-customizer" ) . '</span>' .
                    '<span class="sorting-indicator"></span>' .
                  '</a>' .
                '</th>' .
                '<th scope="col" class="manage-column sortable ' . $order_by_class . ' count">' .
                  '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=count&order=' .  $order_by . $search_permalink . '">' .
                    '<span>' . __( "Count", "permalinks-customizer" ) . '</span>' .
                    '<span class="sorting-indicator"></span>' .
                  '</a>' .
                '</th>' .
                '<th scope="col" class="manage-column sortable ' . $order_by_class . ' accessed">' .
                  '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=last_accessed&order=' .  $order_by . $search_permalink . '">' .
                    '<span>' . __( "Last Accessed", "permalinks-customizer" ) . '</span>' .
                    '<span class="sorting-indicator"></span>' .
                  '</a>' .
                '</th>' .
              '</tr>';
    }

    return $nav;
  }
}
