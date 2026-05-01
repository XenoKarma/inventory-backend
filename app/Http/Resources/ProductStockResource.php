<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductStockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'warehouse' => new WarehouseResource($this->whenLoaded('warehouse')),
            'quantity' => $this->quantity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
