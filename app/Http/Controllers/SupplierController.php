<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        return SupplierResource::collection(
            Supplier::latest()->paginate(15)
        );
    }

    public function store(StoreSupplierRequest $request)
    {
        $supplier = Supplier::create($request->validated());

        return $this->createdResponse(
            new SupplierResource($supplier),
            'Supplier created successfully'
        );
    }

    public function show(Supplier $supplier)
    {
        return $this->successResponse(
            new SupplierResource($supplier->load('products'))
        );
    }

    public function update(StoreSupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return $this->successResponse(
            new SupplierResource($supplier),
            'Supplier updated successfully'
        );
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return $this->deletedResponse();
    }
}
