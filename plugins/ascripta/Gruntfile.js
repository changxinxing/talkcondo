'use strict';
module.exports = function (grunt) {

    // Require all the dependencies.
    require('load-grunt-tasks')(grunt, {
        scope: 'devDependencies'
    });

    // Create the initial configuration.
    grunt.initConfig({

        // Automatically run tasks and reload when resources have changed.  
        watch: {
            sass: {
                files: ['assets/sass/**/*.{scss,sass}'],
                tasks: ['sass:dist', 'cssmin'],
                options: {
                    livereload: true
                }
            },
            js: {
                files: [
                    'assets/js/admin/asc-editor.js',
                    'assets/js/ascripta.js'
                ],
                tasks: ['jshint', 'uglify', 'babel'],
                options: {
                    livereload: true
                }
            }
        },

        // Compile certain SCSS files into CSS.
        sass: {
            dist: {
                files: [{
                    expand: true,
                    sourceMap: true,
                    cwd: 'assets/sass',
                    src: ['**/*.scss'],
                    dest: 'assets/css',
                    ext: '.css'
                }]
            }
        },

        // Add vendor prefixes for better browser support.  
        autoprefixer: {
            options: {
                browsers: ['last 2 versions', 'ie 9', 'ios 6', 'android 4'],
                map: true
            },
            files: {
                expand: true,
                flatten: true,
                src: 'assets/css/*.css',
                dest: 'assets/css'
            },
        },

        // Minify all CSS files in the assets directory.  
        cssmin: {
            options: {
                keepSpecialComments: 1
            },
            minify: {
                expand: true,
                cwd: 'assets/css',
                src: ['**/*.css', '**/!*.min.css'],
                dest: 'assets/css',
                ext: '.min.css'
            }
		},
		
		// Convert ECMAScript 2015+ code into backwards compatible JavaScript.
		babel: {
			options: {
				sourceMap: true,
				presets: ['es2015']
			},
			dist: {
				files: {
					'assets/js/asc-framework.min.js': 'assets/js/asc-framework.js'
				}
			}
		},

        // Lint the JavaScript using the .jshintrc file.  
        jshint: {
            options: {
                jshintrc: '.jshintrc',
                "force": true
            },
            all: [
                'Gruntfile.js',
                'assets/js/ascripta.js',
                'assets/js/admin/asc-editor.js'
            ]
        },

        // Minify and create the maps for certain scripts.  
        uglify: {
            dist: {
                options: {
                    sourceMap: 'assets/js/asc-framework.js.map',
                    sourceMappingURL: 'asc-framework.js.map',
                    sourceMapPrefix: 2
                },
                files: {
                    'assets/js/admin/asc-editor.min.js': [
                        'assets/js/admin/asc-editor.js'
                    ],
                    'assets/js/asc-framework.min.js': [
                        'assets/js/asc-framework.js'
                    ]
                }
            }
        },

        // Order properties within the CSS assets.  
        csscomb: {
            dist: {
                options: {
                    outputStyle: 'expanded',
                    config: '.csscomb.json'
                },
                files: [{
                    expand: true,
                    cwd: 'assets/css',
                    src: ['**/*.css', '**/!*.min.css'],
                    dest: 'assets/css',
                    ext: '.css'
                }]
            }
        },

        // Beautify and adjust white space for static assets.  
        jsbeautifier: {
            css: {
                src: ['assets/css/**/*.css', 'assets/css/**/!*.min.css']
            },
            js: {
                src: ['assets/js/**/*.js', 'assets/js/**/!*.min.js']
            }
        },

        // Clean the development files.
        clean: {
            build: [
                '.sass-cache',
                'bower_components',
                'node_modules',
            ]
        }

    });

    // Register the 'default' task used for development.
    grunt.registerTask(
        'default', [
            'sass:dist', 'uglify', 'jsbeautifier', 'babel', 'cssmin', 'watch'
        ]
    );

    // Register the 'setup' task used for initialization.    
    grunt.registerTask(
        'setup', [
            'sass:dist', 'uglify', 'jsbeautifier', 'babel', 'cssmin', 'watch'
        ]
    );

    // Register the 'build' task used to create the distributable version of the plugin.
    grunt.registerTask(
        'build', [
            'sass:dist', 'autoprefixer', 'csscomb', 'jsbeautifier', 'cssmin', 'uglify', 'babel', 'clean:build'
        ]
    );

};