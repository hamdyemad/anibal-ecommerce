<?php

namespace Modules\Customer\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Modules\Customer\app\Models\Customer;
use Modules\Customer\app\Transformers\CustomerApiResource;

class GoogleAuthController extends Controller
{
    use Res;

    /**
     * Redirect to Google OAuth (for web flow)
     * GET /api/v1/auth/google/redirect
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    /**
     * Handle Google callback (for web flow)
     * GET /api/v1/auth/google/callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();

            $customer = $this->findOrCreateCustomer($googleUser);

            if (!$customer->status) {
                // Redirect to React with error
                $frontendUrl = env('FRONT_END_URL', 'http://127.0.0.1:3000');
                return redirect()->to($frontendUrl . '/auth/google/callback?error=account_inactive');
            }

            // Create Sanctum token
            $token = $customer->createToken('google-auth')->plainTextToken;

            // Get the frontend URL from env
            $frontendUrl = env('FRONT_END_URL', 'http://127.0.0.1:3000');
            
            // Redirect to React with token and user data
            return redirect()->to(
                $frontendUrl . '/auth/google/callback?' . http_build_query([
                    'token' => $token,
                    'user' => json_encode([
                        'id' => $customer->id,
                        'first_name' => $customer->first_name,
                        'last_name' => $customer->last_name,
                        'email' => $customer->email,
                        'avatar' => $customer->avatar,
                    ])
                ])
            );

        } catch (\Exception $e) {
            // Redirect to React with error
            $frontendUrl = env('FRONT_END_URL', 'http://127.0.0.1:3000');
            return redirect()->to(
                $frontendUrl . '/auth/google/callback?error=' . urlencode($e->getMessage())
            );
        }
    }

    /**
     * Login with Google access token (for mobile/SPA apps)
     * POST /api/v1/auth/google/login
     * Body: { "access_token": "google_access_token" }
     */
    public function loginWithGoogle(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
        ]);

        try {
            // Get user info from Google using the access token
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($request->access_token);

            $customer = $this->findOrCreateCustomer($googleUser);

            if (!$customer->status) {
                return $this->sendRes(
                    trans('customer::customer.account_inactive'),
                    false,
                    [],
                    [],
                    403
                );
            }

            // Create Sanctum token
            $token = $customer->createToken('google-auth')->plainTextToken;

            return $this->sendRes(
                trans('customer::customer.login_successful'),
                true,
                [
                    'customer' => CustomerApiResource::make($customer)->resolve(),
                    'token' => $token,
                ],
                [],
                200
            );

        } catch (\Exception $e) {
            return $this->sendRes(
                trans('customer::customer.google_login_failed') . ': ' . $e->getMessage(),
                false,
                [],
                [],
                500
            );
        }
    }

    /**
     * Find or create customer from Google user data
     */
    private function findOrCreateCustomer($googleUser)
    {
        // Try to find customer by email
        $customer = Customer::where('email', $googleUser->email)->first();

        if ($customer) {
            // Update Google ID and avatar if not set
            if (!$customer->google_id) {
                $customer->google_id = $googleUser->id;
            }
            // Save Google avatar to both avatar and image fields
            if ($googleUser->avatar) {
                $customer->avatar = $googleUser->avatar;
                // Download and save the Google avatar image
                if (!$customer->image) {
                    $customer->image = $this->downloadGoogleAvatar($googleUser->avatar, $customer->id);
                }
            }
            $customer->save();

            return $customer;
        }

        // Split the name into first and last name
        $nameParts = explode(' ', $googleUser->name, 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        // Create new customer
        $customer = Customer::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $googleUser->email,
            'google_id' => $googleUser->id,
            'avatar' => $googleUser->avatar,
            'password' => Hash::make(Str::random(32)), // Random password
            'email_verified_at' => now(), // Google emails are verified
            'status' => true, // Active by default
            'country_id' => 1, // Default country (Egypt) - adjust as needed
        ]);

        // Download and save the Google avatar image
        if ($googleUser->avatar) {
            $imagePath = $this->downloadGoogleAvatar($googleUser->avatar, $customer->id);
            if ($imagePath) {
                $customer->image = $imagePath;
                $customer->save();
            }
        }

        return $customer;
    }

    /**
     * Download Google avatar and save to storage
     */
    private function downloadGoogleAvatar($avatarUrl, $customerId)
    {
        try {
            // Get the image content from Google
            $imageContent = file_get_contents($avatarUrl);
            
            if ($imageContent === false) {
                return null;
            }

            // Generate a unique filename using the same path as regular customer avatars
            $filename = 'customers/avatars/google_' . $customerId . '_' . time() . '.jpg';
            
            // Save to storage/app/public/customers/avatars/
            $path = public_path('storage/' . $filename);
            
            // Create directory if it doesn't exist
            $directory = dirname($path);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Save the file
            file_put_contents($path, $imageContent);
            
            return $filename;
        } catch (\Exception $e) {
            // If download fails, just return null and continue
            \Log::warning('Failed to download Google avatar: ' . $e->getMessage());
            return null;
        }
    }
}
