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
   */
  private function taxonomy_settings() {
    $message = '';
    if ( isset( $_POST['_wpnonce'] )
      && wp_verify_nonce( $_POST['_wpnonce'], 'permalinks-customizer_taxonomy_settings' )
    ) {
      $permalinks_customizer_taxes = array();
      foreach ( $_POST as $key => $value ) {
        if ( '_wpnonce' === $key || '_wp_http_referer' === $key ) {
          continue;
        }
        $permalinks_customizer_taxes[$key . '_settings'] = array(
          'structure' => $value,
        );
        unset( $_POST[$key] );
      }
      update_option(
        'permalinks_customizer_taxonomy_settings', $permalinks_customizer_taxes
      );
      $message = __( 'Taxonomies Permalinks Settings are updated.', 'permalinks-customizer' );
    }
    $permalinks_customizer_settings = get_option( 'permalinks_customizer_taxonomy_settings', '' );

    if ( is_string( $permalinks_customizer_settings ) ) {
      $permalinks_customizer_settings = unserialize( $permalinks_customizer_settings );
    }

    $args = array(
      'public' => true
    );
    $taxonomies = get_taxonomies( $args, 'objects' );
    ?>
    <div class="wrap">
      <h1>
        <?php _e( 'Taxonomies Permalinks Settings', 'permalinks-customizer' ); ?>
      </h1>
      <?php if ( ! empty( $message ) ) { ?>
        <div id="message" class="updated notice notice-success is-dismissible">
          <p><?php echo $message; ?></p>
        </div>
      <?php } ?>
      <div>
        <p>
          <?php printf( __( 'You can define the structure of the permalinks for the Taxonomies. It can be the same or different as per your requirements. This structure is used when creating the Term/Category or using the Regenerate Permalink button on the Edit page.', 'permalinks-customizer' ), site_url() ); ?>
        </p>
        <p><?php printf( __( 'Please check the <a href="%1$s/wp-admin/admin.php?page=permalinks-customizer-tags&tab=taxonomies-tags" title="Structure Tags" target="_blank">Structure Tags</a> and use the appropriate tags for the Taxonomies according to your need.', 'permalinks-customizer' ), site_url(), site_url() ); ?></p>
      </div>

      <form enctype="multipart/form-data" action="" method="POST">
        <?php wp_nonce_field( 'permalinks-customizer_taxonomy_settings' ); ?>
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
            <td>
              <?php echo site_url(); ?>/<input type="text" name="<?php echo $taxonomy->name; ?>" value="<?php echo $value; ?>" class="regular-text" />
            </td>
          </tr>
          <?php } ?>
        </table>
        <p class="submit">
          <input type="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'permalinks-customizer' ); ?>" />
        </p>
      </form>
    </div>
    <?php
  }
}
