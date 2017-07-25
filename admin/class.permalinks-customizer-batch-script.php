<?php
/**
 * @package PermalinksCustomizer\Admin\BatchScript
 */

class Permalinks_Customizer_Batch_Script {

	/**
	 * Call Conversion Function
	 */
	function __construct() {
		$this->permalinks_customizer_conversion();
  }
	
	/**
	 * This Function converts the Custom Permalinks to Permalink Customizer using Batch Script
	 */
	private function permalinks_customizer_conversion() {
		global $wpdb;
		$plugin_slug = 'permalinks-customizer-convert-url';
		$step        = isset( $_GET['processing'] ) ? absint( $_GET['processing'] ) : 1;
		$steps       = isset( $_GET['limit'] ) ? $_GET['limit'] : 0;
		$data        = $wpdb->get_row( "SELECT meta_id from $wpdb->postmeta where meta_key = 'custom_permalink' LIMIT 1");
		echo '<div class="wrap"><h2>'.esc_html( get_admin_page_title() ).'</h2>';
		if ( isset( $_GET['processing'] ) ) :
			if ( isset($data) && !empty($data) ) {
				$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->postmeta SET meta_key = 'permalink_customizer' where meta_id = %d ", $data->meta_id) );
				echo '<p>The batch update routine has started. Please be patient as this may take some time to complete <img class="conversion-in-process" src="'.includes_url( 'images/spinner-2x.gif' ).'" alt="Loading..." ) width="20px" height="20px" style="vertical-align:bottom" /></p>';
				echo '<p class="processing"><strong>Converting '.(int) $step.' out of '.(int) $steps.' custom permalinks</strong></p>'; 
				?>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						$.post( ajaxurl, { action: 'permalinks-customizer-convert-url', processing: '<?php echo $step; ?>', limit: '<?php echo absint( $_GET["limit"] ); ?>'}, function(res){
							var step = '<?php echo $step; ?>';
							var total = '<?php echo $steps; ?>';
							if ( step == total ) {
								$('.conversion-in-process').remove();
								window.location = window.location.pathname="?page=permalinks-customizer-convert-url&processed="+total;
								return;
							} else {
								document.location.href = '<?php echo add_query_arg( array( "page" => $plugin_slug, "processing" => (int) $step + 1, "limit" => absint( $_GET["limit"] ) ) ); ?>';
							}
						}, 'json');
					});
				</script>
			<?php } else { ?>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						window.location = window.location.pathname="?page=permalinks-customizer-convert-url&processed=<?php echo $step; ?>&no-permalink=1";
					});
				</script>
			<?php } 
		else : 
			if( $_GET["no-permalink"] == 1 ) {
				$completed = $_GET["processed"] - 1;
				$cat_data = $wpdb->get_row( "SELECT option_id from $wpdb->options where option_name LIKE '%custom_permalink_table%' " );
				if( isset($cat_data) && !empty($cat_data)) {
					$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->options SET option_name = 'permalinks_customizer_table' where option_id = %d ", $cat_data->option_id) );
				}
				echo '<div class="updated"><p>'. $completed .' <strong>Custom Permalink</strong> have been converted to <strong>Permalink Customizer</strong> successfully.</p></div>';
			} elseif( $_GET["processed"] > 0 ) {
				$cat_data = $wpdb->get_row( "SELECT option_id from $wpdb->options where option_name LIKE '%custom_permalink_table%' " );
				if( isset($cat_data) && !empty($cat_data)) {
					$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->options SET option_name = 'permalinks_customizer_table' where option_id = %d ", $cat_data->option_id) );
				}
				echo '<div class="updated"><p>'. $_GET["processed"] .' <strong>Custom Permalink</strong> have been converted to <strong>Permalink Customizer</strong> successfully.</p></div>';
			}
			echo '<p>Click on the "Convert Permalink" button to convert custom permalink to Permalink Customizer. By doing this, all of your previous permalink which was created by custom permalink plugin would be converted to Permalink Customizer.</p>'; 
			echo '<form id="permalinks-customizer-convert-url" method="get" action="'.add_query_arg( 'page', 'permalinks-customizer-convert-url' ).'">';
			echo '<input type="hidden" name="page" value="'.$plugin_slug.'" />';
			echo '<input type="hidden" name="processing" value="1" />';
			echo '<input type="number" name="limit" value="100" />';
			echo '<p><input class="button button-primary" type="submit" name="submit" value="Convert Permalink" /></p>';
			echo '</form>';
		endif;
		echo '</div>';
	}
}