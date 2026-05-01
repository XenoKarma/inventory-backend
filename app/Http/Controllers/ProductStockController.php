<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductStockRequest;
use App\Http\Resources\ProductStockResource;
use App\Models\ProductStock;
use Illuminate\Http\Request;

class ProductStockController extends Controller
{
    public function index()
    {
        return ProductStockResource::collection(
            ProductStock::with('product', 'warehouse')->latest()->paginate(15)
        );
    }

    public function show(ProductStock $productStock)
    {
        return $this->successResponse(
            new ProductStockResource($productStock->load('product', 'warehouse'))
        );
    }

    public function update(Request $request, ProductStock $productStock)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $productStock->update($request->only('quantity'));

        return $this->successResponse(
            new ProductStockResource($productStock->load('product', 'warehouse')),
            'Stock updated successfully'
        );
    }
}
