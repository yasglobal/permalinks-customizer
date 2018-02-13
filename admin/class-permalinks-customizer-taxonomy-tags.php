<?php
/**
 * @package PermalinksCustomizer\Admin
 */

class Permalinks_Customizer_Taxonomy_Tags {

	/**
	 * Call Taxonomy Tags Function
	 */
	function __construct() {
		$this->available_taxonomy_tags();
	}

	/**
	 * Shows all the Tags which this Plugin Supports for Taxonomies
	 */
	private function available_taxonomy_tags() {
		?>
		<div class="wrap">
			<h1><?php _e( 'Structure Tags for Taxonomies', 'permalinks-customizer' ); ?></h1>
			<div><?php _e( 'These tags can be used to create Permalink Customizers for each post type.', 'permalinks-customizer' ); ?></div>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">%name%</th>
					<td><?php _e( 'Name of the Term/Category. let&#039;s say the name is "External API" so, it becomes external-api in the URI.', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%term_id%</th>
					<td><?php _e( 'The unique ID # of the Term/Category, for example 423', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%slug%</th>
					<td><?php _e( 'A sanitized version of the name of the Term/Category. So &quot;External API&quot; becomes external-api in the URI.', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%parent_slug%</th>
					<td><?php _e( 'A sanitized version of the name of the Term/Category. So &quot;External API&quot; becomes external-api in the URI. This <strong>Tag</strong> contains Immediate <strong>Parent Term/Category Slug</strong> if any parent Term/Category is selected before adding it.', 'permalinks-customizer' ); ?></td>
				</tr>
				<tr valign="top">
					<th scope="row">%all_parents_slug%</th>
					<td><?php _e( 'A sanitized version of the name of the Term/Category. So &quot;External API&quot; becomes external-api in the URI. This <strong>Tag</strong> contains all the <strong>Parent Term/Category Slug</strong> if any parent Term/Category is selected before adding it.', 'permalinks-customizer' ); ?></td>
				</tr>
			</table>
		</div>
		<?php
	}
}
