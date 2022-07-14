<?php

/**
 * Download Media File
 *
 * @package    Download_Media_File
 * @author     CoderBloc
 * @copyright  2018 CoderBloc
 * @license    GPL-3.0+
 *
 * @wordpress-plugin
 * Plugin Name: Download Media File
 * Plugin URI:  https://coderbloc.com/download-media-file
 * Description: Adds a button to media modal to download media file.
 * Version:     1.0.0
 * Author:      CoderBloc
 * Author URI:  https://coderbloc.com/
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: download-media-file
 * Domain Path: /languages
 *
 * Download Media File is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 2 of the License, or any later version.
 *
 * Download Media File is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Download Media File. If not, see http://www.gnu.org/licenses/gpl-3.0.txt.
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

// Defines constant DOWNLOAD_MEDIA_FILE_SLUG
if (!defined('DOWNLOAD_MEDIA_FILE_SLUG')) {
	define('DOWNLOAD_MEDIA_FILE_SLUG', 'download-media-file');
}

// Defines constant DOWNLOAD_MEDIA_FILE_VERSION
if (!defined('DOWNLOAD_MEDIA_FILE_VERSION')) {
	define('DOWNLOAD_MEDIA_FILE_VERSION', '3.0.0');
}

// Defines constant DOWNLOAD_MEDIA_FILE_PLUGIN_FILE
if (!defined('DOWNLOAD_MEDIA_FILE_PLUGIN_FILE')) {
	define('DOWNLOAD_MEDIA_FILE_PLUGIN_FILE', __FILE__);
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-download-media-file-activator.php
 */
function activate_download_media_file() {
	require_once plugin_dir_path(DOWNLOAD_MEDIA_FILE_PLUGIN_FILE) . 'includes/class-download-media-file-activator.php';
	Download_Media_File_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-download-media-file-deactivator.php
 */
function deactivate_download_media_file() {
	require_once plugin_dir_path(DOWNLOAD_MEDIA_FILE_PLUGIN_FILE) . 'includes/class-download-media-file-deactivator.php';
	Download_Media_File_Deactivator::deactivate();
}

register_activation_hook(DOWNLOAD_MEDIA_FILE_PLUGIN_FILE, 'activate_download_media_file');
register_deactivation_hook(DOWNLOAD_MEDIA_FILE_PLUGIN_FILE, 'deactivate_download_media_file');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(DOWNLOAD_MEDIA_FILE_PLUGIN_FILE) . 'includes/class-download-media-file.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_download_media_file() {
	return Download_Media_File::instance();
}

run_download_media_file();
