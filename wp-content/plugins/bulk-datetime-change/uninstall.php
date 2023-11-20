<?php
/**
 * Uninstall
 *
 * @package Bulk Datetime Change
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;

/* For Single site */
if ( ! is_multisite() ) {
	$blogusers = get_users( array( 'fields' => array( 'ID' ) ) );
	foreach ( $blogusers as $user ) {
		delete_user_option( $user->ID, 'bulkdatetimechange', false );
		delete_user_option( $user->ID, 'bdtc_per_page', false );
		delete_user_option( $user->ID, 'bulkdatetimechange_all_post_type', false );
		delete_user_option( $user->ID, 'bulkdatetimechange_filter_post_type', false );
		delete_user_option( $user->ID, 'bulkdatetimechange_filter_category', false );
		delete_user_option( $user->ID, 'bulkdatetimechange_filter_mime_type', false );
		delete_user_option( $user->ID, 'bulkdatetimechange_filter_post_status', false );
		delete_user_option( $user->ID, 'bulkdatetimechange_filter_user', false );
		delete_user_option( $user->ID, 'bulkdatetimechange_search_text', false );
		delete_user_option( $user->ID, 'bulkdatetimechange_current_logs', false );
	}
} else {
	/* For Multisite */
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->prefix}blogs" );
	$original_blog_id = get_current_blog_id();
	foreach ( $blog_ids as $blogid ) {
		switch_to_blog( $blogid );
		$blogusers = get_users(
			array(
				'blog_id' => $blogid,
				'fields' => array( 'ID' ),
			)
		);
		foreach ( $blogusers as $user ) {
			delete_user_option( $user->ID, 'bulkdatetimechange', false );
			delete_user_option( $user->ID, 'bdtc_per_page', false );
			delete_user_option( $user->ID, 'bulkdatetimechange_all_post_type', false );
			delete_user_option( $user->ID, 'bulkdatetimechange_filter_post_type', false );
			delete_user_option( $user->ID, 'bulkdatetimechange_filter_category', false );
			delete_user_option( $user->ID, 'bulkdatetimechange_filter_mime_type', false );
			delete_user_option( $user->ID, 'bulkdatetimechange_filter_post_status', false );
			delete_user_option( $user->ID, 'bulkdatetimechange_filter_user', false );
			delete_user_option( $user->ID, 'bulkdatetimechange_search_text', false );
			delete_user_option( $user->ID, 'bulkdatetimechange_current_logs', false );
		}
	}
	switch_to_blog( $original_blog_id );
}
