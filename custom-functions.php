<?php
/**
 * Plugin Name: Integrate WP Theme Custom Functions
 * Plugin URI: https://github.com/jones3036/integrate-wp-functions
 * Description: Site-specific WordPress tweaks that survive theme updates.
 * Version: 1.4.1
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

if ( ! defined( 'IWF_GITHUB_TOKEN_OPTION' ) ) {
	define( 'IWF_GITHUB_TOKEN_OPTION', 'iwf_github_token' );
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
		'hide_wp_version'                => true,
		'disable_user_enumeration'       => true,
		'disable_login_errors'           => true,
		'force_admin_ssl'                => true,
		'remove_howdy_greeting'          => true,
		'disable_jetpack'                => true,
		'force_strong_passwords'         => true,
		'custom_css'                      => false,
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
	register_setting( 'iwf_settings_group', IWF_GITHUB_TOKEN_OPTION, 'iwf_sanitize_token' );

	add_settings_section(
		'iwf_features_section',
		__( 'Feature toggles', 'integrate-wp-functions' ),
		'iwf_settings_section_description',
		'iwf_settings'
	);

	add_settings_section(
		'iwf_github_section',
		__( 'GitHub Private Repository', 'integrate-wp-functions' ),
		'iwf_github_section_description',
		'iwf_settings'
	);

	add_settings_section(
		'iwf_custom_css_section',
		__( 'Custom CSS', 'integrate-wp-functions' ),
		'iwf_custom_css_section_description',
		'iwf_settings'
	);

	add_settings_field(
		'iwf_github_token',
		__( 'GitHub Personal Access Token', 'integrate-wp-functions' ),
		'iwf_render_token_field',
		'iwf_settings',
		'iwf_github_section'
	);

	add_settings_field(
		'iwf_custom_css',
		__( 'Custom CSS Code', 'integrate-wp-functions' ),
		'iwf_render_custom_css_field',
		'iwf_settings',
		'iwf_custom_css_section'
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
		'hide_wp_version'              => __( 'Hide WordPress version from frontend', 'integrate-wp-functions' ),
		'disable_user_enumeration'     => __( 'Disable user enumeration via ?author= archives', 'integrate-wp-functions' ),
		'disable_login_errors'         => __( 'Disable login error hints (hide if user exists)', 'integrate-wp-functions' ),
		'force_admin_ssl'              => __( 'Force SSL on admin login', 'integrate-wp-functions' ),
		'remove_howdy_greeting'        => __( 'Remove "Howdy" greeting in admin bar', 'integrate-wp-functions' ),
		'disable_jetpack'              => __( 'Disable Jetpack integration', 'integrate-wp-functions' ),
		'force_strong_passwords'       => __( 'Force strong password requirements', 'integrate-wp-functions' ),
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
 * Sanitize GitHub token.
 */
