<?php
/**
 * @package PermalinksCustomizer\Frontend\Form
 */

class Permalinks_Customizer_Form {

	/**
	 * Initialize WordPress Hooks
	 */
	public function init() {
		add_filter( 'get_sample_permalink_html',
			array( $this, 'permalinks_customizer_get_sample_permalink_html' ), 10, 4
		);

		add_action( 'save_post',
			array( $this, 'permalinks_customizer_customization' ), 10, 3
		);
		add_action( 'delete_post',
			array( $this, 'permalinks_customizer_delete_permalink' ), 10
		);

		add_action( 'created_term',
			array( $this, 'permalinks_customizer_create_term' ), 10, 3
		);
		add_action( 'edited_term',
			array( $this, 'permalinks_customizer_create_term' ), 10, 3
		);
		add_action( 'delete_term',
			array( $this, 'permalinks_customizer_delete_term' ), 10, 3
		);

		add_action( 'update_option_page_on_front',
			array( $this, 'permalinks_customizer_static_page' ), 10, 2
		);
		add_action( 'init', array( $this, 'register_taxonomies_form' ) );
	}

	/**
	 * Generate Form for editing the Permalinks for Post/Pages/Categories
	 */
	private function permalinks_customizer_get_form( $permalink, $original = "", $renderContainers = true, $postname = "" ) {
		$encoded_permalink = htmlspecialchars( urldecode( $permalink ) );
		echo '<input value="true" type="hidden" name="permalinks_customizer_edit" />
				<input value="false" type="hidden" name="permalinks_customizer_regenerate_permalink" id="permalinks_customizer_regenerate_permalink" />
				<input value="'. home_url() .'" type="hidden" name="permalinks_customizer_home_url" id="permalinks_customizer_home_url" />
				<input value="' . $encoded_permalink . '" type="hidden" name="permalinks_customizer" id="permalinks_customizer" />';

		if ( $renderContainers ) {
			echo '<table class="form-table" id="permalinks_customizer_form">
					<tr>
						<th scope="row">' . __( 'Permalink', 'permalinks-customizer' ) . '</th>
						<td>';
		}

		if ( '' == $permalink && defined( 'POLYLANG_VERSION' ) ) {
			require_once(
				PERMALINKS_CUSTOMIZER_PATH . 'frontend/class-permalinks-customizer-conflicts.php'
			);

			$permalinks_customizer_conflicts = new Permalinks_Customizer_Conflicts();
			$original = $permalinks_customizer_conflicts->permalinks_customizer_check_conflicts( $original );
		}

		$permalink_edit_value = htmlspecialchars( $permalink ? urldecode( $permalink ) : urldecode( $original ) );
		$original_permalink   = htmlspecialchars( urldecode( $original ) );

		$postname_html = '';
		if ( isset( $postname ) && $postname != "" ) {
			$postname_html = '<input type="hidden" id="new-post-slug" class="text" value="' . $postname . '" />';
		}

		$permalink_edit_field = home_url() . '/
									<span id="editable-post-name" title="Click to edit this part of the permalink">
										'. $postname_html .'
										<input type="text" id="permalinks-customizer-post-slug" class="text" value="' . $permalink_edit_value . '" style="width: 250px; color: #ddd;" onfocus="focusPermalinkField()" onblur="blurPermalinkField()" />
										<input type="hidden" value="' . $original_permalink . '" id="original_permalink" />
									</span>';
		echo apply_filters( 'edit_permalink_field', $permalink_edit_field );

		echo '<script type="text/javascript">
				var newPostSlug = document.getElementById("permalinks-customizer-post-slug"),
						originalPermalink = document.getElementById("original_permalink");
				function focusPermalinkField() {
					if (!newPostSlug) return;
					newPostSlug.style.color = "#000";
				}

				function blurPermalinkField() {
					if (!newPostSlug) return;
					document.getElementById("permalinks_customizer").value = newPostSlug.value;
					if ( newPostSlug.value == "" || newPostSlug.value == originalPermalink.value ) {
						newPostSlug.value = originalPermalink.value;
						newPostSlug.style.color = "#ddd";
					}
				}
				</script>';

		if ( $renderContainers ) {
			wp_enqueue_script( 'permalink-customizer-admin', plugins_url( '/js/script-form.min.js', __FILE__ ), array(), false, true );
			if ( isset( $permalink ) && ! empty( $permalink ) ) {
				echo '<span id="view-post-btn"><a href="/' . $permalink . '" class="button button-small" target="_blank">View</a></span><span id="regenerate_permalink"><a href="javascript:void(0);" class="button button-small">Regenerate Permalink</a></span>';
			} elseif ( isset( $original_permalink ) && ! empty( $original_permalink ) ) {
				echo '<span id="view-post-btn"><a href="/' . $original_permalink . '" class="button button-small" target="_blank">View</a></span><span id="regenerate_permalink"><a href="javascript:void(0);" class="button button-small">Regenerate Permalink</a></span>';
			}
			echo '</td></tr></table>';
		}
	}

