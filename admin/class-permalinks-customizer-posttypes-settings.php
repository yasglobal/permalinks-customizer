<?php
/**
 * @package PermalinksCustomizer\Admin
 */

class Permalinks_Customizer_PostTypes_Settings {

  /**
   * Call Post Settings Function.
   */
  function __construct() {
    $this->post_settings();
  }

  /**
   * Shows the main Settings Page Where user can provide different
   * Permalink Structure for their PostTypes.
   *
   * @access public
   * @since 0.1
   *
   * @return void
   */
  private function post_settings() {
    if ( isset( $_POST['submit'] ) ) {
      foreach ( $_POST as $key => $value ) {
        if ( 'submit' === $key ) {
          continue;
        }
        update_option( $key, $value );
      }
    }
    $args = array(
      'public'   => true
    );
    $post_types = get_post_types( $args, 'objects' );
    ?>
    <div class="wrap">
      <h1><?php _e( 'PostTypes Permalinks Settings', 'permalinks-customizer' ); ?></h1>
      <div>
        <p><?php _e( 'Define the Permalinks for each post type. You can define different structures for each post type.', 'permalinks-customizer' ); ?></p>
        <p><?php printf( __( 'Please check all the <a href="%1$s/wp-admin/admin.php?page=permalinks-customizer-post-tags" title="structured tags">structured tags</a> which can be used with this plugin, <a href="%2$s/wp-admin/admin.php?page=permalinks-customizer-post-tags" title="here">here</a>', 'permalinks-customizer' ), site_url(), site_url() ); ?>.</p>
      </div>
      <form enctype="multipart/form-data" action="" method="POST">
        <table class="form-table">
          <?php
          foreach ( $post_types as $post_type ) {
            if ( 'attachment' == $post_type->name ) {
              continue;
            }
            $perm_struct = 'permalinks_customizer_' . $post_type->name;
            $value       = esc_attr( get_option( $perm_struct, '' ) );
          ?>
          <tr valign="top">
            <th scope="row"><?php echo $post_type->labels->name; ?></th>
            <td><?php echo site_url(); ?>/<input type="text" name="<?php echo $perm_struct; ?>" value="<?php echo $value; ?>" class="regular-text" /></td>
          </tr>
          <?php } ?>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', 'permalinks-customizer' ); ?>" /></p>
      </form>
    </div>
    <?php
  }
}
