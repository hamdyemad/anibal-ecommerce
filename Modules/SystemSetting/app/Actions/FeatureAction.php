<?php

namespace Modules\SystemSetting\app\Actions;

use Illuminate\Http\Request;
use Modules\SystemSetting\app\Models\Feature;

class FeatureAction
{
    public function getDatatableData(Request $request)
    {
        $query = Feature::with('translations', 'attachments');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('active') && $request->active !== '') {
            $query->where('active', $request->active);
        }

        if ($request->filled('created_date_from')) {
            $query->whereDate('created_at', '>=', $request->created_date_from);
        }

        if ($request->filled('created_date_to')) {
            $query->whereDate('created_at', '<=', $request->created_date_to);
        }

        // Pagination
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $total = $query->count();
        $data = $query->skip(($page - 1) * $perPage)
                     ->take($perPage)
                     ->latest()
                     ->get();

        return [
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
            'total' => $total,
            'current_page' => $page,
        ];
    }
}
