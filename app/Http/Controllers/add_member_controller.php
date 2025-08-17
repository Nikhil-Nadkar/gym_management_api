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
use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use PHPUnit\Framework\Attributes\Group;

class add_member_controller extends Controller
{
    // add member
    public function addMember(Request $request)
    {

        DB::beginTransaction();

        try {

            // image store to upload and db
            if ($request->hasFile('profile_photo')) {
                $image = $request->file('profile_photo');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public/uploads', $imageName); // saves in storage/app/public/uploads
                $request['profile_photo'] = 'uploads/' . $imageName; // save path in DB
            }

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

            $member = member_detail::with([
                'gym_plan' => fn($q) => $q->orderBy('created_at', 'desc'),
                'personal_trainer',
                'member_payment'
            ])->where('id', $id)->findOrFail($id);

            // ✅ Make sure `$member->plans` is a collection
            $groupPlan = $member->gym_plan->groupBy(function ($plan) {
                return Carbon::parse($plan->created_at)->toDateString();
            });

            // ✅ Replace the original relation with grouped version
            $member->setRelation('gym_plan', $groupPlan);

            return response()->json(['member' => $member,]);

            // if ($member->isnotEmpty()) {
            //     return response()->json(['member' => $member,]);
            // } else {
            //     return response()->json(['message' => 'Member not found'], 404);
            // }
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
            $allMember = member_detail::with([
                'gym_plan:id,user_id,plan_name',
                'personal_trainer:id,user_id,pt_name,start_date,end_date',
                'member_payment:id,user_id,total_amount,payment_status,next_payment_amount'
            ])->select('id', 'name', 'phone')
                ->where('gym_id', $request['gym_id'])
                ->get();

            // $allPlans = gym_plan::where('gym_id', $request->gym_id)
            //     ->select('id', 'user_id', 'plan_name', 'created_at')
            //     ->orderBy('created_at', 'desc')
            //     ->get()
            // ->groupBy(function ($plan) {
            //     return $plan->user_id;
            // })->map(function ($plans) {
            //     return $plans->groupBy(function ($plan) {
            //         return Carbon::parse($plan->created_at)->format('Y-m-d');
            //     });
            // }); 
            // ->groupBy('user_id');
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

    // update member
    public function updateMember(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $member = member_detail::findOrFail($id);

            $member->update([
                // 'gym_id' => $request['gym_id'],
                'name' => $request['name'],
                'gender' => $request['gender'],
                'dob' => $request['dob'],
                'phone' => $request['phone'],
                'email' => $request['email'],
                'ref_by' => $request['ref_by'],
                'address' => $request['address'],
                'profile_photo' => $request['profile_photo'] ?? '',
            ]);

            $payment = member_payment::where('user_id', $id)->latest()->first();
            $payment->update([
                // 'gym_id' => $request['gym_id'],
                // 'user_id' => $member->id,
                'total_amount' => $request['total_amount'],
                'paid_amount' => $request['paid_amount'],
                'payment_status' => $request['payment_status'],
                'installment' => $request['installment'],
                'next_payment_amount' => $request['next_payment_amount'],
                'next_payment_date' => $request['next_payment_date'],
            ]);


            if (empty($request['pt_name'])) {
                personal_trainer::where('user_id', $id)->delete();
            } else {
                personal_trainer::updateOrCreate(
                    ['user_id' => $id],   // Search by foreign key
                    [
                        'user_id' => $member->id,
                        'gym_id' => $request['gym_id'],
                        'pt_name' => $request['pt_name'],
                        'period' => $request['pt_period'],
                        'start_date' => $request['pt_start_date'],
                        'end_date' => $request['pt_end_date'],
                        'price' => $request['pt_price'],
                    ]
                );
            };




            gym_plan::where('user_id', $id)->delete();
            foreach ($request->plans as $plan) {
                gym_plan::create([
                    'user_id' => $member->id,
                    'gym_id' => $member->gym_id,
                    'plan_name' => $plan['plan_name'],
                    'period' => $plan['period'],
                    'start_date' => $plan['start_date'],
                    'end_date' => $plan['end_date'],
                    'price' => $plan['price'],
                ]);
            }



            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Member Updated successfully',
            ], 200);
        } catch (Exception $e) {
            Log::error('User Login Error: ' . $e->getMessage());

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ]);
        }
    }

    // member renew Plan
    public function renewPlan(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $member = member_detail::findOrFail($id);

            foreach ($request->plans as $plan) {
                gym_plan::create([
                    'user_id' => $member->id,
                    'gym_id' => $member->gym_id,
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
                    'gym_id' => $member->gym_id,
                    'pt_name' => $request['pt_name'],
                    'period' => $request['pt_period'],
                    'start_date' => $request['pt_start_date'],
                    'end_date' => $request['pt_end_date'],
                    'price' => $request['pt_price'],
                ]);
            }

            member_payment::create([
                'gym_id' => $member->gym_id,
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
                'message' => 'Plan renew successfully',
            ], 200);
        } catch (Exception $e) {
            Log::error('User Login Error: ' . $e->getMessage());

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ]);
        }
    }

    // Total profit
    public  function totalProfit(Request $request, $id) {}
}
