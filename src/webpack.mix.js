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

mix.setPublicPath('public');
mix.setResourceRoot('../');

//Main pages resources
mix.js('resources/js/app.js', 'public/js')
    //.react()
    .css('resources/css/app.css','public/css');

//Bootstrap is compiled to its own css and js files, separate from rest of project, due to its size.
mix.js('resources/js/bootstrap.js', 'public/js/bootstrap')
    .sass('resources/sass/app.scss', 'public/css/bootstrap');

/* Pre-generated code.
mix.js('resources/js/app.js', 'public/js')
    .react()
    .sass('resources/sass/app.scss', 'public/css');
*/
