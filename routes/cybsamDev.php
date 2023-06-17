<?php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Http\Controllers\adminController;
use App\Http\Controllers\controller;

//basic user
Route::prefix('User')->middleware('auth')->group(function(){
    Route::get('Dashboard',[App\Http\Controllers\HomeController::class, 'index'])->name('basuserDash');
});
//admin
Route::prefix('Admin')->middleware('auth')->group(function(){
    Route::get('Dashboard',[App\Http\Controllers\adminController::class, 'index'])->name('adminDash');
});



?>