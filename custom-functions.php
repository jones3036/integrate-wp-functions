<?php
/**
 * Plugin Name: Integrate WP Theme Custom Functions
 * Plugin URI: https://github.com/jones3036/integrate-wp-functions
 * Description: Site-specific WordPress tweaks that survive theme updates.
 * Version: 1.3.1
 * Author: Integrate Solutions
 * Author URI: https://integrate-it.co.uk
 * Text Domain: integrate-wp-functions
 * Domain Path: /languages
 * Repository: https://github.com/jones3036/integrate-wp-functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'IWF_PLUGIN_FILE' ) ) {
	define( 'IWF_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'IWF_PLUGIN_BASENAME' ) ) {
	define( 'IWF_PLUGIN_BASENAME', plugin_basename( IWF_PLUGIN_FILE ) );
}

if ( ! defined( 'IWF_OPTION_KEY' ) ) {
	define( 'IWF_OPTION_KEY', 'iwf_settings' );
}

if ( ! defined( 'IWF_GITHUB_REPO' ) ) {
	define( 'IWF_GITHUB_REPO', 'https://api.github.com/repos/jones3036/integrate-wp-functions' );
}

/**
 * Default plugin settings.
 */
function iwf_get_default_settings() {
	return array(
		'disable_gutenberg'             => true,
		'disable_widgets_block_editor'   => true,
		'remove_gutenberg_styles'        => true,
		'cleanup_head'                   => true,
		'disable_xmlrpc'                 => true,
		'hide_admin_bar'                 => true,
		'disable_comments'               => true,
		'minimize_asset_query_strings'   => true,
		'limit_revisions'                => true,
		'disable_rest_for_guests'        => false,
		'recommended_plugins_notice'     => true,
	);
}

/**
 * Merge saved settings with defaults.
 */
function iwf_get_settings() {
	$defaults = iwf_get_default_settings();
	$options = get_option( IWF_OPTION_KEY, array() );

	if ( ! is_array( $options ) ) {
		$options = array();
	}

	return wp_parse_args( $options, $defaults );
}

/**
 * Return a setting value while allowing constant overrides.
 */
function iwf_setting_enabled( $key ) {
	$constant = 'IWF_' . strtoupper( $key );

	if ( defined( $constant ) ) {
		return (bool) constant( $constant );
	}

	$settings = iwf_get_settings();

	return ! empty( $settings[ $key ] );
}

/**
 * Ensure default settings exist.
 */
function iwf_maybe_register_default_settings() {
	if ( false === get_option( IWF_OPTION_KEY ) ) {
		add_option( IWF_OPTION_KEY, iwf_get_default_settings() );
	}
}
add_action( 'init', 'iwf_maybe_register_default_settings' );

/**
 * Load translations.
 */
