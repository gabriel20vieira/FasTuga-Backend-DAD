<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Types\UserType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Pagination\Paginator;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Traits\StoresImages;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;

class UsersController extends Controller
{

    use StoresImages;

    /**
     * Authorization for this resource
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $builder = User::query()->with('customer');
        $builder->ofType($request->input('type'));
        return UserResource::collection($this->paginateBuilder($builder, $request->input('size')));
    }

    /**
     * Stores resource
     *
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request, bool $returnModel = false)
    {
        $user = new User($request->safe()->except(['password', 'password_confirmation', 'blocked', 'type']));
        $created = DB::transaction(function () use ($request, $user) {
            $user->password = bcrypt($request->password);
            $user->blocked = $request->input('blocked', 0);

            if (optional($request->user('api'))->isManager() ?? false) {
                $user->type = $request->input('type');
            } else {
                $user->type = UserType::CUSTOMER->value;
            }

            $user->markEmailAsVerified();

            $image = (new self)->storeImage($request, 'fotos', 'image');
            $user->photo_url = $image ?? null;

            return $user->save();
        });

        return $returnModel ? $user : (new UserResource($user))->additional([
            'message' => $created ? "User created with success" : "User was not created."
        ]);
    }

    /**
     * Displays resource
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Updates resource
     *
     * @param StoreUserRequest $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user, bool $returnModel = false)
    {
        $updated = DB::transaction(function () use ($request, $user) {
            $user->update($request->safe()->except(['password', 'password_confirmation', 'image', 'type']));
            $image = (new self)->storeImage($request, 'fotos', 'image');
            $user->photo_url = $image ?? $user->photo_url;
            if ($request->user('api')->id != $user->id && $request->user('api')->isManager()) {
                $user->type = $request->input('type', $user->type);
            }
            return $user->save();
        });

        return $returnModel ? $user : (new UserResource($user))->additional([
            'message' => $updated ? "User updated with success." : "User was not updated."
        ]);
    }

    /**
     * Change user password
     *
     * @param UpdateUserRequest $request
     * @return void
     */
    public function changePassword(UpdateUserPasswordRequest $request)
    {
        /** @var User $user */
        $saved = DB::transaction(function () use ($request) {
            $user = $request->user();
            $user->password = bcrypt($request->password);
            return $user->save();
        });

        return (new UserResource($request->user()))->additional([
            'message' => $saved ? 'Password changed successfully.' : 'Password not changed.'
        ]);
    }

    /**
     * Destroys resource
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $deleted = DB::transaction(function () use ($user) {
            $user->customer()->forceDelete();
            return $user->forceDelete();
        });

        return (new UserResource($user))->additional([
            'message' => $deleted ? "User deleted with success." : "User was not deleted."
        ]);
    }

    /**
     * Showw logged in user
     *
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function me(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * Stores users (static to allow access from customers)
     *
     * @param StoreUserRequest|FormRequest $request
     * @return User
     */
    public static function createUser(StoreUserRequest $request)
    {
        return (new self)->store($request, true);
    }

    /**
     * Updates users (static access)
     *
     * @param UpdateUserRequest $request
     * @return User
     */
    public static function updateUser(UpdateUserRequest $request, User $user)
    {
        return (new self)->update($request, $user, true);
    }
}
