<?php

Auth::routes();
Route::get('/', function () {
    return laravel_lti()->launch();
//    return view('welcome');
});

Route::get('/app-store', function() {
    return app_store()->get_store();
});

Route::get('/permissions', function() {
    return roles_permissions()->index();
});

Route::get('/lti', '\\EONConsulting\\LaravelLTI\\Http\\Controllers\\LTIBaseController@index');
Route::post('/lti', '\\EONConsulting\\LaravelLTI\\Http\\Controllers\\LTIBaseController@index');

Route::get('/home', 'HomeController@index');
