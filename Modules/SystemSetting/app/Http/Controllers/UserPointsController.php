<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\SystemSetting\app\Models\UserPoints;
use Illuminate\Http\Request;

class UserPointsController extends Controller
{
    /**
     * Display user points list
     */
    public function index()
    {
        $data = [
            'title' => trans('systemsetting::points.user_points_management'),
        ];

        return view('systemsetting::user_points.index', $data);
    }

    /**
     * Get user points datatable
     */
    public function datatable(Request $request)
    {
        try {
            $query = UserPoints::with(['user'])->latest();

            // Search filter
            $searchValue = $request->input('search.value');
            if ($searchValue) {
                $query->whereHas('user', function ($q) use ($searchValue) {
                    $q->where('name', 'like', "%$searchValue%")
                      ->orWhere('email', 'like', "%$searchValue%");
                });
            }

            // Total records
            $totalRecords = $query->count();

            // Apply pagination
            $perPage = $request->input('length', 10);
            $skip = $request->input('start', 0);
            $userPoints = $query->skip($skip)->take($perPage)->get();

            // Format data for datatable
            $data = [];
            foreach ($userPoints as $index => $point) {
                $data[] = [
                    'id' => $point->id,
                    'index' => $skip + $index + 1,
                    'user_information' => [
                        'name' => $point->user->name ?? '-',
                        'email' => $point->user->email ?? '-',
                        'photo' => $point->user->photo ?? null,
                    ],
                    'total_points' => number_format($point->total_points, 2),
                    'earned_points' => number_format($point->earned_points, 2),
                    'redeemed_points' => number_format($point->redeemed_points, 2),
                    'expired_points' => number_format($point->expired_points, 2),
                ];
            }

            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show user points details
     */
    public function show($lang, $countryCode, $id)
    {
        try {
            $userPoint = UserPoints::with(['user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $userPoint->id,
                    'user_name' => $userPoint->user->name,
                    'user_email' => $userPoint->user->email,
                    'user_photo' => $userPoint->user->photo,
                    'total_points' => $userPoint->total_points,
                    'earned_points' => $userPoint->earned_points,
                    'redeemed_points' => $userPoint->redeemed_points,
                    'expired_points' => $userPoint->expired_points,
                    'available_points' => $userPoint->available_points,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('common.error_occurred'),
            ], 404);
        }
    }

    /**
     * Get user points transactions
     */
    public function transactions(Request $request, $lang, $countryCode, $userId)
    {
        try {
            $query = \Modules\SystemSetting\app\Models\UserPointsTransaction::where('user_id', $userId)
                ->latest();

            // Filter by type
            $type = $request->input('type');
            if ($type) {
                $query->where('type', $type);
            }

            // Total records
            $totalRecords = $query->count();

            // Apply pagination
            $perPage = $request->input('length', 10);
            $skip = $request->input('start', 0);
            $transactions = $query->skip($skip)->take($perPage)->get();

            // Format data
            $data = [];
            foreach ($transactions as $index => $transaction) {
                $data[] = [
                    'id' => $transaction->id,
                    'index' => $skip + $index + 1,
                    'points' => number_format($transaction->points, 2),
                    'type' => $transaction->type,
                    'type_label' => trans('systemsetting::points.type_' . $transaction->type),
                    'description' => $transaction->description ?? '-',
                    'expires_at' => $transaction->expires_at ? $transaction->expires_at->format('Y-m-d H:i') : '-',
                    'is_expired' => $transaction->is_expired,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i'),
                ];
            }

            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
