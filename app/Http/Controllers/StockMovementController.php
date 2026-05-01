<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMovementRequest;
use App\Http\Resources\StockMovementResource;
use App\Models\ProductStock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{
    public function index()
    {
        return StockMovementResource::collection(
            StockMovement::with('product', 'fromWarehouse', 'toWarehouse', 'user')
                ->latest()
                ->get()
        );
    }

    public function store(StoreStockMovementRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['user_id'] = $request->user()->id;

            $movement = StockMovement::create($data);

            // Update product_stocks based on movement type
            if ($data['type'] === 'in') {
                $this->handleIncomingStock($data);
            } elseif ($data['type'] === 'out') {
                $this->handleOutgoingStock($data);
            } elseif ($data['type'] === 'transfer') {
                $this->handleTransferStock($data);
            }

            return new StockMovementResource($movement->load('product', 'fromWarehouse', 'toWarehouse', 'user'));
        });
    }

    public function show(StockMovement $stockMovement)
    {
        return new StockMovementResource($stockMovement->load('product', 'fromWarehouse', 'toWarehouse', 'user'));
    }

    protected function handleIncomingStock(array $data): void
    {
        $stock = ProductStock::firstOrCreate(
            [
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['to_warehouse_id'],
            ],
            ['quantity' => 0]
        );

        $stock->increment('quantity', $data['quantity']);
    }

    protected function handleOutgoingStock(array $data): void
    {
        $stock = ProductStock::where([
            'product_id' => $data['product_id'],
            'warehouse_id' => $data['from_warehouse_id'],
        ])->firstOrFail();

        if ($stock->quantity < $data['quantity']) {
            abort(400, 'Insufficient stock');
        }

        $stock->decrement('quantity', $data['quantity']);
    }

    protected function handleTransferStock(array $data): void
    {
        // Reduce from source
        $fromStock = ProductStock::where([
            'product_id' => $data['product_id'],
            'warehouse_id' => $data['from_warehouse_id'],
        ])->firstOrFail();

        if ($fromStock->quantity < $data['quantity']) {
            abort(400, 'Insufficient stock for transfer');
        }

        $fromStock->decrement('quantity', $data['quantity']);

        // Add to destination
        $toStock = ProductStock::firstOrCreate(
            [
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['to_warehouse_id'],
            ],
            ['quantity' => 0]
        );

        $toStock->increment('quantity', $data['quantity']);
    }
}
