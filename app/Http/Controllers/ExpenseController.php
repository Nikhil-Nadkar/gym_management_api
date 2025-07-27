<?php

namespace App\Http\Controllers;

use App\Models\expense;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    // add expense
    public function AddExpense(Request $request)
    {
        try {
            expense::create([
                'gym_id' => $request['gym_id'],
                'expenseType' => $request['expenseType'],
                'expenseRemark' => $request['expenseRemark'],
                'expenseAmount' => $request['expenseAmount'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expense added successfully',
            ], 201);
        } catch (Exception $e) {
            Log::error('Expense Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ], 500);
        }
    }

    // get expense
    public function GetExpensebyID($id)
    {
        try {
            $expense = expense::findOrFail($id);

            return response()->json([
                'success' => true,
                'expense' => $expense,
            ], 200);
        } catch (Exception $e) {
            Log::error('Expense Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ], 500);
        }
    }

    // get all expense
    public function GetAllExpense(Request $request)
    {
        try {
            if (empty($request['gym_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'gym_id is requird.',
                ], 404);
            }
            $expense = expense::where('gym_id', $request['gym_id'])->get();
            return response()->json([
                'success' => true,
                'expense' => $expense,
            ], 200);
        } catch (Exception $e) {
            Log::error('Expense Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ], 500);
        }
    }

    // update expense
    public function UpdateExpense(Request $request, $id)
    {
        try {
            $user = expense::findOrFail($id);
            $user->update([
                'gym_id' => $request['gym_id'],
                'expenseType' => $request['expenseType'],
                'expenseRemark' => $request['expenseRemark'],
                'expenseAmount' => $request['expenseAmount'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expense updated successfully',
            ], 200);
        } catch (Exception $e) {
            Log::error('Expense Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ], 500);
        }
    }
    // update expense
    public function DeleteExpense($id)
    {
        try {
            $user = expense::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Expense Deleted successfully',
            ], 200);
        } catch (Exception $e) {
            Log::error('Expense Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ], 500);
        }
    }
}
