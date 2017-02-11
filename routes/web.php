<?php

Auth::routes();
Route::get('/', function () {
    return view('welcome');
});

Route::get('/data', function() {
    return meta_classification()->get_icons();
});

Route::get('/permissions', function() {
    return roles_permissions()->index();
});


Route::get('/home', 'HomeController@index');
