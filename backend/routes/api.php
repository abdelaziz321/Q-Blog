<?php

use Illuminate\Http\Request;

// ======================= public site routes =======================
Route::get('sidebar', 'HomeController@sidebar');


// ==== search routes: used in search inputs
Route::get('users/search', 'UserController@search');
Route::get('tags/search', 'HomeController@searchTags');
Route::get('categories/search', 'HomeController@searchCategories');


// ==== posts routes
Route::resource('posts', 'PostController', [
    'only' => ['index', 'show']
]);
Route::get('posts/{post}/comments', 'PostController@postComments');
Route::post('posts/{post}/recommendation', 'PostController@recommendation')->middleware('auth:api');


// ==== comments routes
Route::middleware('auth:api')->group(function () {
    Route::resource('comments', 'CommentController', [
        'only' => ['store', 'update', 'destroy']
    ]);

    Route::post('comments/{comment}/voting', 'CommentController@vote');
});


// ==== users routes
Route::put('users/{user}', 'UserController@update')->middleware('auth:api');


// ======================= admin panel routes =======================
Route::namespace('Admin')->prefix('admin')->middleware('auth:api')->group(function () {
    // ==== categories routes
    Route::get('categories/{category}/posts', 'CategoryController@getCategoryPosts');
    Route::resource('categories', 'CategoryController', [
        'except' => ['create', 'edit']
    ]);


    // ==== tags routes
    Route::get('tags/{slug}/posts', 'TagController@getTagPosts');
    Route::resource('tags', 'TagController', [
        'only' => ['index', 'show', 'update', 'destroy']
    ]);


    // ==== users routes
    Route::get('users/search', 'UserController@search');
    Route::get('users/{user}/posts', 'UserController@getUserPosts');
    Route::get('users/banned', 'UserController@bannedUsers');
    Route::post('users/ban/{user}', 'UserController@ban');
    Route::post('users/unban/{user}', 'UserController@unban');
    Route::post('users/assign-role/{user}', 'UserController@assignRole');

    Route::resource('users', 'UserController', [
        'only' => ['index', 'destroy', 'show']
    ]);


    // ==== posts routes
    Route::get('posts/{post}/comments', 'PostController@postComments');
    Route::get('posts/unpublished', 'PostController@unPublishedPosts');
    Route::post('posts/publish/{post}', 'PostController@publish');
    Route::post('posts/unpublish/{post}', 'PostController@unpublish');
    Route::post('posts/tags/{post}', 'PostController@assignTags');
    Route::resource('posts', 'PostController', [
        'except' => ['create', 'edit']
    ]);


    // ==== comments routes
    Route::resource('comments', 'CommentController', [
        'only' => ['index', 'destroy']
    ]);


    // ==== dashboard routes
    Route::get('dashboard', 'DashboardController@index');
});


// ======================= authentication routes =======================
Route::prefix('auth')->middleware('api')->group(function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');

    Route::post('refresh', 'AuthController@refresh');
    Route::get('user', 'AuthController@user');
});

DB::listen(function ($query) {
    $b = implode($query->bindings, ' | ');
    error_log("Q take time: {{$query->time}} with params: {{$b}}\n==> {{$query->sql}}\n");
});
