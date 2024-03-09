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

Route::get('/patients', [PatientController::class, 'index'])->name('list_all_patients');

Route::get('/patients/{id}', [PatientController::class, 'show'])->name('show_patient');

Route::post('/patients/create', [PatientController::class, 'create'])->name('create_patient');

Route::put('/patients/{id}/update', [PatientController::class, 'update'])->name('update_patient');

Route::delete('/patients/{id}/delete', [PatientController::class, 'delete'])->name('delete_patient');

Route::get('/search-cep/{cep}', [CepController::class, 'searchCep']);

Route::post('/patients/upload-csv', [PatientController::class, 'uploadCsv']);

Route::get('/patients/search', [PatientController::class, 'search'])->name('search_search');
