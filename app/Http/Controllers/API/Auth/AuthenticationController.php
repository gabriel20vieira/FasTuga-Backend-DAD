<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthenticationController extends Controller
{

    /**
     * Registers new customer
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email|unique:App\Models\User,email',
            'password' => 'required|min:8',
        ]);

        $password = bcrypt($request->password);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password
        ]);

        $token = $user->createToken('LaravelAuthApp')->accessToken;

        return response()->json(['token' => $token], 200);
    }

    /**
     * Provides user login token
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if (auth()->attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['token' => $token]);
        }

        return response()->json(['error' => 'Unauthorised'], 401);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json(['message' => 'You have been successfully logged out!']);
    }
}