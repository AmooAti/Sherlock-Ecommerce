<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Customer\RegisterCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Registers a customer at the customer's table.
     *
     * @param RegisterCustomerRequest $request
     *
     * @return jsonResponse
     */
    public function register(RegisterCustomerRequest $request): JsonResponse
    {
        $request->validated();

        $customer = Customer::create([
            'firstname'    => $request->get('firstname'),
            'lastname'     => $request->get('lastname'),
            'phone_number' => $request->get('phone_number'),
            'email'        => $request->get('email'),
            'password'     => Hash::make($request->get('password')),
        ]);

        return response()
            ->json(['message' => "The customer (#$customer->id) is created successfully."])
            ->setStatusCode(201);
    }
}
