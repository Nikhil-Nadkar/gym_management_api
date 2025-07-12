<?php

namespace App\Http\Controllers;

use App\Models\member_detail;
use App\Models\gym_plan;
use App\Models\personal_trainer;
use App\Models\member_payment;
use App\Models\plan_types;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\DB;

class add_member_controller extends Controller
{
    // add member
    public function addMember(Request $request)
    {

        DB::beginTransaction();

        try {
            // var_dump("memberdetails");
            $member = member_detail::create([
                'gym_id' => $request['gym_id'],
                'name' => $request['name'],
                'gender' => $request['gender'],
                'dob' => $request['dob'],
                'phone' => $request['phone'],
                'email' => $request['email'],
                'ref_by' => $request['ref_by'],
                'address' => $request['address'],
                'profile_photo' => $request['profile_photo'] ?? '',
            ]);



            foreach ($request->plans as $plan) {
                $exists = plan_types::where('plan_name', $plan['plan_name'])
                    ->where('gym_id', $request['gym_id'])
                    ->exists();

                if (!$exists) {
                    plan_types::create([
                        'gym_id' => $request['gym_id'],
                        'plan_name' => $plan['plan_name'],
                    ]);
                }
            }


            foreach ($request->plans as $plan) {
                gym_plan::create([
                    'user_id' => $member->id,
                    'gym_id' => $request['gym_id'],
                    'plan_name' => $plan['plan_name'],
                    'period' => $plan['period'],
                    'start_date' => $plan['start_date'],
                    'end_date' => $plan['end_date'],
                    'price' => $plan['price'],
                ]);
            }

            if (!empty($request['pt_name'])) {
                personal_trainer::create([
                    'user_id' => $member->id,
                    'gym_id' => $request['gym_id'],
                    'pt_name' => $request['pt_name'],
                    'period' => $request['pt_period'],
                    'start_date' => $request['pt_start_date'],
                    'end_date' => $request['pt_end_date'],
                    'price' => $request['pt_price'],
                ]);
            }

            member_payment::create([
                'gym_id' => $request['gym_id'],
                'user_id' => $member->id,
                'total_amount' => $request['total_amount'],
                'paid_amount' => $request['paid_amount'],
                'payment_status' => $request['payment_status'],
                'installment' => $request['installment'],
                'next_payment_amount' => $request['next_payment_amount'],
                'next_payment_date' => $request['next_payment_date'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Member added successfully',
            ], 201);
        } catch (Exception $e) {
            Log::error('User Login Error: ' . $e->getMessage());

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ], 500);
        }
    }

    // get member
    public function getMemberByID(Request $request, $id)
    {
        try {

            $member = member_detail::with('gym_plan', 'personal_trainer', 'member_payment')
                ->where('id', $id)->get();

            if ($member->isnotEmpty()) {
                return response()->json(['member' => $member]);
            } else {
                return response()->json(['message' => 'Member not found'], 404);
            }
        } catch (Exception $e) {
            Log::error('User Login Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ]);
        }
    }

    // get all member by gym
    public function getAllMembers(Request $request)
    {
        try {
            $allMember = member_detail::with(['gym_plan', 'personal_trainer', 'member_payment'])
                ->where('gym_id', $request->gym_id)
                ->get();
            return response()->json(['data' => $allMember], 200);
        } catch (Exception $e) {
            Log::error('User Login Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ]);
        }
    }

    // delete by ID
    public function DeleteMemberById(Request $request, $id)
    {
        try {
            $member = member_detail::find($id);
            if ($member) {
                $member->delete();
                return response()->json(['message' => 'User deleted']);
            } else {
                return response()->json(['message' => 'User not found'], 404);
            };
        } catch (Exception $e) {
            Log::error('User Login Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ]);
        }
    }
}
