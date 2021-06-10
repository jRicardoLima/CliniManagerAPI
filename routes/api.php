<?php

use App\Http\Controllers\ApiExternalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BussinessUnitsController;
use App\Http\Controllers\EmployeeController;
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

        //ApiExternal
        Route::post('/admin/cep-api/cep',[ApiExternalController::class,'cep']);
        //End apiExternal

        //Occupation
        Route::post('/admin/occupation/search',[OccupationController::class,'search']);
        Route::post('/admin/occupation/store',[OccupationController::class,'store']);
        Route::put('/admin/occupation/{id}',[OccupationController::class,'update']);
        Route::delete('/admin/occupation/{id}',[OccupationController::class,'delete']);
        Route::get('/admin/occupation/occupation-list',[OccupationController::class,'listOccupation']);
        //End occupation

        //BussinessUnits
        Route::post('/admin/bussiness-units/store',[BussinessUnitsController::class,'store']);
        Route::post('/admin/bussiness-units/search',[BussinessUnitsController::class,'search']);
        Route::put('/admin/bussiness-units/{id}',[BussinessUnitsController::class,'update']);
        Route::delete('/admin/bussiness-units/{id}',[BussinessUnitsController::class,'delete']);
        Route::get('/admin/bussiness-unites/bussiness-list',[BussinessUnitsController::class,'listBussiness']);
        //End bussinessUnits

        //Employee
        Route::post('/admin/employee/store',[EmployeeController::class,'store']);
        Route::post('/admin/employee/search',[EmployeeController::class,'search']);
        Route::put('admin/employee/{id}',[EmployeeController::class,'update']);
        Route::delete('admin/employee/{id}',[EmployeeController::class,'delete']);
        Route::get('admin/employee/image/{id}',[EmployeeController::class,'photoUser']);
        //End employee
    });
});