function iwf_load_textdomain() {
	load_plugin_textdomain( 'integrate-wp-functions', false, dirname( plugin_basename( IWF_PLUGIN_FILE ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'iwf_load_textdomain' );

/**
 * Register settings page and fields.
 */
function iwf_register_settings() {
	register_setting( 'iwf_settings_group', IWF_OPTION_KEY, 'iwf_sanitize_settings' );

	add_settings_section(
		'iwf_features_section',
		__( 'Feature toggles', 'integrate-wp-functions' ),
		'iwf_settings_section_description',
		'iwf_settings'
	);

	$fields = array(
		'disable_gutenberg'           => __( 'Disable Gutenberg for posts', 'integrate-wp-functions' ),
		'disable_widgets_block_editor' => __( 'Disable block widget editor', 'integrate-wp-functions' ),
		'remove_gutenberg_styles'      => __( 'Remove Gutenberg front-end styles', 'integrate-wp-functions' ),
		'cleanup_head'                 => __( 'Clean up WordPress head output', 'integrate-wp-functions' ),
		'disable_xmlrpc'               => __( 'Disable XML-RPC', 'integrate-wp-functions' ),
		'hide_admin_bar'               => __( 'Hide admin bar on the front end', 'integrate-wp-functions' ),
		'disable_comments'             => __( 'Disable comments sitewide', 'integrate-wp-functions' ),
		'minimize_asset_query_strings' => __( 'Remove query strings from static assets', 'integrate-wp-functions' ),
		'limit_revisions'              => __( 'Limit post revisions to 3', 'integrate-wp-functions' ),
		'disable_rest_for_guests'      => __( 'Disable REST API for unauthenticated visitors', 'integrate-wp-functions' ),
		'recommended_plugins_notice'   => __( 'Show recommended plugin install/activate notices', 'integrate-wp-functions' ),
	);

	foreach ( $fields as $name => $label ) {
		add_settings_field(
			'name_' . $name,
			$label,
			'iwf_render_checkbox_field',
			'iwf_settings',
			'iwf_features_section',
			array(
				'label_for' => $name,
				'name'      => $name,
			)
		);
	}
}
add_action( 'admin_init', 'iwf_register_settings' );

/**
 * Sanitize option values.
 */
function iwf_sanitize_settings( $input ) {
	$defaults = iwf_get_default_settings();
	$output = array();

	foreach ( $defaults as $key => $value ) {
		$output[ $key ] = isset( $input[ $key ] ) ? true : false;
	}

	return $output;
}

/**
 * Render a checkbox field.
 */
function iwf_render_checkbox_field( $args ) {
	$name = $args['name'];
	$settings = iwf_get_settings();
	$checked = isset( $settings[ $name ] ) && $settings[ $name ];

	echo '<input type="checkbox" id="' . esc_attr( $name ) . '" name="' . esc_attr( IWF_OPTION_KEY ) . '[' . esc_attr( $name ) . ']" value="1"' . checked( true, $checked, false ) . ' />';
}

/**
 * Settings section description.
 */
function iwf_settings_section_description() {
	echo '<p>' . esc_html__( 'Enable or disable site-specific features without editing plugin code.', 'integrate-wp-functions' ) . '</p>';
}

/**
 * Add the settings page.
 */
function iwf_add_admin_menu() {
	add_options_page(
		__( 'Integrate WP Functions', 'integrate-wp-functions' ),
		__( 'Integrate WP Functions', 'integrate-wp-functions' ),
		'manage_options',
		'integrate-wp-functions',
		'iwf_render_settings_page'
	);
}
add_action( 'admin_menu', 'iwf_add_admin_menu' );

/**
 * Output the settings page.
 */
function iwf_render_settings_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Integrate WP Functions Settings', 'integrate-wp-functions' ); ?></h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'iwf_settings_group' );
			do_settings_sections( 'iwf_settings' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Register core feature hooks.
 */
function iwf_setup_features() {
	if ( iwf_setting_enabled( 'disable_gutenberg' ) ) {
		add_filter( 'use_block_editor_for_post', '__return_false', 10 );
	}

	if ( iwf_setting_enabled( 'disable_widgets_block_editor' ) ) {
		add_filter( 'use_widgets_block_editor', '__return_false' );
	}

	if ( iwf_setting_enabled( 'remove_gutenberg_styles' ) ) {
		add_action( 'wp_enqueue_scripts', 'iwf_remove_gutenberg_styles', 100 );
	}

	if ( iwf_setting_enabled( 'cleanup_head' ) ) {
		add_action( 'init', 'iwf_cleanup_head' );
	}

	if ( iwf_setting_enabled( 'disable_xmlrpc' ) ) {
		add_filter( 'xmlrpc_enabled', '__return_false' );
	}

	if ( iwf_setting_enabled( 'hide_admin_bar' ) ) {
		add_filter( 'show_admin_bar', '__return_false' );
	}

	if ( iwf_setting_enabled( 'disable_comments' ) ) {
		iwf_disable_comments();
	}

	if ( iwf_setting_enabled( 'minimize_asset_query_strings' ) ) {
		add_filter( 'style_loader_src', 'iwf_remove_version_query_string', 999 );
		add_filter( 'script_loader_src', 'iwf_remove_version_query_string', 999 );
	}

	if ( iwf_setting_enabled( 'limit_revisions' ) ) {
		add_filter( 'wp_revisions_to_keep', 'iwf_limit_revisions', 10, 2 );
	}

	if ( iwf_setting_enabled( 'disable_rest_for_guests' ) ) {
		add_filter( 'rest_authentication_errors', 'iwf_disable_rest_for_guests' );
	}

	if ( iwf_setting_enabled( 'recommended_plugins_notice' ) ) {
		add_action( 'admin_notices', 'iwf_recommended_plugins_notice' );
	}
}
add_action( 'init', 'iwf_setup_features' );

/**
 * Remove Gutenberg front-end CSS.
 */
function iwf_remove_gutenberg_styles() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
}

/**
 * Clean up WordPress head output.
 */
function iwf_cleanup_head() {
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
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'feed_links', 2 );
}

/**
 * Disable comments sitewide.
 */
function iwf_disable_comments() {
	add_action( 'admin_init', 'iwf_disable_comments_admin_init' );
	add_action( 'admin_menu', 'iwf_disable_comments_admin_menu' );

	add_filter( 'comments_open', '__return_false', 20, 2 );
	add_filter( 'pings_open', '__return_false', 20, 2 );
	add_filter( 'comments_array', '__return_empty_array', 10, 2 );
}

/**
 * Remove comments support and comments screen.
 */
function iwf_disable_comments_admin_init() {
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
}

/**
 * Remove comments admin menu.
 */
function iwf_disable_comments_admin_menu() {
	remove_menu_page( 'edit-comments.php' );
}

/**
 * Remove query strings from assets.
 */
function iwf_remove_version_query_string( $src ) {
	if ( strpos( $src, '?ver=' ) ) {
		$src = remove_query_arg( 'ver', $src );
	}

	return $src;
}

/**
 * Limit revisions to 3.
 */
function iwf_limit_revisions( $num, $post ) {
	return 3;
}

/**
 * Disable REST API for unauthenticated visitors.
 */
function iwf_disable_rest_for_guests( $result ) {
	if ( ! empty( $result ) ) {
		return $result;
	}

	if ( ! is_user_logged_in() ) {
		return new WP_Error(
			'rest_not_logged_in',
			__( 'REST API access is disabled for unauthenticated users.', 'integrate-wp-functions' ),
			array( 'status' => 401 )
		);
	}

	return $result;
}

/**
 * Recommended plugin list with install/activate links.
 */
function iwf_get_recommended_plugins() {
	return array(
		'elementor' => array(
			'name'        => 'Elementor',
			'plugin_file' => 'elementor/elementor.php',
			'slug'        => 'elementor',
		),
		'elementor-pro' => array(
			'name'        => 'Elementor Pro',
			'plugin_file' => 'elementor-pro/elementor-pro.php',
			'slug'        => 'elementor-pro',
		),
		'yost-seo' => array(
			'name'        => 'Yoast SEO',
			'plugin_file' => 'wordpress-seo/wp-seo.php',
			'slug'        => 'wordpress-seo',
		),
		// 'tsf' => array(
		// 	'name'        => 'The SEO Framework',
		// 	'plugin_file' => 'autodescription/autodescription.php',
		// 	'slug'        => 'autodescription',
		// ),
		// 'redirection' => array(
		// 	'name'        => 'Redirection',
		// 	'plugin_file' => 'redirection/redirection.php',
		// 	'slug'        => 'redirection',
		// ),
		// 'updraftplus' => array(
		// 	'name'        => 'UpdraftPlus',
		// 	'plugin_file' => 'updraftplus/updraftplus.php',
		// 	'slug'        => 'updraftplus',
		// ),
		// 'wp-mail-smtp' => array(
		// 	'name'        => 'WP Mail SMTP',
		// 	'plugin_file' => 'wp-mail-smtp/wp_mail_smtp.php',
		// 	'slug'        => 'wp-mail-smtp',
		// ),
		// 'code-snippets' => array(
		// 	'name'        => 'Code Snippets',
		// 	'plugin_file' => 'code-snippets/code-snippets.php',
		// 	'slug'        => 'code-snippets',
		// ),
		// 'cookieyes' => array(
		// 	'name'        => 'CookieYes',
		// 	'plugin_file' => 'cookieyes/cookieyes.php',
		// 	'slug'        => 'cookieyes',
		// ),
		// 'litespeed-cache' => array(
		// 	'name'        => 'LiteSpeed Cache',
		// 	'plugin_file' => 'litespeed-cache/litespeed-cache.php',
		// 	'slug'        => 'litespeed-cache',
		// ),
		// 'wordpress-importer' => array(
		// 	'name'        => 'WordPress Importer',
		// 	'plugin_file' => 'wordpress-importer/wordpress-importer.php',
		// 	'slug'        => 'wordpress-importer',
		// ),
	);
}

/**
 * Display recommended plugin notices with install/activate links.
 */
function iwf_recommended_plugins_notice() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$all_plugins = get_plugins();
	$items = array();

	foreach ( iwf_get_recommended_plugins() as $plugin ) {
		$plugin_file = $plugin['plugin_file'];
		$plugin_name = $plugin['name'];

		if ( is_plugin_active( $plugin_file ) ) {
			continue;
		}

		if ( ! isset( $all_plugins[ $plugin_file ] ) ) {
			if ( current_user_can( 'install_plugins' ) ) {
				$url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . urlencode( $plugin['slug'] ) ), 'install-plugin_' . $plugin['slug'] );
				$items[] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $url ), esc_html( $plugin_name ) );
			}
		} else {
			if ( current_user_can( 'activate_plugins' ) ) {
				$url = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . urlencode( $plugin_file ) ), 'activate-plugin_' . $plugin_file );
				$items[] = sprintf( '<a href="%1$s">%2$s</a>', esc_url( $url ), esc_html( $plugin_name ) );
			}
		}
	}

	if ( empty( $items ) ) {
		return;
	}

	echo '<div class="notice notice-info"><p><strong>' . esc_html__( 'Recommended plugins:', 'integrate-wp-functions' ) . '</strong> ' . wp_kses_post( implode( ', ', $items ) ) . '</p></div>';
}