	/**
	 * This is the Main Function which gets the Permalink Edit form for the user with validating the Post Types
	 */
	public function permalinks_customizer_get_sample_permalink_html( $html, $id, $new_title, $new_slug ) {
		$permalink = get_post_meta( $id, 'permalink_customizer', true );
		$post = get_post( $id );

		ob_start();
		$permalinks_customizer_frontend_object = new Permalinks_Customizer_Frontend;

		if ( "page" == $post->post_type ) {
			$original_permalink = $permalinks_customizer_frontend_object->permalinks_customizer_original_page_link( $id );
			$view_post = __( 'View Page', 'permalinks-customizer' );
		} else {
			$original_permalink = $permalinks_customizer_frontend_object->permalinks_customizer_original_post_link( $id );
			$view_post = __( 'View ' . ucfirst( $post->post_type ), 'permalinks-customizer' );
		}
		$this->permalinks_customizer_get_form( $permalink, $original_permalink, false, $post->post_name );

		$content = ob_get_contents();
		ob_end_clean();
		if ( $post->post_type == 'attachment' || $post->ID == get_option( 'page_on_front' ) ) {
			return $html;
		}

		if ( 'trash' != $post->post_status ) {
			wp_enqueue_script( 'permalink-customizer-admin',
				plugins_url( '/js/script-form.min.js', __FILE__ ), array(), false, true
			);
			if ( isset( $permalink ) && ! empty( $permalink ) ) {
				$content .= "<span id='view-post-btn'><a href='/$permalink' class='button button-small' target='_blank'>$view_post</a></span><span id='regenerate_permalink'><a href='javascript:void(0);' class='button button-small'>Regenerate Permalink</a></span>\n";
			} else {
				$content .= "<span id='view-post-btn'><a href='/$original_permalink' class='button button-small' target='_blank'>$view_post</a></span><span id='regenerate_permalink'><a href='javascript:void(0);' class='button button-small'>Regenerate Permalink</a></span>\n";
			}
		}
		if ( preg_match( "@view-post-btn.*?href='([^']+)'@s", $html, $matches ) ) {
			$permalink = $matches[1];
		} else {
			list( $permalink, $post_name ) = get_sample_permalink( $post->ID, $new_title, $new_slug );
			if ( false !== strpos( $permalink, '%postname%' )
				|| false !== strpos( $permalink, '%pagename%' ) ) {
				$permalink = str_replace( array( '%pagename%','%postname%' ), $post_name, $permalink );
			}
		}

		return '<strong>' . __( 'Permalink:', 'permalinks-customizer' ) . "</strong>\n" . $content;
	}

	/**
	 *
	 */
	public function permalinks_customizer_term_options( $object ) {
		$permalink = '';
		$original_permalink = '';
		if ( isset( $object ) && isset( $object->term_id ) ) {
			$permalinks_customizer_frontend_object = new Permalinks_Customizer_Frontend;
			$permalink = $permalinks_customizer_frontend_object->permalinks_customizer_permalink_for_term( $object->term_id );

			if ( $object->term_id ) {
				$original_permalink = $permalinks_customizer_frontend_object->permalinks_customizer_original_taxonomy_link( $object->term_id );
			}
		}

		$this->permalinks_customizer_get_form( $permalink, $original_permalink );

		wp_enqueue_script('jquery');
		?>
		<script type="text/javascript">
		jQuery(document).ready(function() {
			var button = jQuery('#permalinks_customizer_form').parent().find('.submit');
			button.remove().insertAfter(jQuery('#permalinks_customizer_form'));
		});
		</script>
	 <?php
	}

