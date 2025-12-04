<?php

namespace Modules\SystemSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('systemsetting::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($lang, $countryCode)
    {
        return view('systemsetting::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($lang, $countryCode, Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($lang, $countryCode, $id)
    {
        return view('systemsetting::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($lang, $countryCode, $id)
    {
        return view('systemsetting::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($lang, $countryCode, Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($lang, $countryCode, $id) {}
}
