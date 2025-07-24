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
Route::post('/getmemberbyid/{id}', [add_member_controller::class, 'getMemberByID']);
Route::post('/getallmember', [add_member_controller::class, 'getAllMembers']);
Route::delete('/deletememberbyid/{id}', [add_member_controller::class, 'DeleteMemberById']);
Route::put('/updatemember/{id}', [add_member_controller::class, 'updateMember']);
Route::post('/renewmemberplan/{id}', [add_member_controller::class, 'renewPlan']);
