<?php
/**
 * @package PermalinksCustomizer
 */

/**
 * Common function which are used on different pages.
 *
 * Returns table header or pagination for settings page.
 *
 * @since 1.3.0
 */
class Permalinks_Customizer_Common_Functions {

  /**
   * Return the Navigation row HTML same as Default Posts page
   * for PostTypes/Taxonomies.
   *
   * @since 1.3.0
   * @access public
   *
   * @param string $order_by_class Class either asc or desc.
   * @param string $order_by set orderby for sorting.
   * @param string $search_permalink Permalink which has been searched or an empty string.
   * @param string $page_url Page Slug set by the Plugin.
   * @param string $position Define the position header/footer row.
   *
   * @return string table row according to the provided params.
   */
  public function get_tablenav( $order_by_class, $order_by, $search_permalink, $page_url, $position ) {
    if ( 'top' === $position ) {
      $nav = '<tr>' .
              '<td id="cb" class="manage-column column-cb check-column">' .
                '<label class="screen-reader-text" for="cb-select-all-1">Select All</label>' .
                '<input id="cb-select-all-1" type="checkbox">' .
              '</td>' .
              '<th scope="col" id="title" class="manage-column column-title column-primary sortable ' . $order_by_class . '">' .
                '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=title&order=' .  $order_by . $search_permalink . '">' .
                  '<span>' . __( "Title", "permalinks-customizer" ) . '</span>' .
                  '<span class="sorting-indicator"></span>' .
                '</a>' .
              '</th>' .
              '<th scope="col" id="type" class="manage-column column-primary sortable ' . $order_by_class . '">' .
                '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=type&order=' .  $order_by . $search_permalink . '">' .
                  '<span>' . __( "Type", "permalinks-customizer" ) . '</span>' .
                  '<span class="sorting-indicator"></span>' .
                '</a>' .
              '</th>' .
              '<th scope="col" id="permalink" class="manage-column column-primary sortable ' . $order_by_class . '">' .
                '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=permalink&order=' .  $order_by . $search_permalink . '">' .
                  '<span>' . __( "Permalink", "permalinks-customizer" ) . '</span>' .
                  '<span class="sorting-indicator"></span>' .
                '</a>' .
              '</th>' .
            '</tr>';
    } else {
      $nav = '<tr>' .
                '<td class="manage-column column-cb check-column">' .
                  '<label class="screen-reader-text" for="cb-select-all-1">Select All</label>' .
                  '<input id="cb-select-all-2" type="checkbox">' .
                '</td>' .
                '<th scope="col" class="manage-column column-title column-primary sortable ' . $order_by_class . '">' .
                  '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=title&order=' .  $order_by . $search_permalink . '">' .
                    '<span>' . __( "Title", "permalinks-customizer" ) . '</span>' .
                    '<span class="sorting-indicator"></span>' .
                  '</a>' .
                '</th>' .
                '<th scope="col" class="manage-column column-primary sortable ' . $order_by_class . '">' .
                  '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=type&order=' .  $order_by . $search_permalink . '">' .
                    '<span>' . __( "Type", "permalinks-customizer" ) . '</span>' .
                    '<span class="sorting-indicator"></span>' .
                  '</a>' .
                '</th>' .
                '<th scope="col" class="manage-column column-primary sortable ' . $order_by_class . '">' .
                  '<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=permalink&order=' .  $order_by . $search_permalink . '">' .
                    '<span>' . __( "Permalink", "permalinks-customizer" ) . '</span>' .
                    '<span class="sorting-indicator"></span>' .
                  '</a>' .
                '</th>' .
              '</tr>';
    }

    return $nav;
  }

  /**
   * Return the Pager HTML.
   *
   * @since 1.3.0
   * @access public
   *
   * @param int $total_permalinks No. of total results found.
   * @param int $current_pager_value Optional. Current Page. 1.
   * @param int $total_pager Optional. Total no. of pages. 0.
   * @param string $position Define the position header/footer row.
   *
   * @return string Return Pagination HTML if pager exist.
   */
  public function get_pager( $total_permalinks, $current_pager_value = 1, $total_pager = 0, $position ) {

    if ( 0 == $total_pager ) {
      return;
    }

    if ( 1 == $total_pager ) {
      $pagination_html = '<div class="tablenav-pages one-page">' .
                            '<span class="displaying-num">' . $total_permalinks . ' items</span>' .
                          '</div>';
      return $pagination_html;
    }

    $remove_pager_uri = explode( '&paged=' . $current_pager_value . '', $_SERVER['REQUEST_URI'] );
    $pagination_html = '<div class="tablenav-pages">' .
                          '<span class="displaying-num">' . $total_permalinks . ' items</span>' .
                          '<span class="pagination-links">';

    if ( 1 == $current_pager_value ) {
      $pagination_html .= '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>' .
                          ' <span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
    } else {
      $prev_page = $current_pager_value - 1;
      if ( 1 == $prev_page ) {
        $pagination_html .= ' <span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
      } else {
        $pagination_html .= '<a href="' . $remove_pager_uri[0] . '&paged=1" title="First page" class="first-page button">' .
                              '<span class="screen-reader-text">First page</span>' .
                              '<span aria-hidden="true">&laquo;</span>' .
                            '</a>';
      }
      $pagination_html .= ' <a href="' . $remove_pager_uri[0] . '&paged=' . $prev_page . '" title="Previous page" class="prev-page button">' .
                            '<span class="screen-reader-text">Previous page</span>' .
                            '<span aria-hidden="true">&lsaquo;</span>' .
                           '</a>';
    }

    if ( 'top' === $position ) {
      $pagination_html .= ' <span class="paging-input">' .
                            '<label for="current-page-selector" class="screen-reader-text">Current Page</label>' .
                            '<input class="current-page" id="current-page-selector" type="text" name="paged" value="' . $current_pager_value . '" size="1" aria-describedby="table-paging" />' .
                            '<span class="tablenav-paging-text"> of ' .
                              '<span class="total-pages">' . $total_pager . ' </span>' .
                            '</span>' .
                          '</span>';
    } else {
      $pagination_html .= ' <span id="table-paging" class="paging-input">' .
                            '<span class="tablenav-paging-text">' . $current_pager_value . ' of ' .
                              '<span class="total-pages">' . $total_pager . ' </span>' .
                            '</span>' .
                          '</span>';
    }

    if ( $current_pager_value == $total_pager ) {
      $pagination_html .= '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>' .
                          ' <span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
    } else {
      $next_page = $current_pager_value + 1;
      $pagination_html .= ' <a href="' . $remove_pager_uri[0] . '&paged=' . $next_page . '" title="Next page" class="next-page button">' .
                              '<span class="screen-reader-text">Next page</span>' .
                              '<span aria-hidden="true">&rsaquo;</span>' .
                            '</a>';
      if ( $total_pager == $next_page ) {
        $pagination_html .= ' <span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
      } else {
        $pagination_html .= ' <a href="' . $remove_pager_uri[0] . '&paged=' . $total_pager . '" title="Last page" class="last-page button">' .
                              '<span class="screen-reader-text">Last page</span>' .
                              '<span aria-hidden="true">&raquo;</span>' .
                            '</a>';
      }
    }
    $pagination_html .= '</span></div>';

    return $pagination_html;
  }
}
