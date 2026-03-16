<?php

use Dcat\Admin\Scheduling\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('scheduling', Controllers\SchedulingController::class.'@index');
Route::get('scheduling/logview', Controllers\SchedulingController::class.'@logview');
Route::get('scheduling/taskrun', Controllers\SchedulingController::class.'@taskrun');
Route::get('scheduling-run-page', Controllers\SchedulingController::class.'@runpage')->name('scheduling-runpage');
Route::post('scheduling-run', Controllers\SchedulingController::class.'@run')->name('scheduling-run');
Route::post('scheduling-task-run', Controllers\SchedulingController::class.'@runEvent')->name('scheduling-task-run');

//Route::get('ycookies/scheduling', Controllers\SchedulingController::class.'@index')->name('scheduling-index');
//Route::post('ycookies/scheduling/run', Controllers\SchedulingController::class.'runEvent')->name('scheduling-run');
