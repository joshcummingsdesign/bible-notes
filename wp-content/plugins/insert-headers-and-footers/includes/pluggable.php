<?php

if ( ! function_exists( 'wpcode_get_snippet' ) ) {
	/**
	 * Load a snippet by id, WP_Post or array.
	 *
	 * @param array|int|WP_Post $snippet Load a snippet by id, WP_Post or array.
	 *
	 * @return WPCode_Snippet
	 */
	function wpcode_get_snippet( $snippet ) {
		return new WPCode_Snippet( $snippet );
	}
}

if ( ! function_exists( 'wpcode_get_post_type' ) ) {
	/**
	 * Get the post type we use for snippets.
	 *
	 * @return string
	 */
	function wpcode_get_post_type() {
		return 'wpcode';
	}
}
