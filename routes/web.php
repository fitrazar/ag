<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\WorkingController;
use App\Http\Controllers\ScheduleController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['register' => false]);

Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('index');
    Route::resource('/group', GroupController::class)->except('show');
    Route::resource('/major', MajorController::class)->except('show');
    Route::resource('/subject', SubjectController::class)->except('show');
    Route::resource('/working', WorkingController::class)->except('show');
    Route::get('/working/export-excel', [WorkingController::class, 'exportExcel'])->name('working.exportExcel');
    Route::get('/working/export-pdf', [WorkingController::class, 'exportPdf'])->name('working.exportPdf');
    Route::resource('/student', StudentController::class)->except('show');
    Route::resource('/teacher', TeacherController::class)->except('show');
    // Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/schedule/generate-schedule', [ScheduleController::class, 'generateSchedule'])->name('schedule.index');
    Route::get('/schedule/export-pdf', [ScheduleController::class, 'exportPdf'])->name('schedule.exportPdf');
    Route::get('/schedule/export-excel', [ScheduleController::class, 'exportExcel'])->name('schedule.exportExcel');


});
