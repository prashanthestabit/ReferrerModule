<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\ReferrerModule\Http\Controllers\ReferrerModuleController;

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



Route::group(['middleware' => ['jwt.verify']], function() {

    Route::get('get-referrer-id', [ ReferrerModuleController::class,'getReferrerId'])->name('getReferrerId');

    Route::post('referrers/store', [ ReferrerModuleController::class,'store'])->name('referrer.save');

    Route::put('referrers/update/{id}', [ ReferrerModuleController::class,'update'])->name('referrer.update');

    Route::delete('referrers/{id}', [ ReferrerModuleController::class,'destroy'])->name('referrer.delete');

});
