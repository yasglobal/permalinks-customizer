=== Permalinks Customizer ===
Contributors: aliya-yasir, sasiddiqui
Donate link: https://www.paypal.me/yasglobal
Tags: Link, Permalink, URL, Redirects, Tags
Requires at least: 3.5
Requires PHP: 5.2.4
Tested up to: 4.9
Stable tag: 2.0.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.html

Set permalinks for default PostTypes, custom PostTypes, default Taxonomies (Category/Tags) and custom Taxonomies which can be changed from the edit page.

== Description ==

Customize your URL and set the slug. You can use basic keywords which is defined by the WordPress for defining the permalinks as well as some new keywords which is defined by this plugin. All the keywords are defined on the Tags page under Permalinks Customizer.

By using <strong>Permalinks Customizer</strong> you can set the different permalink structure for each PostType and Taxonomy.

= How to set the Permalinks for the PostTypes separately =

Let's assume that you have 6 <strong>PostTypes</strong> and they all have different style of <strong>permalinks</strong>. Like:

1. <strong>Blog :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/blog/year-month-date-postname/
2. <strong>Customers :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/customers/postname/
3. <strong>Events :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/events/year-month-date-postname/
4. <strong>Press :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/press/category/year/postname/
5. <strong>News :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/news/year/postname/
6. <strong>Sponsors :</strong> For this post type you want to create a <strong>permalink</strong> which looks like this: http://www.example.com/company/sponsor/post_title/

This plugin allows you to do this very easily. You just need to go on <strong>Permalinks Customizer</strong> Settings Page. Where text fields are shown with PostType name. You can define your permalinks you want to create for each post type.

If you leave any PostType field empty. So, <strong>Permalinks Customizer</strong> would create a permalink for that PostType by using the default <strong>permalink</strong> settings.

> <strong>How to Configure Permalinks Customizer</strong>
>
> You can configure the plugin by going to the menu `Permalinks Customizer` that appears in your admin menu.<br><br><strong>                                   OR</strong><br><br> http://www.example.com/wp-admin/admin.php?page=permalinks-customizer-posts-settings

== Structure Tags ==

=== Tags for PostTypes ===

* <strong>%title%</strong> : Title of the post. let's say the title is "This Is A Great Post!" so, it becomes this-is-a-great-post in the URI.
* <strong>%year%</strong> : The year of the post, four digits, for example 2004
* <strong>%monthnum%</strong> : Month of the year, for example 05
* <strong>%day%</strong> : Day of the month, for example 28
* <strong>%hour%</strong> : Hour of the day, for example 15
* <strong>%minute%</strong> : Minute of the hour, for example 43
* <strong>%second%</strong> : Second of the minute, for example 33
* <strong>%post_id%</strong> : The unique ID # of the post, for example 423
* <strong>%postname%</strong> : A sanitized version of the title of the post (post slug field on Edit Post/Page panel). So "This Is A Great Post!" becomes this-is-a-great-post in the URI.
* <strong>%parent_postname%</strong> : A sanitized version of the title of the post (post slug field on Edit Post/Page panel). So "This Is A Great Post!" becomes this-is-a-great-post in the URI. This <strong>Tag</strong> contains Immediate <strong>Parent Page Slug</strong> if any parent page is selected before publishing.
* <strong>%all_parents_postname%</strong> : A sanitized version of the title of the post (post slug field on Edit Post/Page panel). So "This Is A Great Post!" becomes this-is-a-great-post in the URI. This <strong>Tag</strong> contains all the <strong>Parent Page Slugs</strong> if any parent page is selected before publishing.
* <strong>%category%</strong> : A sanitized version of the category name (category slug field on New/Edit Category panel). Nested sub-categories appear as nested directories in the URI.
* <strong>%child-category%</strong> : A sanitized version of the category name (category slug field on New/Edit Category panel).
* <strong>%product_cat%</strong> : A sanitized version of the product category name (category slug field on New/Edit Category panel). Nested sub-categories appear as nested directories in the URI. <i>This <strong>tag</strong> is specially used for WooCommerce Products.</i>
* <strong>&lt;%ctax_custom_taxonomy%&gt;</strong> : A sanitized version of the custom taxonomy where the taxonomy name is <strong><i>custom_taxonomy</i></strong>. Replace the <i>custom_taxonomy</i> with your appropriate created taxonomy name.
  If you want to provide the default slug which is used when the category/taxonomy doesn\'t be selected so, make sure to provide default name/slug which looks like this: <strong><i>&lt;%ctax_custom_taxonomy??sales%&gt;</i></strong>. Value which is written between the <strong><i>??</i></strong> and <strong><i>%&gt;</i></strong> is used as default slug.
