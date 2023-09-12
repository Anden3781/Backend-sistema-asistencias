<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginService {

    public function attempLogin(array $credentials) 
    {
        return Auth::attempt($credentials);
    }

    public function createTokenForUser(User $user): string
    {
        return $user->createToken('User Access Token')->plainTextToken;
    }

    public function getUserByCredentials(array $credentials): ?User
    {
        return User::where('username', $credentials['username'])->first();
    }

    public function isUserBlocked(User $user): bool
    {
        return $user->status == 0;
    }
}