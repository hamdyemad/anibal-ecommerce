<?php

namespace Modules\SystemSetting\database\seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\SystemSetting\app\Models\UserPoints;

class UserPointsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all customers (users with user_type_id = 5 or similar)
        $customers = User::where('user_type_id', 5) // Adjust based on your customer type ID
            ->orWhere('user_type_id', 6) // Include other customer types if needed
            ->get();

        foreach ($customers as $customer) {
            // Check if user already has points record
            $userPoints = UserPoints::where('user_id', $customer->id)->first();

            if (!$userPoints) {
                // Generate random points data
                $totalPoints = rand(100, 5000);
                $earnedPoints = rand(50, $totalPoints);
                $redeemedPoints = rand(0, (int)($earnedPoints * 0.5));
                $expiredPoints = rand(0, (int)($earnedPoints * 0.2));

                // Create user points record
                UserPoints::create([
                    'user_id' => $customer->id,
                    'total_points' => $totalPoints,
                    'earned_points' => $earnedPoints,
                    'redeemed_points' => $redeemedPoints,
                    'expired_points' => $expiredPoints,
                ]);

                echo "Created points for customer: {$customer->name}\n";
            } else {
                echo "Points already exist for customer: {$customer->name}\n";
            }
        }

        echo "User points seeding completed!\n";
    }
}
