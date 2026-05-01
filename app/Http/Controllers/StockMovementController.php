<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMovementRequest;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use App\Traits\HasStockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    use HasStockMovement;

    public function index(Request $request)
    {
        $query = StockMovement::with('product', 'fromWarehouse', 'toWarehouse', 'user');

        // Filter by product
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by warehouse (either from or to)
        if ($request->has('warehouse_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('from_warehouse_id', $request->warehouse_id)
                  ->orWhere('to_warehouse_id', $request->warehouse_id);
            });
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        return $this->successResponse(
            StockMovementResource::collection(
                $query->latest()->paginate($request->get('per_page', 15))
            )
        );
    }

    public function store(StoreStockMovementRequest $request)
    {
        try {
            $movement = $this->createStockMovement($request->validated(), $request->user()->id);

            return $this->createdResponse(
                new StockMovementResource($movement->load('product', 'fromWarehouse', 'toWarehouse', 'user')),
                'Stock movement recorded successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function show(StockMovement $stockMovement)
    {
        return $this->successResponse(
            new StockMovementResource($stockMovement->load('product', 'fromWarehouse', 'toWarehouse', 'user'))
        );
    }

    public function stockHistory(Request $request, $productId)
    {
        $query = StockMovement::with('product', 'fromWarehouse', 'toWarehouse', 'user')
            ->where('product_id', $productId);

        // Filter by warehouse
        if ($request->has('warehouse_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('from_warehouse_id', $request->warehouse_id)
                  ->orWhere('to_warehouse_id', $request->warehouse_id);
            });
        }

        return $this->successResponse(
            StockMovementResource::collection(
                $query->latest()->paginate($request->get('per_page', 15))
            )
        );
    }
}
