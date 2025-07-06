<?php

use App\Http\Controllers\add_member_controller;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/test', function () {
    return "Only Auth user have access to this route";
})->middleware('auth:sanctum');;

// Route::get("/login", [AuthController::class, 'userLogin'])->name('login');

Route::post("/signup", [AuthController::class, 'userSignup']);
Route::post("/login", [AuthController::class, 'userLogin']);
Route::post('/addmember', [add_member_controller::class, 'addMember'])->middleware('auth:sanctum');
