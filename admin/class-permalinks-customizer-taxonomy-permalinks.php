<?php
/**
 * @package PermalinksCustomizer\Admin
 */

class Permalinks_Customizer_Taxonomy_Permalinks {

	/**
	 * Call Taxonomy Permalinks Function
	 */
	function __construct() {
		$this->tax_permalinks();
	}

	/**
	 * Shows all the Permalinks created by using this Plugin with Pager and Search Functionality of Taxonomies
	 */
	private function tax_permalinks() {
		global $wpdb;
		$filter_options   = '';
		$search_permalink = '';
		$html             = '';

		// Handle Bulk Operations
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'delete' )
			|| ( isset( $_POST['action2'] ) && $_POST['action2'] == 'delete' )
			&& isset( $_POST['permalink'] ) && ! empty( $_POST['permalink'] ) ) {
			$taxonomy_ids =  implode( ',', $_POST['permalink'] );
			if ( preg_match( '/^\d+(?:,\d+)*$/', $taxonomy_ids ) ) {
				$wpdb->query( "DELETE FROM $wpdb->termmeta WHERE term_id IN ($taxonomy_ids) AND meta_key = 'permalink_customizer'" );
			} else {
				$error = '<div id="message" class="error"><p>' . __( 'There is some error to proceed your request. Please retry with your request or contact to the plugin author.', 'permalinks-customizer' ) . '</p></div>';
			}
		}

		require_once(
			PERMALINKS_CUSTOMIZER_PATH . 'admin/class-permalinks-customizer-common-functions.php'
		);
		$common_functions = new Permalinks_Customizer_Common_Functions();

		$html .= '<div class="wrap">
					<h1 class="wp-heading-inline">' . __( 'Taxonomy Permalinks', 'permalinks-customizer' ) . '</h1>';

		$search_value     = '';
		$filter_permalink = '';
		if ( isset( $_GET['s'] ) && ! empty( $_GET['s'] ) ) {
			$search_value     = htmlspecialchars( ltrim( $_GET['s'], '/' ) );
			$filter_permalink = 'AND tm.meta_value LIKE "%' . $search_value . '%"';
			$search_permalink = '&s=' . $search_value . '';
			$html            .= '<span class="subtitle">Search results for "' . $search_value . '"</span>';
		}
		$page_limit = 'LIMIT 0, 20';
		if ( isset( $_GET['paged'] ) && is_numeric( $_GET['paged'] ) && $_GET['paged'] > 1 ) {
			$pager      = 20 * ( $_GET['paged'] - 1 );
			$page_limit = 'LIMIT ' . $pager . ', 20';
		}
		$sorting_by     = 'ORDER By t.term_id DESC';
		$order_by       = 'asc';
		$order_by_class = 'desc';
		if ( isset( $_GET['orderby'] ) && $_GET['orderby'] == 'title' ) {
			$filter_options .= '<input type="hidden" name="orderby" value="title" />';
			if ( isset( $_GET['order'] ) && $_GET['order'] == 'desc' ) {
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
		$count_query = "SELECT COUNT(t.term_id) AS total_permalinks FROM $wpdb->terms AS t LEFT JOIN $wpdb->termmeta AS tm ON (t.term_id = tm.term_id) WHERE tm.meta_key = 'permalink_customizer' AND tm.meta_value != '' " . $filter_permalink . "";
		$count_tax   = $wpdb->get_row( $count_query );

		$html .= '<form action="' . $_SERVER["REQUEST_URI"] . '" method="get">';
		$html .= '<p class="search-box">';
		$html .= '<input type="hidden" name="page" value="permalinks-customizer-taxonomy-permalinks" />';
		$html .= $filter_options;
		$html .= '<label class="screen-reader-text" for="permalinks-customizer-search-input">Search Permalinks Customizer:</label>';
		$html .= '<input type="search" id="permalinks-customizer-search-input" name="s" value="' . $search_value . '">';
		$html .= '<input type="submit" id="search-submit" class="button" value="Search Permalink"></p>';
		$html .= '</form>';
		$html .= '<form action="' . $_SERVER["REQUEST_URI"] . '" method="post">';
		$html .= '<div class="tablenav top">';
		$html .= '<div class="alignleft actions bulkactions">
					<label for="bulk-action-selector-top" class="screen-reader-text">' . __( "Select bulk action", "permalinks-customizer" ) . '</label>
					<select name="action" id="bulk-action-selector-top">
							<option value="-1">' . __( "Bulk Actions", "permalinks-customizer" ) . '</option>
							<option value="delete">' . __( "Delete Permalinks", "permalinks-customizer" ) . '</option>
					</select>
					<input type="submit" id="doaction" class="button action" value="Apply">
				</div>';

		$taxonomies = 0;
		if ( isset( $count_tax->total_permalinks )
			&& $count_tax->total_permalinks > 0 ) {
			$html .= '<h2 class="screen-reader-text">Permalinks Customizer navigation</h2>';

			$query = "SELECT t.term_id, t.name, tm.meta_value, tt.taxonomy FROM $wpdb->terms AS t LEFT JOIN $wpdb->termmeta AS tm ON (t.term_id = tm.term_id) LEFT JOIN $wpdb->term_taxonomy AS tt ON (t.term_id = tt.term_id) WHERE tm.meta_key = 'permalink_customizer' AND tm.meta_value != '' " . $filter_permalink . " " . $sorting_by . " " . $page_limit . "";
			$taxonomies = $wpdb->get_results( $query );

			$pagination_html = '';
			$total_pages     = ceil( $count_tax->total_permalinks / 20 );
			if ( isset( $_GET['paged'] ) && is_numeric( $_GET['paged'] )
				&& $_GET['paged'] > 0 ) {
				$pagination_html = $common_functions->permalinks_customizer_pager(
					$count_tax->total_permalinks, $_GET['paged'], $total_pages
				);
				if ( $_GET['paged'] > $total_pages ) {
					$redirect_uri = explode(
						'&paged=' . $_GET['paged'] . '', $_SERVER['REQUEST_URI']
					);
					header( 'Location: ' . $redirect_uri[0], 301 );
					exit();
				}
			} elseif ( ! isset( $_GET['paged'] ) ) {
				$pagination_html = $common_functions->permalinks_customizer_pager(
					$count_tax->total_permalinks, 1, $total_pages
				);
			}

			$html .= $pagination_html;
		}
		$table_navigation = $common_functions->permalinks_customizer_tablenav(
			$order_by_class, $order_by, $search_permalink, $_GET['page']
		);

		$html .= '</div>';
		$html .= '<table class="wp-list-table widefat fixed striped posts">
				  <thead>' . $table_navigation . '</thead>
				  <tbody>';
		if ( $taxonomies != 0 && ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$html .= '<tr valign="top">';
				$html .= '<th scope="row" class="check-column"><input type="checkbox" name="permalink[]" value="' . $taxonomy->term_id . '" /></th>';
				$html .= '<td><strong><a class="row-title" href="edit-tags.php?action=edit&taxonomy=' . $taxonomy->taxonomy . '&tag_ID=' . $taxonomy->term_id . ' ">' . $taxonomy->name . '</a></strong></td>';
				$html .= '<td>' . ucwords( $taxonomy->taxonomy ) . '</td>';
				$html .= '<td><a href="/' . $taxonomy->meta_value . '" target="_blank" title="' . __( "Visit " . $taxonomy->name, "permalinks-customizer" ) . '">/' . urldecode( $taxonomy->meta_value ) . '</a></td></tr>';
 			}
		} else {
			$html .= '<tr class="no-items"><td class="colspanchange" colspan="10">No permalinks found.</td></tr>';
		}
		$html .= '</tbody>
				  <tfoot>' . $table_navigation . '</tfoot>
				  </table>';

		$html .= '<div class="tablenav bottom">
					<div class="alignleft actions bulkactions">
						<label for="bulk-action-selector-bottom" class="screen-reader-text">' .  __( "Select bulk action", "permalinks-customizer" ) . '</label>
						<select name="action2" id="bulk-action-selector-bottom">
							<option value="-1">' .  __( "Bulk Actions", "permalinks-customizer" ) . '</option>
							<option value="delete">' .  __( "Delete Permalinks", "permalinks-customizer" ) . '</option>
						</select>
						<input type="submit" id="doaction2" class="button action" value="Apply">
					</div>
					' . $pagination_html . '
				</div>';
		$html .= '</form></div>';
		echo $html;
	}
}
