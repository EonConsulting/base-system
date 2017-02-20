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

Route::get('/create-role', function() {
//    $dept = roles_permissions()->create_department('Economics');
//    dd(auth()->user()->giveRole($dept, 'Super Admin'));
//    dd(roles_permissions()->remove_department($dept));
//    $role = roles_permissions()->create_role('Student');
//    roles_permissions()->create_permission($role, 'Create Content', 'View Content', 'Update Content', 'Delete Content');
//    dd($dept);
});
