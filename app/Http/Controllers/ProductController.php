<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return ProductResource::collection(
            Product::with('category', 'supplier')->latest()->paginate(15)
        );
    }

    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return $this->createdResponse(
            new ProductResource($product->load('category', 'supplier')),
            'Product created successfully'
        );
    }

    public function show(Product $product)
    {
        return $this->successResponse(
            new ProductResource($product->load('category', 'supplier', 'productStocks.warehouse'))
        );
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return $this->successResponse(
            new ProductResource($product->load('category', 'supplier')),
            'Product updated successfully'
        );
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return $this->deletedResponse();
    }
}
