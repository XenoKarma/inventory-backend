<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $fillable = [
        'name',
        'location',
    ];

    public function productStocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    public function fromMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'from_warehouse_id');
    }

    public function toMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'to_warehouse_id');
    }
}
