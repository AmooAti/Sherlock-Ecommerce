<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Customer\LoginRequest;
use App\Http\Requests\API\Customer\RegisterRequest;
use App\Http\Resources\API\Customer\LoginResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Registers a customer at the customer's table.
     *
     * @param RegisterRequest $request
     *
     * @return jsonResponse
     */
    public function store(RegisterRequest $request): JsonResponse
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

    public function login(LoginRequest $request): JsonResponse|LoginResource
    {
        $credentials = $request->validated();
        $customer = Customer::where('email', $credentials['email'])->first();

        if (!$customer || !Hash::check($credentials['password'], $customer->password)) {
            return response()
                ->json(['error' => 'The provided credentials are incorrect.'], 401);
        }

        $token = $customer->createToken('api_token');

        $customer = Customer::find($customer->id);
        $customer->last_login = $token->accessToken->created_at;
        $customer->save();

        return LoginResource::make($token);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()
            ->json(['message' => "The customer logged out successfully."]);
    }
}
