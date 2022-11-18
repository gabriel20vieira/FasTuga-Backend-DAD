<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;

class UsersController extends Controller
{
    //Display a listing of users
    public function index()
    {
        return UserResource::collection(User::latest()->paginate());
    }

    //Store a newly created user in storage
    public function store(StoreUserRequest $request)
    {
        $newUser = User::create($request->validated());
        return new UserResource($newUser);
    }

    //Display the specified user
    public function show(User $user)
    {
        return new UserResource($user);
    }

    //Update the specified user in storage
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());
        return new UserResource($user);
    }

    //Remove the specified user from storage
    public function destroy(User $user)
    {
        $user->delete();
        return new UserResource($user);
    }
}
