<?php

namespace App\UseCases\Services;


use App\Models\User;

use Illuminate\Support\Facades\DB;
use App\Http\Requests\RegisterRequest;

class RegisterService
{
    public function register(RegisterRequest $request): User
    {
        $createdUser = DB::transaction(function () use ($request): User {
            $user = User::register($request->email, $request->password);
            return $user;
        });

        return $createdUser;
    }
}
