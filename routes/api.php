<?php 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;

Route::group(['middleware' => ['central.auth']], function () {
   Route::get('me', [UserController::class, 'me']);

   Route::group(['prefix' => 'users'], function () {

   }); 
});