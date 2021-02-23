<?php

/*
Plugin Name: SiteSpecificPlugin
*/

function sitespecificplugin_init() {
	// update_option( 'initial_db_version', get_option( 'db_version' ) );
	// update_option( 'recently_activated', __return_empty_array() );

	if ( 'twentytwelve' !== get_option( 'template' ) ) {
		update_option( 'template', 'twentytwelve' );
	}

	if ( 'simpletheme' !== get_option( 'stylesheet' ) ) {
		update_option( 'stylesheet', 'simpletheme' );
	}

	if ( '' !== get_option( 'blogdescription' ) ) {
		update_option( 'blogdescription', '' );
	}

	if ( ORIGINAL_WP_SITEURL !== get_option( 'siteurl' ) ) {
		update_option( 'siteurl', ORIGINAL_WP_SITEURL );
	}

	if ( ORIGINAL_WP_HOME !== get_option( 'home' ) ) {
		update_option( 'home', ORIGINAL_WP_HOME );
	}

	if ( 'Asia/Manila' !== get_option( 'timezone_string' ) ) {
		update_option( 'timezone_string', 'Asia/Manila' );
	}

	if ( 0 != get_option( 'thumbnail_size_w' ) ) {
		update_option( 'thumbnail_size_w', 0 );
	}

	if ( 0 != get_option( 'thumbnail_size_h' ) ) {
		update_option( 'thumbnail_size_h', 0 );
	}

	if ( '' !== get_option( 'thumbnail_crop' ) ) {
		update_option( 'thumbnail_crop', '' );
	}

	if ( 0 != get_option( 'medium_size_w' ) ) {
		update_option( 'medium_size_w', 0 );
	}

	if ( 0 != get_option( 'medium_size_h' ) ) {
		update_option( 'medium_size_h', 0 );
	}

	if ( 0 != get_option( 'large_size_w' ) ) {
		update_option( 'large_size_w', 0 );
	}

	if ( 0 != get_option( 'large_size_h' ) ) {
		update_option( 'large_size_h', 0 );
	}

	if ( '../public/media' !== get_option( 'upload_path' ) ) {
		update_option( 'upload_path', '../public/media' );
	}

	if ( ORIGINAL_WP_HOME . '/public/media' !== get_option( 'upload_url_path' ) ) {
		update_option( 'upload_url_path', ORIGINAL_WP_HOME . '/public/media' );
	}

	if ( '' !== get_option( 'uploads_use_yearmonth_folders' ) ) {
		update_option( 'uploads_use_yearmonth_folders', '' );
	}

	if ( 'closed' !== get_option( 'default_comment_status' ) ) {
		update_option( 'default_comment_status', 'closed' );
	}

	if ( 'closed' !== get_option( 'default_ping_status' ) ) {
		update_option( 'default_ping_status', 'closed' );
	}

	if ( '' !== get_option( 'show_avatars' ) ) {
		update_option( 'show_avatars', '' );
	}

	if ( 0 != get_option( 'medium_large_size_w' ) ) {
		update_option( 'medium_large_size_w', 0 );
	}

	if ( 0 != get_option( 'medium_large_size_h' ) ) {
		update_option( 'medium_large_size_h', 0 );
	}

	if ( false !== get_option( 'permalink_structure' ) ) {
		if ( '/%postname%/' !== get_option( 'permalink_structure' ) ) {
			update_option( 'permalink_structure', '/%postname%/' );
		}
	}

	unregister_taxonomy_for_object_type( 'category', 'post' );
}
add_action( 'init', 'sitespecificplugin_init' );

/**
 *
 */
function is_build_and_deploy() {
	if ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) && false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'HTTrack' ) ) {
		return true;
	}

	return false;
}

/**
 *
 */
function sitespecificplugin_template_redirect() {
	global $post;

	if ( is_build_and_deploy() ) {
		return;
	}

	if ( ! is_attachment() && ! is_author() && ! is_date() && ! is_tag( 'how-to' ) ) {
		return;
	}

	$location = home_url();

	if ( is_attachment() && 0 != $post->post_parent ) {
		$location = get_permalink( $post->post_parent );
	}

	wp_redirect( $location, 301 );
	exit;
}
add_action( 'template_redirect', 'sitespecificplugin_template_redirect' );

/**
 *
 */
