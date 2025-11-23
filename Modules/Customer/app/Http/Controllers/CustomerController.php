<?php

namespace Modules\Customer\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Customer\app\Services\CustomerService;
use Modules\Customer\app\Interfaces\CustomerRepositoryInterface;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService,
        protected CustomerRepositoryInterface $customerRepository
    ) {}

    public function index()
    {
        return view('customer::customer.index', [
            'title' => __('customer::customer.customers_management'),
        ]);
    }

    public function datatable(Request $request)
    {
        $filters = $request->all();

        $query = $this->customerRepository->getCustomersQuery($filters);

        $total = $query->count();

        $perPage = $filters['per_page'] ?? 10;
        $page = $filters['page'] ?? 1;

        $customers = $query->paginate($perPage, ['*'], 'page', $page);

        $data = $customers->map(function ($customer, $index) use ($page, $perPage) {
            return [
                'index' => ($page - 1) * $perPage + $index + 1,
                'id' => $customer->id,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'full_name' => $customer->full_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'status' => $customer->status,
                'email_verified_at' => $customer->email_verified_at,
                'created_at' => $customer->created_at,
            ];
        })->toArray();

        return response()->json([
            'draw' => intval($request->get('draw', 1)),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $data,
            'total' => $customers->total(),
            'current_page' => $customers->currentPage(),
        ]);
    }

    public function create()
    {
        return view('customer::create');
    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
        return view('customer::show');
    }

    public function edit($id)
    {
        return view('customer::edit');
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
        $this->customerRepository->deleteCustomer($id);
        return response()->json(['success' => true, 'message' => __('customer::customer.customer_deleted')]);
    }
}
