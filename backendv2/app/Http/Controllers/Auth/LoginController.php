<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\LoginService;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class LoginController extends Controller
{
    protected $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function __invoke(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['username', 'password']);

        if(!$this->loginService->attempLogin($credentials)) {
            return response()->json(['message' => __('auth.unauthorized')], 401);
        }

        $loggedInUser = auth()->user();

        if($this->loginService->isUserBlocked($loggedInUser)) {
            return response()->json(['message' => __('auth.account_blocked')], 403);
        }

        $token = $this->loginService->createTokenForUser($loggedInUser);
        $user = User::where('username', $request['username'])->first(['id','name','surname','image', 'shift']);
        $role = $loggedInUser->roles->first();
        return response()->json([
            'access_token' => $token,
            'user' => $user,
            'role' => $role
        ]);
    }
}
