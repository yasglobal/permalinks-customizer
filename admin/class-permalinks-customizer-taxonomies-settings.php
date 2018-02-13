<?php
/**
 * @package PermalinksCustomizer\Admin
 */

class Permalinks_Customizer_Taxonomies_Settings {

	/**
	 * Call Taxonomies Settings Function
	 */
	function __construct() {
		$this->taxonomy_settings();
	}

  	/**
	 * Shows the main Settings Page Where user can provide different Permalink Structure for their Taxonomies
	 */
	private function taxonomy_settings() {
		if ( isset( $_POST['submit'] ) ) {
			$permalinks_customizer_taxes = array();
			foreach ( $_POST as $key => $value ) {
				if ( $key === 'submit' )
					continue;
				$permalinks_customizer_taxes[$key . '_settings'] = array(
					'structure' => $value,
				);
			}
			$permalinks_customizer_taxes = serialize( $permalinks_customizer_taxes );
			update_option(
				'permalinks_customizer_taxonomy_settings', $permalinks_customizer_taxes
			);
		}
		$permalinks_customizer_settings = unserialize(
			get_option( 'permalinks_customizer_taxonomy_settings' )
		);
		?>
		<div class="wrap">
			<h1><?php _e( 'Taxonomies Permalinks Settings', 'permalinks-customizer' ); ?></h1>
			<div>
				<p><?php _e( 'Define the Permalinks for each taxonomy type so, the Permalinks would be created automatically on creating the Term/Category. Otherwise, you need to create the Permalinks manually from the Edit Term/Category Page.', 'permalinks-customizer' ); ?></p>
				<p><?php printf( __( 'Please check all the <a href="%1$s/wp-admin/admin.php?page=permalinks-customizer-taxonomy-tags" title="structured tags">structured tags</a> which can be used with this plugin, <a href="%1$s/wp-admin/admin.php?page=permalinks-customizer-taxonomy-tags" title="here">here</a>.', 'permalinks-customizer' ), site_url(), site_url() ); ?></p>
			</div>

			<form enctype="multipart/form-data" action="" method="POST">
				<table class="form-table">
					<?php
					$taxonomies = get_taxonomies();
					foreach ( $taxonomies as $taxonomy ) {
						if ( $taxonomy == 'nav_menu' ) {
							continue;
						}
						$value = '';
						if ( isset( $permalinks_customizer_settings[$taxonomy . '_settings'] )
							&& isset( $permalinks_customizer_settings[$taxonomy . '_settings']['structure'] )
							&& ! empty( $permalinks_customizer_settings[$taxonomy . '_settings']['structure'] ) ) {
							$value = $permalinks_customizer_settings[$taxonomy . '_settings']['structure'];
						}
					?>
					<tr valign="top">
						<th scope="row"><?php echo $taxonomy; ?></th>
						<td><?php echo site_url(); ?>/<input type="text" name="<?php echo $taxonomy; ?>" value="<?php echo $value; ?>" class="regular-text" /></td>
					</tr>
					<?php } ?>
				</table>
				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'permalinks-customizer' ); ?>" /></p>
			</form>
		</div>
		<?php
	}
}
