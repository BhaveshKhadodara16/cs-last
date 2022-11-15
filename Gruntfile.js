module.exports = function(grunt){

    //file system module use for filter file name
    var fs = require('fs');

    //Define global variables
    var SOURCE_DIR = './', //root path of plugin folder
        ZIP_FILE_NAME = 'woocommerce-conditional-discount-rules-for-checkout-premium.zip', //add name of final zip
        BKP_FILE_SGGESTION = "(backup|bkp)", //add multiple back up file slug here to remove from plugin before making zip
        TEXT_DOMAIN = ['woo-conditional-discount-rules-for-checkout'], //list out textdomain for check in whole plguin files
        PLUGIN_MAIN_FILE = 'woo-conditional-discount-rules-for-checkout.php', //write down main file name of plugin
        POT_FILE_NAME = 'woo-conditional-discount-rules-for-checkout.pot', //write down POT file name
        //exclude plugin meta data from POT file
        EXCLUDED_META = [
            'Plugin Name of the plugin/theme',
            'Plugin URI of the plugin/theme',
            'Author of the plugin/theme',
            'Author URI of the plugin/theme',
            'Description of the plugin/theme'
        ];

    //Configuration
    grunt.initConfig({
        // make a zipfile
        compress: {
            main: {
                options: {
                    archive: ZIP_FILE_NAME
                },
                files: [{
                    src: ['**', '!**/node_modules/**', '!**/vendor/**', '!**/Gruntfile.js', '!**/composer.json', '!**/composer.lock', '!**/package-lock.json', '!**/package.json', '!**/*.zip', '!**/phpcs_report.txt', '!**/phpcs.xml' ], 
                    dest: '.'
                }]
            }
        },

        //PHPCS call
        phpcs: {
            application: {
                expand: true,
                src: ['**/*.php', '!**/freemius/**', '!**/node_modules/**', '!**/vendor/**'],
            },
            options: {
                bin: 'vendor/bin/phpcs',
                standard: 'WordPressVIPMinimum',
                reportFile: './phpcs_report.txt',
                warningSeverity: 0
            }
        },

        //Set text-domain rules
        checktextdomain: {
            options: {
                correct_domain: false,
                text_domain: TEXT_DOMAIN,
                keywords: [
                '__:1,2d',
                '_e:1,2d',
                '_x:1,2c,3d',
                '_n:1,2,4d',
                '_ex:1,2c,3d',
                '_nx:1,2,4c,5d',
                'esc_attr__:1,2d',
                'esc_attr_e:1,2d',
                'esc_attr_x:1,2c,3d',
                'esc_html__:1,2d',
                'esc_html_e:1,2d',
                'esc_html_x:1,2c,3d',
                '_n_noop:1,2,3d',
                '_nx_noop:1,2,3c,4d'
                ]
            },
            files: {
                cwd: SOURCE_DIR,
                src: ['**/*.php', '!**/freemius/**', '!**/node_modules/**', '!**/vendor/**'],
                expand: true
            }
        },

        //Make POT file
        makepot: {
            src: {
                options: {
                    cwd: SOURCE_DIR,
                    domainPath: '/languages',
                    exclude: ['node_modules/*','freemius/*', 'vendor/*'], // List of files or directories to ignore.
                    mainFile: PLUGIN_MAIN_FILE,
                    potFilename: POT_FILE_NAME,
                    potHeaders: { // Headers to add to the generated POT file.
                        poedit: true, // Includes common Poedit headers.
                        'Last-Translator': 'Sagar Jariwala <sagar.jariwala@multidots.com>',
                        'Language-Team': 'Sagar Jariwala <sagar.jariwala@multidots.com>',
                        'report-msgid-bugs-to': 'https://www.multidots.com/contact/',
                        'x-poedit-keywordslist': true, // Include a list of all possible gettext functions.
                        'x-poedit-country': 'India',
                    },
                    type: 'wp-plugin',
                    updateTimestamp: true, // Whether the POT-Creation-Date should be updated without other changes.
                    updatePoFiles: false, // Whether to update PO files in the same directory as the POT file.
                    processPot: function( pot ) {
                        var translation;
    
                        for ( translation in pot.translations[''] ) {
                            if ( 'undefined' !== typeof pot.translations[''][ translation ].comments.extracted ) {
                                if ( EXCLUDED_META.indexOf( pot.translations[''][ translation ].comments.extracted ) >= 0 ) {
                                    grunt.log.writeln( 'Excluded meta: '['green'] + pot.translations[''][ translation ].comments.extracted );
                                    delete pot.translations[''][ translation ];
                                }
                            }
                        }
                        return pot;
                    },
                }
            }
        },

        //Remove backup files from plugin
        clean:{
            bkp: {
                src: ['**/*', '!**/freemius/**', '!**/node_modules/**', '!**/vendor/**'],
                filter: function( filepath ){
                    var stats = fs.statSync(filepath);
                    if(stats.isFile()){
                        
                        //get only file name
                        var filename = filepath.substring(filepath.lastIndexOf('/')+1, filepath.lastIndexOf("."));                        
                        
                        //start with specific string
                        var re_start = new RegExp("^"+BKP_FILE_SGGESTION+"[a-z]*", "i");

                        //end with specific string
                        var re_end = new RegExp(BKP_FILE_SGGESTION+"[\.min]*$", "i");

                        if ( filename.match(re_start) || filename.match(re_end) ) {
                            grunt.log.writeln( 'Removed: '['green'] + filepath );
                            return true;
                        }
                    }
                }
            }
        },

        //JS validation
        jsvalidate: {
            options: {
                globals: {},
                esprimaOptions: {},
                verbose: true
            },
            src: [ '**/*.js', '!**/freemius/**', '!**/node_modules/**', '!**/vendor/**', '!**/*.min.js', '!**/Gruntfile.js', '!**/jquery.*.js'],
        },

        //JS hint for formated and valid code
        jshint: {
            options:{
                boss: true,
                curly: true,
                eqeqeq: false,
                eqeqeq: true,
                expr: true,
                immed: true,
                noarg: true,
                quotmark: "single",
                undef: false,
                unused: true,
                esversion: 6,
                browser: true,
                devel: true,
                maxerr: 99999999,
                laxbreak:true,
                scripturl:true,
                globals: {
                    "_": false,
                    "ajaxurl": false,
                    "jQuery": false,
                    "wp": false
                }
            },
            src: [ '**/*.js', '!**/freemius/**', '!**/node_modules/**', '!**/vendor/**', '!**/*.min.js', '!**/Gruntfile.js', '!**/jquery.*.js']
        },

    });

    //Load plugins
    grunt.loadNpmTasks( 'grunt-wp-i18n' );
    grunt.loadNpmTasks( 'grunt-checktextdomain' );
    grunt.loadNpmTasks( 'grunt-contrib-compress' );
    grunt.loadNpmTasks( 'grunt-phpcs' );
    grunt.loadNpmTasks( 'grunt-contrib-clean' );
    grunt.loadNpmTasks( 'grunt-jsvalidate' );
    grunt.loadNpmTasks( 'grunt-contrib-jshint' );

    //Register tasks to perform
    grunt.registerTask( 'dotcmdstart', function(){

        var d = new Date(); 
        var stamp = d.toLocaleString();

        grunt.log.writeln('WelCome to '+'thdotStore'['blue'].bold+' gruntJS task runner...');
        grunt.log.writeln('Script has been started @ '['green']+stamp.bold['blue']);

    });

    grunt.registerTask( 'dotcmdend', function(){
        var d = new Date(); 
        var stamp = d.toLocaleString();
        console.log('Script has been ended @ '['green']+stamp.bold['blue']);
    });

    grunt.registerTask( 'default', [ 'dotcmdstart', 'clean', 'jsvalidate', 'jshint', 'checktextdomain', 'phpcs', 'makepot:src', 'compress', 'dotcmdend'] );

}