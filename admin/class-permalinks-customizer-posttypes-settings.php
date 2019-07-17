<?php
/**
 * @package PermalinksCustomizer
 */

/**
 * Create PostTypes Permalinks Settings page.
 *
 * Create PostTypes Permalinks Settings HTML and display the page.
 *
 * @since 1.0.0
 */
class Permalinks_Customizer_PostTypes_Settings {

  /**
   * Call Post Settings Function.
   */
  function __construct() {
    $this->post_settings();
  }

  /**
   * Shows the main Settings Page Where user can provide different Permalink
   * Structure for their PostTypes.
   *
   * @since 1.0.0
   * @access public
   */
  private function post_settings() {
    $message = '';
    if ( isset( $_POST['_wpnonce'] )
      && wp_verify_nonce( $_POST['_wpnonce'], 'permalinks-customizer_posttype_settings' )
    ) {
      foreach ( $_POST as $key => $value ) {
        if ( '_wpnonce' === $key || '_wp_http_referer' === $key ) {
          continue;
        }
        update_option( $key, $value );
        unset( $_POST[$key] );
      }
      $message = __( 'PostTypes Permalinks Settings are updated.', 'permalinks-customizer' );
    }
    if ( isset( $_GET['cache'] ) && 1 == $_GET['cache'] ) {
      // Remove rewrite rules and then recreate rewrite rules.
      flush_rewrite_rules();

      $action_comp = array(
        'action' => wp_kses( 'cache', array() ),
      );
      update_option( 'permalinks_customizer_clear_cache', $action_comp );
      unset( $_GET['cache'] );
      $rebuild_query = '/wp-admin/admin.php?' . http_build_query( $_GET );
      header( 'Location: ' . $rebuild_query, 301 );
      exit();
      $message = __( 'Permalinks cache cleared.', 'permalinks-customizer' );
    }

    $applied_action = get_option( 'permalinks_customizer_clear_cache', '' );
    if ( ! empty( $applied_action ) ) {
      delete_option( 'permalinks_customizer_clear_cache' );
      if ( isset( $applied_action['action'] ) ) {
        $message = __( 'Permalinks cache cleared.', 'permalinks-customizer' );
      }
    }

    $args = array(
      'public' => true
    );
    $post_types = get_post_types( $args, 'objects' );
    ?>
    <div class="wrap">
      <h1>
        <?php _e( 'PostTypes Permalinks Settings', 'permalinks-customizer' ); ?>
      </h1>
      <?php if ( ! empty( $message ) ) { ?>
        <div id="message" class="updated notice notice-success is-dismissible">
          <p><?php echo $message; ?></p>
        </div>
      <?php } ?>
      <div>
        <p>
          <?php printf( __( 'You can define the structure of the permalinks for the PostTypes. It can be the same or different as per your requirements. If you <strong>DO NOT</strong> define the structure for any PostType then the same structure is used as defined in the <a href="%1$s/wp-admin/options-permalink.php" title="WordPress Permalink" target="_blank">WordPress Permalink</a> page.', 'permalinks-customizer' ), site_url() ); ?>
        </p>
        <p>
          <?php printf( __( 'Please check the <a href="%1$s/wp-admin/admin.php?page=permalinks-customizer-tags" title="Structure Tags" target="_blank">Structure Tags</a> and use the appropriate tags for the PostTypes according to your need.', 'permalinks-customizer' ), site_url(), site_url() ); ?>
        </p>
      </div>
      <form enctype="multipart/form-data" method="POST">
        <?php wp_nonce_field( 'permalinks-customizer_posttype_settings' ); ?>
        <table class="form-table">
          <?php
          foreach ( $post_types as $post_type ) {
            $perm_struct = 'permalinks_customizer_' . $post_type->name;
            $value       = esc_attr( get_option( $perm_struct, '' ) );
          ?>
          <tr valign="top">
            <th scope="row"><?php echo $post_type->labels->name; ?></th>
            <td>
              <?php echo site_url(); ?>/<input type="text" name="<?php echo $perm_struct; ?>" value="<?php echo $value; ?>" class="regular-text" />
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
