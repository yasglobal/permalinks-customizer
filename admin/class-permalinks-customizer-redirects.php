<?php
/**
 * @package PermalinksCustomizer\Admin
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
   * @access private
   * @since 2.0.0
   *
   * @return void
   */
  private function show_redirects() {
  
    global $wpdb;

    $filter_options   = '';
    $search_permalink = '';
    $html             = '';

    // Handle Bulk Operations
    if ( isset( $_POST['permalink'] ) && ! empty( $_POST['permalink'] )
      && ( isset( $_POST['action'] ) || isset( $_POST['action2'] ) ) ) {
      $rids = implode( ',', $_POST['permalink'] );
      if ( 'delete' === $_POST['action'] || 'delete' === $_POST['action2'] ) {
        if ( preg_match( '/^\d+(?:,\d+)*$/', $rids ) ) {
          $wpdb->query( "DELETE FROM {$wpdb->prefix}permalinks_customizer_redirects WHERE id IN ($rids)" );
        }
      } elseif ( 'disable' === $_POST['action'] || 'disable' === $_POST['action2'] ) {
        if ( preg_match( '/^\d+(?:,\d+)*$/', $rids ) ) {
          $wpdb->query( "UPDATE {$wpdb->prefix}permalinks_customizer_redirects SET enable = 0 WHERE id IN ($rids)" );
        }
      } elseif ( 'enable' === $_POST['action'] || 'enable' === $_POST['action2'] ) {
        if ( preg_match( '/^\d+(?:,\d+)*$/', $rids ) ) {
          $wpdb->query( "UPDATE {$wpdb->prefix}permalinks_customizer_redirects SET enable = 1 WHERE id IN ($rids)" );
        }
      } else {
        echo '<div id="message" class="error">' .
                '<p>' . __( "There is some error to proceed your request. Please retry with your request or contact to the plugin author.", "permalinks-customizer" ) . '</p>' .
              '</div>';
      }
    }

    require_once(
      PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-common-functions.php'
    );
    $common_functions = new Permalinks_Customizer_Common_Functions();

    $plugin_url = plugins_url( '/admin', PERMALINKS_CUSTOMIZER_FILE );
    wp_enqueue_style( 'style', $plugin_url . '/css/style.css' );

    $html .= '<div class="wrap">' .
              '<h1 class="wp-heading-inline">' .
                __( 'Redirects', 'permalinks-customizer' ) .
              '</h1>';

    $search_value     = '';
    $filter_permalink = '';
    if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
      $search_value     = htmlspecialchars( ltrim( $_GET['s'], '/' ) );
      $filter_permalink = 'WHERE redirect_from LIKE "%' . $search_value . '%" OR redirect_to LIKE "%' . $search_value . '%"';
      $search_permalink = '&s=' . $search_value . '';
      $html            .= '<span class="subtitle">Search results for "' . $search_value . '"</span>';
    }
    $page_limit = 'LIMIT 0, 20';
    if ( isset( $_GET['paged'] ) && is_numeric( $_GET['paged'] )
      && $_GET['paged'] > 1 ) {
      $pager      = 20 * ( $_GET['paged'] - 1 );
      $page_limit = 'LIMIT ' . $pager . ', 20';
    }
    $sorting_by     = 'ORDER By id DESC';
    $order_by       = 'asc';
    $order_by_class = 'desc';
    if ( isset( $_GET['orderby'] ) && ( 'redirect_from' === $_GET['orderby']
      || 'redirect_to' === $_GET['orderby'] || 'type' === $_GET['orderby']
      || 'enable' === $_GET['orderby'] || 'count' === $_GET['orderby']
      || 'last_accessed' === $_GET['orderby'] )
    ) {
      $set_orderby     = $_GET['orderby'];
      $filter_options .= '<input type="hidden" name="orderby" value="' . $set_orderby . '" />';
      if ( isset( $_GET['order'] ) && $_GET['order'] == 'desc' ) {
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

    $html .= '<form action="' . $_SERVER["REQUEST_URI"] . '" method="get">';
    $html .= '<p class="search-box">';
    $html .= '<input type="hidden" name="page" value="permalinks-customizer-redirects" />';
    $html .= $filter_options;
    $html .= '<label class="screen-reader-text" for="permalinks-customizer-search-input">Search Permalinks Customizer:</label>';
    $html .= '<input type="search" id="permalinks-customizer-search-input" name="s" value="' . $search_value . '">';
    $html .= '<input type="submit" id="search-submit" class="button" value="Search Permalink"></p>';
    $html .= '</form>';
    $html .= '<form action="' . $_SERVER["REQUEST_URI"] . '" method="post">';
    $html .= '<div class="tablenav top">';
    $html .= '<div class="alignleft actions bulkactions">' .
                '<label for="bulk-action-selector-top" class="screen-reader-text">' .
                  'Select bulk action' .
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
                '<input type="submit" id="doaction" class="button action" value="Apply">' .
              '</div>';

    $redirects       = 0;
    $pagination_html = '';
    if ( isset( $count_posts->total_rids ) && $count_posts->total_rids > 0 ) {
      $html .= '<h2 class="screen-reader-text">' .
                  __( "Permalinks Customizer navigation", "permalinks-customizer" ) .
                '</h2>';
      $query = "SELECT * FROM {$wpdb->prefix}permalinks_customizer_redirects " .
        " $filter_permalink $sorting_by $page_limit";

      $redirects   = $wpdb->get_results( $query );
      $total_pages = ceil( $count_posts->total_rids / 20 );
      if ( isset( $_GET['paged'] ) && is_numeric( $_GET['paged'] )
        && $_GET['paged'] > 0 ) {
        $pagination_html = $common_functions->get_pager(
          $count_posts->total_rids, $_GET['paged'], $total_pages
        );
        if ( $_GET['paged'] > $total_pages ) {
          $redirect_uri = explode( '&paged=' . $_GET['paged'] . '', $_SERVER['REQUEST_URI'] );
          header( 'Location: ' . $redirect_uri[0], 301 );
          exit();
        }
      } elseif ( ! isset( $_GET['paged'] ) ) {
        $pagination_html = $common_functions->get_pager(
          $count_posts->total_rids, 1, $total_pages
        );
      }

      $html .= $pagination_html;
    }
    $table_navigation = $this->redirect_nav(
      $order_by_class, $order_by, $search_permalink, $_GET['page']
    );

    $html .= '</div>';
    $html .= '<table class="wp-list-table widefat fixed striped redirects">' .
              '<thead>' . $table_navigation . '</thead>' .
              '<tbody>';
    if ( 0 != $redirects && ! empty( $redirects ) ) {
      foreach ( $redirects as $row ) {
        $f_red = home_url() . '/' . $row->redirect_from;
        $t_red = home_url() . '/' . $row->redirect_to;
        $html .= '<tr valign="top">';
        $html .= '<th scope="row" class="check-column">';
        $html .= '<input type="checkbox" name="permalink[]" value="' . $row->id . '" />';
        $html .= '</th>';
        $html .= '<td><strong><a class="row-title" href="' . $f_red . '">/' . $row->redirect_from . '</a></strong></td>';
        $html .= '<td><strong><a class="row-title" href="' . $t_red . '">/' . $row->redirect_to . '</a></strong></td>';
        $html .= '<td class="type">' . ucwords( $row->type ) . '</td>';
        if ( 1 == $row->enable) {
          $html .= '<td class="status enabled"> ' .
            __( "Enabled", "permalinks-customizer" ) .
            ' </td>';
        } else {
          $html .= '<td class="status disabled"> ' .
            __( "Disabled", "permalinks-customizer" ) .
            ' </td>';
        }
        $html .= '<td class="count">' . $row->count . '</td>';
        if ( '' == $row->last_accessed) {
          $html .= '<td class="accessed">Never</td>';
        } else {
          $html .= '<td class="accessed">' . $row->last_accessed . '</td>';
        }
        $html .= '</tr>';
      }
    } else {
      $html .= '<tr class="no-items"><td class="colspanchange" colspan="7">' .
                  __( "No redirects found.", "permalinks-customizer" ) .
                ' </td></tr>';
    }
    $html .= '</tbody>' .
              '<tfoot>' . $table_navigation . '</tfoot>' .
              '</table>';

    $html .= '<div class="tablenav bottom">' .
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
                '<input type="submit" id="doaction2" class="button action" value="Apply">' .
                '</div>' .
                $pagination_html .
              '</div>';
    $html .= '</form></div>';
    echo $html;
  }

  /**
   * Return the Navigation row HTML for Redirects Page.
   *
   * @access private
   * @since 2.0.0
   *
   * @param string $order_by_class
   *   Class either asc or desc
   * @param string $order_by
   *   set orderby for sorting
   * @param string $search_permalink
   *   Permalink which has been searched or an empty string
   * @param string $page_url
   *   Page Slug set by the Plugin
   *
   * @return string $nav
   *   Return table row according to the provided params.
   */
  private function redirect_nav( $order_by_class, $order_by, $search_permalink, $page_url ) {
    $nav = '<tr>' .
              '<td id="cb" class="column-cb check-column">' .
                '<label class="screen-reader-text" for="cb-select-all-1">Select All</label>' .
                '<input id="cb-select-all-1" type="checkbox">' .
              '</td>' .
              '<th scope="col" id="redirect_from" class="manage-column sortable ' . $order_by_class . '">' .
                '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=redirect_from&order=' .  $order_by . $search_permalink . '"><span>' . __( "Redirect From", "permalinks-customizer" ) . '</span><span class="sorting-indicator"></span></a>' .
              '</th>' .
              '<th scope="col" id="redirect_to" class="manage-column sortable ' . $order_by_class . '">' .
                '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=redirect_to&order=' .  $order_by . $search_permalink . '"><span>' . __( "Redirect To", "permalinks-customizer" ) . '</span><span class="sorting-indicator"></span></a>' .
              '</th>' .
              '<th scope="col" id="type" class="manage-column sortable ' . $order_by_class . ' type">' .
                '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=type&order=' .  $order_by . $search_permalink . '"><span>' . __( "Type", "permalinks-customizer" ) . '</span><span class="sorting-indicator"></span></a>' .
              '</th>' .
              '<th scope="col" id="enable" class="manage-column sortable ' . $order_by_class . ' status">' .
                '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=enable&order=' .  $order_by . $search_permalink . '"><span>' . __( "Status", "permalinks-customizer" ) . '</span><span class="sorting-indicator"></span></a>' .
              '</th>' .
              '<th scope="col" id="count" class="manage-column sortable ' . $order_by_class . ' count">' .
                '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=count&order=' .  $order_by . $search_permalink . '"><span>' . __( "Count", "permalinks-customizer" ) . '</span><span class="sorting-indicator"></span></a>' .
              '</th>' .
              '<th scope="col" id="last_accessed" class="manage-column sortable ' . $order_by_class . ' accessed">' .
                '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=last_accessed&order=' .  $order_by . $search_permalink . '"><span>' . __( "Last Accessed", "permalinks-customizer" ) . '</span><span class="sorting-indicator"></span></a>' .
              '</th>' .
            '</tr>';
    return $nav;
  }
}
