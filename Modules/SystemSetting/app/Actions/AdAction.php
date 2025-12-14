<?php

namespace Modules\SystemSetting\app\Actions;

use Modules\SystemSetting\app\Models\Ad;

class AdAction
{
    public function getDatatableData($request)
    {
        $query = Ad::with('translations', 'attachments');

        // Apply filters - handle both old and new search format
        $search = null;
        if ($request->has('search')) {
            if (is_array($request->search) && !empty($request->search['value'])) {
                $search = $request->search['value'];
            } elseif (is_string($request->search) && !empty($request->search)) {
                $search = $request->search;
            }
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('translations', function($query) use ($search) {
                    $query->where('lang_value', 'like', "%{$search}%");
                })
                ->orWhere('link', 'like', "%{$search}%");
            });
        }

        // Position filter
        if ($request->has('position') && !empty($request->position)) {
            $query->where('position', $request->position);
        }

        // Status filter
        if ($request->has('active') && $request->active !== '') {
            $query->where('active', $request->active);
        }

        // Date filters
        if ($request->has('created_date_from') && !empty($request->created_date_from)) {
            $query->whereDate('created_at', '>=', $request->created_date_from);
        }

        if ($request->has('created_date_to') && !empty($request->created_date_to)) {
            $query->whereDate('created_at', '<=', $request->created_date_to);
        }

        // Get total records before pagination
        $totalRecords = $query->count();

        // Apply sorting
        if ($request->has('order')) {
            $orderColumn = $request->columns[$request->order[0]['column']]['data'];
            $orderDir = $request->order[0]['dir'];

            if ($orderColumn === 'title') {
                $query->orderBy('id', $orderDir);
            } else {
                $query->orderBy($orderColumn, $orderDir);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Apply pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;

        $ads = $query->skip($start)->take($length)->get();

        return [
            'draw' => $request->draw ?? 1,
            'recordsTotal' => Ad::count(),
            'recordsFiltered' => $totalRecords,
            'data' => $ads,
        ];
    }
}
