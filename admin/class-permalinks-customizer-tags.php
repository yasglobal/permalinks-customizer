<?php
/**
 * @package PermalinksCustomizer
 */

/**
 * Display available tags.
 *
 * Create tags page HTML and display the page.
 *
 * @since 2.7.0
 */
class Permalinks_Customizer_Tags {

  /**
   * Call available Tags Function.
   */
  function __construct() {
    $this->available_tags();
  }

  /**
   * Shows all the Tags which can be used with this Plugin.
   *
   * @since 2.7.0
   * @access private
   */
  private function available_tags() {
    $plugin_url = plugins_url( '/admin', PERMALINKS_CUSTOMIZER_FILE );
    wp_enqueue_style( 'style', $plugin_url . '/css/style.min.css' );
    $tab1_checked = 'checked';
    $tab2_checked = '';
    if ( isset( $_GET['tab'] ) && 'taxonomies-tags' === $_GET['tab'] ) {
      $tab1_checked = '';
      $tab2_checked = 'checked';
    }
    ?>
    <div class="wrap">
      <h1><?php _e( 'Structure Tags', 'permalinks-customizer' ); ?></h1>
      <p><?php _e( 'You can find all the tags which are currently supported by the <strong>Permalinks Customizer</strong>.', 'permalinks-customizer' ); ?></p>

      <div class="tabs">
        <input id="tab1" type="radio" name="tabs" <?php echo $tab1_checked; ?>>
        <label for="tab1"><?php _e( 'PostTypes Tags', 'permalinks-customizer' ); ?></label>

        <input id="tab2" type="radio" name="tabs" <?php echo $tab2_checked; ?>>
        <label for="tab2"><?php _e( 'Taxonomies Tags', 'permalinks-customizer' ); ?></label>

        <section id="posttypes-tags">
          <h2><?php _e( 'Default Tags for PostTypes', 'permalinks-customizer' ); ?></h2>
          <div><?php _e( 'These tags are provided by the <strong>WordPress</strong> and can be used on default <strong>Permalink</strong> Settings page of <strong>WordPress</strong> as well as <strong>PostTypes Settings</strong> page of <strong>Permalinks Customizer</strong>.', 'permalinks-customizer' ); ?></div>
          <table class="wp-list-table widefat fixed striped">
            <thead>
              <tr>
                <th class="manage-column column-title column-primary"><strong><?php _e( 'Tag Name', 'permalinks-customizer' ); ?></strong></th>
                <th class="manage-column column-primary"><strong><?php _e( 'Description', 'permalinks-customizer' ); ?></strong></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th>%year%</th>
                <td><?php _e( 'The year of the post, four digits, for example 2004', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%monthnum%</th>
                <td><?php _e( 'Month of the year, for example 05', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%day%</th>
                <td><?php _e( 'Day of the month, for example 28', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%hour%</th>
                <td><?php _e( 'Hour of the day, for example 15', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%minute%</th>
                <td><?php _e( 'Minute of the hour, for example 43', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%second%</th>
                <td><?php _e( 'Second of the minute, for example 33', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%post_id%</th>
                <td><?php _e( 'The unique ID # of the post, for example 423', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%postname%</th>
                <td><?php _e( 'A sanitized version of the title of the post (post slug field on Edit Post/Page panel). So "This Is A Great Post!" becomes this-is-a-great-post in the URI.', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%category%</th>
                <td><?php _e( 'A sanitized version of the category name (category slug field on New/Edit Category panel). Nested sub-categories appear as nested directories in the URI.', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%author%</th>
                <td><?php _e( 'A sanitized version of the author name.', 'permalinks-customizer' ); ?></td>
              </tr>
            </tbody>
          </table>

          <table class="note">
            <tbody>
              <tr>
                <td class="icon">
                  <img src="<?php echo $plugin_url . '/icons/info.svg'; ?>" class="icon info-icon" title="Note" alt="Note" />
                </td>
                <td>
                  <p>
                    <?php _e( '<code>%postname%</code> is similar as of the <code>%title%</code> tag but the difference is that <code>%postname%</code> can only be set once whereas <code>%title%</code> can be changed. let&#039;s say the title is &quot;This Is A Great Post!&quot; so, it becomes &quot;this-is-a-great-post&quot; in the URI(At the first time, <code>%postname%</code> and <code>%title%</code> works same) but if you edit and change title let&#039;s say &quot;This Is A WordPress Post!&quot; so, <code>%postname%</code> in the URI remains same &quot;this-is-a-great-post&quot; whereas <code>%title%</code> in the URI becomes &quot;this-is-a-wordpress-post&quot;.', 'permalinks-customizer' ); ?>
                  </p>
                </td>
              </tr>
            </tbody>
          </table>

          <h2><?php _e( 'Custom Tags for PostTypes', 'permalinks-customizer' ); ?></h2>
          <div><?php _e( 'These tags are provided by the <strong>Permalinks Customizer</strong> and can not be used on the default <b>Permalink</b> settings page of <strong>WordPress</strong>.', 'permalinks-customizer' ); ?></div>
          <table class="wp-list-table widefat fixed striped">
            <thead>
              <tr>
                <th class="manage-column column-title column-primary"><strong><?php _e( 'Tag Name', 'permalinks-customizer' ); ?></strong></th>
                <th class="manage-column column-primary"><strong><?php _e( 'Description', 'permalinks-customizer' ); ?></strong></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th>%title%</th>
                <td><?php _e( 'Title of the post. let&#039;s say the title is "This Is A Great Post!" so, it becomes this-is-a-great-post in the URI.', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%parent_title%</th>
                <td><?php _e( 'This tag is similar as <code>%title%</code>.<br><br>Only the difference is that it appends the Immediate <strong>Parent Page Title</strong> in the URI if any parent page is selected.', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%all_parents_title%</th>
                <td><?php _e( 'This tag is similar as <code>%title%</code>.<br><br>Only the difference is that it appends all the <strong>Parents Page Title</strong> in the URI if any parent page is selected.', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%parent_postname%</th>
                <td><?php _e( 'This tag is similar as <code>%postname%</code>.<br><br>Only the difference is that it appends the Immediate <strong>Parent Page Slug</strong> in the URI if any parent page is selected.', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%all_parents_postname%</th>
                <td><?php _e( 'This tag is similar as <code>%postname%</code>. Only the difference is that it appends all the <strong>Parents Page Slug</strong> in the URI if any parent page is selected.', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%child-category%</th>
                <td><?php _e( ' A sanitized version of the category name (category slug field on New/Edit Category panel).', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%product_cat%</th>
                <td><?php _e( 'A sanitized version of the product category name (category slug field on New/Edit Category panel). Nested sub-categories appear as nested directories in the URI.<br><br><i>This <strong>tag</strong> is specially used for WooCommerce Products.</i>', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>&lt;%ctax_custom_taxonomy%&gt;</th>
                <td><?php _e( 'A sanitized version of the custom taxonomy where the taxonomy name is <b><i>custom_taxonomy</i></b>. Replace the <i>custom_taxonomy</i> with your appropriate created taxonomy name.<br><br>If you want to provide the default slug which is used when the category/taxonomy doesn\'t be selected so, make sure to provide default name/slug which looks like this: <b><i>&lt;%ctax_custom_taxonomy??sales%&gt;</i></b>. Value which is written between the <b><i>??</i></b> and <b><i>%&gt;</i></b> is used as default slug.', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>&lt;%ctaxparents_custom_taxonomy%&gt;</th>
                <td><?php _e( 'This tag is similar as <code>&lt;%ctax_custom_taxonomy%&gt;</code>.<br><br>Only the difference is that it appends all the <strong>Parents Slug</strong> in the URI if any parent category/term is selected.', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%author_firstname%</th>
                <td><?php _e( 'A sanitized version of the author first name. If author first name is not available then it uses the author&#39;s username.', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%author_lastname%</th>
                <td><?php _e( 'A sanitized version of the author last name. If author last name is not available then it uses the author&#39;s username.', 'permalinks-customizer' ); ?></td>
              </tr>
            </tbody>
          </table>
        </section>

        <section id="taxonomies-tags">
          <div><?php _e( 'These tags are provided by the <strong>Permalinks Customizer</strong> and can not be used on the default <strong>Permalink</strong> settings page of <strong>WordPress</strong>.', 'permalinks-customizer' ); ?></div>
          <table class="wp-list-table widefat fixed striped">
            <thead>
              <tr>
                <th class="manage-column column-title column-primary"><strong><?php _e( 'Tag Name', 'permalinks-customizer' ); ?></strong></th>
                <th class="manage-column column-primary"><strong><?php _e( 'Description', 'permalinks-customizer' ); ?></strong></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th>%name%</th>
                <td><?php _e( 'Name of the Term/Category. let&#039;s say the name is "External API" so, it becomes external-api in the URI.', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%term_id%</th>
                <td><?php _e( 'The unique ID # of the Term/Category, for example 423', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%slug%</th>
                <td><?php _e( 'A sanitized version of the name of the Term/Category. So "External API" becomes external-api in the URI.', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%parent_slug%</th>
                <td><?php _e( 'This tag is similar as <code>%slug%</code>.<br><br> Only the difference is that it appends the immediate <strong>Parent Term/Category Slug</strong> in the URI if any parent Term/Category is selected.', 'permalinks-customizer' ); ?></td>
              </tr>
              <tr>
                <th>%all_parents_slug%</th>
                <td><?php _e( 'This tag is similar as <code>%slug%</code>.<br><br> Only the difference is that it appends all the <strong>Parent Terms/Category Slugs</strong> in the URI if any parent Term/Category is selected.', 'permalinks-customizer' ); ?></td>
              </tr>
            </tbody>
          </table>
        </section>
      </div>
    </div>
    <?php
  }
}
