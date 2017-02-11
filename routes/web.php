<?php

Auth::routes();
Route::get('/', function () {
    return view('welcome');
});

Route::get('/app-store', function() {
    return app_store()->get_store();
});

Route::get('/permissions', function() {
    return roles_permissions()->index();
});


Route::get('/home', 'HomeController@index');
