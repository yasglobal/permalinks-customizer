<?php
/**
 * @package PermalinksCustomizer\Frontend
 */

class Permalinks_Customizer_Frontend {

	/**
	 * Initialize WordPress Hooks
	 */
	public function init() {
		add_filter( 'request',
			array( $this, 'permalinks_customizer_request' ), 10, 1
		);

		add_action( 'template_redirect',
			array( $this, 'permalinks_customizer_redirect' ), 5
		);

		add_filter( 'post_link',
			array( $this, 'permalinks_customizer_post_link' ), 10, 2
		);
		add_filter( 'post_type_link',
			array( $this, 'permalinks_customizer_post_link' ), 10, 2
		);
		add_filter( 'page_link',
			array( $this, 'permalinks_customizer_page_link' ), 10, 2
		);

		add_filter( 'term_link',
			array( $this, 'permalinks_customizer_term_link' ), 10, 3
		);

		add_filter( 'user_trailingslashit',
			array( $this, 'permalinks_customizer_trailingslash' ), 10, 2
		);
	}

	/**
	 * Filter to rewrite the query if we have a matching post
	 */
	public function permalinks_customizer_request( $query ) {
		global $wpdb;
		global $_CPRegisteredURL;
		$original_url = NULL;
		$url          = parse_url( get_bloginfo( 'url' ) );
		$url          = isset( $url['path'])  ? $url['path'] : '';
		$request      = ltrim( substr( $_SERVER['REQUEST_URI'], strlen( $url ) ), '/' );
		$request      = ( ( $pos = strpos( $request, '?' ) ) ? substr( $request, 0, $pos ) : $request );

		if ( ! $request ) {
			return $query;
		}

		$exclude_url = apply_filters(
			'permalinks_customizer_exclude_request', $request
		);

		if ( '__true' === $exclude_url ) {
			return $query;
		}

		if ( defined( 'POLYLANG_VERSION' ) ) {
			require_once(
				PERMALINKS_CUSTOMIZER_PATH . 'frontend/class-permalinks-customizer-conflicts.php'
			);

			$permalinks_customizer_conflicts = new Permalinks_Customizer_Conflicts();
			$request = $permalinks_customizer_conflicts->permalinks_customizer_check_conflicts( $request );
		}

		$request_noslash = preg_replace( '@/+@', '/', trim( $request, '/' ) );

		$sql = $wpdb->prepare( "SELECT $wpdb->posts.ID, $wpdb->postmeta.meta_value, $wpdb->posts.post_type, $wpdb->posts.post_status FROM $wpdb->posts " .
				" LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) WHERE " .
				" meta_key = 'permalink_customizer' AND meta_value != '' AND " .
				" ( LOWER(meta_value) = LEFT(LOWER('%s'), LENGTH(meta_value)) OR " .
				"   LOWER(meta_value) = LEFT(LOWER('%s'), LENGTH(meta_value)) ) " .
				"  AND post_status != 'trash' AND post_type != 'nav_menu_item'" .
				" ORDER BY LENGTH(meta_value) DESC, " .
				" FIELD(post_status,'publish','private','draft','auto-draft','inherit')," .
				" FIELD(post_type,'post','page'), $wpdb->posts.ID ASC LIMIT 1",
				$request_noslash, $request_noslash . "/" );

		$posts = $wpdb->get_results( $sql );

		if ( $posts ) {
			if ( $request_noslash == trim( $posts[0]->meta_value, '/' ) ) {
				$_CPRegisteredURL = $request;
			}

			if ( $posts[0]->post_status == 'draft' ) {
				if ( $posts[0]->post_type == 'page' ) {
					$original_url = "?page_id=" . $posts[0]->ID;
				} elseif ( 'post' == $posts[0]->post_type ) {
					$original_url = "?p=" . $posts[0]->ID;
				} else {
          			$original_url = "?post_type=" . $posts[0]->post_type . "&p=" . $posts[0]->ID;
        		}
			} else {
				$original_url = preg_replace( '@/+@', '/', str_replace( trim( strtolower( $posts[0]->meta_value ), '/' ),
									( $posts[0]->post_type == 'page' ? $this->permalinks_customizer_original_page_link( $posts[0]->ID ) : $this->permalinks_customizer_original_post_link( $posts[0]->ID ) ), strtolower( $request_noslash ) ) );
			}
		}
		if ( NULL === $original_url ) {
			$sql = $wpdb->prepare( "SELECT * FROM $wpdb->termmeta WHERE " .
					" meta_key = 'permalink_customizer' AND meta_value != '' AND " .
					" ( LOWER(meta_value) = LEFT(LOWER('%s'), LENGTH(meta_value)) OR " .
					" LOWER(meta_value) = LEFT(LOWER('%s'), LENGTH(meta_value)) ) " .
					" ORDER BY LENGTH(meta_value) DESC, $wpdb->termmeta.term_id ASC LIMIT 1",
					$request_noslash, $request_noslash . "/" );

			$taxonomy_term_data = $wpdb->get_results( $sql );

			$get_term_permalink = NULL;
			if ( is_array( $taxonomy_term_data ) && isset( $taxonomy_term_data[0]->term_id ) ) {
				$get_term_permalink = $this->permalinks_customizer_original_taxonomy_link( $taxonomy_term_data[0]->term_id );
				if ( isset( $get_term_permalink ) && '' != $get_term_permalink ) {
					if ( $request_noslash == trim( $taxonomy_term_data[0]->meta_value, '/' ) ) {
						$_CPRegisteredURL = $request;
					}

					$original_url = str_replace(
						trim( strtolower( $taxonomy_term_data[0]->meta_value ), '/' ),
						$get_term_permalink, trim( strtolower( $request ) )
					);
				}
			}

			if ( NULL === $original_url && NULL === $get_term_permalink ) {
				$table = get_option( 'permalinks_customizer_table' );
				if ( ! $table ) {
					return $query;
				}

				foreach ( array_keys( $table ) as $permalink ) {
					if ( $permalink == substr( $request_noslash, 0, strlen( $permalink ) )
						|| $permalink == substr( $request_noslash . '/', 0, strlen( $permalink ) ) ) {
						$term = $table[$permalink];
						if ( $request_noslash == trim( $permalink, '/' ) ) {
							$_CPRegisteredURL = $request;
						}

						$original_url = str_replace(
							trim( $permalink, '/' ),
							$this->permalinks_customizer_original_taxonomy_link( $term['id'] ),
							trim( $request, '/' )
						);
					}
				}
			}
		}

		if ( $original_url !== null ) {
			$original_url = str_replace( '//', '/', $original_url );

			if ( ( $pos = strpos( $_SERVER['REQUEST_URI'], '?' ) ) !== false ) {
				$queryVars     = substr( $_SERVER['REQUEST_URI'], $pos + 1 );
				$original_url .= ( strpos( $original_url, '?' ) === false ? '?' : '&' ) . $queryVars;
			}
			$oldRequestUri           = $_SERVER['REQUEST_URI'];
			$oldQueryString          = $_SERVER['QUERY_STRING'];
			$_SERVER['REQUEST_URI']  = '/' . ltrim( $original_url, '/' );
			$pos = strpos( $original_url, '?' );
			$_SERVER['QUERY_STRING'] = ( ( $pos ) !== false ? substr( $original_url, $pos + 1 ) : '' );
			parse_str( $_SERVER['QUERY_STRING'], $queryArray );
			$oldValues = array();
			if ( is_array( $queryArray ) ) {
				foreach ( $queryArray as $key => $value ) {
					$oldValues[$key] = $_REQUEST[$key];
					$_REQUEST[$key]  = $_GET[$key] = $value;
				}
			}
			remove_filter( 'request',
				array( $this, 'permalinks_customizer_request' ), 10, 1
			);
			global $wp;
			$wp->parse_request();
			$query = $wp->query_vars;
			add_filter(
				'request', array( $this, 'permalinks_customizer_request' ), 10, 1
			);
			$_SERVER['REQUEST_URI']  = $oldRequestUri;
			$_SERVER['QUERY_STRING'] = $oldQueryString;
			foreach ( $oldValues as $key => $value ) {
				$_REQUEST[$key] = $value;
			}
		}

		return $query;
	}

