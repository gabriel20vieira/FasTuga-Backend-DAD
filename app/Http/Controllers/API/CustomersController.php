<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\Types\UserType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\CustomerResource;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Controllers\API\UsersController;
use App\Http\Resources\UserCustomerResource;

class CustomersController extends Controller
{

    /**
     * Authorization for this resource
     */
    public function __construct()
    {
        $this->authorizeResource(Customer::class, 'customer');
    }

    /**
     * Returns all customers
     *
     * @return void
     */
    public function index(Request $request)
    {
        return CustomerResource::collection(
            $this->paginateBuilder(Customer::query(), $request->input('size'))
        );
    }

    /**
     * Stores customers
     *
     * @param StoreCustomerRequest $request
     * @return void
     */
    public function store(StoreCustomerRequest $request, bool $returnModel = false)
    {
        $customer = new Customer($request->safe()->except(['points', 'user_id']));
        $created = DB::transaction(function () use ($request, $customer) {
            $user = UsersController::createUser($request);
            $user->type = UserType::CUSTOMER->value;
            $user->save();

            $customer->user()->associate($user);
            $customer->points = 0;
            return $customer->save();
        });

        return $returnModel ? $customer : (new UserCustomerResource($customer->user))->additional([
            'message' => $created ? "Customer created with success." : "Customer was not created."
        ]);
    }

    /**
     * Shows one customer record
     *
     * @param Customer $customer
     * @return void
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * Updates a given record
     *
     * @param UpdateCustomerRequest $request
     * @param Customer $customer
     * @return void
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $updated = DB::transaction(function () use ($request, $customer) {
            $user = UsersController::updateUser($request, $customer->user);
            $user->save();
            return $customer->update($request->validated());
        });

        return (new UserCustomerResource($customer->user))->additional([
            'message' => $updated ? "Customer updated with success." : "Customer was not updated."
        ]);
    }

    /**
     * Deletes a given record
     *
     * @param Customer $customer
     * @return void
     */
    public function destroy(Customer $customer)
    {
        $deleted = DB::transaction(function () use ($customer) {
            $customer->user()->forceDelete();
            return $customer->forceDelete();
        });

        return (new CustomerResource($customer))->additional([
            'message' => $deleted ? "Customer deleted with success." : "Customer was not deleted."
        ]);
    }

    /**
     * Stores user (static call)
     *
     * @param StoreCustomerRequest $request
     * @return Customer
     */
    public static function createCustomer(StoreCustomerRequest $request)
    {
        return (new self)->store($request, true);
    }
}
