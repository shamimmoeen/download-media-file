<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://coderbloc.com
 * @since      1.0.0
 *
 * @package    Download_Media_File
 * @subpackage Download_Media_File/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Download_Media_File
 * @subpackage Download_Media_File/includes
 * @author     CoderBloc <support@coderbloc.com>
 */
class Download_Media_File
{
	/**
	 * Unique identifier for the plugin.
	 *
	 * The variable name is used as the text domain when internationalizing
	 * strings of text.
	 *
	 * @var        string
	 */
	public $plugin_slug;

	/**
	 * Plugin version, used for cache-busting of style and script file
	 * references.
	 *
	 * @var        string
	 */
	public $plugin_version;

	/**
	 * Adds a download button.
	 *
	 * @param      string   $meta   The meta
	 * @param      WP_Post  $post   The post
	 *
	 * @return     string
	 */
	public function add_download_button($meta, $post) {
		$form = '<form class="download-media-file-form" method="POST" style="margin: 7px 0 0;">';
			$form .= wp_nonce_field('download_media_file_action', 'download_media_file_nonce_field', true, false);
			$form .= '<input type="hidden" name="post_id" value="' . esc_attr($post->ID) . '">';
			$form .= '<input type="submit" class="button button-primary button-small" name="download_media_file" value="' . esc_html__('Download', 'download-media-file') . '">';
		$form .= '</form>';

		$meta .= $form;

		return $meta;
	}

	/**
	 * Defines constants if not already defined.
	 *
	 * @param      string       $name   The name
	 * @param      string|bool  $value  The value
	 */
	public function define($name, $value) {
		if (!defined($name)) {
			define($name, $value);
		}
	}

	/**
	 * Define constants.
	 */
	public function define_constants() {
		$this->define('DOWNLOAD_MEDIA_FILE_PATH', $this->get_plugin_path());
		$this->define('DOWNLOAD_MEDIA_FILE_URL', $this->get_plugin_url());
	}

	/**
	 * Process downloading the media file.
	 */
	public function process_downloading_media_file() {
		if (!isset($_POST['download_media_file'])) {
			return;
		}

		// Verify nonce and then proceed.
		if (check_admin_referer('download_media_file_action', 'download_media_file_nonce_field')) {
			if (isset($_POST['post_id']) && $post_id = absint($_POST['post_id'])) {
				/**
				 * Hooks to validate other permissions.
				 *
				 * @param      int   $post_id  The file id
				 */
				do_action('download_media_file_validate_permissions', $post_id);

				// Get file full path
				$file_path = get_attached_file($post_id);

				if (!file_exists($file_path)) {
					wp_die(esc_html__('File not found', 'download-media-file'));
				}

				$ctype = get_post_mime_type($post_id);
				$file_name = wp_basename($file_path);
				$file_size = filesize($file_path);

				if (!ini_get('safe_mode')) {
					@set_time_limit(0);
				}

				if (function_exists('get_magic_quotes_runtime') && get_magic_quotes_runtime()) {
					@set_magic_quotes_runtime(0);
				}

				if (function_exists('apache_setenv')) {
					@apache_setenv('no-gzip', 1);
				}

				@session_write_close();
				@ini_set('zlib.output_compression', 'Off');

				/**
				 * Prevents errors, for example: transfer closed with 3 bytes
				 * remaining to read
				 */
				@ob_end_clean(); // Clear the output buffer

				if (ob_get_level()) {
					$levels = ob_get_level();

					for ($i = 0; $i < $levels; $i++) {
						@ob_end_clean(); // Zip corruption fix
					}
				}

				global $is_IE;

				if ($is_IE && is_ssl()) {
					// IE bug prevents download via SSL when Cache Control and Pragma
					// no-cache headers set.
					header('Expires: Wed, 11 Jan 1984 05:00:00 GMT');
					header('Cache-Control: private');
				} else {
					nocache_headers();
				}

				header("X-Robots-Tag: noindex, nofollow", true);
				header("Content-Type: " . $ctype);
				header("Content-Description: File Transfer");
				header("Content-Disposition: attachment; filename=\"" . $file_name . "\";");
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: " . $file_size);

				/**
				 * Hooks to add additional HTTP Header.
				 *
				 * @param      int   $post_id  The file id
				 */
				do_action('download_media_file_set_http_header', $post_id);

				$this->readfile_chunked($file_path) or wp_die(esc_html__('File not found', 'download-media-file'));

				exit;
			}
		}
	}

	/**
	 * Loads the required files.
	 */
	public function includes() {}

	/**
	 * Hook into actions and filters.
	 */
	public function init_hooks() {
		add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
		add_filter('media_meta', array($this, 'add_download_button'), 10, 2);
		add_action('admin_init', array($this, 'process_downloading_media_file'));
	}

	/**
	 * Returns an instance of this class.
	 *
	 * @return     Download_Media_File
	 */
	public static function instance() {
		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been ran previously
		if (null === $instance) {
			$instance = new Download_Media_File();
			$instance->run();
		}

		return $instance;
	}

	/**
	 * Loads the plugin's translated strings.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain('download-media-file', false, $this->get_plugin_path() . 'languages');
	}

	/**
	 * Gets the plugin slug.
	 *
	 * @return     string  The plugin slug.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Gets the plugin version.
	 *
	 * @return     string  The plugin version.
	 */
	public function get_plugin_version() {
		return $this->plugin_version;
	}

	/**
	 * Gets the plugin path.
	 *
	 * @return     string  The plugin path.
	 */
	public function get_plugin_path() {
		return plugin_dir_path(DOWNLOAD_MEDIA_FILE_PLUGIN_FILE);
	}

	/**
	 * Gets the plugin url.
	 *
	 * @return     string  The plugin url.
	 */
	public function get_plugin_url() {
		return plugin_dir_url(DOWNLOAD_MEDIA_FILE_PLUGIN_FILE);
	}

	/**
	 * Reads file in chunks so big downloads are possible without changing
	 * PHP.INI
	 *
	 * @param      string           $file      The file path
	 * @param      boolean          $retbytes  The retbytes
	 *
	 * @return     boolean|integer
	 */
	public function readfile_chunked($file, $retbytes = true) {
		$chunksize = 1 * (1024 * 1024);
		$buffer = '';
		$cnt = 0;

		$handle = @fopen($file, 'r');

		if ($handle === false) {
			return false;
		}

		while (!feof($handle)) {
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			@ob_flush();
			@flush();

			if ($retbytes) {
				$cnt += strlen($buffer);
			}
		}

		$status = fclose($handle);

		if ($retbytes && $status) {
			return $cnt;
		}

		return $status;
	}

	/**
	 * Runs the class.
	 */
	public function run() {
		$this->plugin_slug = DOWNLOAD_MEDIA_FILE_SLUG;
		$this->plugin_version = DOWNLOAD_MEDIA_FILE_VERSION;

		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

}
