<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Password\ChangeController;

Route::middleware(['auth:sanctum'])->group(function () {

// ---------------- PASSWORD URL's ---------------- //
Route::post('v1/changePassword', ChangeController::class)->name('v1.changePassword');

// ---------------- DEPARTMENTS URL's ---------------- //
Route::prefix('v1')->group(function () {
    Route::get('departments/list', [App\Http\Controllers\DepartmentsController::class, 'getDepartments']);
    Route::post('departments/create', [App\Http\Controllers\DepartmentsController::class, 'createDepartment']);
    Route::post('departments/update/{id}', [App\Http\Controllers\DepartmentsController::class, 'updateDepartment']);
    Route::delete('departments/delete/{id}', [App\Http\Controllers\DepartmentsController::class, 'deleteDepartment']);
});

// ---------------- CORES URL's ---------------- //
Route::prefix('v1')->group(function () {
    Route::get('cores/list', [App\Http\Controllers\CoresController::class, 'getCores']);
    Route::post('cores/create', [App\Http\Controllers\CoresController::class, 'createCore']);
    Route::post('cores/update/{id}', [App\Http\Controllers\CoresController::class, 'updateCore']);
    Route::delete('cores/delete/{id}', [App\Http\Controllers\CoresController::class, 'deleteCore']);
});

// ---------------- POSITIONS URL's ---------------- //
Route::prefix('v1')->group(function () {
    Route::get('position/list', [App\Http\Controllers\PositionController::class, 'getProfiles']);
    Route::post('position/create', [App\Http\Controllers\PositionController::class, 'createProfile']);
    Route::post('position/update/{id}', [App\Http\Controllers\PositionController::class, 'updateProfile']);
    Route::delete('position/delete/{id}', [App\Http\Controllers\PositionController::class, 'deleteProfile']);
});

// ---------------- JUSTIFICATIONS URL's ---------------- //
Route::prefix('v1')->group(function () {
    Route::get('justification/list', [App\Http\Controllers\JustificationController::class, 'getJustifications']);
    Route::post('justification/create', [App\Http\Controllers\JustificationController::class, 'createJustifications']);
    Route::post('justification/accept/{id}', [App\Http\Controllers\JustificationController::class, 'acceptJustifications']);
    Route::post('justification/decline/{id}', [App\Http\Controllers\JustificationController::class, 'declineJustifications']);
});

// ---------------- USERS URL´s ---------------------- //
Route::prefix('v1')->group(function () {
    Route::get('users', [App\Http\Controllers\UserController::class, 'index']);
    Route::get('users/{id}', [App\Http\Controllers\UserController::class, 'show']);
    Route::get('users/{id}/update', [App\Http\Controllers\UserController::class, 'update']);
});

// ---------------- BIRTHDAYS URL's ---------------- //
Route::prefix('v1')->group(function () {
    Route::get('/birthday/details', [\App\Http\Controllers\BirthdayController::class, 'detailsbirthdayMonth']);
    Route::get('/birthday/nextBirthday', [\App\Http\Controllers\BirthdayController::class, 'getUpcomingBirthdaysWithUsers']);
});

// ---------------- ATTENDANCES URL's --------------- //
Route::prefix('v1')->group(function () {
    Route::get('attendance', [App\Http\Controllers\AttendanceController::class, 'getAttendances']);
    Route::post('attendance/create', [App\Http\Controllers\AttendanceController::class, 'createAttendance']);
    Route::post('attendance/id', [App\Http\Controllers\AttendanceController::class, 'show']);
    Route::get('attendance/procedure', [App\Http\Controllers\AttendanceController::class, 'callDatabaseProcedure']);
});

// ---------------- EVALUATION URL's --------------- //
Route::prefix('v1')->group(function () {
    Route::get('evaluation/list', [App\Http\Controllers\EvaluationController::class, 'getEvaluations']);
    Route::post('evaluation/create', [App\Http\Controllers\EvaluationController::class, 'createEvaluation']);
    Route::post('evaluation/notes/{id}', [App\Http\Controllers\EvaluationController::class, 'storeNotes']);
});

// ---------------- SCHEDULE URL's --------------- //
Route::prefix('v1')->group(function () {
    Route::get('schedule/list', [App\Http\Controllers\ScheduleController::class, 'getSchedules']);
    Route::post('schedule/check', [App\Http\Controllers\ScheduleController::class, 'checkAttendance']);
    Route::post('schedule/create', [App\Http\Controllers\ScheduleController::class, 'createSchedule']);
});


});

