<?php
/**
 * Bulk Datetime Change
 *
 * @package    Bulk Datetime Change
 * @subpackage Bulk Datetime Change Management screen
	Copyright (c) 2020- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
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

$bulkdatetimechangeadmin = new BulkDatetimeChangeAdmin();

/** ==================================================
 * Management screen
 */
class BulkDatetimeChangeAdmin {

	/** ==================================================
	 * Construct
	 *
	 * @since 1.00
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'register_settings' ) );

		add_action( 'admin_menu', array( $this, 'add_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_custom_wp_admin_style' ) );
		add_filter( 'plugin_action_links', array( $this, 'settings_link' ), 10, 2 );

		if ( ! class_exists( 'TT_BulkDatetimeChange_List_Table' ) ) {
			require_once __DIR__ . '/class-tt-bulkdatetimechange-list-table.php';
		}
	}

	/** ==================================================
	 * Add a "Settings" link to the plugins page
	 *
	 * @param  array  $links  links array.
	 * @param  string $file   file.
	 * @return array  $links  links array.
	 * @since 1.00
	 */
	public function settings_link( $links, $file ) {
		static $this_plugin;
		if ( empty( $this_plugin ) ) {
			$this_plugin = 'bulk-datetime-change/bulkdatetimechange.php';
		}
		if ( $file == $this_plugin ) {
			$links[] = '<a href="' . admin_url( 'admin.php?page=bulkdatetimechange' ) . '">Bulk Datetime Change</a>';
			$links[] = '<a href="' . admin_url( 'admin.php?page=bulkdatetimechange-settings' ) . '">' . __( 'Settings' ) . '</a>';
		}
		return $links;
	}

	/** ==================================================
	 * Add page
	 *
	 * @since 1.00
	 */
	public function add_pages() {
		add_menu_page(
			'Bulk Datetime Change',
			'Bulk Datetime Change',
			'publish_posts',
			'bulkdatetimechange',
			array( $this, 'manage_page' ),
			'dashicons-clock'
		);
		add_submenu_page(
			'bulkdatetimechange',
			__( 'Settings' ),
			__( 'Settings' ),
			'publish_posts',
			'bulkdatetimechange-settings',
			array( $this, 'settings_page' )
		);
	}

	/** ==================================================
	 * Add Css and Script
	 *
	 * @since 1.00
	 */
	public function load_custom_wp_admin_style() {
		if ( $this->is_my_plugin_screen() ) {
			wp_enqueue_style( 'tooltip', plugin_dir_url( __DIR__ ) . 'css/tooltip.css', array(), '1.0.0' );
			wp_enqueue_style( 'jquery-datetimepicker', plugin_dir_url( __DIR__ ) . 'css/jquery.datetimepicker.css', array(), '2.3.4' );
			wp_enqueue_script( 'jquery' );

			$bulkdatetimechange_settings = get_user_option( 'bulkdatetimechange', get_current_user_id() );
			if ( $bulkdatetimechange_settings['picker'] ) {
				wp_enqueue_script( 'jquery-datetimepicker', plugin_dir_url( __DIR__ ) . 'js/jquery.datetimepicker.js', null, '2.3.4', true );
			}
			wp_enqueue_script( 'jquery-datetimepicker-bdtc', plugin_dir_url( __DIR__ ) . 'js/jquery.datetimepicker.bdtc.js', array( 'jquery' ), array(), '1.00', false );
			wp_enqueue_script( 'bulkdatetimechange-js', plugin_dir_url( __DIR__ ) . 'js/jquery.bulkdatetimechange.js', array( 'jquery' ), array(), '1.00', false );
		}
	}

	/** ==================================================
	 * For only admin style
	 *
	 * @since 1.00
	 */
	private function is_my_plugin_screen() {
		$screen = get_current_screen();
		if ( is_object( $screen ) && 'toplevel_page_bulkdatetimechange' === $screen->id ||
				is_object( $screen ) && 'bulk-datetime-change_page_bulkdatetimechange-settings' === $screen->id ) {
			return true;
		} else {
			return false;
		}
	}

