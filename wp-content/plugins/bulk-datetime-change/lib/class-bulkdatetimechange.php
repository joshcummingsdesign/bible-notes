<?php
/**
 * Bulk Datetime Change
 *
 * @package    Bulk Datetime Change
 * @subpackage BulkDatetimeChange Main function
/*  Copyright (c) 2020- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; version 2 of the License.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bulkdatetimechange = new BulkDatetimeChange();

/** ==================================================
 * Class Main function
 *
 * @since 1.00
 */
class BulkDatetimeChange {

	/** ==================================================
	 * Add on bool
	 *
	 * @var $is_add_on_activate  is_add_on_activate.
	 */
	public $is_add_on_activate;

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {

		$this->is_add_on_activate = false;
		if ( function_exists( 'bulk_datetime_change_add_on_load_textdomain' ) ) {
			$this->is_add_on_activate = true;
		}

		add_action( 'bdtc_filter_form', array( $this, 'filter_form' ), 10, 1 );
		add_action( 'bdtc_update', array( $this, 'datetime_update' ), 10, 3 );
		add_action( 'bdtc_bulk_input', array( $this, 'bulk_input' ) );
		add_action( 'bdtc_per_page_set', array( $this, 'per_page_set' ), 10, 1 );
	}

	/** ==================================================
	 * Date time update
	 *
	 * @param int    $pid  post id.
	 * @param string $postdate  date/time.
	 * @param string $write  settings.
	 * @since 1.00
	 */
	public function datetime_update( $pid, $postdate, $write ) {

		$postdategmt = get_gmt_from_date( $postdate );
		if ( 'date' === $write ) {
			$update_array = array(
				'ID' => $pid,
				'post_date' => $postdate,
				'post_date_gmt' => $postdategmt,
			);
		} else if ( 'modified' === $write ) {
			$update_array = array(
				'ID' => $pid,
				'post_modified' => $postdate,
				'post_modified_gmt' => $postdategmt,
			);
		} else {
			$update_array = array(
				'ID' => $pid,
				'post_date' => $postdate,
				'post_date_gmt' => $postdategmt,
				'post_modified' => $postdate,
				'post_modified_gmt' => $postdategmt,
			);
		}

		global $wpdb;
		$id_array = array( 'ID' => $pid );
		$wpdb->show_errors();
		$wpdb->update( $wpdb->prefix . 'posts', $update_array, $id_array, array( '%s' ), array( '%d' ) );
	}