	/**
	 * Action to redirect to the custom permalink
	 */
	public function permalinks_customizer_redirect() {
		$url     = parse_url( get_bloginfo( 'url' ) );
		$url     = isset( $url['path'] ) ? $url['path'] : '';
		$request = ltrim( substr( $_SERVER['REQUEST_URI'], strlen( $url ) ), '/' );

		if ( ( $pos= strpos( $request, "?" ) ) ) {
			$request = substr( $request, 0, $pos );
		}

		if ( defined( 'POLYLANG_VERSION' ) ) {
			require_once(
				PERMALINKS_CUSTOMIZER_PATH . 'frontend/class-permalinks-customizer-conflicts.php'
			);

			$permalinks_customizer_conflicts = new Permalinks_Customizer_Conflicts();
			$request = $permalinks_customizer_conflicts->permalinks_customizer_check_conflicts( $request );
		}
		global $wp_query;

		$permalinks_customizer = '';
		$original_permalink    = '';

		if ( is_single() || is_page() ) {
			$post = $wp_query->post;
			$permalinks_customizer = get_post_meta( $post->ID, 'permalink_customizer', true );
			$original_permalink    = ( $post->post_type == 'page' ? $this->permalinks_customizer_original_page_link( $post->ID ) : $this->permalinks_customizer_original_post_link( $post->ID ) );
		} else if ( is_tag() || is_category() || is_tax() ) {
			$theTerm                = $wp_query->get_queried_object();
			$permalinks_customizer  = $this->permalinks_customizer_permalink_for_term( $theTerm->term_id );
			$original_permalink     = $this->permalinks_customizer_original_taxonomy_link( $theTerm->term_id );
		}
		if ( $permalinks_customizer
			&& ( substr( $request, 0, strlen( $permalinks_customizer ) ) != $permalinks_customizer
			|| $request == $permalinks_customizer . "/" ) ) {
			$url = $permalinks_customizer;

			if ( substr( $request, 0, strlen( $original_permalink ) ) == $original_permalink
				&& trim( $request, '/' ) != trim( $original_permalink, '/' ) ) {
				$url = preg_replace( '@//*@', '/', str_replace( trim( $original_permalink, '/' ), trim( $permalinks_customizer, '/' ), $request ) );
				$url = preg_replace( '@([^?]*)&@', '\1?', $url );
			}

			$url .= strstr( $_SERVER['REQUEST_URI'], "?" );

			wp_redirect( home_url() . "/" . $url, 301 );
			exit();
		}
	}

