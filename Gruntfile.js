/* jshint node:true */
module.exports = function(grunt) {
	'use strict';

	grunt.initConfig({

		makepot: {
			download_media_file: {
				options: {
					processPot: function( pot ) {
						var translation,
							excluded_meta = [
								'Plugin Name of the plugin/theme',
								'Theme Name of the plugin/theme',
								'Plugin URI of the plugin/theme',
								'Theme URI of the plugin/theme',
								'Description of the plugin/theme',
								'Author of the plugin/theme',
								'Author URI of the plugin/theme',
								'Tags of the plugin/theme',
							];

						for ( translation in pot.translations[''] ) {
							if ( 'undefined' !== typeof pot.translations[''][ translation ].comments.extracted ) {
								if ( excluded_meta.indexOf( pot.translations[''][ translation ].comments.extracted ) >= 0 ) {
									console.log( 'Excluded meta: ' + pot.translations[''][ translation ].comments.extracted );
									delete pot.translations[''][ translation ];
								}
							}
						}

						return pot;
					},
					type: 'wp-plugin',
					domainPath: '/languages',
					exclude: [
						'node_modules'
					],
					mainFile:    'download-media-file.php',
					potFilename: 'download-media-file.pot'
				}
			}
		},

		addtextdomain: {
			download_media_file: {
				options: {
					textdomain: 'download-media-file'
				},
				files: {
					src: [
						'*.php',
						'**/*.php',
						'!node_modules/**'
					]
				}
			}
		},

		// Generate README.md
		wp_readme_to_markdown: {
			download_media_file: {
				files: {
					'README.md': 'readme.txt'
				},
			},
		},

	});

	// Load NPM tasks to be used here
	grunt.loadNpmTasks('grunt-wp-i18n');
	grunt.loadNpmTasks('grunt-wp-readme-to-markdown');

	// Just an alias for pot file generation
	grunt.registerTask('pot', [
		'makepot'
	]);

	// Just an alias to generate README.md file
	grunt.registerTask('generatereadme', [
		'wp_readme_to_markdown'
	]);

};
