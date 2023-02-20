<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\LoginRequest;
use App\Http\Resources\API\Admin\LoginResource;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login to the admin account.
     *
     * @param LoginRequest $request
     *
     * @return JsonResponse|LoginResource
     */
    public function login(LoginRequest $request): jsonResponse|LoginResource
    {
        $request->validated();

        $admin = Admin::where('email', $request['email'])->first();

        if (!$admin || !Hash::check($request['password'], $admin->password)) {
            return response()
                ->json(['message' => 'The email or password are incorrect!'], 401);
        }

        $authToken = $admin->createToken('auth_token', ['admin']);

        $admin->last_login = $authToken->accessToken->created_at;

        $admin->save();

        return LoginResource::make($authToken);
    }

    /**
     * Logout from the admin account.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logout(Request $request): jsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()
            ->json(['message' => 'Logged out has been successful.']);
    }
}
