<?php
/**
 * Plugin Name: LINE APP Tags RSS Feed
 * Plugin URI:
 * Description:
 * Version:     1.0
 * Author:      CGG
 * Author URI:  CGG
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

namespace cggLineAPP\LineAppRSSFeed;

/**
 * Registers our custom feed
 */
function register() {
	add_feed( 'line-tags-feed', __NAMESPACE__ . '\generate_content' );
}
add_action( 'init', __NAMESPACE__ . '\register' );

/**
 * Generates the content of our custom feed
 */
function generate_content() {

	add_filter( 'the_content_feed', __NAMESPACE__ . '\prepend_thumbnail' );
	add_filter( 'the_excerpt_rss',  __NAMESPACE__ . '\prepend_thumbnail' );
	$dir = plugin_dir_path( __FILE__ );

	if ( file_exists( ABSPATH . 'wp-content/plugins/wp-line-feed/feed-rss2.php' ) ) {
		require( ABSPATH . 'wp-content/plugins/wp-line-feed/feed-rss2.php' );
	}

	remove_filter( 'the_content_feed', __NAMESPACE__ . '\prepend_thumbnail' );
	remove_filter( 'the_excerpt_rss',  __NAMESPACE__ . '\prepend_thumbnail' );
}

/**
 * Prepends the post's featured image to the feed content
 *
 * @param string $content The feed content.
 *
 * @return string The filtered content.
 */
function prepend_thumbnail( $content ) {

	if ( ! has_post_thumbnail() ) {
		return $content;
	}

	$thumbnail_html = sprintf( "<p>%s</p>\n",
		get_the_post_thumbnail()
	);

	return $thumbnail_html . $content;
}
