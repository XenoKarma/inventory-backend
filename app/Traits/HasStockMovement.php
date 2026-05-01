<?php

namespace App\Traits;

use App\Models\ProductStock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

trait HasStockMovement
{
    public function createStockMovement(array $data, $userId): StockMovement
    {
        return DB::transaction(function () use ($data, $userId) {
            $data['user_id'] = $userId;
            $movement = StockMovement::create($data);
            $this->updateStock($data);
            return $movement;
        });
    }

    protected function updateStock(array $data): void
    {
        match ($data['type']) {
            'in' => $this->handleIncoming($data),
            'out' => $this->handleOutgoing($data),
            'transfer' => $this->handleTransfer($data),
        };
    }

    protected function handleIncoming(array $data): void
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

    protected function handleOutgoing(array $data): void
    {
        $stock = ProductStock::where([
            'product_id' => $data['product_id'],
            'warehouse_id' => $data['from_warehouse_id'],
        ])->firstOrFail();

        if ($stock->quantity < $data['quantity']) {
            throw new \Exception('Insufficient stock');
        }

        $stock->decrement('quantity', $data['quantity']);
    }

    protected function handleTransfer(array $data): void
    {
        $fromStock = ProductStock::where([
            'product_id' => $data['product_id'],
            'warehouse_id' => $data['from_warehouse_id'],
        ])->firstOrFail();

        if ($fromStock->quantity < $data['quantity']) {
            throw new \Exception('Insufficient stock for transfer');
        }

        $fromStock->decrement('quantity', $data['quantity']);

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