function iwf_sanitize_token( $input ) {
	if ( empty( $input ) ) {
		return '';
	}
	return sanitize_text_field( wp_unslash( $input ) );
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
 * Render GitHub token field.
 */
function iwf_render_token_field() {
	$token = get_option( IWF_GITHUB_TOKEN_OPTION );
	$masked_token = ! empty( $token ) ? '••••••••••••••••' . substr( $token, -4 ) : '';
	?>
	<input 
		type="password" 
		id="iwf_github_token" 
		name="<?php echo esc_attr( IWF_GITHUB_TOKEN_OPTION ); ?>" 
		value="" 
		placeholder="<?php echo esc_attr( $masked_token ); ?>"
		style="min-width: 300px;"
	/>
	<p class="description">
		<?php esc_html_e( 'Optional: For private repositories, enter a GitHub Personal Access Token (PAT) with repo access. Leave blank for public repos.', 'integrate-wp-functions' ); ?>
	</p>
	<?php
}

/**
 * Settings section description.
 */
function iwf_settings_section_description() {
	echo '<p>' . esc_html__( 'Enable or disable site-specific features without editing plugin code.', 'integrate-wp-functions' ) . '</p>';
}

/**
 * GitHub section description.
 */
function iwf_github_section_description() {
	echo '<p>' . esc_html__( 'Configure GitHub access for private repository updates.', 'integrate-wp-functions' ) . '</p>';
}

/**
 * Custom CSS section description.
 */
function iwf_custom_css_section_description() {
	 echo '<p>' . esc_html__( 'Add custom CSS to override site styles. This will be output in the site header.', 'integrate-wp-functions' ) . '</p>';
}

/**
 * Render custom CSS textarea field.
 */
function iwf_render_custom_css_field() {
	$settings = iwf_get_settings();
	$custom_css = isset( $settings['custom_css'] ) ? $settings['custom_css'] : '';
	?>
	<textarea
		id="iwf_custom_css"
		name="<?php echo esc_attr( IWF_OPTION_KEY ); ?>[custom_css]"
		rows="10"
		cols="50"
		class="large-text code"
		style="font-family: monospace;"
	><?php echo esc_textarea( $custom_css ); ?></textarea>
	<p class="description">
		<?php esc_html_e( 'Enter your custom CSS here. This will be added to the <head> section of your site.', 'integrate-wp-functions' ); ?>
	</p>
	<?php
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

	if ( iwf_setting_enabled( 'hide_wp_version' ) ) {
		add_filter( 'the_generator', '__return_empty_string' );
		remove_action( 'wp_head', 'wp_generator' );
	}

	if ( iwf_setting_enabled( 'disable_user_enumeration' ) ) {
		add_action( 'template_redirect', 'iwf_disable_user_enumeration' );
	}

	if ( iwf_setting_enabled( 'disable_login_errors' ) ) {
		add_filter( 'login_errors', '__return_empty_string' );
	}

	if ( iwf_setting_enabled( 'force_admin_ssl' ) ) {
		if ( defined( 'FORCE_SSL_ADMIN' ) === false ) {
			define( 'FORCE_SSL_ADMIN', true );
		}
	}

	if ( iwf_setting_enabled( 'remove_howdy_greeting' ) ) {
		add_filter( 'admin_bar_menu', 'iwf_remove_howdy_greeting', 25 );
	}

	if ( iwf_setting_enabled( 'disable_jetpack' ) ) {
		add_filter( 'jetpack_just_in_time_msgs', '__return_false' );
		add_filter( 'jetpack_show_promotions', '__return_false' );
		deactivate_plugins( 'jetpack/jetpack.php' );
	}

	if ( iwf_setting_enabled( 'force_strong_passwords' ) ) {
		add_action( 'user_profile_update_errors', 'iwf_validate_password_strength', 10, 3 );
	}

	if ( iwf_setting_enabled( 'custom_css' ) ) {
		add_action( 'wp_head', 'iwf_output_custom_css', 99 );
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
 * Disable user enumeration via ?author= archives.
 */
function iwf_disable_user_enumeration() {
	if ( is_author() ) {
		wp_safe_redirect( home_url() );
		exit;
	}
}

/**
 * Remove "Howdy" greeting from admin bar.
 */
function iwf_remove_howdy_greeting( $wp_admin_bar ) {
	if ( isset( $wp_admin_bar->get_node( 'my-account' )->title ) ) {
		$user_node = $wp_admin_bar->get_node( 'my-account' );
		if ( $user_node ) {
			$user_node->title = str_replace( 'Howdy, ', '', $user_node->title );
			$wp_admin_bar->add_node( $user_node );
		}
	}
	return $wp_admin_bar;
}

/**
 * Validate password strength.
 */
function iwf_validate_password_strength( $errors, $update, $user_data ) {
	if ( empty( $_POST['pass1'] ) || empty( $_POST['pass2'] ) ) {
		return;
	}

	$password = sanitize_text_field( wp_unslash( $_POST['pass1'] ) );

	// Check minimum length (12 characters)
	if ( strlen( $password ) < 12 ) {
		$errors->add( 'password_too_short', __( 'Password must be at least 12 characters long.', 'integrate-wp-functions' ) );
	}

	// Check for uppercase
	if ( ! preg_match( '/[A-Z]/', $password ) ) {
		$errors->add( 'password_no_uppercase', __( 'Password must contain at least one uppercase letter.', 'integrate-wp-functions' ) );
	}

	// Check for lowercase
	if ( ! preg_match( '/[a-z]/', $password ) ) {
		$errors->add( 'password_no_lowercase', __( 'Password must contain at least one lowercase letter.', 'integrate-wp-functions' ) );
	}

	// Check for numbers
	if ( ! preg_match( '/[0-9]/', $password ) ) {
		$errors->add( 'password_no_numbers', __( 'Password must contain at least one number.', 'integrate-wp-functions' ) );
	}

	// Check for special characters
	if ( ! preg_match( '/[!@#$%^&*()_+\-=\[\]{};:"\',.?\/]/', $password ) ) {
		$errors->add( 'password_no_special', __( 'Password must contain at least one special character (!@#$%^&* etc).', 'integrate-wp-functions' ) );
	}
}

/**
 * Output custom CSS in the site header.
 */
function iwf_output_custom_css() {
	$settings = iwf_get_settings();
	$custom_css = isset( $settings['custom_css'] ) ? $settings['custom_css'] : '';

	if ( ! empty( $custom_css ) ) {
		echo "\n" . '<!-- Custom CSS from Integrate WP Functions -->' . "\n";
		echo '<style type="text/css">' . "\n";
		echo wp_strip_all_tags( $custom_css ) . "\n";
		echo '</style>' . "\n";
	}
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
 * Get GitHub token from constant or options.
 */
function iwf_get_github_token() {
	// Check for constant first (most secure)
	if ( defined( 'IWF_GITHUB_TOKEN' ) ) {
		return sanitize_text_field( IWF_GITHUB_TOKEN );
	}

	// Fall back to option
	$token = get_option( IWF_GITHUB_TOKEN_OPTION );
	return ! empty( $token ) ? sanitize_text_field( $token ) : '';
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
		$headers = array(
			'Accept'     => 'application/vnd.github.v3+json',
			'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . ' (' . get_site_url() . ')',
		);

		$token = iwf_get_github_token();
		if ( ! empty( $token ) ) {
			$headers['Authorization'] = 'token ' . $token;
		}

		$remote_response = wp_remote_get(
			trailingslashit( IWF_GITHUB_REPO ) . 'releases/latest',
			array(
				'headers'   => $headers,
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

