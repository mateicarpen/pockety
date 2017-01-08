const elixir = require('laravel-elixir');

elixir((mix) => {
    var nodeModulesFolder = '/../../../node_modules/';

    mix.styles([
        nodeModulesFolder + 'bootstrap/dist/css/bootstrap.min.css',
        'app.css'
    ], 'public/css/app.css');

    mix.scripts([
        nodeModulesFolder + 'jquery/dist/jquery.js',
        nodeModulesFolder + 'bootstrap/dist/js/bootstrap.js',
        nodeModulesFolder + 'vue/dist/vue.js',
        'app.js',
    ], 'public/js/app.js');
});
