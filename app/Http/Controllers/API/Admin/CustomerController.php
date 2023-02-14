<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\CreateCustomerRequest;
use App\Http\Resources\API\Admin\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $customers = Customer::all()
            ->forPage($request->get('page'), $request->get('limit'));

        return response()->json(['customers' => $customers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateCustomerRequest $request
     *
     * @return CustomerResource
     */
    public function store(CreateCustomerRequest $request): CustomerResource
    {
        $request->validated();

        $customer = Customer::create([
            'firstname'    => $request->get('firstname'),
            'lastname'     => $request->get('lastname'),
            'email'        => $request->get('email'),
            'password'     => Hash::make($request->get('password')),
            'phone_number' => $request->get('phone_number'),
            'is_suspended' => $request->get('is_suspended'),
        ]);

        return CustomerResource::make($customer);
    }
}