/**
 * Fetch plugin data in a safe way.
 */
function iwf_get_plugin_data() {
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	return get_plugin_data( IWF_PLUGIN_FILE, false, false );
}

/**
 * Add GitHub update info to the plugin update transient.
 */
function iwf_github_update_checker( $transient ) {
	if ( ! is_object( $transient ) ) {
		return $transient;
	}

	if ( ! current_user_can( 'update_plugins' ) ) {
		return $transient;
	}

	$plugin_file = IWF_PLUGIN_BASENAME;
	$plugin_data = iwf_get_plugin_data();
	$current_version = $plugin_data['Version'];

	$remote_data = get_transient( 'iwf_update_check' );

	if ( false === $remote_data ) {
		$remote_response = wp_remote_get(
			trailingslashit( IWF_GITHUB_REPO ) . 'releases/latest',
			array(
				'headers'   => array(
					'Accept'     => 'application/vnd.github.v3+json',
					'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . ' (' . get_site_url() . ')',
				),
				'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
			)
		);

		if ( is_wp_error( $remote_response ) ) {
			return $transient;
		}

		$remote_data = json_decode( wp_remote_retrieve_body( $remote_response ), true );

		if ( is_array( $remote_data ) && isset( $remote_data['tag_name'] ) ) {
			set_transient( 'iwf_update_check', $remote_data, 12 * HOUR_IN_SECONDS );
		}
	}

	if ( ! is_array( $remote_data ) || ! isset( $remote_data['tag_name'] ) ) {
		return $transient;
	}

	$remote_version = ltrim( $remote_data['tag_name'], 'v' );

	if ( version_compare( $remote_version, $current_version, '>' ) ) {
		$transient->response[ $plugin_file ] = (object) array(
			'id'          => 'integrate-wp-functions',
			'slug'        => 'integrate-wp-functions',
			'plugin'      => $plugin_file,
			'new_version' => $remote_version,
			'url'         => isset( $remote_data['html_url'] ) ? $remote_data['html_url'] : '',
			'package'     => isset( $remote_data['zipball_url'] ) ? $remote_data['zipball_url'] : '',
			'tested'      => get_bloginfo( 'version' ),
			'requires'    => '5.0',
		);
	}

	return $transient;
}
add_filter( 'site_transient_update_plugins', 'iwf_github_update_checker' );

/**
 * Clear GitHub update caches.
 */
function iwf_clear_update_cache() {
	delete_transient( 'iwf_update_check' );
	delete_site_transient( 'update_plugins' );
}
add_action( 'activated_plugin', 'iwf_clear_update_cache' );
add_action( 'deactivated_plugin', 'iwf_clear_update_cache' );

