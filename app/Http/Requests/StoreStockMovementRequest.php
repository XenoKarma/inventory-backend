<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'from_warehouse_id' => 'nullable|exists:warehouses,id',
            'to_warehouse_id' => 'nullable|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:in,out,transfer',
            'note' => 'nullable|string',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->type === 'transfer') {
                if (!$this->from_warehouse_id || !$this->to_warehouse_id) {
                    $validator->errors()->add('warehouse', 'Transfer requires both from_warehouse_id and to_warehouse_id');
                }
            } elseif ($this->type === 'in' && !$this->to_warehouse_id) {
                $validator->errors()->add('to_warehouse_id', 'Incoming stock requires to_warehouse_id');
            } elseif ($this->type === 'out' && !$this->from_warehouse_id) {
                $validator->errors()->add('from_warehouse_id', 'Outgoing stock requires from_warehouse_id');
            }
        });
    }
}
