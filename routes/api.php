<?php

use App\Http\Controllers\add_member_controller;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\VisitorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/test', function () {
    return "Only Auth user have access to this route";
})->middleware('auth:sanctum');;

// Route::get("/login", [AuthController::class, 'userLogin'])->name('login');


// auth routes->users
Route::post("/signup", [AuthController::class, 'userSignup']);
Route::post("/login", [AuthController::class, 'userLogin']);

// member routes
Route::post('/addmember', [add_member_controller::class, 'addMember'])->middleware('auth:sanctum');
Route::get('/getmemberbyid/{id}', [add_member_controller::class, 'getMemberByID']);
Route::get('/getallmember', [add_member_controller::class, 'getAllMembers']);
Route::delete('/deletememberbyid/{id}', [add_member_controller::class, 'DeleteMemberById']);
Route::put('/updatemember/{id}', [add_member_controller::class, 'updateMember']);
Route::post('/renewmemberplan/{id}', [add_member_controller::class, 'renewPlan']);

// visitor routes
Route::post('/addvisitor', [VisitorController::class, 'AddVisitor']);
Route::get('/getvisitorbyid/{id}', [VisitorController::class, 'GetVisitorbyID']);
Route::get('/getallvisitor', [VisitorController::class, 'GetAllVisitor']);
Route::put('/updatevisitor/{id}', [VisitorController::class, 'UpdateVisitor']);
Route::delete('/deletevisitor/{id}', [VisitorController::class, 'DeleteVisitor']);

// Expense routes
Route::post('/addexpense', [ExpenseController::class, 'AddExpense']);
Route::get('/getexpensebyid/{id}', [ExpenseController::class, 'GetExpensebyID']);
Route::get('/getallexpense', [ExpenseController::class, 'GetAllExpense']);
Route::put('/updateexpense/{id}', [ExpenseController::class, 'UpdateExpense']);
Route::delete('/deleteexpense/{id}', [ExpenseController::class, 'DeleteExpense']);
