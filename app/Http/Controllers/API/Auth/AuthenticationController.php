<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

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
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password
        ]);


        return response()->json(['message' => 'Register successful'], 200);
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
            $user = new UserResource(auth()->user());
            $token = $user->createToken('LaravelAuthApp')->accessToken;
            return $user->additional(['token' => $token]);
        }

        return response()->json(['message' => 'Authentication has failed!'], 401);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json(['message' => 'You have been successfully logged out!']);
    }
}