	/** ==================================================
	 * Main
	 *
	 * @since 1.00
	 */
	public function manage_page() {

		if ( ! current_user_can( 'publish_posts' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}

		$bulkdatetimechange_settings = get_user_option( 'bulkdatetimechange', get_current_user_id() );
		$scriptname = admin_url( 'admin.php?page=bulkdatetimechange' );

		if ( isset( $_POST['per_page_change'] ) && ! empty( $_POST['per_page_change'] ) ) {
			if ( check_admin_referer( 'bdtc_update', 'bulk_datetime_change_update' ) ) {
				if ( ! empty( $_POST['per_page'] ) ) {
					update_user_option( get_current_user_id(), 'bdtc_per_page', absint( $_POST['per_page'] ) );
				}
			}
		}

		if ( isset( $_POST['bulk-datetime-change-update1'] ) && ! empty( $_POST['bulk-datetime-change-update1'] ) ||
				isset( $_POST['bulk-datetime-change-update2'] ) && ! empty( $_POST['bulk-datetime-change-update2'] ) ) {
			if ( check_admin_referer( 'bdtc_update', 'bulk_datetime_change_update' ) ) {
				$update_ids = array();
				if ( ! empty( $_POST['bulk_date_check'] ) ) {
					$update_ids = filter_var(
						wp_unslash( $_POST['bulk_date_check'] ),
						FILTER_CALLBACK,
						array(
							'options' => function ( $value ) {
								return absint( $value );
							},
						)
					);
				}
				$update_datetime = array();
				if ( ! empty( $_POST['bulk_date_update'] ) ) {
					$update_datetime = filter_var(
						wp_unslash( $_POST['bulk_date_update'] ),
						FILTER_CALLBACK,
						array(
							'options' => function ( $value ) {
								return sanitize_text_field( $value );
							},
						)
					);
				}
				if ( ! empty( $update_ids ) && ! empty( $update_datetime ) ) {
					$messages = array();
					$log_messages = array();
					foreach ( $update_ids as $update_id ) {
						$post = get_post( $update_id );
						if ( 'modified' === $bulkdatetimechange_settings['method'] ) {
							$org_datetime = $post->post_modified;
						} else {
							$org_datetime = $post->post_date;
						}
						do_action( 'bdtc_update', $update_id, $update_datetime[ $update_id ], $bulkdatetimechange_settings['write'] );
						if ( 'date_modified' === $bulkdatetimechange_settings['write'] ) {
							$change = __( 'Posted And Modified', 'bulk-datetime-change' );
						} else if ( 'date' === $bulkdatetimechange_settings['write'] ) {
							$change = __( 'Only Posted', 'bulk-datetime-change' );
						} else if ( 'modified' === $bulkdatetimechange_settings['write'] ) {
							$change = __( 'Only Modified', 'bulk-datetime-change' );
						}
						/* translators: %1$d ID %2$s Org Datetime %3$s Update Datetime */
						$message_txt = sprintf( __( 'ID: %1$d Title: %2$s Date/time: %3$s -> %4$s Change: %5$s', 'bulk-datetime-change' ), $update_id, $post->post_title, $org_datetime, $update_datetime[ $update_id ], $change );
						$messages[] = '[' . $message_txt . ']';
						$log_messages[] = $message_txt;
					}
					krsort( $log_messages );
					if ( 0 < count( $messages ) ) {
						/* translators: %1$d: message count %2$s: message */
						echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html( sprintf( __( 'Updated %1$d items. %2$s', 'bulk-datetime-change' ), count( $messages ), implode( ' ', $messages ) ) ) . '</li></ul></div>';
					}
					$logs = get_user_option( 'bulkdatetimechange_current_logs', get_current_user_id() );
					if ( ! empty( $logs ) ) {
						$log_messages = array_merge( $log_messages, $logs );
					}
					$log_messages = array_slice( $log_messages, 0, 100 );
					update_user_option( get_current_user_id(), 'bulkdatetimechange_current_logs', $log_messages );
				}
			}
		}

		?>
		<div class="wrap">

		<h2>Bulk Datetime Change
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=bulkdatetimechange-settings' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Settings' ); ?></a>
		</h2>
		<div style="clear: both;"></div>

			<details>
			<summary><strong><?php esc_html_e( 'Various links of this plugin', 'bulk-datetime-change' ); ?></strong></summary>
			<?php $this->credit(); ?>
			</details>

			<div style="margin: 5px; padding: 5px;">
				<form method="post" id="bulkdatetimechange_forms">
				<?php wp_nonce_field( 'bdtc_update', 'bulk_datetime_change_update' ); ?>
				</form>
				<?php
				$bulk_datetime_change_list_table = new TT_BulkDatetimeChange_List_Table();
				$bulk_datetime_change_list_table->prepare_items();
				?>
				<div class="cp_tooltip_update">
				<?php
				submit_button( __( 'Update' ), 'primary', 'bulk-datetime-change-update1', false, array( 'form' => 'bulkdatetimechange_forms' ) );
				?>
				<span class="cp_tooltip_update_text"><?php esc_html_e( 'This "Update" button changes the date and time of the items checked in the checkbox.', 'bulk-datetime-change' ); ?></span>
				</div>
				<?php
				do_action( 'bdtc_per_page_set', get_current_user_id() );
				$bulk_datetime_change_list_table->display();
				?>
				<div class="cp_tooltip_update">
				<?php
				submit_button( __( 'Update' ), 'primary', 'bulk-datetime-change-update2', false, array( 'form' => 'bulkdatetimechange_forms' ) );
				?>
				<span class="cp_tooltip_update_text"><?php esc_html_e( 'This "Update" button changes the date and time of the items checked in the checkbox.', 'bulk-datetime-change' ); ?></span>
				</div>
			</div>

		</div>
		<?php
	}

