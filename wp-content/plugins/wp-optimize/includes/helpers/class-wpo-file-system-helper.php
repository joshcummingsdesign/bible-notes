<?php
if (!defined('ABSPATH')) die('No direct access allowed');

if (!class_exists('WPO_File_System_Helper')) :
class WPO_File_System_Helper {
	
	/**
	 * Create a directory if it does not exist
	 *
	 * @param string $directory Directory path
	 */
	public static function create_directory(string $directory) {
		if (!file_exists($directory)) {
			wp_mkdir_p($directory);
		}
	}
	
	
	/**
	 * Gets a WP_Filesystem_Base object.
	 *
	 * @return WP_Filesystem_Base|null The WP_Filesystem_Base object, or null if an error occurred.
	 */
	public static function get_wp_filesystem(): ?WP_Filesystem_Base {
		global $wp_filesystem;
		
		if (empty($wp_filesystem)) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			
			if (!WP_Filesystem()) {
				return null;
			}
		}
		
		return $wp_filesystem;
	}
	
	/**
	 * Writes data to a file using WP_Filesystem.
	 *
	 * @param string $filename The path to the file.
	 * @param string $data     The data to write to the file.
	 * @param int    $flags    Optional. File write flags. Default is 0.
	 *                         Currently, other than default, only FILE_APPEND is supported.
	 * @return bool True on success, false on failure.
	 */
	public static function write_to_file(string $filename, string $data, int $flags = 0): bool {
		$filesystem = self::get_wp_filesystem();
		
		if (!$filesystem) {
			return false;
		}
		
		if (FILE_APPEND === $flags) {
			$content = $filesystem->get_contents($filename);
			$data = $content . $data;
		}
		
		return $filesystem->put_contents($filename, $data, FS_CHMOD_FILE);
	}
	
	/**
	 * Gets the content of a file using WP_Filesystem.
	 *
	 * @param string $filename The path to the file.
	 * @return string|false The content of the file, or false on failure.
	 */
	public static function get_file_contents(string $filename) {
		$filesystem = self::get_wp_filesystem();
		
		if (!$filesystem) {
			return false;
		}
		
		return $filesystem->get_contents($filename);
	}
	
	/**
	 * Deletes a file or directory using WP_Filesystem.
	 *
	 * @param string $path      The path to the file or directory.
	 * @param bool $recursive Optional. Whether to delete recursively. Default is false.
	 * @return bool True on success, false on failure.
	 */
	public static function delete(string $path, bool $recursive = false): bool {
		$filesystem = self::get_wp_filesystem();
		
		if (!$filesystem) {
			return false;
		}
		
		return $filesystem->delete($path, $recursive);
	}
	
	/**
	 * Wrapper for WP_Filesystem::chmod method
	 *
	 * @param string $file Full path to a file
	 * @param mixed $mode File permissions
	 * @return bool
	 */
	public static function chmod(string $file, $mode = false, bool $recursive = false): bool {
		$filesystem = self::get_wp_filesystem();
		
		if (!$filesystem) {
			return false;
		}
		return $filesystem->chmod($file, $mode, $recursive);
	}
}
endif;
