<?php

use Illuminate\Http\Request;

// ======================= public site routes =======================
Route::get('sidebar', 'HomeController@sidebar');


// ==== search routes: used in search inputs
Route::get('users/search', 'UserController@searchAuthors');
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

    Route::post('comments/{comment}/voting', 'CommentController@voting');
});


// ==== users routes
Route::put('users/{user}', 'UserController@update')->middleware('auth:api');


// ======================= admin panel routes =======================
Route::namespace('Admin')->prefix('admin')->middleware('auth:api')->group(function () {
    // ==== categories routes
    Route::get('categories/{category}/posts', 'CategoryController@getCategoryPosts');
    Route::resource('categories', 'CategoryController', [
        'only' => ['index', 'show', 'store', 'update', 'destroy']
    ]);


    // ==== tags routes
    Route::get('tags/{slug}/posts', 'TagController@getTagPosts');
    Route::resource('tags', 'TagController', [
        'only' => ['index', 'show', 'update', 'destroy']
    ]);


    // ==== chat routes
    Route::get('messages', 'ChatController@fetchMessages');
    Route::post('messages', 'ChatController@sendMessage');


    // ==== users routes
    Route::get('users/search', 'UserController@search');

    Route::post('users/{user}/assign-role', 'UserController@assignRole');
    Route::get('users/banned', 'UserController@bannedUsers');

    Route::get('users/{user}/posts', 'UserController@getUserPosts');
    Route::resource('users', 'UserController', [
        'only' => ['index', 'show', 'destroy']
    ]);


    // ==== posts routes
    Route::post('posts/{post}/publishing', 'PostController@publishing');
    Route::post('posts/{post}/assign-tags', 'PostController@assignTags');
    Route::get('posts/{post}/comments', 'PostController@postComments');
    Route::get('posts/unpublished', 'PostController@unPublishedPosts');
    Route::resource('posts', 'PostController', [
        'only' => ['index', 'show', 'store', 'update', 'destroy']
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