	/** ==================================================
	 * Settings page
	 *
	 * @since 1.00
	 */
	public function settings_page() {

		if ( ! current_user_can( 'publish_posts' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}

		$bulkdatetimechange = new BulkDatetimeChange();

		$this->options_updated();

		$bulkdatetimechange_settings = get_user_option( 'bulkdatetimechange', get_current_user_id() );

		$scriptname = admin_url( 'admin.php?page=bulkdatetimechange-settings' );

		?>
		<div class="wrap">

		<h2><a href="<?php echo esc_url( admin_url( 'admin.php?page=bulkdatetimechange' ) ); ?>" style="text-decoration: none;">Bulk Datetime Change</a>&nbsp;&nbsp;<?php esc_html_e( 'Settings' ); ?>
		</h2>
		<div style="clear: both;"></div>

			<div class="wrap">
				<details style="margin-bottom: 5px;">
				<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><?php esc_html_e( 'Add on', 'bulk-datetime-change' ); ?></summary>
				<?php
				if ( $bulkdatetimechange->is_add_on_activate ) {
					do_action( 'bdtc_addon_license' );
				} else {
					$plugin_base_dir = untrailingslashit( plugin_dir_path( __DIR__ ) );
					$slugs = explode( '/', $plugin_base_dir );
					$slug = end( $slugs );
					$plugin_dir = untrailingslashit( rtrim( $plugin_base_dir, $slug ) );
					$add_on_dir = $plugin_dir . '/bulk-datetime-change-add-on';
					?>
					<div style="display: block;padding:5px 5px">
						<h2>Bulk Datetime Change Add On</h2>
						<p class="description">
						<?php esc_html_e( 'This add-on will add to "Bulk Datetime Change" the ability to add custom post types, read media Exif times, and automatically change some random posts to the current date and time at 24 hour intervals.', 'bulk-datetime-change' ); ?>
						</p>
						<div style="margin: 0 10px 10px; ">
						<?php
						if ( is_dir( $add_on_dir ) ) {
							?>
							<span style="color: red;"><?php esc_html_e( 'Installed & Deactivated', 'bulk-datetime-change' ); ?>
							<?php
						} else {
							?>
							<a href="<?php echo esc_url( __( 'https://shop.riverforest-wp.info/bulk-datetime-change-add-on/', 'bulk-datetime-change' ) ); ?>" target="_blank" rel="noopener noreferrer"><?php submit_button( __( 'BUY', 'bulk-datetime-change' ), 'primary', 'buylink', false ); ?></a>
							<?php
						}
						?>
						</div>
					</div>
					<?php
				}
				?>
				</details>
				<details style="margin-bottom: 5px;" open>
				<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><?php esc_html_e( 'View' ); ?></summary>
				<form method="post" action="<?php echo esc_url( $scriptname ); ?>">
					<?php wp_nonce_field( 'bdtc_settings', 'bulk_datetime_change_settings' ); ?>
					<div style="display: block;padding:5px 5px">
						<input type="checkbox" name="picker" value="1" <?php checked( true, $bulkdatetimechange_settings['picker'] ); ?> />DateTimePicker
					</div>
					<div style="display: block;padding:5px 5px">
						<input type="radio" name="method" value="posted" 
						<?php
						if ( 'posted' === $bulkdatetimechange_settings['method'] ) {
							echo 'checked';}
						?>
						><?php esc_html_e( 'Posted', 'bulk-datetime-change' ); ?>
					</div>
					<div style="display: block;padding:5px 5px">
						<input type="radio" name="method" value="modified" 
						<?php
						if ( 'modified' === $bulkdatetimechange_settings['method'] ) {
							echo 'checked';}
						?>
						><?php esc_html_e( 'Last updated' ); ?>
					</div>
					<?php
					if ( $bulkdatetimechange->is_add_on_activate ) {
						do_action( 'bdtc_exif_settings', get_current_user_id() );
					} else {
						?>
						<div style="display: block;padding:5px 5px">
							<input type="checkbox" disabled="disabled" />
							<?php esc_html_e( 'Exif Shooting Date Time', 'bulk-datetime-change' ); ?> <span style="color: red;"><?php esc_html_e( 'Add On is required.', 'bulk-datetime-change' ); ?></span>
						</div>
						<?php
					}
					?>
				</details>
				<details style="margin-bottom: 5px;" open>
				<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><?php esc_html_e( 'Change' ); ?></summary>
					<div style="display: block;padding:5px 5px">
						<input type="radio" name="write" value="date_modified" 
						<?php
						if ( 'date_modified' === $bulkdatetimechange_settings['write'] ) {
							echo 'checked';}
						?>
						><?php esc_html_e( 'Posted And Modified', 'bulk-datetime-change' ); ?>
					</div>
					<div style="display: block;padding:5px 5px">
						<input type="radio" name="write" value="date" 
						<?php
						if ( 'date' === $bulkdatetimechange_settings['write'] ) {
							echo 'checked';}
						?>
						><?php esc_html_e( 'Only Posted', 'bulk-datetime-change' ); ?>
					</div>
					<div style="display: block;padding:5px 5px">
						<input type="radio" name="write" value="modified" 
						<?php
						if ( 'modified' === $bulkdatetimechange_settings['write'] ) {
							echo 'checked';}
						?>
						><?php esc_html_e( 'Only Modified', 'bulk-datetime-change' ); ?>
					</div>
				</details>
				<details style="margin-bottom: 5px;">
				<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><strong><?php esc_html_e( 'Logs', 'bulk-datetime-change' ); ?></strong></summary>
				<p class="description">
				<?php esc_html_e( 'Displays the last 100 logs.', 'bulk-datetime-change' ); ?>
				</p>
				<?php
				$logs = get_user_option( 'bulkdatetimechange_current_logs', get_current_user_id() );
				if ( ! empty( $logs ) ) {
					foreach ( $logs as $value ) {
						?>
						<div style="display: block;padding:5px 5px"><?php echo esc_html( $value ); ?></div>
						<?php
					}
				}
				?>
				</details>
				<details style="margin-bottom: 5px;" open>
				<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><?php esc_html_e( 'Custom post types', 'bulk-datetime-change' ); ?></summary>
					<?php
					if ( $bulkdatetimechange->is_add_on_activate ) {
						do_action( 'bdtc_custompost_settings', get_current_user_id() );
					} else {
						?>
						<div style="display: block;padding:5px 5px">
							<span style="color: red;"><?php esc_html_e( 'Add On is required.', 'bulk-datetime-change' ); ?></span>
						</div>
						<?php
					}
					?>
				</details>
				<details style="margin-bottom: 5px;" open>
				<summary style="cursor: pointer; padding: 10px; border: 1px solid #ddd; background: #f4f4f4; color: #000;"><?php esc_html_e( 'Automatically random update', 'bulk-datetime-change' ); ?></summary>
					<?php
					if ( $bulkdatetimechange->is_add_on_activate ) {
						do_action( 'bdtc_randomupdate_settings', get_current_user_id() );
					} else {
						?>
						<div style="display: block;padding:5px 5px">
							<span style="color: red;"><?php esc_html_e( 'Add On is required.', 'bulk-datetime-change' ); ?></span>
						</div>
						<?php
					}
					?>
				</details>
				<?php submit_button( __( 'Save Changes' ), 'primary', 'bulk-datetime-change-settings-options-apply', true ); ?>
				</form>

			</div>

		</div>
		<?php
	}

	/** ==================================================
	 * Credit
	 *
	 * @since 1.00
	 */
	private function credit() {

		$plugin_name    = null;
		$plugin_ver_num = null;
		$plugin_path    = plugin_dir_path( __DIR__ );
		$plugin_dir     = untrailingslashit( wp_normalize_path( $plugin_path ) );
		$slugs          = explode( '/', $plugin_dir );
		$slug           = end( $slugs );
		$files          = scandir( $plugin_dir );
		foreach ( $files as $file ) {
			if ( '.' === $file || '..' === $file || is_dir( $plugin_path . $file ) ) {
				continue;
			} else {
				$exts = explode( '.', $file );
				$ext  = strtolower( end( $exts ) );
				if ( 'php' === $ext ) {
					$plugin_datas = get_file_data(
						$plugin_path . $file,
						array(
							'name'    => 'Plugin Name',
							'version' => 'Version',
						)
					);
					if ( array_key_exists( 'name', $plugin_datas ) && ! empty( $plugin_datas['name'] ) && array_key_exists( 'version', $plugin_datas ) && ! empty( $plugin_datas['version'] ) ) {
						$plugin_name    = $plugin_datas['name'];
						$plugin_ver_num = $plugin_datas['version'];
						break;
					}
				}
			}
		}
		$plugin_version = __( 'Version:' ) . ' ' . $plugin_ver_num;
		/* translators: FAQ Link & Slug */
		$faq       = sprintf( __( 'https://wordpress.org/plugins/%s/faq', 'bulk-datetime-change' ), $slug );
		$support   = 'https://wordpress.org/support/plugin/' . $slug;
		$review    = 'https://wordpress.org/support/view/plugin-reviews/' . $slug;
		$translate = 'https://translate.wordpress.org/projects/wp-plugins/' . $slug;
		$facebook  = 'https://www.facebook.com/katsushikawamori/';
		$twitter   = 'https://twitter.com/dodesyo312';
		$youtube   = 'https://www.youtube.com/channel/UC5zTLeyROkvZm86OgNRcb_w';
		$donate    = __( 'https://shop.riverforest-wp.info/donate/', 'bulk-datetime-change' );

		?>
		<span style="font-weight: bold;">
		<div>
		<?php echo esc_html( $plugin_version ); ?> | 
		<a style="text-decoration: none;" href="<?php echo esc_url( $faq ); ?>" target="_blank" rel="noopener noreferrer">FAQ</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $support ); ?>" target="_blank" rel="noopener noreferrer">Support Forums</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $review ); ?>" target="_blank" rel="noopener noreferrer">Reviews</a>
		</div>
		<div>
		<a style="text-decoration: none;" href="<?php echo esc_url( $translate ); ?>" target="_blank" rel="noopener noreferrer">
		<?php
		/* translators: Plugin translation link */
		echo esc_html( sprintf( __( 'Translations for %s' ), $plugin_name ) );
		?>
		</a> | <a style="text-decoration: none;" href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-facebook"></span></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $twitter ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-twitter"></span></a> | <a style="text-decoration: none;" href="<?php echo esc_url( $youtube ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-video-alt3"></span></a>
		</div>
		</span>

		<div style="width: 250px; height: 180px; margin: 5px; padding: 5px; border: #CCC 2px solid;">
		<h3><?php esc_html_e( 'Please make a donation if you like my work or would like to further the development of this plugin.', 'bulk-datetime-change' ); ?></h3>
		<div style="text-align: right; margin: 5px; padding: 5px;"><span style="padding: 3px; color: #ffffff; background-color: #008000">Plugin Author</span> <span style="font-weight: bold;">Katsushi Kawamori</span></div>
		<button type="button" style="margin: 5px; padding: 5px;" onclick="window.open('<?php echo esc_url( $donate ); ?>')"><?php esc_html_e( 'Donate to this plugin &#187;' ); ?></button>
		</div>

		<?php
	}

