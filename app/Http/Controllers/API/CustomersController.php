<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;

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
        return CustomerResource::collection(Customer::latest()->paginate(env('PAGINATE', 15)));
    }

    /**
     * Stores customers
     *
     * @param StoreCustomerRequest $request
     * @return void
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = DB::transaction(function () use ($request) {
            $customer = new Customer($request->validated());
            $customer->save();
            return $customer;
        });

        return new CustomerResource($customer);
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
            $customer->delete();
        });

        return new CustomerResource($customer);
    }
}
