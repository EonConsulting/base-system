<?php

Auth::routes();
Route::get('/', function () {
    return view('welcome');
});

Route::get('/app-store', function() {
    return app_store()->get_store();
});

Route::any('/lti/tools/tao', function() {
    print laravel_lti()->launch_tao($_GET['page']);
});

Route::get('/lti', '\\EONConsulting\\LaravelLTI\\Http\\Controllers\\LTIBaseController@index');
Route::post('/lti', '\\EONConsulting\\LaravelLTI\\Http\\Controllers\\LTIBaseController@index');

Route::get('/lti/test', '\\EONConsulting\\PHPStencil\\Http\\Controllers\\TestStencilController@test');
Route::post('/lti/test', '\\EONConsulting\\PHPStencil\\Http\\Controllers\\TestStencilController@test');

Route::get('/home', 'HomeController@index');