	/** ==================================================
	 * Update wp_options table.
	 *
	 * @since 1.00
	 */
	private function options_updated() {

		if ( isset( $_POST['bulk-datetime-change-settings-options-apply'] ) && ! empty( $_POST['bulk-datetime-change-settings-options-apply'] ) ) {
			if ( check_admin_referer( 'bdtc_settings', 'bulk_datetime_change_settings' ) ) {
				$bulkdatetimechange_settings = get_user_option( 'bulkdatetimechange', get_current_user_id() );
				if ( ! empty( $_POST['method'] ) ) {
					$bulkdatetimechange_settings['method'] = sanitize_text_field( wp_unslash( $_POST['method'] ) );
				}
				if ( ! empty( $_POST['write'] ) ) {
					$bulkdatetimechange_settings['write'] = sanitize_text_field( wp_unslash( $_POST['write'] ) );
				}
				if ( ! empty( $_POST['picker'] ) ) {
					$bulkdatetimechange_settings['picker'] = true;
				} else {
					$bulkdatetimechange_settings['picker'] = false;
				}
				update_user_option( get_current_user_id(), 'bulkdatetimechange', $bulkdatetimechange_settings );
				do_action( 'bdtc_custompost_options_updated', get_current_user_id() );
				do_action( 'bdtc_exif_options_updated', get_current_user_id() );
				do_action( 'bdtc_randomupdate_options_updated', get_current_user_id() );
				echo '<div class="notice notice-success is-dismissible"><ul><li>' . esc_html( __( 'Settings' ) . ' --> ' . __( 'Changes saved.' ) ) . '</li></ul></div>';
			}
		}
	}

