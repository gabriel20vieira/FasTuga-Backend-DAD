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
        $builder = User::query()
            ->with('customer');
        // ->where('type', '!=', [UserType::CUSTOMER->value]); // In case is needed
        $builder->ofType($request->input('type'));

        return UserResource::collection($this->paginateBuilder($builder), $request->input('size'));
    }

    /**
     * Stores resource
     *
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $user = self::createUser($request);

        return new UserResource($user);
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
    public function update(UpdateUserRequest $request, User $user)
    {
        DB::transaction(function () use ($request, $user) {
            $user->update($request->safe()->except(['password', 'password_confirmation']));
            $user->save();

            $path = (new self)->storeImage($request, 'fotos', 'image');
            if ($path) {
                $image = $path;
                $image = str_replace("\\", "", $path);
                $image = explode("/", $image);
                $image = end($image);
                $user->photo_url = $image;
            }
            $user->save();

            return $user;
        });

        return new UserResource($user);
    }

    /**
     * CHange user password
     *
     * @param UpdateUserRequest $request
     * @return void
     */
    public function changePassword(UpdateUserRequest $request)
    {
        $user = auth('api')->user();

        $changed = DB::transaction(function () use ($request, $user) {
            $user->password = bcrypt($request->password);
            return $user->save();
            return false;
        });

        return  response()->json(['message' => $changed ? 'Password changed successfully.' : 'Password not changed.']);
    }

    /**
     * Destroys resource
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            $user->customer()->delete();
            $user->delete();
        });

        return new UserResource($user);
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
     * @return void
     */
    public static function createUser(StoreUserRequest|FormRequest $request)
    {
        $user = DB::transaction(function () use ($request) {
            $user = new User($request->safe()->except(['password', 'password_confirmation']));
            $user->password = bcrypt($request->password);
            $user->unblock();

            $path = (new self)->storeImage($request, 'fotos', 'image');
            $image = $path;
            $image = str_replace("\\", "", $path);
            $image = explode("/", $image);
            $image = end($image);
            $user->photo_url = $image;
            $user->save();

            return $user;
        });

        return $user;
    }
}
