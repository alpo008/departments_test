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

Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/departments', 'HomeController@departments')->name('departments');
Route::get('/users', 'HomeController@users')->name('users');
Route::resource('department', 'DepartmentController')->except([
    'create', 'edit'
]);
Route::resource('user', 'UserController')->except([
    'create', 'edit'
]);

Route::get('/add-user', 'HomeController@addUser');
Route::get('/edit-user/{id}', 'HomeController@editUser');
Route::get('/add-department', 'HomeController@addDepartment');
Route::get('/edit-department/{id}', 'HomeController@editDepartment');