	/**
	 * This Function call when the Post/Page has been Saved
	 */
	public function permalinks_customizer_customization( $post_id, $post, $update ) {

		if ( ! isset( $_REQUEST['permalinks_customizer_edit'] )
			|| $_REQUEST['permalinks_customizer_edit'] != true ) {
			return;
		}

		if ( $post_id == get_option( 'page_on_front' ) ) {
			$this->permalinks_customizer_delete_permalink( $post_id );
			return;
		}

		$post_status = $post->post_status;
		if ( $post_status == 'inherit' ) {
			$post_id = $post->post_parent;
			$post    = '';
			$post    = get_post( $post_id );
		}

		$url              = get_post_meta( $post_id, 'permalink_customizer', true );
		$permalink_status = get_post_meta( $post_id, 'permalink_customizer_regenerate_status', true );

		if ( ( empty( $url ) && $post_status != 'trash' )
			|| ( !empty( $url ) && $url == $_REQUEST['permalinks_customizer']
				&& isset( $permalink_status ) && $permalink_status != ''
				&& $permalink_status != 1 && $post_status != 'trash' )
				|| ( isset( $_REQUEST['permalinks_customizer_regenerate_permalink'] )
				&& $_REQUEST['permalinks_customizer_regenerate_permalink'] === "true"
				&& $post_status != 'trash' ) ) {

			$get_permalink = esc_attr(
				get_option( 'permalinks_customizer_' . $post->post_type )
			);
			if ( empty( $get_permalink ) ) {
				$get_permalink = esc_attr( get_option('permalink_structure' ) );
			}
			$set_permalink = $this->permalinks_customizer_replace_tags(
				$post_id, $post, $get_permalink
			);

			global $wpdb;
			$trailing_slash = substr( $set_permalink, -1 );
			if ( '/' == $trailing_slash ) {
				$set_permalink = rtrim( $set_permalink, '/' );
			}
			$permalink = $set_permalink;
			$qry       = "SELECT * FROM $wpdb->postmeta WHERE meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "' AND post_id != " . $post_id . " OR meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "/' AND post_id != " . $post_id . " LIMIT 1";
			$check_exist_url = $wpdb->get_results( $qry );
			if ( ! empty( $check_exist_url ) ) {
				$i = 2;
				while (1) {
					$permalink       = $set_permalink . '-' . $i;
					$qry             = "SELECT * FROM $wpdb->postmeta WHERE meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "' AND post_id != " . $post_id . " OR meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "/' AND post_id != " . $post_id . " LIMIT 1";
					$check_exist_url = $wpdb->get_results( $qry );
					if ( empty( $check_exist_url ) ) break;
					$i++;
				}
			}

			if ( '/' == $trailing_slash )
				$permalink = $permalink . '/';

			if ( 0 === strpos( $permalink, '/' ) )
				$permalink = substr( $permalink, 1 );

			$permalink = preg_replace( "/(\/+)/", "/", $permalink );
			$permalink = preg_replace( "/(\-+)/", "-", $permalink );
			update_post_meta( $post_id, 'permalink_customizer', $permalink );
			if ( 'publish' == $post_status ) {
				// permalink_customizer_regenerate_status = 1 means Permalink won't be generated again on updating the post
				update_post_meta( $post_id, 'permalink_customizer_regenerate_status', 1 );
			} else {
				// permalink_customizer_regenerate_status = 0 means Permalink will be generated again on updating the post
				update_post_meta( $post_id, 'permalink_customizer_regenerate_status', 0 );
			}
		} elseif ( isset( $_REQUEST['permalinks_customizer'] )
			&& ! empty( $_REQUEST['permalinks_customizer'] )
			&& $url != $_REQUEST['permalinks_customizer'] ) {

			$permalink = $_REQUEST['permalinks_customizer'];
			$permalink = preg_replace( "/(\/+)/", "/", $permalink );
			$permalink = preg_replace( "/(\-+)/", "-", $permalink );
			update_post_meta( $post_id, 'permalink_customizer', $permalink );
			// permalink_customizer_regenerate_status = 1 means Permalink won't be generated again on updating the post (Once, user changed it)
			update_post_meta( $post_id, 'permalink_customizer_regenerate_status', 1 );
		}
	}

