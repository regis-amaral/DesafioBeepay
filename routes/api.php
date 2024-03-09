<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\PatientController;
use \App\Http\Controllers\CepController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('patients', PatientController::class);

Route::get('/search-cep/{cep}', [CepController::class, 'searchCep']);

Route::post('/patients/upload-csv', [PatientController::class, 'uploadCsv']);

Route::get('search', [PatientController::class, 'search']);