	/**
	 * Filter to replace the post permalink with the custom one
	 */
	public function permalinks_customizer_post_link( $permalink, $post ) {
		$permalinks_customizer = get_post_meta( $post->ID, 'permalink_customizer', true );
		if ( $permalinks_customizer ) {
			$post_type = isset( $post->post_type ) ? $post->post_type : 'post';
			$language_code = apply_filters(
				'wpml_element_language_code', null,
				array( 'element_id' => $post->ID, 'element_type' => $post_type )
			);
			if ( $language_code )
				return apply_filters(
					'wpml_permalink', home_url() . "/" . $permalinks_customizer, $language_code
				);
			else
				return apply_filters(
					'wpml_permalink', home_url() . "/" . $permalinks_customizer
				);
		}
		return $permalink;
	}

	/**
	 * Filter to replace the page permalink with the custom one
	 */
	public function permalinks_customizer_page_link( $permalink, $page ) {
		$permalinks_customizer = get_post_meta( $page, 'permalink_customizer', true );
		if ( $permalinks_customizer ) {
			$language_code = apply_filters(
				'wpml_element_language_code', null,
				array( 'element_id' => $page, 'element_type' => 'page' )
			);
			if ( $language_code )
				return apply_filters(
					'wpml_permalink', home_url() . "/" . $permalinks_customizer, $language_code
				);
			else
				return apply_filters(
					'wpml_permalink', home_url() . "/" . $permalinks_customizer
				);
		}
		return $permalink;
	}

	/**
	 * Filter to replace the term permalink with the custom one
	 */
	public function permalinks_customizer_term_link( $permalink, $term ) {
		if ( is_object( $term ) ) {
			$term = $term->term_id;
		}
		$permalinks_customizer = $this->permalinks_customizer_permalink_for_term( $term );

		if ( $permalinks_customizer ) {
			$taxonomy = get_term( $term );
			if ( isset( $taxonomy ) && isset( $taxonomy->term_taxonomy_id ) ) {
				$term_type = isset( $taxonomy->taxonomy ) ? $taxonomy->taxonomy : 'category';
				$language_code = apply_filters(
					'wpml_element_language_code', null,
					array( 'element_id' => $taxonomy->term_taxonomy_id, 'element_type' => $term_type )
				);
				return apply_filters(
					'wpml_permalink', home_url() . "/" . $permalinks_customizer, $language_code
				);
			} else {
				return apply_filters(
					'wpml_permalink', home_url() . "/" . $permalinks_customizer
				);
			}
		}
		return $permalink;
	}