function sitespecificplugin_caching_headers() {
	if ( ! is_build_and_deploy() ) {
		return;
	}

	if ( is_user_logged_in() || empty( get_option( 'permalink_structure' ) ) )  {
		return;
	}

	if ( ! is_home() && ! is_singular() ) {
		return;
	}

	$last_modified = mysql2date( 'D, d M Y H:i:s', get_lastpostmodified( 'gmt' ), false ) . ' GMT';

	/* if ( $post_id = get_queried_object_id() ) {
		$last_modified = get_post_modified_time( 'D, d M Y H:i:s', true, $post_id ) . ' GMT';
	} */

	$etag = '"' . sha1( $last_modified ) . '"';

	// header( 'Cache-Control: public, max-age=0, must-revalidate' );
	header( "Last-Modified: $last_modified" );
	header( "ETag: $etag" );

	$if_modified_since = ( isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) ) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : '';
	$if_none_match = ( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) ) ? stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) : '';

	if ( $if_modified_since === $last_modified && $if_none_match === $etag ) {
		status_header( 304 );
	}
}
add_filter( 'template_redirect', 'sitespecificplugin_caching_headers' );

/**
 *
 */
function sitespecificplugin_document_title_separator( $sep ) {
	return '|';
}
add_filter( 'document_title_separator', 'sitespecificplugin_document_title_separator' );

/**
 *
 */
function sitespecificplugin_wp_enqueue_scripts() {
	global $extras;

	// wp_enqueue_script( 'instantpage', 'https://cdn.jsdelivr.net/npm/instant.page/instantpage.min.js', array(), '5.1.0', true );
	wp_deregister_script( 'wp-embed' );
	wp_deregister_script( 'jquery' );
	wp_register_script ( 'jquery', 'https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js', array(), '3.5.1', true );
	// wp_enqueue_script( 'sitespecificplugin-secret', plugin_dir_url( __FILE__ ) . 'js/secret.js', array( 'jquery' ), $extras->version( plugin_dir_path( __FILE__ ) . 'js/secret.js' ), true );
	wp_enqueue_style( 'sitespecificplugin-secret', plugin_dir_url( __FILE__ ) . 'css/secret.css', array(), $extras->version( plugin_dir_path( __FILE__ ) . 'css/secret.css' ) );

	if ( ! is_user_logged_in() ) {
		// wp_enqueue_style( 'cookieconsent', 'https://cdn.jsdelivr.net/npm/cookieconsent/build/cookieconsent.min.css' );
	}
}
add_action( 'wp_enqueue_scripts', 'sitespecificplugin_wp_enqueue_scripts', 12 );

/**
 *
 */
function sitespecificplugin_admin_enqueue_scripts() {
	// wp_enqueue_script( 'instantpage', 'https://cdn.jsdelivr.net/npm/instant.page/instantpage.min.js', array(), '5.1.0', true );
}
add_action( 'admin_enqueue_scripts', 'sitespecificplugin_admin_enqueue_scripts' );

/**
 *
 */
function sitespecificplugin_goals() {
	$count_posts = wp_count_posts( 'post' );
	$posts = 100;
	$complete = $count_posts->publish / $posts;
	$complete = number_format( $complete * 100, 0 ) . '%';
	$deadline = strtotime( '2021-9-13 15:59:59' );
	$date = date( 'F j, Y', $deadline + 28800 );
	$time = $deadline - time();
	$days = floor( $time / 86400 ) + 1;
	echo "<p>Posts: <strong>$count_posts->publish/$posts ($complete Complete)</strong></p><p>Deadline: <strong>$date</strong></p><p>There " . ( ( 1 < $days ) ? "are" : "is" ) . " only <strong>$days " . ( ( 1 < $days ) ? "days" : "day" ) . "</strong> left until the deadline.</p>";
}

/**
 *
 */
function sitespecificplugin_wp_dashboard_setup() {
	wp_add_dashboard_widget( 'dashboard_goals', 'Goals', 'sitespecificplugin_goals' );
	remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
}
add_action( 'wp_dashboard_setup', 'sitespecificplugin_wp_dashboard_setup' );

/**
 *
 */
