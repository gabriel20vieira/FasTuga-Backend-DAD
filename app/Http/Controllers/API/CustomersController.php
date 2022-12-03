<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\CustomerResource;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Controllers\API\UsersController;
use App\Http\Resources\UserResource;

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
    public function index()
    {
        return CustomerResource::collection(
            Customer::latest()->paginate(env('PAGINATE', 15))
        );
    }

    /**
     * Stores customers
     *
     * @param StoreCustomerRequest $request
     * @return void
     */
    public function store(StoreCustomerRequest $request)
    {
        $user = UsersController::createUser($request);
        $customer = DB::transaction(function () use ($request, $user) {
            $customer = new Customer($request->safe()->except(['points', 'default_payment_reference', 'user_id']));
            $customer->user()->associate($user);
            $customer->points = 0;
            $customer->default_payment_reference = Str::random();
            $customer->save();
            return $customer;
        });

        return new UserResource($customer->user);
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

        DB::transaction(function () use ($request, $customer) {
            $customer->update($request->validated());
        });

        return new CustomerResource($customer);
    }

    /**
     * Deletes a given record
     *
     * @param Customer $customer
     * @return void
     */
    public function destroy(Customer $customer)
    {
        DB::transaction(function () use ($customer) {
            $customer->user()->delete();
            $customer->delete();
        });

        return new CustomerResource($customer);
    }
}
