<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->middleware('can:dashboard.view')->only(['index']);
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $user = Auth::user()->load('roles.translations');
        $userName = $user->translations()->where('lang_key', 'name')->value('lang_value') ?? $user->email;
        
        $data = [
            'title' => __('menu.dashboard.title'),
            'user_name' => $userName,
            'welcome_message' => __('dashboard.welcome_message'),
        ];

        return view('pages.dashboard.dashboard-simple', $data);
    }
}