function sitespecificplugin_admin_bar_menu( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'wp-logo' );
	$wp_admin_bar->remove_node( 'themes' );
	$wp_admin_bar->remove_node( 'widgets' );
	$wp_admin_bar->remove_node( 'customize' );
	// $wp_admin_bar->remove_node( 'menus' );
	$wp_admin_bar->remove_node( 'comments' );
	$wp_admin_bar->remove_node( 'new-user' );
}
add_action( 'admin_bar_menu', 'sitespecificplugin_admin_bar_menu', 71 );

/**
 *
 */
function sitespecificplugin_login_head() {
	?>
	<style>
		.login h1 {
			display: none;
		}
	</style>
	<?php
}
add_action( 'login_head', 'sitespecificplugin_login_head' );

/**
 *
 */
function sitespecificplugin_admin_head() {
	if ( 'light' === get_user_option( 'admin_color' ) ) {
		?>
		<style>
			#wp-content-editor-tools {
				background-color: #f5f5f5;
			}
		</style>
		<?php
	}
	?>
	<style>
		#dashboard_right_now li.comment-count,
		#dashboard_right_now li.comment-mod-count {
			display: none;
		}
		.user-comment-shortcuts-wrap {
			display: none;
		}
		#wp-link .link-target {
			display: none;
		}
	</style>
	<?php
}
add_action( 'admin_head', 'sitespecificplugin_admin_head' );

/**
 *
 */
function sitespecificplugin_breadcrumbs( $format = 'json-ld', $sep = '' ) {
	if ( ! is_singular() ) {
		return;
	}

	$links[] = array(
		'name' => 'Home',
		'item' => home_url( '/' )
	);
	$link_count = 0;
	$breadcrumb_items = array(
		'@context' => 'https://schema.org',
		'@type'    => 'BreadcrumbList'
	);
	$html_tag = 'ol';
	$html = '';
	$style = ( is_page() ) ? 'display: none;' : '';

	if ( $tags = get_the_tags() ) {
		foreach ( $tags as $tag ) {
			$links[] = array(
				'name' => $tag->name,
				'item' => get_tag_link( $tag->term_id )
			);
		}
	}

	$term = get_term_by( 'slug', 'how-to', 'post_tag' );
	if ( $term && ! has_term( $term->slug, $term->taxonomy ) ) {
		$links[] = array(
			'name' => $term->name,
			'item' => esc_url( get_tag_link( $term->term_id ) )
		);
	}

	if ( is_singular() ) {
		$links[] = array(
			'name' => get_the_title(),
			'item' => get_the_permalink()
		);
	}

	foreach ( $links as $link ) {
		$breadcrumb_items['itemListElement'][] = array(
			'@type'    => 'ListItem',
			'position' => $link_count + 1,
			'name'     => $link['name'],
			'item'     => $link['item']
		);
		$link_count++;
	}

	if ( 'json-ld' !== $format ) {
		$items = $breadcrumb_items['itemListElement'];
		if ( '' !== $sep ) {
			$html_tag = 'p';
			foreach ( $items as $item ) {
				$html .= '<a href="' . $item['item'] . '">' . $item['name'] . '</a>' . ( ( count( $items ) > $item['position'] ) ? $sep : '' );
			}
		} else {
			foreach ( $items as $item ) {
				$html .= '<li>' . '<a href="' . $item['item'] . '">' . $item['name'] . '</a>' . '</li>';
			}
		}
		$html = '<' . $html_tag . ' class="breadcrumbs"' . ( ( $style ) ? ' style="' . $style . '"' : '' ) . '>' . $html . '</' . $html_tag . '>';
	} else {
		$html = json_encode( $breadcrumb_items, JSON_UNESCAPED_SLASHES );
	}

	return $html;
}

/**
 *
 */
function sitespecificplugin_wp_head() {
	if ( $breadcrumbs = sitespecificplugin_breadcrumbs() ) {
		echo '<script type="application/ld+json">' . $breadcrumbs . '</script>' . "\n";
	}
}
add_action( 'wp_head', 'sitespecificplugin_wp_head' );

/**
 *
 */
