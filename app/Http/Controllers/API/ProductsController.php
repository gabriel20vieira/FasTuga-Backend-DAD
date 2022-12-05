<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Traits\StoresImages;

class ProductsController extends Controller
{

    use StoresImages;

    /**
     * Authorization for this resource
     */
    public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $builder = Product::query();
        $builder->ofType($request->input('type'));

        return ProductResource::collection(
            $this->paginateBuilder($builder, $request->input('size', 9999))
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        $product = new Product($request->safe()->except('image'));
        $created = DB::transaction(function () use ($request, $product) {
            $image = $this->storeImage($request, 'products', 'image');
            $product->photo_url = $image ?? $product->photo_url;
            return $product->save();
        });

        return (new ProductResource($product))->additional(
            ['message' => $created ? "Product created with success." : "Product was not created."]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $changed = DB::transaction(function () use ($request, $product) {
            $product->update($request->safe()->except('image'));
            $image = $this->storeImage($request, 'products', 'image');
            $product->photo_url = $image ?? $product->photo_url;
            return $product->save();
        });

        return (new ProductResource($product))->additional([
            'message' => $changed ? "Product updated." : "Product was not updated."
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $deleted = DB::transaction(function () use ($product) {
            $product->delete();
        });

        return (new ProductResource($product))->additional([
            'message' => $deleted ? "Product deleted with success." : "Product was not deleted."
        ]);;
    }
}
