<?php
/**
 * @package PermalinksCustomizer\Admin
 */

class Permalinks_Customizer_Taxonomies_Settings {

  /**
   * Call Taxonomies Settings Function.
   */
  function __construct() {
    $this->taxonomy_settings();
  }

  /**
   * Shows the main Settings Page Where user can provide different
   * Permalink Structure for their Taxonomies.
   *
   * @access private
   * @since 1.1
   *
   * @return void
   */
  private function taxonomy_settings() {
    if ( isset( $_POST['submit'] ) ) {
      $permalinks_customizer_taxes = array();
      foreach ( $_POST as $key => $value ) {
        if ( 'submit' === $key ) {
          continue;
        }
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
    $args = array(
      'public' => true
    );
    $taxonomies = get_taxonomies( $args, 'objects' );
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
          foreach ( $taxonomies as $taxonomy ) {
            $value = '';
            if ( isset( $permalinks_customizer_settings[$taxonomy->name . '_settings'] )
              && isset( $permalinks_customizer_settings[$taxonomy->name . '_settings']['structure'] )
              && ! empty( $permalinks_customizer_settings[$taxonomy->name . '_settings']['structure'] ) ) {
              $value = $permalinks_customizer_settings[$taxonomy->name . '_settings']['structure'];
            }
          ?>
          <tr valign="top">
            <th scope="row"><?php echo $taxonomy->labels->name; ?></th>
            <td><?php echo site_url(); ?>/<input type="text" name="<?php echo $taxonomy->name; ?>" value="<?php echo $value; ?>" class="regular-text" /></td>
          </tr>
          <?php } ?>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'permalinks-customizer' ); ?>" /></p>
      </form>
    </div>
    <?php
  }
}
