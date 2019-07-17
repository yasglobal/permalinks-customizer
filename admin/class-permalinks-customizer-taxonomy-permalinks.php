<?php
/**
 * @package PermalinksCustomizer
 */

/**
 * Create Taxonomy Permalinks page.
 *
 * Create Taxonomy Permalinks page HTML and display the page.
 *
 * @since 1.3.3
 */
class Permalinks_Customizer_Taxonomy_Permalinks {

  /**
   * Call Taxonomy Permalinks Function.
   */
  function __construct() {
    $this->tax_permalinks();
  }

  /**
   * Shows all the Permalinks created by using this Plugin with Pager
   * and Search Functionality of Taxonomies.
   *
   * @since 1.3.3
   * @access private
   */
  private function tax_permalinks() {
    global $wpdb;

    $filter_options   = '';
    $search_permalink = '';
    $page_html        = '';

    if ( isset( $_GET['_wpnonce'] )
      && wp_verify_nonce( $_GET['_wpnonce'], 'permalinks-customizer_taxonomy_permalinks' )
    ) {
      // Handle Bulk Operations
      if ( ( ( isset( $_GET['action'] ) && 'delete' == $_GET['action'] )
        || ( isset( $_GET['action2'] ) && 'delete' == $_GET['action2'] ) )
        && isset( $_GET['permalink'] ) && ! empty( $_GET['permalink'] )
      ) {
        $taxonomy_ids = implode( ',', $_GET['permalink'] );
        if ( preg_match( '/^\d+(?:,\d+)*$/', $taxonomy_ids ) ) {
          $wpdb->query( "DELETE FROM $wpdb->termmeta WHERE term_id IN ($taxonomy_ids) AND meta_key = 'permalink_customizer'" );

          $action_comp = array(
            'action' => wp_kses( 'deleted', array() ),
            'total'  => wp_kses( count( $_GET['permalink'] ), array() )
          );

          update_option( 'permalinks_customizer_taxonomy_permalinks_action',
            $action_comp
          );
        }
      }
    }

    $flag_redirect = 0;
    if ( isset( $_GET ) ) {
      foreach ( $_GET as $key => $value ) {
        if ( 'page' !== $key && 'paged' !== $key ) {
          if ( 's' === $key && '' !== $value ) {
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
    $applied_action = get_option( 'permalinks_customizer_taxonomy_permalinks_action', '' );
    if ( ! empty( $applied_action ) ) {
      delete_option( 'permalinks_customizer_taxonomy_permalinks_action' );
      if ( isset( $applied_action['action'] )
        && isset( $applied_action['total'] )
        && is_numeric( $applied_action['total'] ) && 0 < $applied_action['total']
      ) {
        $del_items = $applied_action['total'];
        $message   = sprintf( '<div id="message" class="updated notice notice-success is-dismissible"><p>' .
          _n( '%s Permalink is deleted.',
            '%s Permalinks are deleted.',
            $del_items,
            'permalinks-customizer'
          ) . '</p></div>', $del_items );
      }
    }

    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-common-functions.php'
    );
    $common_functions = new Permalinks_Customizer_Common_Functions();

    $page_html .= '<div class="wrap">' .
                    '<h1 class="wp-heading-inline">' .
                      __( "Taxonomy Permalinks", "permalinks-customizer" ) .
                    '</h1>';
    $page_html .= $message;

    $search_value     = '';
    $filter_permalink = '';
    $page_limit       = 'LIMIT 0, 20';
    $current_page     = 1;

    if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
      $search_value     = htmlspecialchars( ltrim( $_GET['s'], '/' ) );
      $filter_permalink = 'AND tm.meta_value LIKE "%' . $search_value . '%"';
      $search_permalink = '&s=' . $search_value . '';
      $page_html       .= '<span class="subtitle">' .
                            __( "Search results for", "permalinks-customizer" ) . ' "' . $search_value . '"' .
                          '</span>';
    }

    if ( isset( $_GET['paged'] ) && is_numeric( $_GET['paged'] ) ) {
      $current_page = $_GET['paged'];
    }

    if ( 1 < $current_page ) {
      $pager      = 20 * ( $current_page - 1 );
      $page_limit = 'LIMIT ' . $pager . ', 20';
    }

    $sorting_by     = 'ORDER By t.term_id DESC';
    $order_by       = 'asc';
    $order_by_class = 'desc';
    if ( isset( $_GET['orderby'] ) && 'title' == $_GET['orderby'] ) {
      $filter_options .= '<input type="hidden" name="orderby" value="title" />';
      if ( isset( $_GET['order'] ) && 'desc' == $_GET['order'] ) {
        $sorting_by      = 'ORDER By t.name DESC';
        $order_by        = 'asc';
        $order_by_class  = 'desc';
        $filter_options .= '<input type="hidden" name="order" value="desc" />';
      } else {
        $sorting_by      = 'ORDER By t.name';
        $order_by        = 'desc';
        $order_by_class  = 'asc';
        $filter_options .= '<input type="hidden" name="order" value="asc" />';
      }
    }
    if ( isset( $_GET['orderby'] ) && ( 'title' === $_GET['orderby']
      || 'permalink' === $_GET['orderby'] || 'type' === $_GET['orderby'] )
    ) {
      if ( 'permalink' === $_GET['orderby'] ) {
        $set_orderby = 'tm.meta_value';
      } elseif ( 'type' === $_GET['orderby'] ) {
        $set_orderby = 'tt.taxonomy';
      } else {
        $set_orderby = 't.name';
      }
      $filter_options .= '<input type="hidden" name="orderby" value="' . $set_orderby . '" />';
      if ( isset( $_GET['order'] ) && 'desc' == $_GET['order'] ) {
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
    $count_query = "SELECT COUNT(t.term_id) AS total_permalinks FROM $wpdb->terms AS t " .
                    " LEFT JOIN $wpdb->termmeta AS tm ON (t.term_id = tm.term_id) " .
                    " WHERE tm.meta_key = 'permalink_customizer' " .
                    " AND tm.meta_value != '' " . $filter_permalink . "";
    $count_tax   = $wpdb->get_row( $count_query );


    $page_html .= '<form id="permalinks-filter" method="get">' .
                    '<p class="search-box">' .
                      '<label class="screen-reader-text" for="permalinks-customizer-search-input">' .
                        __( "Search Permalinks:", "permalinks-customizer" ) .
                      '</label>' .
                      '<input type="search" id="permalinks-customizer-search-input" name="s" value="' . $search_value . '">' .
                      '<input type="submit" id="search-submit" class="button" value="' . __( "Search Permalinks", "permalinks-customizer" ) . '">' .
                    '</p>' .
                    '<input type="hidden" name="page" value="permalinks-customizer-taxonomy-permalinks" />' .
                    $filter_options .
                    wp_nonce_field( 'permalinks-customizer_taxonomy_permalinks' ) .
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
                          __( "Delete Permalinks", "permalinks-customizer" ) .
                        '</option>' .
                      '</select>' .
                      '<input type="submit" id="doaction" class="button action" value="' . __( "Apply", "permalinks-customizer" ) . '">' .
                    '</div>';

    $taxonomies        = 0;
    $top_pagination    = '';
    $bottom_pagination = '';
    if ( isset( $count_tax->total_permalinks )
      && 0 < $count_tax->total_permalinks
    ) {
      $page_html .= '<h2 class="screen-reader-text">'
                      . __( "Permalinks Customizer navigation", "permalinks-customizer" ) .
                    '</h2>';

      $query = "SELECT t.term_id, t.name, tm.meta_value, tt.taxonomy FROM $wpdb->terms AS t " .
                " LEFT JOIN $wpdb->termmeta AS tm ON (t.term_id = tm.term_id) " .
                " LEFT JOIN $wpdb->term_taxonomy AS tt ON (t.term_id = tt.term_id) " .
                " WHERE tm.meta_key = 'permalink_customizer' AND tm.meta_value != '' " .
                $filter_permalink . " " . $sorting_by . " " . $page_limit . "";
      $taxonomies = $wpdb->get_results( $query );

      $total_pages    = ceil( $count_tax->total_permalinks / 20 );
      $top_pagination = $common_functions->get_pager(
        $count_tax->total_permalinks, $current_page, $total_pages, 'top'
      );
      $bottom_pagination = $common_functions->get_pager(
        $count_tax->total_permalinks, $current_page, $total_pages, 'bottom'
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
    $top_navigation = $common_functions->get_tablenav(
      $order_by_class, $order_by, $search_permalink, $_GET['page'], 'top'
    );
    $bottom_navigation = $common_functions->get_tablenav(
      $order_by_class, $order_by, $search_permalink, $_GET['page'], 'bottom'
    );

    $page_html .= '</div>';
    $page_html .= '<table class="wp-list-table widefat fixed striped posts">' .
                    '<thead>' . $top_navigation . '</thead>' .
                    '<tbody>';
    if ( 0 != $taxonomies && ! empty( $taxonomies ) ) {
      foreach ( $taxonomies as $taxonomy ) {
        $tview      = home_url() . '/' . $taxonomy->meta_value;
        $page_html .= '<tr valign="top">' .
                        '<th scope="row" class="check-column">' .
                          '<input type="checkbox" name="permalink[]" value="' . $taxonomy->term_id . '" />' .
                        '</th>' .
                        '<td>' .
                          '<strong>' .
                            '<a class="row-title" href="edit-tags.php?action=edit&taxonomy=' . $taxonomy->taxonomy . '&tag_ID=' . $taxonomy->term_id . ' ">' .
                              $taxonomy->name .
                            '</a>' .
                          '</strong>' .
                        '</td>' .
                        '<td>' . ucwords( $taxonomy->taxonomy ) . '</td>' .
                        '<td>' .
                          '<a href="' . $tview . '" target="_blank" title="' . __( "Visit ", "permalinks-customizer" ) . $taxonomy->name . '">' .
                            '/' . urldecode( $taxonomy->meta_value ) .
                          '</a>' .
                        '</td>' .
                      '</tr>';
      }
    } else {
      $page_html .= '<tr class="no-items">' .
                      '<td class="colspanchange" colspan="4">' .
                        __( "No permalinks found.", "permalinks-customizer" ) .
                      '</td>' .
                    '</tr>';
    }
    $page_html .= '</tbody>' .
                  '<tfoot>' . $bottom_navigation . '</tfoot>' .
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
                          __( "Delete Permalinks", "permalinks-customizer" ) .
                        '</option>' .
                      '</select>' .
                     '<input type="submit" id="doaction2" class="button action" value="' . __( "Apply", "permalinks-customizer" ) . '">' .
                    '</div>' .
                    $bottom_pagination .
                  '</div>';
    $page_html .= '</form></div>';

    echo $page_html;
  }
}
