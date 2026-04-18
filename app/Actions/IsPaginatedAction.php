<?php

namespace App\Actions;

class IsPaginatedAction
{
    public function handle($query, $per_page, $paginated = null, $page = null)
    {
        $per_page = $per_page ?? 10;
        $page = $page ?? 1;
        
        // If paginated is null (not passed), return all results without pagination
        if ($paginated === null) {
            return $query->get();
        }
        
        // Convert string values to boolean
        if (is_string($paginated)) {
            $paginated = in_array(strtolower($paginated), ['true', '1', 'yes', 'ok'], true);
        }
        
        if ($paginated) {
            // Set the current page for the paginator
            \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
            
            return $query->paginate($per_page, ['*'], 'page', $page);
        }
        
        // When paginated is explicitly false, return all results
        return $query->get();
    }
}
