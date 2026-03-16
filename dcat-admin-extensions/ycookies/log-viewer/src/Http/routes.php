<?php

use Dcat\Admin\LogViewer\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::get('ycookies/log-viewer', Controllers\LogViewerController::class.'@index')->name('ycookies.log-viewer');
Route::get('ycookies/log-viewer/{file}', Controllers\LogViewerController::class.'@index')->name('ycookies.log-viewer.log-viewer-file');
Route::get('ycookies/log-viewer/download', Controllers\LogViewerController::class.'@download')->name('ycookies.log-viewer.download');

/*Route::get('/', ['as' => 'dcat-log-viewer', 'uses' => 'LogController@index',]);
Route::get('download', ['as' => 'dcat-log-viewer.download', 'uses' => 'LogController@download',]);
Route::get('{file}', ['as' => 'dcat-log-viewer.file', 'uses' => 'LogController@index',]);*/

/*Route::get('ycookies/logs', 'Encore\Admin\LogViewer\LogController@index')->name('log-viewer-index');
Route::get('ycookies/logs/{file}', 'Encore\Admin\LogViewer\LogController@index')->name('log-viewer-file');
Route::get('ycookies/logs/{file}/tail', 'Encore\Admin\LogViewer\LogController@tail')->name('log-viewer-tail');*/
