module.exports = (grunt) ->
  grunt.initConfig
    pkg: grunt.file.readJSON('package.json')
    banner: '/*!\n' +
             ' * <%= pkg.name %> v<%= pkg.version %> by <%= pkg.author %>\n' +
             ' * Copyright <%= grunt.template.today("yyyy") %> <%= pkg.copyright %>\n' +
             ' * Licensed under <%= pkg.license %>\n' +
             ' */'

    replace: {
      xml: {
        options: {
          expression: false
          force: true

          patterns: [
            {
              match: 'name'
              replacement: '<%= pkg.name %>'
            },
            {
              match: 'version'
              replacement: '<%= pkg.version %>'
            },
            {
              match: 'description'
              replacement: '<%= pkg.description %>'
            },
            {
              match: 'description'
              replacement: '<%= pkg.author%>'
            },
            {
              match: 'description'
              replacement: '<%= pkg.copyright %>'
            },
            {
              match: 'description'
              replacement: '<%= pkg.authorEmail %>'
            },
            {
              match: 'description'
              replacement: '<%= pkg.creationDate %>'
            },
            {
              match: 'description'
              replacement: '<%= pkg.authorUrl %>'
            },
            {
              match: 'description'
              replacement: '<%= pkg.license %>'
            }

          ]
        }
        files: [
          {
            expand: true,
            flatten: true,
            cwd: '_source/',
            src: ['**/*.xml'],
            dest: '.tmp'
          }
        ]
      }
    }

    sass:
      options:
        precision: 5
        sourcemap: false
      build:
        files: [{
          expand: true,
          cwd:  '_source/css/',
          dest: '.tmp/css/',
          src:  ['*.scss', '!_*.scss']
          ext:  '.css'
        }]

    autoprefixer:
      options:
        browsers: ['last 2 version', 'ie 9', '> 1%']
      build:
        expand: true
        # flatten: true
        cwd: '.tmp/css/'
        src: '**/*.css'
        dest: '.tmp/css/'

    coffee:
      options:
        sourceMap: true
        bare: true
      compile:
        expand: true,
        flatten: true,
        cwd: '.tmp/js',
        src: ['*.coffee'],
        dest: '.tmp/js',
        ext: '.js'

    uglify:
      options:
        preserveComments: 'some'
        report: 'gzip'
        compress:
          global_defs:
            "DEBUG": false
          dead_code: true
      build:
        expand: true
        flatten: true
        cwd: '.tmp/js/'
        src: '*.js'
        dest: '.tmp/js'

    copy:
      css:
        expand: true
        cwd: '.tmp/css'
        src: ['**/*.css']
        dest: 'media/css/'
      php:
        expand: true
        cwd: '_source/'
        src: ['**/*.php', '**/*.html']
        dest: ''
      jsTmp:
        expand: true
        cwd: '_source/js/'
        src: ['**/*.js', '**/*.coffee']
        dest: '.tmp/js/'
      js:
        expand: true
        cwd: '.tmp/js/'
        src: '**/*.js'
        dest: 'media/js'
      xml:
        expand: true
        cwd: '.tmp/'
        src: ['**/*.xml']
        dest: '';

    watch:
      css:
        files: ['_source/**/*.scss', '_source/**/*.css']
        tasks: ['sass', 'autoprefixer', 'copy:css']
      cssPhp:
        files: '_source/**/*.css.php'
        tasks: 'copy:cssPhp'
      php:
        files: ['_source/**/*.php', '_source/**/*.html']
        tasks: 'copy:php'
      xml:
        files: ['_source/**/*.xml']
        tasks: ['replace:xml', 'copy:xml']

    # release

  # !Load Tasks
  require("load-grunt-tasks") grunt

  grunt.registerTask 'default', [
    'sass'
    'autoprefixer'
    'copy:jsTmp'
    'coffee:compile'
    'uglify:build'
    'copy:js'
    'copy:php'
    'copy:css'
    'replace:xml'
    'copy:xml'
    'watch'
  ]