	/**
	 * Find the Permalink for the provided term id
	 */
	public function permalinks_customizer_permalink_for_term( $id ) {

		$term_link = get_term_meta( $id, 'permalink_customizer', true );

		if ( $term_link ) {
			return $term_link;
		}

		$table = get_option( 'permalinks_customizer_table' );
		if ( $table ) {
			foreach ( $table as $link => $info ) {
				if ( $info['id'] == $id ) {
					return $link;
				}
			}
		}
		return false;
	}

	/**
	 * Remove the post_link and post_type_link Filter for getting the original Permalink of the Posts and set it back.
	 */
	public function permalinks_customizer_original_post_link( $post_id ) {
		remove_filter( 'post_link',
			array( $this, 'permalinks_customizer_post_link' ), 10, 2
		);
		remove_filter( 'post_type_link',
			array( $this, 'permalinks_customizer_post_link' ), 10, 2
		);
		$original_permalink = ltrim(
			str_replace( home_url(), '', get_permalink( $post_id ) ), '/'
		);
		add_filter( 'post_link',
			array( $this, 'permalinks_customizer_post_link' ), 10, 2
		);
		add_filter( 'post_type_link',
			array( $this, 'permalinks_customizer_post_link' ), 10, 2
		);
		return $original_permalink;
	}

	/**
	 * Remove the page_link Filter for getting the original Permalink of the Page and set it back.
	 */
	public function permalinks_customizer_original_page_link( $post_id ) {
		remove_filter( 'page_link',
			array( $this, 'permalinks_customizer_page_link' ), 10, 2
		);
		$original_permalink = ltrim(
			str_replace( home_url(), '', get_permalink( $post_id ) ), '/'
		);
		add_filter( 'page_link',
			array( $this, 'permalinks_customizer_page_link' ), 10, 2
		);
		return $original_permalink;
	}

	/**
	 * Remove the term_link and user_trailingslashit Filter for getting the original Permalink of the Term and set it back.
	 */
	public function permalinks_customizer_original_taxonomy_link( $term_id ) {

		remove_filter( 'term_link',
			array( $this, 'permalinks_customizer_term_link' ), 10, 2
		);
		remove_filter( 'user_trailingslashit',
			array( $this, 'permalinks_customizer_trailingslash' ), 10, 2
		);

		$term      = get_term( $term_id );
		$term_link = get_term_link( $term );

		add_filter( 'user_trailingslashit',
			array( $this, 'permalinks_customizer_trailingslash' ), 10, 2
		);
		add_filter( 'term_link',
			array( $this, 'permalinks_customizer_term_link' ), 10, 2
		);

		if ( is_wp_error( $term_link ) ) {
			return '';
		}

		$original_permalink = ltrim(
			str_replace( home_url(), '', $term_link ), '/'
		);

		return $original_permalink;
	}

	/**
	 * Use to Add Trailing Slash
	 */
	public function permalinks_customizer_trailingslash( $string, $type ) {
		global $_CPRegisteredURL;

		remove_filter( 'user_trailingslashit',
			array( $this, 'permalinks_customizer_trailingslash' ), 10, 2
		);
		$url = parse_url( get_bloginfo( 'url' ) );
		$request = ltrim( isset( $url['path'] ) ? substr( $string, strlen( $url['path'] ) ) : $string, '/' );
		add_filter( 'user_trailingslashit',
			array( $this, 'permalinks_customizer_trailingslash' ), 10, 2
		);

		if ( ! trim( $request ) ) {
			return $string;
		}

		if ( trim( $_CPRegisteredURL, '/' ) == trim( $request, '/' ) ) {
			if ( isset( $url['path'] ) ) {
				return ( $string{0} == '/' ? '/' : '' ) . trailingslashit( $url['path'] ) . $_CPRegisteredURL;
			} else {
				return ( $string{0} == '/' ? '/' : '' ) . $_CPRegisteredURL;
			}
		}
		return $string;
	}
}
