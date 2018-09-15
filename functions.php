<?php
/**
 * WP Code Reference Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WPCodeReference
 */

namespace dimadin\WP\Theme\WPCodeReference;

use DevHub;

if ( ! defined( 'WPORGPATH' ) ) {
	/**
	 * Set directory `wporg` as if it's root of WordPress.org content.
	 *
	 * @var string
	 */
	define( 'WPORGPATH', get_stylesheet_directory() . '/wporg/' );
}

/**
 * Initialize theme.
 */
function init() {
	// Use native page title function.
	add_theme_support( 'title-tag' );

	// Do not add feed links to <head>.
	remove_theme_support( 'automatic-feed-links' );

	// Do not show any menu.
	add_filter( 'pre_wp_nav_menu', '__return_empty_string' );
}
add_action( 'init', __NAMESPACE__ . '\init' );

/**
 * Remove hooks for WP-CLI commands documentation.
 *
 * Unfortunately, it can't be done differently.
 */
function remove_wp_cli_hooks() {
	remove_action( 'init', array( 'DevHub_CLI', 'action_init_register_cron_jobs' ) );
	remove_action( 'init', array( 'DevHub_CLI', 'action_init_register_post_types' ) );
	remove_action( 'pre_get_posts', array( 'DevHub_CLI', 'action_pre_get_posts' ) );
	remove_action( 'devhub_cli_manifest_import', array( 'DevHub_CLI', 'action_devhub_cli_manifest_import' ) );
	remove_action( 'devhub_cli_markdown_import', array( 'DevHub_CLI', 'action_devhub_cli_markdown_import' ) );
	remove_action( 'breadcrumb_trail', array( 'DevHub_CLI', 'filter_breadcrumb_trail' ) );
	remove_action( 'the_content', array( 'DevHub_CLI', 'filter_the_content' ) );
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\remove_wp_cli_hooks' );

/**
 * Enqueue scripts and styles.
 */
function enqueue_styles() {
	// Style taken from wp.org.
	wp_enqueue_style( 'wporg-developer-style', get_stylesheet_directory_uri() . '/assets/css/wp4.css', [], '77' );
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_styles', 1 );

/**
 * Skip files in `tests` and `vendor` directories.
 *
 * @param bool $open    Whether the current post is open for comments.
 * @param int  $post_id The post ID.
 * @return bool
 */
function close_comments( $open, $post_id ) {
	if ( DevHub\is_parsed_post_type( get_post( $post_id )->post_type ) ) {
		return false;
	}

	return $open;
}
add_filter( 'comments_open', __NAMESPACE__ . '\close_comments', 10, 2 );

/**
 * Replace URL to one to plugin's GitHub repository.
 *
 * @param string $good_protocol_url The cleaned URL to be returned.
 * @param string $original_url      The URL prior to cleaning.
 * @param string $_context          If 'display', replace ampersands and single quotes only.
 * @return string
 */
function filter_source_file_url( $good_protocol_url, $original_url, $_context ) {
	if ( 0 === strpos( $original_url, 'https://core.trac.wordpress.org/browser/tags/' ) ) {
		$github_repository = get_option( 'wp_parser_gh_repository' );
		$current_version   = get_option( 'wp_parser_imported_wp_version' );

		if ( $github_repository && $current_version ) {
			$haystack = 'https://core.trac.wordpress.org/browser/tags/' . DevHub\get_current_version() . '/src/';
			$needle   = 'https://github.com/' . $github_repository . '/blob/' . $current_version . '/';
			return str_replace( $haystack, $needle, $original_url );
		} else {
			return '';
		}
	}

	return $good_protocol_url;
}
add_filter( 'clean_url', __NAMESPACE__ . '\filter_source_file_url', 10, 3 );

/**
 * Replace 'View on Trac' with 'View on GitHub' text.
 *
 * @param string $translation  Translated text.
 * @param string $text         Text to translate.
 * @param string $domain       Text domain. Unique identifier for retrieving translated strings.
 * @return string
 */
function replace_trac_with_github( $translation, $text, $domain ) {
	if ( 'View on Trac' === $text ) {
		$github_repository = get_option( 'wp_parser_gh_repository' );
		$current_version   = get_option( 'wp_parser_imported_wp_version' );

		if ( $github_repository && $current_version ) {
			return __( 'View on GitHub', 'wp-code-reference' );
		} else {
			return '';
		}
	}

	return $translation;
}
add_filter( 'gettext', __NAMESPACE__ . '\replace_trac_with_github', 10, 3 );

/**
 * Save version of imported plugin after import is completed.
 */
function add_imported_plugin_version() {
	$parts = explode( '/', trim( get_option( 'wp_parser_root_import_dir' ), '/' ) );

	$slug = array_reverse( $parts )[0];

	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	foreach ( get_plugins() as $plugins_path => $plugin_data ) {
		if ( 0 === strpos( $plugins_path, $slug . '/' ) ) {
			$version = $plugin_data['Version'];
			break;
		}
	}

	if ( isset( $version ) ) {
		update_option( 'wp_parser_imported_wp_version', $version );
	}
}
add_action( 'wp_parser_ending_import', __NAMESPACE__ . '\add_imported_plugin_version' );

/**
 * Skip files in `tests` and `vendor` directories.
 *
 * @param bool  $import Whether to proceed with importing the file. Default true.
 * @param array $file   File data.
 * @return bool
 */
function skip_test_vendor_files( $import, $file ) {
	if ( 0 === strpos( $file['path'], 'tests/' ) || 0 === strpos( $file['path'], 'vendor/' ) ) {
		return false;
	}

	return $import;
}
add_filter( 'wp_parser_pre_import_file', __NAMESPACE__ . '\skip_test_vendor_files', 10, 2 );