	/** ==================================================
	 * Filter form
	 *
	 * @param int $uid  current user id.
	 * @since 1.00
	 */
	public function filter_form( $uid ) {

		$scriptname = admin_url( 'admin.php?page=bulkdatetimechange' );

		if ( ! $this->is_add_on_activate ) {
			?>
			<div class="cp_tooltip_update">
				<span class="cp_tooltip_update_text"><?php esc_html_e( 'Currently, only the default WordPress post type is supported. Other custom post types (e.g. WooCommerce products, orders) require an add-on to be supported.', 'bulk-datetime-change' ); ?></span>
			<?php
		}
		?>
		<form method="post" action="<?php echo esc_url( $scriptname ); ?>">
		<?php
		wp_nonce_field( 'bdtc_filter', 'bulk_datetime_change_filter' );

		$post_types = get_user_option( 'bulkdatetimechange_all_post_type', $uid )
		?>
		<select name="post_type">
		<?php
		$type_filter = get_user_option( 'bulkdatetimechange_filter_post_type', $uid );
		$selected_type = false;
		foreach ( $post_types as $key => $value ) {
			if ( 1 === count( $type_filter ) && in_array( $key, $type_filter ) ) {
				?>
				<option value="<?php echo esc_attr( $key ); ?>" selected><?php echo esc_html( $value ); ?></option>
				<?php
				$selected_type = true;
			} else {
				?>
				<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
				<?php
			}
		}
		$all_keys = implode( ',', array_keys( $post_types ) );
		if ( ! $selected_type ) {
			?>
			<option value="<?php echo esc_attr( $all_keys ); ?>" selected><?php esc_html_e( 'All post types', 'bulk-datetime-change' ); ?></option>
			<?php
		} else {
			?>
			<option value="<?php echo esc_attr( $all_keys ); ?>"><?php esc_html_e( 'All post types', 'bulk-datetime-change' ); ?></option>
			<?php
		}
		?>
		</select>
		<?php
		if ( ! $this->is_add_on_activate ) {
			?>
			</div>
			<?php
		}

		if ( current_user_can( 'manage_options' ) ) {
			$users = get_users(
				array(
					'orderby' => 'nicename',
					'order' => 'ASC',
				)
			);
			$user_filter = get_user_option( 'bulkdatetimechange_filter_user', $uid );
			?>
			<select name="user_id">
			<?php
			$selected_user = false;
			foreach ( $users as $user ) {
				if ( user_can( $user->ID, 'upload_files' ) ) {
					if ( $user_filter == $user->ID ) {
						?>
						<option value="<?php echo esc_attr( $user->ID ); ?>" selected><?php echo esc_html( $user->display_name ); ?></option>
						<?php
						$selected_user = true;
					} else {
						?>
						<option value="<?php echo esc_attr( $user->ID ); ?>"><?php echo esc_html( $user->display_name ); ?></option>
						<?php
					}
				}
			}
			if ( ! $selected_user ) {
				?>
				<option value="" selected><?php esc_html_e( 'All users', 'bulk-datetime-change' ); ?></option>
				<?php
			} else {
				?>
				<option value=""><?php esc_html_e( 'All users', 'bulk-datetime-change' ); ?></option>
				<?php
			}
			?>
			</select>
			<?php
		}

		$categories = get_categories(
			array(
				'hide_empty' => false,
				'orderby' => 'name',
				'order' => 'ASC',
			)
		);
		$cat_filter = get_user_option( 'bulkdatetimechange_filter_category', $uid );
		?>
		<select name="category_id">
		<?php
		$selected_category = false;
		foreach ( $categories as $category ) {
			if ( $cat_filter == $category->term_id ) {
				?>
				<option value="<?php echo esc_attr( $category->term_id ); ?>" selected><?php echo esc_html( $category->name ); ?></option>
				<?php
				$selected_category = true;
			} else {
				?>
				<option value="<?php echo esc_attr( $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?></option>
				<?php
			}
		}
		if ( ! $selected_category ) {
			?>
			<option value="" selected><?php esc_html_e( 'All categories', 'bulk-datetime-change' ); ?></option>
			<?php
		} else {
			?>
			<option value=""><?php esc_html_e( 'All categories', 'bulk-datetime-change' ); ?></option>
			<?php
		}
		?>
		</select>
		<?php

		$all_post_type = array_keys( $post_types );
		$args = array(
			'post_type'      => $all_post_type,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		$all_posts = get_posts( $args );

		foreach ( $all_posts as $post ) {
			$year = get_the_time( 'Y', $post->ID );
			$month = get_the_time( 'F', $post->ID );
			/* translators: month year for media archive */
			$year_month = sprintf( __( '%1$s %2$s', 'bulk-datetime-change' ), $month, $year );
			$archive_list[ $year_month ][] = $post->ID;
		}
		$monthly_filter = get_user_option( 'bulkdatetimechange_filter_monthly', get_current_user_id() );
		?>
		<select name="monthly">
		<?php
		$selected_monthly = false;
		if ( ! empty( $archive_list ) ) {
			foreach ( $archive_list as $key => $value ) {
				$pid_csv = implode( ',', $value );
				if ( $value == $monthly_filter ) {
					?>
					<option value="<?php echo esc_attr( $pid_csv ); ?>" selected><?php echo esc_html( $key ); ?></option>
					<?php
					$selected_monthly = true;
				} else {
					?>
					<option value="<?php echo esc_attr( $pid_csv ); ?>"><?php echo esc_html( $key ); ?></option>
					<?php
				}
			}
		}
		if ( ! $selected_monthly ) {
			?>
			<option value="" selected><?php esc_html_e( 'All dates', 'bulk-datetime-change' ); ?></option>
			<?php
		} else {
			?>
			<option value=""><?php esc_html_e( 'All dates', 'bulk-datetime-change' ); ?></option>
			<?php
		}
		?>
		</select>
		<?php

		$ext_mime = array();
		$mimes = get_allowed_mime_types( $uid );
		foreach ( $mimes as $type => $mime ) {
			$types = explode( '|', $type );
			foreach ( $types as $value ) {
				$ext_mime[ $value ] = $mime;
			}
		}
		$type_mime = array();
		$type_text = array();
		$type_exts_arr = wp_get_ext_types();
		foreach ( $type_exts_arr as $type => $exts ) {
			$ext_mimes = array();
			foreach ( $exts as $value ) {
				if ( array_key_exists( $value, $ext_mime ) ) {
					$ext_mimes[] = $ext_mime[ $value ];
				}
			}
			$ext_mimes = array_filter( $ext_mimes );
			$ext_mimes_csv = implode( ',', $ext_mimes );
			if ( '' == $type ) {
				$type = 'other';
			}
			$type_mime[ $type ] = $ext_mimes_csv;
			switch ( $type ) {
				case 'image':
					$type_text[ $type ] = __( 'Image', 'bulk-datetime-change' );
					break;
				case 'audio':
					$type_text[ $type ] = __( 'Audio', 'bulk-datetime-change' );
					break;
				case 'video':
					$type_text[ $type ] = __( 'Video', 'bulk-datetime-change' );
					break;
				case 'document':
					$type_text[ $type ] = __( 'Document', 'bulk-datetime-change' );
					break;
				case 'spreadsheet':
					$type_text[ $type ] = __( 'Spreadsheet', 'bulk-datetime-change' );
					break;
				case 'interactive':
					$type_text[ $type ] = __( 'Interactive', 'bulk-datetime-change' );
					break;
				case 'text':
					$type_text[ $type ] = __( 'Text', 'bulk-datetime-change' );
					break;
				case 'archive':
					$type_text[ $type ] = __( 'Archive', 'bulk-datetime-change' );
					break;
				case 'code':
					$type_text[ $type ] = __( 'Code', 'bulk-datetime-change' );
					break;
				case 'other':
					$type_text[ $type ] = __( 'Other', 'bulk-datetime-change' );
					break;
				default:
					$type_text[ $type ] = $type;
					break;
			}
		}

		$mime_filter = get_user_option( 'bulkdatetimechange_filter_mime_type', get_current_user_id() );
		?>
		<select name="mime_type">
		<?php
		$selected_mime_type = false;
		foreach ( $type_mime as $type => $mime ) {
			if ( $mime_filter === $mime ) {
				?>
				<option value="<?php echo esc_attr( $mime ); ?>" selected><?php echo esc_html( $type_text[ $type ] ); ?></option>
				<?php
				$selected_mime_type = true;
			} else {
				?>
				<option value="<?php echo esc_attr( $mime ); ?>"><?php echo esc_html( $type_text[ $type ] ); ?></option>
				<?php
			}
		}
		if ( ! $selected_mime_type ) {
			?>
			<option value="" selected><?php esc_html_e( 'All media items', 'bulk-datetime-change' ); ?></option>
			<?php
		} else {
			?>
			<option value=""><?php esc_html_e( 'All media items', 'bulk-datetime-change' ); ?></option>
			<?php
		}
		?>
		</select>

		<div class="cp_tooltip_change">
		<span class="cp_tooltip_change_text"><?php esc_html_e( 'Can filter by directly entering the relevant text: post types, users, categories, date/time, and media. Can also refine your search by entering post tags.', 'bulk-datetime-change' ); ?></span>
		<?php
		$search_text = get_user_option( 'bulkdatetimechange_search_text', $uid );
		if ( ! $search_text ) {
			?>
			<input style="vertical-align: middle;" name="search_text" type="text" value="" placeholder="<?php echo esc_attr__( 'Search', 'bulk-datetime-change' ); ?>">
			<?php
		} else {
			?>
			<input style="vertical-align: middle;" name="search_text" type="text" value="<?php echo esc_attr( $search_text ); ?>">
			<?php
		}
		?>
		</span>
		</div>
		<?php

		submit_button( __( 'Filter', 'bulk-datetime-change' ), 'large', 'bulk-datetime-change-filter', false );
		?>
		</form>
		<?php
	}

	/** ==================================================
	 * Bulk input form
	 *
	 * @since 1.00
	 */
	public function bulk_input() {

		if ( function_exists( 'wp_date' ) ) {
			$now_date_time = wp_date( 'Y-m-d H:i:s' );
		} else {
			$now_date_time = date_i18n( 'Y-m-d H:i:s' );
		}
		?>
		<div style="margin: 0px; text-align: right;">
			<div class="cp_tooltip_change">
			<?php esc_html_e( 'Bulk Change', 'bulk-datetime-change' ); ?> : 
			<span class="cp_tooltip_change_text"><?php esc_html_e( 'Changes made by pressing this "Change" button are temporary. To confirm the change, check the checkbox for the date and time you wish to change and press the "Update" button.', 'bulk-datetime-change' ); ?></span>
			<input type="text" id="datetimepicker-bdtc" name="all_change_datetime" value="<?php echo esc_html( $now_date_time ); ?>" />
			<?php submit_button( __( 'Change', 'bulk-datetime-change' ), 'large', 'all_change', false ); ?>
			</div>
		</div>
		<?php
	}

	/** ==================================================
	 * Per page input form
	 *
	 * @param int $uid  user ID.
	 * @since 1.01
	 */
	public function per_page_set( $uid ) {

		?>
		<div style="margin: 0px; text-align: right;">
			<?php esc_html_e( 'Number of items per page:', 'bulk-datetime-change' ); ?><input type="number" step="1" min="1" max="9999" style="width: 80px;" name="per_page" value="<?php echo esc_attr( get_user_option( 'bdtc_per_page', $uid ) ); ?>" form="bulkdatetimechange_forms" />
			<?php submit_button( __( 'Change', 'bulk-datetime-change' ), 'large', 'per_page_change', false, array( 'form' => 'bulkdatetimechange_forms' ) ); ?>
		</div>
		<?php
	}
}


