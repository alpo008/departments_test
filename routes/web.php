<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect('home');

});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/departments', 'HomeController@departments')->name('departments');
Route::get('/users', 'HomeController@users')->name('users');
Route::get('/add-department', 'HomeController@addDepartment');
Route::resource('department', 'DepartmentController')->except([
    'create', 'edit'
]);
