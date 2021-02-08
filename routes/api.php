<?php

use Illuminate\Http\Request;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
//Passport para la autenticaciion por token


Route::post('createStudent','UserController@createStudent')->middleware('auth:api');
Route::post('login','UserController@login');
Route::get('getRoles','RoleController@getRoles');
Route::post('createCourse','ClassController@createClass');
Route::put('updateClass','ClassController@updateClass')->middleware('auth:api');
Route::get('showClass/{courseId}','ClassController@showClass')->middleware('auth:api');
Route::get('getClasses','ClassController@getClasses')->middleware('auth:api');
Route::delete('deleteClass/{courseId}','ClassController@deleteClass')->middleware('auth:api');
Route::put('updateUser','UserController@updateUser')->middleware('auth:api');
Route::get('showStudent/{userId}','UserController@showStudent')->middleware('auth:api');
Route::get('getStudents','UserController@getStudents')->middleware('auth:api');
Route::delete('deleteStudent/{userId}','UserController@deleteStudent')->middleware('auth:api');
Route::post('assignCourseToStudent','ClassController@assignCourseToStudent')->middleware('auth:api');
Route::delete('deleteUserClass/{user_id}/{class_id}','ClassController@deleteUserCourse')->middleware('auth:api');
Route::get('getMyCourses', 'UserController@getMyCourses')->middleware('auth:api');
Route::get('getStudentsCourses','UserController@getStudentsCourses')->middleware('auth:api');
