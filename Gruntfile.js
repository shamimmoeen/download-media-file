module.exports = function( grunt ) {
    'use strict';

    grunt.initConfig( {

        // Generate README.md
        wp_readme_to_markdown: {
            download_media_file: {
                files: {
                    'README.md': 'readme.txt'
                },
            },
        },

    } );

    // Load NPM tasks to be used here
    grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );

    // Just an alias to generate README.md file
    grunt.registerTask( 'readme', [ 'wp_readme_to_markdown' ] );

};
