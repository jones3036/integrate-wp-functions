<?php
/**
 * Plugin Name: Integrate WP theme custom functions
 * Description: Site-specific WordPress tweaks that should survive theme updates.
 * Version: 1.1.0
 * Repository: https://github.com/jones3036/integrate-wp-functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*** Disable Gutenberg for posts ***/
add_filter( 'use_block_editor_for_post', '__return_false', 10 );

/*** Disable Gutenberg widgets screen ***/
add_filter( 'use_widgets_block_editor', '__return_false' );

/*** Remove Gutenberg front-end styles ***/
add_action( 'wp_enqueue_scripts', function() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
}, 100 );


/*** Clean up WordPress head output ***/
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
remove_action( 'template_redirect', 'rest_output_link_header', 11 );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

/*** Disable XML-RPC ***/
add_filter( 'xmlrpc_enabled', '__return_false' );

/*** Hide admin bar on the front end ***/
add_filter( 'show_admin_bar', '__return_false' );

/*** Disable comments sitewide ***/
add_action( 'admin_init', function() {
	global $pagenow;

	if ( 'edit-comments.php' === $pagenow ) {
		wp_safe_redirect( admin_url() );
		exit;
	}

	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );

	foreach ( get_post_types() as $post_type ) {
		if ( post_type_supports( $post_type, 'comments' ) ) {
			remove_post_type_support( $post_type, 'comments' );
			remove_post_type_support( $post_type, 'trackbacks' );
		}
	}
} );

add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

add_action( 'admin_menu', function() {
	remove_menu_page( 'edit-comments.php' );
} );

add_action( 'init', function() {
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'feed_links', 2 );
} );

/*** Add recommended must-use plugins notice ***/
add_action( 'admin_notices', function() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$recommended_plugins = array(
		'elementor/elementor.php'                    => 'Elementor',
		'elementor-pro/elementor-pro.php'            => 'Elementor Pro',
		'wordpress-seo/wp-seo.php'                   => 'Yoast SEO',
		// 'autodescription/autodescription.php'        => 'The SEO Framework',
		// 'redirection/redirection.php'                => 'Redirection',
		// 'updraftplus/updraftplus.php'                => 'UpdraftPlus',
		// 'wp-mail-smtp/wp_mail_smtp.php'              => 'WP Mail SMTP',
		// 'code-snippets/code-snippets.php'            => 'Code Snippets',
		// 'cookieyes/cookieyes.php'                    => 'CookieYes',
		// 'litespeed-cache/litespeed-cache.php'        => 'LiteSpeed Cache',
		// 'wordpress-importer/wordpress-importer.php'  => 'WordPress Importer',
	);

	$missing_plugins = array();

	foreach ( $recommended_plugins as $plugin_file => $plugin_name ) {
		if ( ! is_plugin_active( $plugin_file ) ) {
			$missing_plugins[] = $plugin_name;
		}
	}

	if ( empty( $missing_plugins ) ) {
		return;
	}

	echo '<div class="notice notice-info"><p><strong>Recommended plugins missing:</strong> ' . esc_html( implode( ', ', $missing_plugins ) ) . '</p></div>';
} );

/*** Enable SVG uploads ***/
add_filter( 'upload_mimes', function( $mimes ) {
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	return $mimes;
} );

// Sanitize SVG uploads to prevent XSS attacks
add_filter( 'wp_handle_upload_prefilter', function( $file ) {
	if ( in_array( $file['type'], array( 'image/svg+xml', 'image/svg' ), true ) ) {
		$file_path = $file['tmp_name'];
		$svg_data = file_get_contents( $file_path );
		
		// Basic SVG sanitization: remove script tags and event handlers
		$svg_data = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/is', '', $svg_data );
		$svg_data = preg_replace( '/on\w+\s*=\s*["\']([^"\']*)["\']|on\w+\s*=\s*(\S+)/i', '', $svg_data );
		
		file_put_contents( $file_path, $svg_data );
	}
	return $file;
} );

