<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => '\Modules\Transaction\Http\Controllers', 'as' => 'backend.', 'middleware' => ['web', 'auth', 'can:view_backend'], 'prefix' => 'admin'], function () {
    /*
    * These routes need view-backend permission
    * (good if you want to allow more than one group in the backend,
    * then limit the backend features by different roles or permissions)
    *
    * Note: Administrator has all permissions so you do not have to specify the administrator role everywhere.
    */

    /*
     *
     *  Tags Routes
     *
     * ---------------------------------------------------------------------
     */
    $module_name = 'transactions';
    $controller_name = 'TransactionController';
    //Route::get("$module_name/index_list", ['as' => "$module_name.index_list", 'uses' => "$controller_name@index_list"]);
    Route::get("$module_name/index", ['as' => "$module_name.index", 'uses' => "$controller_name@index"]);
    Route::get("$module_name/index_list", ['as' => "$module_name.index_list", 'uses' => "$controller_name@index_list"]);
    Route::get("$module_name/index_data", ['as' => "$module_name.index_data", 'uses' => "$controller_name@index_data"]);
    Route::get("$module_name/create", ['as' => "$module_name.create", 'uses' => "$controller_name@create"]);
    Route::get("$module_name/store", ['as' => "$module_name.store", 'uses' => "$controller_name@create"]);
    Route::get("$module_name/edit/{id}", ['as' => "$module_name.edit", 'uses' => "$controller_name@edit"]);
    Route::get("$module_name/update/{id}", ['as' => "$module_name.update", 'uses' => "$controller_name@edit"]);
    Route::get("$module_name/show/{id}", ['as' => "$module_name.show", 'uses' => "$controller_name@show"]);
    Route::get("$module_name/destroy", ['as' => "$module_name.destroy", 'uses' => "$controller_name@show"]);
    Route::get("$module_name/trashed", ['as' => "$module_name.trashed", 'uses' => "$controller_name@index"]);
});