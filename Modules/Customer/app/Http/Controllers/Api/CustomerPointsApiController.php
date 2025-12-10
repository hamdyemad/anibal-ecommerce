<?php

namespace Modules\Customer\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Modules\SystemSetting\app\Models\UserPoints;
use Modules\SystemSetting\app\Models\UserPointsTransaction;

class CustomerPointsApiController extends Controller
{
    use Res;

    /**
     * Get customer's points summary
     */
    public function myPoints(Request $request)
    {
        try {
            $user = $request->user();

            // Get user points
            $userPoints = UserPoints::where('user_id', $user->id)->first();

            // Get points setting for value conversion
            $pointsSetting = \Modules\SystemSetting\app\Models\PointsSetting::first();
            $pointsValue = $pointsSetting ? $pointsSetting->points_value : 1;

            // Get expiring soon transactions (expiring within 30 days)
            $expiringSoon = UserPointsTransaction::where('user_id', $user->id)
                ->whereNotNull('expires_at')
                ->where('expires_at', '>', now())
                ->where('expires_at', '<=', now()->addDays(30))
                ->orderBy('expires_at', 'asc')
                ->get()
                ->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'points' => $transaction->points,
                        'type' => $transaction->type,
                        'expires_at' => $transaction->expires_at,
                        'days_until_expiry' => $transaction->expires_at->diffInDays(now()),
                    ];
                });

            $data = [
                'total_points' => $userPoints ? number_format($userPoints->total_points, 2) : '0.00',
                'earned_points' => $userPoints ? number_format($userPoints->earned_points, 2) : '0.00',
                'redeemed_points' => $userPoints ? number_format($userPoints->redeemed_points, 2) : '0.00',
                'expired_points' => $userPoints ? number_format($userPoints->expired_points, 2) : '0.00',
                'available_points' => $userPoints ? number_format($userPoints->available_points, 2) : '0.00',
                'points_value' => $pointsValue,
                'expiring_soon' => $expiringSoon,
            ];

            return $this->sendRes(
                trans('systemsetting::points.points_retrieved_successfully'),
                true,
                $data
            );
        } catch (\Exception $e) {
            return $this->sendRes(
                trans('common.error_occurred'),
                false,
                [],
                [],
                500
            );
        }
    }

    /**
     * Get customer's points transactions
     */
    public function transactions(Request $request)
    {
        try {
            $user = $request->user();

            // Get filter parameters
            $type = $request->input('type');
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);

            // Build query
            $query = UserPointsTransaction::where('user_id', $user->id);

            // Apply type filter
            if ($type) {
                $query->where('type', $type);
            }

            // Get total count before pagination
            $total = $query->count();

            // Get paginated transactions
            $transactions = $query->latest()
                ->paginate($perPage, ['*'], 'page', $page);

            // Format transactions
            $formattedTransactions = $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'points' => number_format($transaction->points, 2),
                    'type' => $transaction->type,
                    'type_label' => trans('systemsetting::points.type_' . $transaction->type),
                    'description' => $transaction->description,
                    'expires_at' => $transaction->expires_at ? $transaction->expires_at->format('Y-m-d H:i:s') : null,
                    'is_expired' => $transaction->is_expired,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                ];
            });

            $data = [
                'transactions' => $formattedTransactions,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => $transactions->lastPage(),
                    'from' => $transactions->firstItem(),
                    'to' => $transactions->lastItem(),
                ],
            ];

            return $this->sendRes(
                trans('systemsetting::points.transactions_retrieved_successfully'),
                true,
                $data
            );
        } catch (\Exception $e) {
            return $this->sendRes(
                trans('common.error_occurred'),
                false,
                [],
                [],
                500
            );
        }
    }
}
