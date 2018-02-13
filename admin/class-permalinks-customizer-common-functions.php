<?php
/**
 * @package PermalinksCustomizer\Admin
 */

class Permalinks_Customizer_Common_Functions {

	/**
	 * Return the Navigation row HTML same as Default Posts page for PostTypes/Taxonomies
	 */
	public function permalinks_customizer_tablenav( $order_by_class, $order_by, $search_permalink, $page_url = 'permalinks-customizer-post-permalinks' ) {
		$nav = '<tr>
					<td id="cb" class="manage-column column-cb check-column">
						<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
						<input id="cb-select-all-1" type="checkbox">
					</td>
					<th scope="col" id="title" class="manage-column column-title column-primary sortable ' . $order_by_class . '">
						<a href="/wp-admin/admin.php?page=' . $page_url . '&orderby=title&order=' .  $order_by . $search_permalink . '"><span>' . __( "Title", "permalinks-customizer" ) . '</span><span class="sorting-indicator"></span></a>
					</th>
					<th scope="col">' . __( "Type", "permalinks-customizer" ) . '</th>
					<th scope="col">' . __( "Permalink", "permalinks-customizer" ) . '</th>
				</tr>';
		return $nav;
	}

	/**
	 * Return the Pager HTML
	 */
	public function permalinks_customizer_pager( $total_permalinks, $current_pager_value = 1, $total_pager = 0 ) {

		if ( $total_pager == 0 ) return;

		if ( $total_pager == 1 ) {
			$pagination_html = '<div class="tablenav-pages one-page">
									<span class="displaying-num">' . $total_permalinks . ' items</span>
								</div>';
			return $pagination_html;
		}

		$remove_pager_uri = explode( '&paged=' . $current_pager_value . '', $_SERVER['REQUEST_URI'] );
		$pagination_html = '<div class="tablenav-pages">
								<span class="displaying-num">' . $total_permalinks . ' items</span>
								<span class="pagination-links">';

		if ( $current_pager_value == 1 ) {
			$pagination_html .= '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo; </span>
							 	<span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo; </span>';
		} else {
			$prev_page = $current_pager_value - 1;
			if ( $prev_page == 1 ) {
				$pagination_html .= '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>';
			} else {
				$pagination_html .= '<a href="' . $remove_pager_uri[0] . '&paged=1" title="First page" class="first-page">
										<span class="screen-reader-text">First page</span>
										<span aria-hidden="true">&laquo;</span>
									 </a> ';
			}
			$pagination_html .= '<a href="' . $remove_pager_uri[0] . '&paged=' . $prev_page . '" title="Previous page" class="prev-page">
									<span class="screen-reader-text">Previous page</span>
									<span aria-hidden="true">&lsaquo;</span>
								 </a> ';
		}

		$pagination_html .= '<span class="paging-input">
								<label for="current-page-selector" class="screen-reader-text">Current Page</label>
								<input class="current-page" id="current-page-selector" type="text" name="paged" value="' . $current_pager_value . '" size="1" aria-describedby="table-paging" />
								<span class="tablenav-paging-text"> of <span class="total-pages">' . $total_pager . ' </span> </span>
							</span>';

		if ( $current_pager_value == $total_pager ) {
			$pagination_html .= '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo; </span>
								<span class="tablenav-pages-navspan" aria-hidden="true">&raquo; </span>';
		} else {
			$next_page = $current_pager_value + 1;
			$pagination_html .= ' <a href="' . $remove_pager_uri[0] . '&paged=' . $next_page . '" title="Next page" class="next-page">
									<span class="screen-reader-text">Next page</span>
									<span aria-hidden="true">&rsaquo;</span>
								</a> ';
			if ( $total_pager == $next_page ) {
				$pagination_html .= '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
			} else {
				$pagination_html .= ' <a href="' . $remove_pager_uri[0] . '&paged=' . $total_pager . '" title="Last page" class="last-page">
										<span class="screen-reader-text">Last page</span>
										<span aria-hidden="true">&raquo;</span>
									 </a> ';
			}
		}
		$pagination_html .= '</span></div>';

		return $pagination_html;
	}
}