	/**
	 * Replace the tags with the respective value on generating the Permalink for the Post types
	 */
	private function permalinks_customizer_replace_tags( $post_id, $post, $replace_tag ) {

		$date = new DateTime( $post->post_date );

		// Replace %title% with the respective Sanitize Value of the Title
		if ( false !== strpos( $replace_tag, "%title%" ) ) {
			$title       = sanitize_title( $post->post_title );
			$replace_tag = str_replace( '%title%', $title, $replace_tag );
		}

		// Replace %year% with the respective post publish date year
		if ( false !== strpos( $replace_tag, "%year%" ) ) {
			$year        = $date->format( 'Y' );
			$replace_tag = str_replace( '%year%', $year, $replace_tag );
		}

		// Replace %monthnum% with the respective post publish date month number
		if ( false !== strpos( $replace_tag, "%monthnum%" ) ) {
			$month       = $date->format( 'm' );
			$replace_tag = str_replace( '%monthnum%', $month, $replace_tag );
		}

		// Replace %day% with the respective post publish date day
		if ( false !== strpos( $replace_tag, "%day%" ) ) {
			$day         = $date->format( 'd' );
			$replace_tag = str_replace( '%day%', $day, $replace_tag );
		}

		// Replace %hour% with the respective post publish date hour
		if ( false !== strpos( $replace_tag, "%hour%" ) ) {
			$hour        = $date->format( 'H' );
			$replace_tag = str_replace( '%hour%', $hour, $replace_tag );
		}

		// Replace %minute% with the respective post publish date minute
		if ( false !== strpos( $replace_tag, "%minute%" ) ) {
			$minute      = $date->format( 'i' );
			$replace_tag = str_replace( '%minute%', $minute, $replace_tag );
		}

		// Replace %second% with the respective post publish date second
		if ( false !== strpos( $replace_tag, "%second%" ) ) {
			$second      = $date->format( 's' );
			$replace_tag = str_replace( '%second%', $second, $replace_tag );
		}

		// Replace %post_id% with the respective post id
		if ( false !== strpos( $replace_tag, "%post_id%" ) ) {
			$replace_tag = str_replace( '%post_id%', $post_id, $replace_tag );
		}

		// Replace %postname% with the respective post name
		if ( false !== strpos( $replace_tag, "%postname%" ) ) {
			if ( ! empty( $post->post_name ) ) {
				$replace_tag = str_replace( '%postname%', $post->post_name, $replace_tag );
			} else {
				$title       = sanitize_title( $post->post_title );
				$replace_tag = str_replace( '%postname%', $title, $replace_tag );
				if ( ! empty( $title ) ) {
					wp_update_post( array(
						'ID'        => $post_id,
						'post_name' => $title
					));
				}
			}
		}

		// Replace %parent_postname% with the respective post name with the parent post name if parent post is selected
		if ( false !== strpos( $replace_tag, "%parent_postname%" ) ) {
			$parents   = get_ancestors( $post_id, $post->post_type, 'post_type' );
			$postnames = '';
			if ( $parents && is_array( $parents ) && ! empty( $parents )
				&& count( $parents ) >= 1 ) {
				$parent    = get_post( $parents[0] );
				$postnames = $parent->post_name . '/';
			}

			if ( ! empty( $post->post_name ) ) {
				$postnames .= $post->post_name;
			} else {
				$title      = sanitize_title( $post->post_title );
				$postnames .=  $title;
				if ( ! empty( $title ) ) {
					wp_update_post( array(
						'ID'        => $post_id,
						'post_name' => $title
					));
				}
			}

			$replace_tag = str_replace( '%parent_postname%', $postnames, $replace_tag );
		}

		// Replace %all_parents_postname% with the respective post name with the parents post name if parent post is selected
		if ( false !== strpos( $replace_tag, "%all_parents_postname%" ) ) {
			$parents   = get_ancestors( $post_id, $post->post_type, 'post_type' );
			$postnames = '';
			if ( $parents && is_array( $parents ) && ! empty( $parents )
				&& count( $parents ) >= 1 ) {
				$i = count( $parents ) - 1;
				for ( $i; $i >= 0; $i-- ) {
					$parent     = get_post( $parents[$i] );
					$postnames .= $parent->post_name . '/';
				}
			}

			if ( !empty( $post->post_name ) ) {
				$postnames .= $post->post_name;
			} else {
				$title      = sanitize_title( $post->post_title );
				$postnames .=  $title;
				if ( ! empty( $title ) ) {
					wp_update_post( array(
						'ID'        => $post_id,
						'post_name' => $title
					));
				}
			}

			$replace_tag = str_replace( '%all_parents_postname%', $postnames, $replace_tag );
		}

		// Replace %category% with the respective post category with their parent categories
		if (strpos( $replace_tag, "%category%" ) !== false ) {
			$categories = get_the_category( $post_id );
			$total_cat  = count( $categories );
			$tid = 1;
			if ( $total_cat > 0 && is_array( $categories ) ) {
				$tid = '';
				foreach ( $categories as $cat ) {
					if ( $cat->term_id < $tid || empty( $tid ) ) {
						$tid = $cat->term_id;
						$pid = '';
						if ( ! empty( $cat->parent ) ) {
							$pid = $cat->parent;
						}
					}
				}
			}
			$term_category = get_term( $tid );
			$category      = is_object( $term_category ) ? $term_category->slug : '';
			if ( ! empty( $pid ) ) {
				$parent_category = get_term( $pid );
				$category        = is_object( $parent_category ) ? $parent_category->slug . '/' . $category : '';
			}
			$replace_tag = str_replace( '%category%', $category, $replace_tag );
		}

		// Replace %child-category% with the respective post category
		if ( false !== strpos( $replace_tag, "%child-category%" ) ) {
			$categories = get_the_category( $post_id );
			$total_cat  = count( $categories );
			$tid        = 1;
			if ( $total_cat > 0 && is_array( $categories ) ) {
				$tid = '';
				foreach( $categories as $cat ) {
					if ( $cat->term_id < $tid || empty( $tid ) ) {
						$tid = $cat->term_id;
					}
				}
			}
			$term_category = get_term( $tid );
			$category      = is_object( $term_category ) ? $term_category->slug : '';
			$replace_tag   = str_replace( '%child-category%', $category, $replace_tag );
		}

		// Replace %product_cat% with the respective post (Product Category). Used with WooCommerce
		if ( false !== strpos( $replace_tag, "%product_cat%" ) ) {
			$categories = get_the_terms( $post_id, 'product_cat' );
			$total_cat  = count( $categories );
			$tid = 1;
			if ( $total_cat > 0 && is_array( $categories ) ) {
				$tid = '';
				foreach ( $categories as $cat ) {
					if ( $cat->term_id < $tid || empty( $tid ) ) {
						$tid = $cat->term_id;
						$pid = '';
						if ( ! empty( $cat->parent ) ) {
							$pid = $cat->parent;
						}
					}
				}
			}
			$term_category = get_term( $tid );
			$category      = is_object( $term_category ) ? $term_category->slug : '';
			if ( ! empty( $pid ) ) {
				$parent_category = get_term( $pid );
				$category        = is_object( $parent_category ) ? $parent_category->slug . '/' . $category : $category;
			}
			$replace_tag = str_replace( '%product_cat%', $category, $replace_tag );
		}

		// Replace %author% with the author of the respective post
		if ( false !== strpos( $replace_tag, "%author%" ) ) {
			$author      = get_the_author_meta( 'user_login', $post->post_author );
			$replace_tag = str_replace( '%author%', $author, $replace_tag );
		}

		// Replace %author_firstname% with the first name of author of the respective post
		if ( false !== strpos( $replace_tag, "%author_firstname%" ) ) {
			$author_firstname = get_the_author_meta( 'first_name', $post->post_author );
			if ( $author_firstname && ! empty( $author_firstname ) ) {
				$author_firstname = strtolower( $author_firstname );
				$author_firstname = preg_replace( "/[\s]/", "-", $author_firstname );
				$replace_tag      = str_replace( '%author_firstname%', $author_firstname, $replace_tag );
			} else {
				$author      = get_the_author_meta( 'user_login', $post->post_author );
				$replace_tag = str_replace( '%author_firstname%', $author, $replace_tag );
			}
		}

		// Replace %author_lastname% with the lastname of author of the respective post
		if ( false !== strpos( $replace_tag, "%author_lastname%" ) ) {
			$author_lastname = get_the_author_meta( 'last_name', $post->post_author );
			if ( $author_lastname && ! empty( $author_lastname ) ) {
				$author_lastname = strtolower( $author_lastname );
				$author_lastname = preg_replace( "/[\s]/", "-", $author_lastname );
				$replace_tag     = str_replace( '%author_lastname%', $author_lastname, $replace_tag );
			} else {
				$author      = get_the_author_meta( 'user_login', $post->post_author );
				$replace_tag = str_replace( '%author_lastname%', $author, $replace_tag );
			}
		}

		return $replace_tag;
	}

