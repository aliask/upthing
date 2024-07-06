const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

tailwindcss = require('tailwindcss');

mix.js('resources/js/app.js', 'public/js')

mix.less('resources/less/app.less', 'public/css')
  .options({
    postCss: [
      tailwindcss('tailwind.config.js')
    ]
  });