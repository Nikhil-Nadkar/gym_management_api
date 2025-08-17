<?php

namespace App\Http\Controllers;

use App\Models\member_payment;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function AnalyticsDashBoard(Request $request, $id)
    {
        try {
            // store filter value from user
            $month = $request['month'];

            // store result
            $result = [];

            // calculate totalProfit by month give by user
            $profit = DB::table('member_payments')
                ->where('gym_id', $id)
                ->when($month != 'all', function ($query) use ($month) {
                    $query->whereBetween('created_at', [
                        Carbon::now()->subMonth($month)->startOfDay(),
                        Carbon::now()->endOfDay(),
                    ]);
                })
                ->select('gym_id', DB::raw('SUM(paid_amount) as total_profit'))
                ->groupBy('gym_id')
                ->get();

            $result['TotalProfit'] = $profit[0]->total_profit ?? 0;

            // calculate totalExpense by month give by user
            $Expense = DB::table('expenses')
                ->where('gym_id', $id)
                ->when($month != 'all', function ($query) use ($month) {
                    $query->whereBetween('created_at', [
                        Carbon::now()->subMonth($month)->startOfDay(),
                        Carbon::now()->endOfDay(),
                    ]);
                })
                ->select('gym_id', DB::raw('SUM(expenseAmount) as expense'))
                ->groupBy('gym_id')
                ->get();

            $result['TotalExpense'] = $Expense[0]->expense ?? 0;

            // calculate newMember by month give by user
            $NewMember = DB::table('member_details')
                ->where('gym_id', $id)
                ->when($month != 'all', function ($query) use ($month) {
                    $query->whereBetween('created_at', [
                        Carbon::now()->subMonth($month)->startOfDay(),
                        Carbon::now()->endOfDay(),
                    ]);
                })
                ->select('gym_id', DB::raw('COUNT(name) as count'))
                ->groupBy('gym_id')
                ->get();

            $result['NewMember'] = $NewMember[0]->count ?? 0;


            // calculate NewVisitor by month give by user
            $NewVisitor = DB::table('visitors')
                ->where('gym_id', $id)
                ->when($month != 'all', function ($query) use ($month) {
                    $query->whereBetween('created_at', [
                        Carbon::now()->subMonth($month)->startOfDay(),
                        Carbon::now()->endOfDay(),
                    ]);
                })
                ->select('gym_id', DB::raw('COUNT(visitorName) as count'))
                ->groupBy('gym_id')
                ->get();

            $result['NewVisitor'] = $NewVisitor[0]->count ?? 0;

            // get upcoming DOB
            $DOB = DB::table('member_details')
                ->where('gym_id', $id)
                ->whereBetween(DB::raw("DATE_FORMAT(dob, '%m-%d')"), [
                    Carbon::now()->format('m-d'),
                    Carbon::now()->addWeek()->format('m-d'),
                ])
                ->select('gym_id', DB::raw('COUNT(name) as count'))
                ->groupBy('gym_id')
                ->get();

            $result['DOB'] = $DOB[0]->count ?? 0;

            // get upcoming renewal
            $Renewal = DB::table('member_details')
                ->where('gym_id', $id)
                ->whereMonth('planEndDate', Carbon::now()->month)
                ->select('gym_id', DB::raw('COUNT(name) as count'))
                ->groupBy('gym_id')
                ->get();

            $result['ThisMonthRenewal'] = $Renewal[0]->count ?? 0;

            // inactive member
            $InactiveMember = DB::table('member_details')
                ->where('gym_id', $id)
                ->where('planEndDate', '<', Carbon::today())
                ->select('gym_id', DB::raw('COUNT(name) as count'))
                ->groupBy('gym_id')
                ->get();

            $result['InactiveMember'] = $InactiveMember[0]->count ?? 0;

            // active member
            $ActiveMember = DB::table('member_details')
                ->where('gym_id', $id)
                ->where('planEndDate', '>', Carbon::today())
                ->select('gym_id', DB::raw('COUNT(name) as count'))
                ->groupBy('gym_id')
                ->get();

            $result['ActiveMember'] = $ActiveMember[0]->count ?? 0;



            return response()->json([
                'success' => true,
                'result' => $result,

            ], 200);
        } catch (Exception $e) {
            Log::error('AnalyticsDashBoard Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error'   => $e->getMessage(), // In production, you might want to hide this
            ], 500);
        }
    }
}