	/**
	 * Delete Permalink when the Post is deleted or when the saving Post is selected as Front Page
	 */
	public function permalinks_customizer_delete_permalink( $id ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE meta_key = 'permalink_customizer' AND post_id = %d", $id ) );
	}

	/**
	 * Check and Call the Function which saves the Permalink for Taxonomy
	 */
	public function permalinks_customizer_create_term( $id ) {
		$new_permalink = ltrim( stripcslashes( $_REQUEST['permalinks_customizer'] ), "/" );

		$term = get_term( $id );
		if ( empty( $new_permalink )
			|| ( isset( $_REQUEST['permalinks_customizer_regenerate_permalink'] )
			&& "true" === $_REQUEST['permalinks_customizer_regenerate_permalink'] ) ) {
			$permalinks_customizer_settings = unserialize( get_option( 'permalinks_customizer_taxonomy_settings' ) );
			if ( isset( $permalinks_customizer_settings[$term->taxonomy . '_settings'] )
				&& isset( $permalinks_customizer_settings[$term->taxonomy . '_settings']['structure'] )
				&& ! empty( $permalinks_customizer_settings[$term->taxonomy . '_settings']['structure'] ) ) {
				$new_permalink = $this->permalinks_customizer_replace_taxes_tags( $term, $permalinks_customizer_settings[$term->taxonomy . '_settings']['structure'] );
			}
		}

		if ( empty( $new_permalink ) || $new_permalink == '' ) {
			return;
		}

		$permalinks_customizer_frontend_object = new Permalinks_Customizer_Frontend;
		$old_permalink = $permalinks_customizer_frontend_object->permalinks_customizer_original_taxonomy_link( $id );

		if ( $new_permalink == $old_permalink ) {
			return;
		}

		$this->permalinks_customizer_save_term( $term, str_replace( '%2F', '/', urlencode( $new_permalink ) ) );
	}

