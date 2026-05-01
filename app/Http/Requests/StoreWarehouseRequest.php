<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $warehouse = $this->route('warehouse');

        return [
            'name' => 'required|string|max:255|unique:warehouses,name' . ($warehouse ? ',' . $warehouse->id : ''),
            'location' => 'nullable|string',
        ];
    }
}
