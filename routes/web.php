<?php

/**
 * Guest Routes
 */
Auth::routes();
Route::get('/', 'SiteController@guest');
Route::any('/login/callback', 'Auth\LoginController@pocketCallback');

/**
 * Protected Routes
 */
Route::group(['middleware' => ['auth']], function() {
    Route::get('/home', 'SiteController@home');

    // This isn't really an stateless api, as we need pocket auth
    Route::get('/api/v1/articles', 'Api\ArticlesController@index');
    Route::post('/api/v1/articles/pass', 'Api\ArticlesController@pass');
    Route::post('/api/v1/articles/archive', 'Api\ArticlesController@archive');
    Route::post('/api/v1/articles/tag', 'Api\ArticlesController@tag');
    Route::post('/api/v1/articles/reset', 'Api\ArticlesController@reset');
    Route::resource('/api/v1/tags', 'Api\TagsController', ['only' => ['index', 'store', 'destroy']]);
});