	/**
	 * Replace the tags with the respective value on generating the Permalink for the Taxonmoies
	 */
	private function permalinks_customizer_replace_taxes_tags( $term, $replace_tag ) {

		if ( false !== strpos( $replace_tag, "%name%" ) ) {
			$name        = sanitize_title( $term->name );
			$replace_tag = str_replace( '%name%', $name, $replace_tag );
		}

		if ( false !== strpos( $replace_tag, "%term_id%" ) ) {
			$replace_tag = str_replace( '%term_id%', $term->term_id, $replace_tag );
		}

		if ( false !== strpos( $replace_tag, "%slug%" ) ) {
			if ( ! empty( $term->slug ) ) {
				 $replace_tag = str_replace( '%slug%', $term->slug, $replace_tag );
			} else {
				 $name        = sanitize_title( $term->name );
				 $replace_tag = str_replace( '%slug%', $name, $replace_tag );
			}
		}

		if ( false !== strpos( $replace_tag, "%parent_slug%" ) ) {
			$parents    = get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' );
			$term_names = '';
			if ( $parents && ! empty( $parents ) && count( $parents ) >= 1 ) {
				$parent     = get_term( $parents[0] );
				$term_names = $parent->slug . '/';
			}

			if ( ! empty( $term->slug ) ) {
				 $term_names .= $term->slug;
			} else {
				 $title       = sanitize_title( $term->name );
				 $term_names .=  $title;
			}

			$replace_tag = str_replace( '%parent_slug%', $term_names, $replace_tag );
		}

		if ( false !== strpos( $replace_tag, "%all_parents_slug%" ) ) {
			$parents    = get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' );
			$term_names = '';
			if ( $parents && ! empty( $parents ) && count( $parents ) >= 1 ) {
				$i = count( $parents ) - 1;
				for ( $i; $i >= 0; $i-- ) {
					$parent      = get_term( $parents[$i] );
					$term_names .= $parent->slug . '/';
				}
			}

			if ( ! empty( $term->slug ) ) {
				 $term_names .= $term->slug;
			} else {
				 $title       = sanitize_title( $term->name );
				 $term_names .=  $title;
			}

			$replace_tag = str_replace( '%all_parents_slug%', $term_names, $replace_tag );
		}

		return $replace_tag;
	}

