<?php

namespace Modules\SystemSetting\app\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Modules\SystemSetting\app\Models\UserPoints;

class SeedUserPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:user-points {--force : Force seeding without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed user points for all customers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will create user points records for all customers. Do you want to continue?')) {
                $this->info('Seeding cancelled.');
                return 0;
            }
        }

        // Get all customers
        $customers = User::where('user_type_id', 5) // Customer type
            ->orWhere('user_type_id', 6) // Include other customer types if needed
            ->get();

        $created = 0;
        $skipped = 0;

        $this->withProgressBar($customers, function ($customer) use (&$created, &$skipped) {
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

                $created++;
            } else {
                $skipped++;
            }
        });

        $this->newLine();
        $this->info("User points seeding completed!");
        $this->info("Created: {$created} records");
        $this->info("Skipped: {$skipped} records (already exist)");

        return 0;
    }
}