	/** ==================================================
	 * Settings register
	 *
	 * @since 1.00
	 */
	public function register_settings() {

		$old_per_page = null;
		if ( get_user_option( 'bulkdatetimechange', get_current_user_id() ) ) {
			/* ver 1.00 -> 1.01 */
			$bulkdatetimechange_settings = get_user_option( 'bulkdatetimechange', get_current_user_id() );
			if ( array_key_exists( 'per_page', $bulkdatetimechange_settings ) ) {
				$old_per_page = $bulkdatetimechange_settings['per_page'];
				unset( $bulkdatetimechange_settings['per_page'] );
			}
			update_user_option( get_current_user_id(), 'bulkdatetimechange', $bulkdatetimechange_settings );
		} else {
			$bulkdatetimechange_tbl = array(
				'method' => 'posted',
				'write' => 'date_modified',
				'picker' => true,
			);
			update_user_option( get_current_user_id(), 'bulkdatetimechange', $bulkdatetimechange_tbl );
		}

		/* since ver 1.01 */
		if ( ! get_user_option( 'bdtc_per_page', get_current_user_id() ) ) {
			if ( ! empty( $old_per_page ) ) {
				update_user_option( get_current_user_id(), 'bdtc_per_page', $old_per_page );
			} else {
				update_user_option( get_current_user_id(), 'bdtc_per_page', 20 );
			}
		}

		/* All post types */
		$post_custom_types = array();
		if ( function_exists( 'bulk_datetime_change_add_on_load_textdomain' ) ) {
			$post_custom_types = get_user_option( 'bulkdatetimechange_addon_custompost', get_current_user_id() );
		}
		$post_types = array(
			'post' => __( 'Post' ),
			'page' => __( 'Page' ),
			'attachment' => __( 'Media' ),
		);
		if ( ! empty( $post_custom_types ) ) {
			$custom_types = array();
			foreach ( $post_custom_types as $key => $value ) {
				$custom_types[ $key ] = $value;
			}
			$post_types = array_merge( $post_types, $custom_types );
		}
		update_user_option( get_current_user_id(), 'bulkdatetimechange_all_post_type', $post_types );

		if ( ! get_user_option( 'bulkdatetimechange_filter_post_type', get_current_user_id() ) ) {
			update_user_option( get_current_user_id(), 'bulkdatetimechange_filter_post_type', $post_types );
		} else {
			$filter_types = get_user_option( 'bulkdatetimechange_filter_post_type', get_current_user_id() );
			$all_types = array_keys( $post_types );
			if ( 1 == count( $filter_types ) ) {
				if ( ! array_key_exists( $filter_types[0], $post_types ) ) {
					update_user_option( get_current_user_id(), 'bulkdatetimechange_filter_post_type', $all_types );
				}
			} else {
				$diff1 = array_diff( $filter_types, $all_types );
				$diff2 = array_diff( $all_types, $filter_types );
				if ( ! empty( $diff1 ) || ! empty( $diff2 ) ) {
					update_user_option( get_current_user_id(), 'bulkdatetimechange_filter_post_type', $all_types );
				}
			}
		}
		if ( ! get_user_option( 'bulkdatetimechange_filter_category', get_current_user_id() ) ) {
			update_user_option( get_current_user_id(), 'bulkdatetimechange_filter_category', null );
		}
		if ( ! get_user_option( 'bulkdatetimechange_filter_monthly', get_current_user_id() ) ) {
			update_user_option( get_current_user_id(), 'bulkdatetimechange_filter_monthly', null );
		}
		if ( ! get_user_option( 'bulkdatetimechange_filter_mime_type', get_current_user_id() ) ) {
			update_user_option( get_current_user_id(), 'bulkdatetimechange_filter_mime_type', null );
		}
	}
}


