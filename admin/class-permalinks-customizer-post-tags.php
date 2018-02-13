<?php
/**
 * @package PermalinksCustomizer\Admin
 */

class Permalinks_Customizer_Post_Tags {

	/**
	 * Call Posy Tags Function
	 */
	function __construct() {
		$this->post_available_tags();
	}

	/**
	 * Shows all the Tags which this Plugin Supports for PostTypes
	 */
	private function post_available_tags() {
		?>
		<div class="wrap">
			<h1><?php _e( 'Structure Tags for Posts', 'permalinks-customizer' ); ?></h1>
			<div><?php _e( 'These tags can be used to create Permalink Customizers for each post type.', 'permalinks-customizer' ); ?></div>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">%title%</th>
					<td><?php _e( 'Title of the post. let&#039;s say the title is &quot;This Is A Great Post!&quot; so, it becomes this-is-a-great-post in the URI.', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%year%</th>
					<td><?php _e( 'The year of the post, four digits, for example 2004', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%monthnum%</th>
					<td><?php _e( 'Month of the year, for example 05', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%day%</th>
					<td><?php _e( 'Day of the month, for example 28', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%hour%</th>
					<td><?php _e( 'Hour of the day, for example 15', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%minute%</th>
					<td><?php _e( 'Minute of the hour, for example 43', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%second%</th>
					<td><?php _e( 'Second of the minute, for example 33', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%post_id%</th>
					<td><?php _e( 'The unique ID # of the post, for example 423', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%postname%</th>
					<td><?php _e( 'A sanitized version of the title of the post (post slug field on Edit Post/Page panel). So &quot;This Is A Great Post!&quot; becomes this-is-a-great-post in the URI.', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%parent_postname%</th>
					<td><?php _e( 'A sanitized version of the title of the post (post slug field on Edit Post/Page panel). So &quot;This Is A Great Post!&quot; becomes this-is-a-great-post in the URI. This <strong>Tag</strong> contains Immediate <strong>Parent Page Slug</strong> if any parent page is selected before publishing.', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%all_parents_postname%</th>
					<td><?php _e( 'A sanitized version of the title of the post (post slug field on Edit Post/Page panel). So &quot;This Is A Great Post!&quot; becomes this-is-a-great-post in the URI. This <strong>Tag</strong> contains all the <strong>Parent Page Slugs</strong> if any parent page is selected before publishing.', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%category%</th>
					<td><?php _e( 'A sanitized version of the category name (category slug field on New/Edit Category panel). Nested sub-categories appear as nested directories in the URI.', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%child-category%</th>
					<td><?php _e( ' A sanitized version of the category name (category slug field on New/Edit Category panel).', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%product_cat%</th>
					<td><?php _e( 'A sanitized version of the product category name (category slug field on New/Edit Category panel). Nested sub-categories appear as nested directories in the URI. <i>This <strong>tag</strong> is specially used for WooCommerce Products.', 'permalinks-customizer' ); ?></i></td>
				</tr>
				<tr valign="top">
					<th scope="row">%author%</th>
					<td><?php _e( 'A sanitized version of the author name.', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%author_firstname%</th>
					<td><?php _e( 'A sanitized version of the author first name. If author first name is not available so, it uses the author&#39;s username.', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%author_lastname%</th>
					<td><?php _e( 'A sanitized version of the author last name. If author last name is not available so, it uses the author&#39;s username.', 'permalinks-customizer' ); ?></td>
				</tr>
			</table>
			<p><?php _e( '<b>Note:</b> &quot;%postname%&quot; is similar as of the &quot;%title%&quot; tag but the difference is that &quot;%postname%&quot; can only be set once whereas &quot;%title%&quot; can be changed. let&#039;s say the title is &quot;This Is A Great Post!&quot; so, it becomes &quot;this-is-a-great-post&quot; in the URI(At the first time, &quot;%postname%&quot; and &quot;%title%&quot; works same) but if you edit and change title let&#039;s say &quot;This Is A WordPress Post!&quot; so, &quot;%postname%&quot; in the URI remains same &quot;this-is-a-great-post&quot; whereas &quot;%title%&quot; in the URI becomes &quot;this-is-a-wordpress-post&quot;', 'permalinks-customizer' ); ?> </p>
		</div>
		<?php
	}
}
