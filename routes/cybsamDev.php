<?php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Http\Controllers\adminController;
use App\Http\Controllers\controller;



Route::prefix('Admin')->middleware('auth')->group(function(){
    Route::get('Dashboard',[App\Http\Controllers\adminController::class, 'index'])->name('adminHome');
});



?>