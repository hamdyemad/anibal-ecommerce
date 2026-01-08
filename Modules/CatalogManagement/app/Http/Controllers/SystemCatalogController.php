<?php

namespace Modules\CatalogManagement\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\CatalogManagement\app\Services\SystemCatalogService;

class SystemCatalogController extends Controller
{
    public function __construct(
        protected SystemCatalogService $systemCatalogService
    ) {
        // Accessible to all authenticated users
        $this->middleware('auth');
    }

    public function index()
    {
        // Get all catalog data from service
        $data = $this->systemCatalogService->getAllCatalogData();

        return view('catalogmanagement::system-catalog.index', $data);
    }
}
