<?php

namespace App\Http\Controllers;

use App\Models\visitor;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Empty_;



class VisitorController extends Controller
{
    // add visitor
    public function AddVisitor(Request $request)
    {
        try {
            $vistor  = visitor::create([
                'gym_id' => $request['gym_id'],
                'visitorName' => $request['visitorName'],
                'visitorPhone' => $request['visitorPhone'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Visitor added successfully',
            ], 201);
        } catch (Exception $e) {
            Log::error('Visitor Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ], 500);
        }
    }

    // get visitor
    public function GetVisitorbyID($id)
    {
        try {
            $visitor = visitor::findOrFail($id);

            return response()->json([
                'success' => true,
                'visitor' => $visitor,
            ], 200);
        } catch (Exception $e) {
            Log::error('Visitor Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ], 500);
        }
    }

    // get all visitor
    public function GetAllVisitor(Request $request)
    {
        try {
            if (empty($request['gym_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'gym_id is requird.',
                ], 404);
            }
            $visitors = visitor::where('gym_id', $request['gym_id'])->get();
            return response()->json([
                'success' => true,
                'visitors' => $visitors,
            ], 200);
        } catch (Exception $e) {
            Log::error('Visitor Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ], 500);
        }
    }

    // update visitor
    public function UpdateVisitor(Request $request, $id)
    {
        try {
            $user = visitor::findOrFail($id);
            $user->update([
                'gym_id' => $request['gym_id'],
                'visitorName' => $request['visitorName'],
                'visitorPhone' => $request['visitorPhone'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Visitor updated successfully',
            ], 200);
        } catch (Exception $e) {
            Log::error('Visitor Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ], 500);
        }
    }

    // delete visitor
    public function DeleteVisitor($id)
    {
        try {
            $user = visitor::findOrFail($id);
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Visitor Deleted successfully',
            ], 200);
        } catch (Exception $e) {
            Log::error('Visitor Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ], 500);
        }
    }
}
