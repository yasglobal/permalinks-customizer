<?php
/**
 * @package PermalinksCustomizer
 */

/**
 * Create Taxonomies Permalinks Settings page.
 *
 * Create Taxonomies Permalinks Settings page HTML and display the page.
 *
 * @since 1.3.3
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
   * @since 1.3.3
   * @access private
   *
   * @return void.
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
      print '<div id="message" class="updated notice notice-success is-dismissible">' .
                '<p>' . __(
                  'Taxonomies Permalinks Settings are updated.',
                  'permalinks-customizer'
                ) . '</p>' .
              '</div>';
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
        <p>
          <?php printf( __( 'You can define the structure of the permalinks for the Taxonomies. It can be the same or different as per your requirements. This structure is used when creating the Term/Category or using the Regenerate Permalink button on the Edit page.', 'permalinks-customizer' ), site_url() ); ?>
        </p>
        <p><?php printf( __( 'Please check the <a href="%1$s/wp-admin/admin.php?page=permalinks-customizer-tags&tab=taxonomies-tags" title="Structure Tags" target="_blank">Structure Tags</a> and use the appropriate tags for the Taxonomies according to your need.', 'permalinks-customizer' ), site_url(), site_url() ); ?></p>
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
