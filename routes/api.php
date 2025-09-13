<?php

use App\Models\Sakit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;


Route::get('/test', function(){
    $res = Sakit::orderBy('tanggal', 'desc')->first();
    return $res;
});