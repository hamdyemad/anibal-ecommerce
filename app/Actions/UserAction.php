<?php

namespace App\Actions;

use App\Mail\ResetPasswordMail;
use App\Models\ActivityLog;
use App\Models\User;
use App\Traits\Res;
use App\Traits\LogsActivity;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserAction {

    use Res, LogsActivity;

    public function login($request) {
        $remember = $request->filled('remember');
        $user = User::where('email', $request->email)
            ->with(['vendorByUser:id,user_id,active'])
            ->first();

        if($user) {
            // Check if user account is inactive
            if(!$user->active) {
                $this->logActivityAsync($user, 'login_failed', 'activity_log.login_failed_inactive', $user->email);
                return $this->sendData(__('auth.account_not_activated'), false);
            }

            // Check if user account is blocked
            if($user->block) {
                $this->logActivityAsync($user, 'login_failed', 'activity_log.login_failed_blocked', $user->email);
                return $this->sendData(__('auth.account_blocked'), false);
            }

            // Check if user is a vendor owner (not vendor_id) and vendor is inactive
            if (!$user->vendor_id && $user->relationLoaded('vendorByUser')) {
                $vendor = $user->vendorByUser;
                if ($vendor && !$vendor->active) {
                    $this->logActivityAsync($user, 'login_failed', 'activity_log.login_failed_vendor_inactive', $user->email);
                    return $this->sendData(__('auth.vendor_not_activated'), false);
                }
            }
        }

        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password], $remember)){
            // Log successful login asynchronously
            $user = Auth::user();
            $this->logActivityAsync($user, 'login', 'activity_log.login_success', $request->email);
            return $this->sendData('',true);
        }else{
            // Log failed login attempt - invalid credentials
            if ($user) {
                $this->logActivityAsync($user, 'login_failed', 'activity_log.login_failed_credentials', $user->email);
            }

            return $this->sendData(__('auth.invalid_credentials'), false);
        }
    }

    /**
     * Log activity asynchronously to avoid blocking login
     */
    private function logActivityAsync($user, string $action, string $descriptionKey, string $email)
    {
        try {
            dispatch(function() use ($user, $action, $descriptionKey, $email) {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => $action,
                    'model' => get_class($user),
                    'model_id' => $user->id,
                    'description_key' => $descriptionKey,
                    'description_params' => [],
                    'properties' => ['email' => $email],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            })->afterResponse();
        } catch (\Exception $e) {
            // Silently fail - logging should not break login
        }
    }

    public function forgetPassword($request) {
        $user = User::where('email', $request->email)->first();
        try {
            $reset_code = rand(100000, 900000);
            $data = [
                'email' => $user->email,
                'code' => $reset_code
            ];
            $user->reset_code = Hash::make($reset_code);
            $user->reset_code_timestamp = Carbon::now()->addMinutes(15);
            $user->save();
            Mail::to("$user->email")->send(new ResetPasswordMail($data));

            // Log password reset request
            $this->logActivityForUser(
                user: $user,
                action: 'password_reset_requested',
                descriptionKey: 'activity_log.password_reset_sent',
                properties: ['email' => $user->email],
                model: $user,
            );
        } catch(Exception $e) {
            // Log failed password reset request
            $this->logActivityForUser(
                user: $user,
                action: 'password_reset_failed',
                descriptionKey: 'activity_log.password_reset_email_failed',
                properties: ['error' => $e->getMessage()],
                model: $user,
            );
        }
        return $this->sendData(__('auth.reset password sent to your email please check it'),true, ['uuid' => $user->uuid]);
    }

    public function resetPassword($request, $uuid) {
        $user = User::where('uuid', $uuid)->first();
        if (!$user) {
            return $this->sendData(__('auth.Email Not Exists'),false);
        }

        if (!Hash::check($request->reset_code, $user->reset_code)) {
            $this->logActivityForUser(
                user: $user,
                action: 'password_reset_failed',
                descriptionKey: 'activity_log.password_reset_invalid_code',
                descriptionParams: [],
                model: $user,
                properties: ['email' => $user->email]
            );
            return $this->sendData(__('auth.The provided reset code is invalid.'),false);
        }

        if (Carbon::now()->gt($user->reset_code_timestamp)) {
            $this->logActivityForUser(
                user: $user,
                action: 'password_reset_failed',
                descriptionKey: 'activity_log.password_reset_expired_code',
                descriptionParams: [],
                model: $user,
                properties: ['email' => $user->email]
            );
            return $this->sendData(__('auth.reset code is ignored please reset again'),false);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'reset_code' => null,
            'reset_code_timestamp' => null,
        ]);

        // Log successful password reset
        $this->logActivityForUser(
            user: $user,
            action: 'password_reset_success',
            descriptionKey: 'activity_log.password_reset_completed',
            descriptionParams: [],
            model: $user,
            properties: ['email' => $user->email]
        );

        return $this->sendData(__('auth.your password changed success please login'),true);
    }

    public function logout() {
        $user = Auth::user();
        
        // Log logout before actually logging out
        if ($user) {
            $this->logActivity(
                action: 'logout',
                descriptionKey: 'activity_log.logout_success',
                descriptionParams: [],
                model: $user,
                properties: ['email' => $user->email]
            );
        }

        Auth::logout();
        return $this->sendData(__('auth.logout success'),true);
    }
}