* <strong>%author%</strong> : A sanitized version of the author name.
* <strong>%author_firstname%</strong> : A sanitized version of the author first name. If author first name is not available so, it uses the author\'s username.
* <strong>%author_lastname%</strong> : A sanitized version of the author last name. If author last name is not available so, it uses the author\'s username.

<strong>Note</strong>: *%postname%* is similar as of the *%title%* tag but the difference is that *%postname%* can only be set once whereas *%title%* can be changed. let's say the title is "This Is A Great Post!" so, it becomes "this-is-a-great-post" in the URI(At the first time, *%postname%* and *%title%* works same) but if you edit and change title let's say "This Is A WordPress Post!" so, *%postname%* in the URI remains same "this-is-a-great-post" whereas *%title%* in the URI becomes "this-is-a-wordpress-post"

=== Tags for Taxonomies ===

* <strong>%name%</strong> : Name of the Term/Category. let's say the name is "External API" so, it becomes external-api in the URI.
* <strong>%term_id%</strong> : The unique ID # of the Term/Category, for example 423
* <strong>%slug%</strong> : A sanitized version of the name of the Term/Category. So "External API" becomes external-api in the URI.
* <strong>%parent_slug%</strong> : A sanitized version of the name of the Term/Category. So "External API" becomes external-api in the URI. This Tag contains Immediate Parent Term/Category Slug if any parent Term/Category is selected before adding it.
* <strong>%all_parents_slug%</strong> : A sanitized version of the name of the Term/Category. So "External API" becomes external-api in the URI. This Tag contains all the Parent Term/Category Slug if any parent Term/Category is selected before adding it.

<strong>Be warned:</strong> *This plugin is not a replacement for WordPress's built-in permalink system*. Check your WordPress administration's "Permalinks" settings page first, to make sure that this doesn't already meet your needs.

=== Filter ===

==== Exclude Permalinks ====

If you want to exclude some Permalink to processed with the plugin so, just add the filter looks like this:
`
function yasglobal_exclude_url( $permalink ) {
  if ( false !== strpos( $permalink, '/contact-us/' ) ) {
    return '__true';
  }
  return;
}
add_filter( 'permalinks_customizer_exclude_request', 'yasglobal_exclude_url' );
`
==== Disable automatically create redirects ====

To disable automatically create redirects feature on creating and updating the post/pages/categories, add this filter in your themes functions.php.
```
add_filter( 'permalinks_customizer_auto_created_redirects', '__return_false');
```
This filter stops to be creating new redirects but existed redirects keeps working. To stop existed redirects, add `permalinks_customizer_disable_redirects` filter.

==== Disable Redirects ====

To disable redirects to be applied , add this filter in your themes functions.php.
```
add_filter( 'permalinks_customizer_disable_redirects', '__return_false');
```
This filter only stop redirects to be work but the automatically create redirects still works. To stop automatically create redirects feature add `permalinks_customizer_auto_created_redirects` filter.

=== Thanks for the Support ===

The support from the users that love Permalinks Customizer is huge. You can support Permalinks Customizer future development and help to make it even better by donating or even giving a 5 star rating with a nice message to me :)

