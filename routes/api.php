<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BussinessUnitsController;
use App\Http\Controllers\OccupationController;
use App\Http\Middleware\VerifyTokenApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/auth/register',[AuthController::class,'register']);
Route::post('/auth/login',[AuthController::class,'login'])->name('api_login');
Route::get('/teste',function(){
    echo 'Olá';
});
Route::group(['middleware' => ['auth:sanctum']],function(){
    Route::middleware(['verify.token.api'])->group(function(){
        Route::get('/auth/admin/logout',[AuthController::class,'logout']);

        Route::get('/teste-seguranca',function(){
            echo 'Falhou';
        });

        //Occupation
        Route::post('/admin/occupation/search',[OccupationController::class,'search']);
        Route::post('/admin/occupation/store',[OccupationController::class,'store']);
        Route::put('/admin/occupation/{id}',[OccupationController::class,'update']);
        Route::delete('/admin/occupation/{id}',[OccupationController::class,'delete']);
        //End occupation

        //BussinessUnits
        Route::post('/admin/bussiness-units/store',[BussinessUnitsController::class,'store']);
        //End bussinessUnits
    });
});