	/**
	 * Save Permalink for the Term
	 */
	private function permalinks_customizer_save_term( $term, $permalink ) {
		$url = get_term_meta( $term->term_id, 'permalink_customizer' );
		if ( empty( $url ) ) {
			global $wpdb;
			$trailing_slash = substr( $permalink, -1 );
			if ( '/' == $trailing_slash ) {
				$permalink = rtrim( $permalink, '/' );
			}
			$set_permalink = $permalink;
			$qry = "SELECT * FROM $wpdb->termmeta WHERE meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "' AND term_id != " . $term->term_id . " OR meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "/' AND term_id != " . $term->term_id . " LIMIT 1";
			$check_exist_url = $wpdb->get_results( $qry );
			if ( ! empty( $check_exist_url ) ) {
				$i = 2;
				while (1) {
					$permalink = $set_permalink . '-' . $i;
					$qry = "SELECT * FROM $wpdb->termmeta WHERE meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "' AND term_id != " . $term->term_id . " OR meta_key = 'permalink_customizer' AND meta_value = '" . $permalink . "/' AND term_id != " . $term->term_id . " LIMIT 1";
					$check_exist_url = $wpdb->get_results( $qry );
					if ( empty( $check_exist_url ) ) break;
					$i++;
				}
			}

			if ( $trailing_slash == '/' ) {
				$permalink = $permalink . '/';
			}

			if ( strpos( $permalink, "/" ) === 0 ) {
				$permalink = substr( $permalink, 1 );
			}
		}
		update_term_meta( $term->term_id, 'permalink_customizer', $permalink );
	}

	/**
	 * Delete Permalink when the Term is deleted
	 */
	public function permalinks_customizer_delete_term( $id ) {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->termmeta WHERE meta_key = 'permalink_customizer' AND term_id = %d", $id ) );

		$table = get_option( 'permalinks_customizer_table' );
		if ( $table ) {
			foreach ( $table as $link => $info ) {
				if ( $info['id'] == $id ) {
					unset( $table[$link] );
					break;
				}
			}
		}

		update_option( 'permalinks_customizer_table', $table );
	}

	/**
	 * This Function Just deletes the Permalink for the Page selected as the Front Page
	 */
	public function permalinks_customizer_static_page( $prev_front_page_id, $new_front_page_id ) {
		$this->permalinks_customizer_delete_permalink( $new_front_page_id );
	}

	/**
	 * Register Taxonomy to show Permalink Add/Edit Form
	 */
	public function register_taxonomies_form() {
		$taxonomies = get_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			if ( $taxonomy == 'nav_menu' ) {
				continue;
			}
			add_action( $taxonomy . '_add_form', array( $this, 'permalinks_customizer_term_options' ) );
			add_action( $taxonomy . '_edit_form', array( $this, 'permalinks_customizer_term_options' ) );
    	}
	}
}