function sitespecificplugin_wp_footer() {
	global $cleaner;

	echo '<script>!function(){for(var n=document.links,t=0;t<n.length;t++)n[t].hostname!==location.hostname&&(n[t].target="_blank")}();</script>' . "\n";

	if ( ! is_user_logged_in() ) {
		// echo '<script data-ad-client="" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
		/* echo '<script src="https://cdn.jsdelivr.net/npm/cookieconsent/build/cookieconsent.min.js" data-cfasync="false"></script>';
		echo '<script>window.cookieconsent.initialise({palette:{popup:{background:"#edeff5",text:"#838391"},button:{background:"#4b81e8"}},content:{href:"' . esc_url( home_url( '/privacy/' ) ) . '"}});</script>'; */
	}

	// echo '<script>document.write(' . $cleaner->obfuscate( "<script async src='https://cse.google.com/cse.js?cx=3b7753521c01e8c01'></script>", '' ) . ');</script>';
}
add_action( 'wp_footer', 'sitespecificplugin_wp_footer', 11 );

/**
 *
 */
function sitespecificplugin_admin_menu() {
	global $submenu;

	remove_submenu_page( 'edit-comments.php', 'edit-comments.php' );
	// remove_menu_page( 'edit-comments.php' );
	remove_submenu_page( 'themes.php', 'themes.php' );
	remove_submenu_page( 'themes.php', add_query_arg( 'return', urlencode( remove_query_arg( wp_removable_query_args(), wp_unslash( $_SERVER['REQUEST_URI'] ) ) ), 'customize.php' ) );
	remove_submenu_page( 'themes.php', 'widgets.php' );
	remove_submenu_page( 'themes.php', 'nav-menus.php' );
	// remove_menu_page( 'themes.php' );
	remove_submenu_page( 'users.php', 'users.php' );
	remove_submenu_page( 'users.php', 'user-new.php' );
	remove_submenu_page( 'users.php', 'profile.php' );
	// remove_menu_page( 'users.php' );
	remove_submenu_page( 'tools.php', 'tools.php' );
	remove_submenu_page( 'tools.php', 'import.php' );
	remove_submenu_page( 'tools.php', 'export.php' );
	remove_submenu_page( 'tools.php', 'site-health.php' );
	remove_submenu_page( 'tools.php', 'export-personal-data.php' );
	remove_submenu_page( 'tools.php', 'erase-personal-data.php' );
	// remove_menu_page( 'tools.php' );
	remove_submenu_page( 'options-general.php', 'options-general.php' );
	remove_submenu_page( 'options-general.php', 'options-writing.php' );
	remove_submenu_page( 'options-general.php', 'options-reading.php' );
	remove_submenu_page( 'options-general.php', 'options-discussion.php' );
	remove_submenu_page( 'options-general.php', 'options-media.php' );
	remove_submenu_page( 'options-general.php', 'options-permalink.php' );
	remove_submenu_page( 'options-general.php', 'options-privacy.php' );
	// remove_menu_page( 'options-general.php' );

	foreach ( $submenu as $key => $value ) {
		if ( empty( $submenu[$key] ) ) {
			remove_menu_page( $key );
		}
	}
}
add_action( 'admin_menu', 'sitespecificplugin_admin_menu' );

/**
 *
 */
function sitespecificplugin_manage_posts_columns( $columns ) {
	unset( $columns['author'] );
	unset( $columns['comments'] );
	unset( $columns['date'] );
	return $columns;
}
add_filter( 'manage_posts_columns', 'sitespecificplugin_manage_posts_columns' );

/**
 *
 */
function sitespecificplugin_manage_pages_columns( $columns ) {
	unset( $columns['author'] );
	unset( $columns['comments'] );
	unset( $columns['date'] );
	return $columns;
}
add_filter( 'manage_pages_columns', 'sitespecificplugin_manage_pages_columns' );

/**
 *
 */
function sitespecificplugin_body_class( $classes ) {
	if ( wp_style_is( 'google-fonts', 'queue' ) ) {
		$classes[] = 'google-fonts-enabled';
	}

	return $classes;
}
add_filter( 'body_class', 'sitespecificplugin_body_class' );

/**
 *
 */
function sitespecificplugin_above_the_fold( $ad_code, $content ) {
	$pattern = '/<p(.*)>(.*)<\/p>/';

	if ( preg_match( $pattern, $content, $matches ) ) {
		$content = preg_replace( $pattern, $matches[0] . $ad_code, $content, 1 );
	} else {
		$content = $ad_code . $content;
	}

	return $content;
}

/**
 *
 */
