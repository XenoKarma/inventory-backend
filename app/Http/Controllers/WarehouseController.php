<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        return WarehouseResource::collection(
            Warehouse::latest()->paginate(15)
        );
    }

    public function store(StoreWarehouseRequest $request)
    {
        $warehouse = Warehouse::create($request->validated());

        return $this->createdResponse(
            new WarehouseResource($warehouse),
            'Warehouse created successfully'
        );
    }

    public function show(Warehouse $warehouse)
    {
        return $this->successResponse(
            new WarehouseResource($warehouse->load('productStocks.product'))
        );
    }

    public function update(StoreWarehouseRequest $request, Warehouse $warehouse)
    {
        $warehouse->update($request->validated());

        return $this->successResponse(
            new WarehouseResource($warehouse),
            'Warehouse updated successfully'
        );
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return $this->deletedResponse();
    }
}
