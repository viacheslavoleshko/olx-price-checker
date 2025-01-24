<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Auth\Events\Verified;
use Symfony\Component\HttpFoundation\Response;


class VerifyEmailController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = User::find($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            return response(['message' => 'Verification already success'], Response::HTTP_OK);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response(['message' => 'Verification success'], Response::HTTP_OK);
    }
}
