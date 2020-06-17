=== Permalinks Customizer ===
Contributors: aliya-yasir, sasiddiqui
Tags: Link, Permalink, URL, Redirects, Tags
Requires at least: 3.5
Requires PHP: 5.2.4
Tested up to: 5.4
Stable tag: 2.8.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.html

**Permalinks Customizer** helps you to set the different permalink structure for different PostTypes and Taxonomies which are publicly available.

You can use the tags which are defined by the WordPress as mentioned [here](https://github.com/yasglobal/permalinks-customizer#default-tags-for-posttypes). Also, you can use the tags which are customily defined by this plugin for [PostTypes](https://github.com/yasglobal/permalinks-customizer#custom-tags-for-posttypes) and [Taxonomies](https://github.com/yasglobal/permalinks-customizer#custom-tags-for-taxonomies). You can also find these tags in Permalinks Customizer menu on WordPress Dashboard (if the plugin is installed).

== How to set the Permalinks for different PostTypes ==

Let's assume that you have 6 **PostTypes** and you like to apply differnet **permalink** structure for them. Like:

1. **Blog :** For this post type you want to create a **permalink** which looks like this: http://www.example.com/blog/year-month-date-postname/
1. **Customers :** For this post type you want to create a **permalink** which looks like this: http://www.example.com/customers/postname/
1. **Events :** For this post type you want to create a **permalink** which looks like this: http://www.example.com/events/year-month-date-postname/
1. **Press :** For this post type you want to create a **permalink** which looks like this: http://www.example.com/press/category/year/postname/
1. **News :** For this post type you want to create a **permalink** which looks like this: http://www.example.com/news/year/postname/
1. **Sponsors :** For this post type you want to create a **permalink** which looks like this: http://www.example.com/company/sponsor/post_title/

This plugin allows you to do this very easily. You just need to go on **Permalinks Customizer** Settings Page. Where text fields are shown with PostType name. You can define your permalinks you want to create for each post type.

If you leave any PostType field empty, **Permalinks Customizer** will create a permalink for that PostType by using the default **permalink** settings.

== How to Configure Permalinks Customizer ==
You can configure the plugin by navigating to the `Permalinks Customizer` menu from the WordPress Dashboard

== Structure Tags ==
You can find all the tags which are currently supported by the `Permalinks Customizer`.

=== Default Tags for PostTypes ===
Below mentioned tags are provided by the `WordPress`. These tags can be used on Default `WordPress Permalink` Settings Page as well as `Permalinks Customizer` Settings page.

1. `%year%`:  The year of the post, four digits, for example 2019
1. `%monthnum%`: Month of the year, for example 01
1. `%day%`: Day of the month, for example 02
1. `%hour%`: Hour of the day, for example 15
1. `%minute%`: Minute of the hour, for example 43
1. `%second%`: Second of the minute, for example 33
1. `%post_id%`: The unique ID of the post, for example 123
1. `%postname%`: A sanitized version of the title of the post (post slug field on Edit Post/Page panel). So "This Is A Great Post!" becomes this-is-a-great-post in the URI.
1. `%category%`: A sanitized version of the category name (category slug field on New/Edit Category panel). Nested sub-categories appear as nested directories in the URI.
1. `%author%`: A sanitized version of the author name.

=== Custom Tags for PostTypes ===
Below mentioned tags are provided by the `Permalinks Customizer` for PostTypes. These tags can not be used on the Default `WordPress Permalink` Settings Page.

1. `%title%`: Title of the post. let's say the title is "This Is A Great Post!" so, it becomes this-is-a-great-post in the URI.
1. `%parent_title%`: This tag is similar as `%title%`.<br>Only the difference is that it appends the immediate **Parent Page Title** in the URI if any parent page is selected.
1. `%all_parents_title%`: This tag is similar as `%title%`.<br>Only the difference is that it appends all the **Parents Page Title** in the URI if any parent page is selected.
1. `%parent_postname%`: This tag is similar as `%postname%`.<br>Only difference is that it appends the immediate **Parent Page Slug** if any parent page is selected.
1. `%all_parents_postname%`: This tag is similar as `%postname%`.<br>Only the difference is that it appends all the **Parents Page Slug** in the URI if any parent page is selected.
1. `%child-category%`: A sanitized version of the category name (category slug field on New/Edit Category panel).
1. `%product_cat%`: A sanitized version of the product category name (category slug field on New/Edit Category panel). Nested sub-categories appear as nested directories in the URI.<br><br> *This `tag` is specially used in WooCommerce Products.*
1. `<%ctax_custom_taxonomy%>`: A sanitized version of the custom taxonomy where the taxonomy name is `custom_taxonomy`. Replace the `custom_taxonomy` with your appropriate created taxonomy name.<br><br> If you want to provide the default slug which is used when the category/taxonomy doesn't be selected so, make sure to provide default name/slug which looks like this: `<%ctax_custom_taxonomy??sales%>`. Value which is written between the `??` and `%>` is used as default slug.
1. `<%ctaxparents_custom_taxonomy%>`: This tag is similar as `<%ctax_custom_taxonomy%>`.<br>Only the difference is that it appends all the **Parents Slug** in the URI if any parent Term/Category is selected.
1. `%author_firstname%`: A sanitized version of the author first name. If author first name is not available then it uses the author's username.
1. `%author_lastname%`: A sanitized version of the author last name. If author last name is not available then it uses the author's username.

**Note**: `%title%` is similar as `%postname%` tag but the difference is that `%postname%` can only be set once by WordPress whereas `%title%` can be changed by user at multiple times. let's say the title is "This Is A Great Post!" so, it becomes "this-is-a-great-post" in the URI(At the first time, `%postname%` and `%title%` works same) but if you edit and change title let's say "This Is A WordPress Post!" so, `%postname%` in the URI remains same "this-is-a-great-post" whereas `%title%` in the URI becomes "this-is-a-wordpress-post"

=== Custom Tags for Taxonomies ===
Below mentioned tags are provided by the `Permalinks Customizer` for Taxonomies. These can not be used on the Default `WordPress Permalink` Settings Page.

1. `%name%`: Name of the Term/Category. let's say the name is "External API" so, it becomes external-api in the URI.
1. `%term_id%`: The unique ID # of the Term/Category, for example 423
1. `%slug%`: A sanitized version of the name of the Term/Category. So "External API" becomes external-api in the URI.
1. `%parent_slug%`: This tag is similar as `%slug%`.<br><br> Only the difference is that it appends the immediate **Parent Term/Category Slug** in the URI if any parent Term/Category is selected.
1. `%all_parents_slug%`: This tag is similar as `%slug%`.<br><br> Only the difference is that it appends all the **Parent Terms/Category Slugs** in the URI if any parent Term/Category is selected.

== Filters ==
You can find all the filters below which are provided by `Permalinks Customizer` plugin. These filters can be used as per your Website requirement.

=== Exclude Permalinks ===

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

=== Show Relative Permalink/URL ===

To show relative permalink/url in Edit Post, add this filter in your themes `functions.php`.s
`
add_filter( 'permalinks_customizer_remove_home_url', '__return_true' );
`

=== Exclude PostType from the Plugin ===

To exclude the plugin to be worked on any PostType. Add this filter in your themes `functions.php`.
`
function yasglobal_exclude_post_types( $post_type ) {
  if ( $post_type == 'page' ) {
    return '__true';
  }
  return '__false';
}
add_filter( 'permalinks_customizer_exclude_post_type', 'yasglobal_exclude_post_types');
`
**Note**: Plugin stops working on the backend. *No more permalinks* would be generated by the plugin but the permalink which are already created will remains in work.

=== Disable automatically create redirects ===

To disable automatically create redirects feature on creating and updating the post/pages/categories, add this filter in your themes functions.php.
`
add_filter( 'permalinks_customizer_auto_created_redirects', '__return_false');
`
This filter stops to be creating new redirects but existed redirects keeps working. To stop existed redirects, add `permalinks_customizer_disable_redirects` filter.

=== Disable Redirects ===

To disable redirects to be applied , add this filter in your themes functions.php.
`
add_filter( 'permalinks_customizer_disable_redirects', '__return_false');
`
This filter only stop redirects to be work but the automatically create redirects still works. To stop automatically create redirects feature add `permalinks_customizer_auto_created_redirects` filter.

== Bug reports ==

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

* Permalinks can be set for each and every PostType from PostTypes Settings Page. The empty permalink field for the PostType will use the WordPress Permalink Settings.

* All the available tags which can be used for defining the permalinks for PostTypes are listed here. Some of the tags are defined here may only be used with this plugin only.

* Permalinks can easily be changed for the single post from its post edit page.

* Permalinks can be set for each and every Taxonomies from Taxonomies Settings Page. The empty permalink field for the taxonomy would not create the permalink for it.

* All the available tags which can be used for defining the permalinks for Taxonomies are listed here. These tags may work with this Plugin Only.

* Permalinks can easily be changed for the single Term from its Term edit page.

== Frequently Asked Questions ==

= Q. How to define Settings for the PostType? =
A. Navigate on `Permalinks Customizer` Menu from the `WordPress Dashboard`, Open PostTypes Settings Page, there is a textfield for each and every PostType (if the PostType is available for `Public`) . On this field, you can define structure which is used for that PostType.

= Q. Can i use tags in PostType Settings? =
A. Yes, you can use any tag which are defined in [Default Tags for PostTypes](https://github.com/yasglobal/permalinks-customizer#default-tags-for-posttypes) and [Custom Tags for PostTypes](https://github.com/yasglobal/permalinks-customizer#custom-tags-for-taxonomies).

= Q. Does the plugin supports custom taxonomy tag? =
A. Yes, it supports the custom taxonomy tag as defined [here](https://github.com/yasglobal/permalinks-customizer#custom-tags-for-posttypes).

= Q. Can i see the created permalinks for the PostType? =
A. Yes, you can see all the created permalinks on the PostType Permalinks Page under Permalinks Customizer.

= Q. How to define Settings for the Taxonomies? =
A. Navigate on `Permalinks Customizer` Menu from the `WordPress Dashboard`, Open Taxonomies Settings Page, there is a textfield for each and every Taxonomy (if the Taxonomy is available for `Public`) . On this field, you can define structure which is used for that Taxonomy.

= Q. Can i use tags in Taxonomies Settings? =
A. Yes, you can use any tag which are defined [here](https://github.com/yasglobal/permalinks-customizer#custom-tags-for-taxonomies).

= Q. Can i see the created permalinks for the Taxonomies? =
A. Yes, you can see all the created permalinks on the Taxonomies Permalinks Page under Permalinks Customizer.

= Q. Can i regenerate all the permalinks according to the defined structure? =
A. Yes, you can regenerate all the permalinks according to the defined structure. To have a bulk permalink update, Go to the *All Post* page there is a a option in the bulk action drop down with the name of `Regenerate Permalinks`. Use that option for regenerating the Permalinks.

= Q. Does *Regenerate Permalinks* damage my site SEO? =
A. No, it won't damage your site SEO. As regenerating permalinks added redirect against their previous permalink.

= Q. Can i see the available redirects? =
A. Yes, you can see the all the redirects created by this plugin from the Redirects Page under the Permalinks Customizer in the WordPress Dashboard.

= Q. Can i disable/delete redirects? =
A. Yes, you can disable/delete the redirects from the Redirects Page using Bulk Action.

= Q. Can i exclude PostType from the Plugin? =
A. Yes, you can exclude any posttype from the plugin to be worked on. For this just add the filter as shown in the Filters Section with the name of *Exclude PostType from the Plugin*.

== Changelog ==

= 2.8.2 - June 17, 2020 =

* Enhancement
  * Add custom tag for taxonomy that can be used in **Taxonomies Settings** page and can be manipulated from `functions.php` or plugin
* Bug
  * Fix warning of undefined variables on redirects page (when there is no redirect)

= 2.8.1 - June 17, 2020 =

* Bug
  * [Redirect sorting does not work](https://github.com/yasglobal/permalinks-customizer/issues/44)

= 2.8.0 - May 28, 2020 =

* Enhancement
  * Add custom tag for post type that can be used in **PostTypes Settings** page and can be manipulated from `functions.php` or plugin
* Bug
  * [Excessive redirects creation](https://github.com/yasglobal/permalinks-customizer/issues/32)
  * Fix double slash issue in canonical with WPML
  * Fix WPML language switcher incompatibility issue
  * Fix [Undefined constant](https://wordpress.org/support/topic/undefined-constant-8/)
  * Fix [Array and string offset access syntax with curly braces is deprecated](https://wordpress.org/support/topic/array-and-string-offset-access-syntax-with-curly-braces-is-deprecated/)

= Earlier versions =

* For the changelog of earlier versions, please refer to the separate changelog.txt file.