function sitespecificplugin_the_content( $content ) {
	global $extras;

	if ( ! is_singular() ) {
		return $content;
	}

	/* if ( is_single() && ! is_attachment() ) {
		$content = sitespecificplugin_above_the_fold( $extras->adsbygoogle( array(
			'class'     => 'above-the-fold',
			'style'     => 'margin-bottom: 24px;',
			'ad_name'   => '_adslot_1',
			'ad_class'  => 'adslot adslot_1',
			'ad_client' => '',
			'ad_slot'   => '',
			'echo'      => false
		) ), $content );
		$content .= $extras->adsbygoogle( array(
			'class'     => 'below-the-fold',
			'style'     => 'margin-bottom: 24px; clear: both;',
			'ad_name'   => '_adslot_2',
			'ad_class'  => 'adslot adslot_2',
			'ad_client' => '',
			'ad_slot'   => '',
			'echo'      => false
		) );
	} */

	return $content . sitespecificplugin_breadcrumbs( 'html', '' );
}
add_filter( 'the_content', 'sitespecificplugin_the_content', 11 );

/**
 *
 */
function sitespecificplugin_the_post() {
	global $wp_query, $extras;

	if ( ! is_home() && ! is_archive() && ! is_search() ) {
		return;
	}

	if ( 1 != $wp_query->current_post ) {
		return;
	}

	$extras->adsbygoogle( array(
		'class'     => 'above-the-fold',
		'style'     => 'border-bottom: 1px solid #ededed; margin-bottom: 24px; padding-bottom: 24px;',
		'ad_name'   => '_adslot_1',
		'ad_class'  => 'adslot adslot_1',
		'ad_client' => '',
		'ad_slot'   => '',
		'echo'      => true
	) );
}
// add_action( 'the_post', 'sitespecificplugin_the_post' );

/**
 *
 */
function sitespecificplugin_minify( $html ) {
	global $extras;

	if ( ! is_build_and_deploy() || $extras->is_login_page() || is_user_logged_in() || empty( get_option( 'permalink_structure' ) ) ) {
		return $html;
	}

	$current_slug = add_query_arg( array() );
	$current_url = esc_url( home_url( $current_slug ) );

	if ( is_tag( 'how-to' ) ) {
		$html = '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0; url=' . esc_url( home_url( '/' ) ) . '"></head><body></body></html>' . "\n";
	}

	return $extras->to_static( array(
			'dir'                => PUBLISH_DIR . $current_slug,
			'html'               => $html,
			'strings_to_replace' => array(
				ORIGINAL_WP_HOME                                   => '',
				'\'' . explode( ':', esc_url( home_url() ) )[1]    => '\'' . explode( ':', PUBLIC_HOME )[1],
				'"item":"' . esc_url( home_url() )                 => '"item":"' . PUBLIC_HOME,
				'/' . WP_CONTENT . '/cache/autoptimize' => '',
				'/public'                                          => '',
				esc_url( home_url() )                              => ''
			)
		)
	);
}

/**
 *
 */
function sitespecificplugin_html( $args = '' ) {
	$defaults = array(
		'url'       => '',
		'query_arg' => array()
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args, EXTR_SKIP );

	wp_remote_request( add_query_arg( $query_arg, $url ) );
}

/**
 *
 */
function sitespecificplugin_post_updated( $post_ID ) {
	sitespecificplugin_html( array(
			'url'       => get_the_permalink( $post_ID ),
			'query_arg' => array(
				'cache' => 'true'
			)
		)
	);
}
// add_action( 'post_updated', 'sitespecificplugin_post_updated' );

/**
 *
 */
function sitespecificplugin_sanitize_title( $title ) {
	$title = str_replace( ' ', '_', $title );
	return $title;
}
add_filter( 'sanitize_title', 'sitespecificplugin_sanitize_title', 9 );

/**
 *
 */
function sitespecificplugin_wp_loaded() {
	ob_start( 'sitespecificplugin_minify' );
}
add_action( 'wp_loaded', 'sitespecificplugin_wp_loaded' );

/**
 *
 */
function sitespecificplugin_pre_get_posts( $query ) {
	if ( is_user_logged_in() && is_admin() ) {
		return;
	}

	$query->set( 'post__not_in', array( 1, 2, 3 ) );
}
add_action( 'pre_get_posts', 'sitespecificplugin_pre_get_posts' );
