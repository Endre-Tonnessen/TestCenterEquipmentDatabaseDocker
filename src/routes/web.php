<?php

use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\CalibrationController;
use App\Http\Controllers\CalibrationFrequencyController;
use App\Http\Controllers\DeliverController;
use App\Http\Controllers\EquipmentNoteController;
use App\Http\Controllers\EquipmentPageController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\UserHomeController;
use App\Models\CalibrationFrequency;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\EquipmentController;

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

//Main page
Route::get( '/', [HomeController::Class, 'index']);
Route::post('/search', [HomeController::class, 'indexSearchEquipment']);
Route::post('/searchTag', [HomeController::class, 'indexSearchByTag']);

//Borrow page
Route::get( '/Borrow', [BorrowController::class, 'index']);
Route::post('/Borrow/b', [BorrowController::class, 'borrow']);      //Borrow
Route::post('/Borrow/re', [BorrowController::class, 'reRegister']); //Re-register

//Deliver Page
Route::get( '/Deliver', [DeliverController::class, 'index']);
Route::post('/Deliver/d', [DeliverController::class, 'deliver']);

//Status form Excel export
Route::get('/Equipment/{id}/excel', [ExportController::class, 'statusFormExcel']);
Route::get('/Equipment/{id}/{date}/excel', [ExportController::class, 'statusFormExcelDate']);
//Calibration Range & Accuracy
Route::post('/Equipment/{id}/cCal', [CalibrationController::class, 'store'])->middleware('auth');
Route::post('/Equipment/{id}/dCal', [CalibrationController::class, 'destroy'])->middleware('auth');
Route::post('/Equipment/{id}/eCal', [CalibrationController::class, 'edit'])->middleware('auth');
//Measuring Range & Accuracy
Route::post('/Equipment/{id}/cMes', [MeasurementController::class, 'store'])->middleware('auth');
Route::post('/Equipment/{id}/dMes', [MeasurementController::class, 'destroy'])->middleware('auth');
Route::post('/Equipment/{id}/eMes', [MeasurementController::class, 'edit'])->middleware('auth');
//Calibration Frequency
Route::post('/Equipment/{id}/cCalFreq', [CalibrationFrequencyController::class, 'store'])->middleware('auth');
//Route::post('/Equipment/{id}/dCalFreq', [CalibrationFrequencyController::class, 'destroy'])->middleware('auth'); //TODO: Might need this if we're showing all previous calibration dates.
//Equipment Notes
Route::post('/Equipment/{id}/cNotes', [EquipmentNoteController::class, 'store'])->middleware('auth');

//Equipment Page
Route::get( '/Equipment/{id}', [EquipmentPageController::class, 'index']);
Route::post('/Equipment/{id}/update', [EquipmentController::class, 'update'])->middleware('auth');
Route::post('/Equipment/{id}/updateImage', [EquipmentController::class, 'updateImage'])->middleware('auth');
Route::get('/Equipment/{id}/{version}', [EquipmentPageController::class, 'indexByDateTime']);

//Administrator
Route::get('/Administrator', [AdministratorController::class, 'index'])->middleware('auth');
Route::post('/Administrator/createEquipment', [EquipmentController::class, 'store']);
Route::post('/Administrator/deleteEquipment', [EquipmentController::class, 'destroy']);
Route::post('/Administrator/unDeleteEquipment', [EquipmentController::class, 'unDelete']);
//Admin Backup System
Route::post('/Administrator/createDatabaseBackup', [AdministratorController::class, 'createDatabaseBackup']);
Route::get( '/Administrator/downloadNewestBackup', [AdministratorController::class, 'downloadNewestBackup'])->middleware('auth');
Route::post('/Administrator/restoreFromBackup', [AdministratorController::class, 'restoreFromBackup']);


Auth::routes();

//Logged in users home page.
Route::get('/home', [UserHomeController::class, 'index'])->name('home');

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