[Donate to Permalinks Customizer](https://www.paypal.me/yasglobal)

=== Bug reports ===

Bug reports for Permalinks Customizer are [welcomed on GitHub](https://github.com/yasglobal/permalinks-customizer). Please note GitHub is not a support forum, and issues that aren't properly qualified as bugs will be closed.

== Installation ==

This process defines you the steps to follow either you are installing through WordPress or Manually from FTP.

**From within WordPress**

1. Visit 'Plugins > Add New'
2. Search for Permalinks Customizer
3. Activate Permalinks Customizer from your Plugins page.
4. Go to "after activation" below.

**Manually**

1. Upload the `permalinks-customizer` folder to the `/wp-content/plugins/` directory
2. Activate Permalinks Customizer through the 'Plugins' menu in WordPress
3. Go to "after activation" below.

**After activation**

1. Go to the plugin settings page and set up the plugin for your site.
2. You're done!

== Screenshots ==

* Permalinks can be set for each and every PostType from [here](http://www.example.com/wp-admin/admin.php?page=permalinks-customizer-posts-settings). The empty permalink field for the PostType will use the WordPress Permalink Settings.

* All the available tags which can be used for defining the permalinks for PostTypes are listed [here](http://www.example.com/wp-admin/admin.php?page=permalinks-customizer-post-tags). Some of the tags are defined here may only be use with [Permalinks Customizer](https://wordpress.org/plugins/permalinks-customizer/) plugin only.

* Permalinks can easily be changed for the single post from its post edit page.

* Permalinks can be set for each and every Taxonomies from [here](http://www.example.com/wp-admin/admin.php?page=permalinks-customizer-taxonomies-settings). The empty permalink field for the taxonomy would not create teh permalink for it.

* All the available tags which can be used for defining the permalinks for Taxonomies are listed [here](http://www.example.com/wp-admin/admin.php?page=permalinks-customizer-taxonomy-tags). These tags may work with this Plugin Only.

* Permalinks can easily be changed for the single Term from its Term edit page.

* You can easily convert the custom permalink URLs to permalink customizer URLs by going on [permalinks settings page](http://www.example.com/wp-admin/admin.php?page=permalinks-customizer-convert-url)

* Permalink conversion varies from server to server. So, make sure to convert the url at a time on depending on your server capability.

== Frequently Asked Questions ==

= Q. How to define slug of the post type? =
A. Go to Settings, there is a field with the post type name. On this field, you can define slug for the post type.

= Q. Can i use tags? =
A. Yes, you can use all the tags as defined on the [Permalinks Customizer page](https://wordpress.org/plugins/permalinks-customizer/).

= Q. Does the plugin supports custom taxonomy tag? =
A. Yes, it supports custom taxonomy tag. You can define the tag as mentioned on the [Permalinks Customizer page](https://wordpress.org/plugins/permalinks-customizer/).

= Q. Can i regenerate all the permalinks according to the defined structure? =
A. Yes, you can regenerate all the permalinks according to the defined structure. To have a bulk permalink update, Go to the `All Post` page there is a a option in the bulk action drop down with the name of `Regenerate Permalinks`. Use that option for regenerating the Permalinks.

= Q. Does `Regenerate Permalinks` damage my site SEO? =
A. No, it won't damage your site SEO. As regenerating permalinks added redirect against their previous permalink.

= Q. May this plugin works with custom permalinks? =
A. No, This plugin does not work with [custom permalinks](https://wordpress.org/plugins/custom-permalinks/).

== Changelog ==

= 2.0.1 - July 16, 2018 =

  * Bugs
    * Fixed subdomain URL issue
    * Make the URLs to absolute on Admin Pages so, Permalinks for subdomain points to the correct URL

= 2.0.0 - July 09, 2018 =

  * Enhancements
    * Added Custom Taxonomy Support for PostTypes
      * Define the Custom Taxonomy tag in PostType permalink structure
      * Define default taxonomy which is used in the permalink when no taxonomy is selected
    * Added `Regenerate Permalinks` in Bulk for PostTypes
    * Added `Regenerate Permalinks` in Bulk for Taxonomies
    * Added Redirect Functionality
      * Automatically create redirects on changing the permalink
      * Automatically create redirects on creating post/taxonomy
      * Automatically disable redirects if the newly created permalink was redireted before
      * Enable / Disable redirects from Plugin redirects Page
      * Delete redirects from Plugin redirects Page
      * Added Filters to disable redirects functionality if needed
    * Added Capabilities which allow users to view permalinks and manage settings without having administrator role
    * Reduce Query over non-admin pages
    * Avoid appending slashes and use trailingslashit instead
    * Removed unused PostTypes and Taxonomies (Only Public PostTypes and Taxonomies are shown on Settings Page)
  * Bugs
    * Added a check for create term, to reduce undesired notifications.
    * Fixed undefined variable warning in PostType Permalinks and Taxonomies Permalinks Page
    * Fixed PHP warning on Bulk update over PostType Permalinks and Taxonomies Permalinks Page when no post/category is selected
    * Print `$view_post` varaible value

= 1.3.9 - Feb 13, 2018 =

  * Enhancements
    * Fixed Empty Title issue
    * Added About Page
  * Bugs
    * Fixed PHP Notices

= Earlier versions =

  * For the changelog of earlier versions, please refer to the separate changelog.txt file.
