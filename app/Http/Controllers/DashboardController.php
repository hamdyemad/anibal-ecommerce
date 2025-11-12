<?php

namespace App\Http\Controllers;


class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'title' => __('menu.dashboard.title'),
        ];
        return view('pages.dashboard.dashboard', $data);
    }
}
