<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku' => 'required|string|max:100|unique:products,sku' . ($product ? ',' . $product->id : ''),
            'description' => 'nullable|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ];
    }
}
