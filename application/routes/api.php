<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Developer;
use App\Http\Controllers\Hobby;

Route::prefix("developers")->group(function(){
    Route::post('', [Developer::class, 'createDeveloper']);
    Route::get('', [Developer::class, 'getDevelopers']);
});

Route::prefix("hobbies")->group(function(){
    Route::post('', [Hobby::class, 'createHobby']);
});