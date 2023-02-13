<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\CreateCustomerRequest;
use App\Http\Resources\API\Admin\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param CreateCustomerRequest $request
     *
     * @return AnonymousResourceCollection
     */
    public function store(CreateCustomerRequest $request): AnonymousResourceCollection
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

        return CustomerResource::collection([$customer]);
    }
}
