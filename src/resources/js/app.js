/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */

//import from node
window.$ = window.jQuery = require('jquery');
window.Swal = require('sweetalert2');

require('./vanillaJS/highlightPlugin');
require('./vanillaJS/ScrollToBottom');
//require('./vanillaJS/sweetalert2min');
//require('./vanillaJS/jquery-3.5.1');

//TODO: Used for tabs on equipment-page. Research if a smaller bootstrap javascript file exists for only tabs. Preventing lag.
require('./bootstrap');

/**
 * Next, we will create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//require('./components/Example');

