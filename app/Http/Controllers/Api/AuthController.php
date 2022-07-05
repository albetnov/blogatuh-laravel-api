<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNAUTHORIZED);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'Invalid cresidentials.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid cresidentials.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken("example-token");

        return response()->json([
            'message' => 'Authorized',
            'token' => $token->plainTextToken
        ], Response::HTTP_OK);
    }
}
