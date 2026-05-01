<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(
            Category::latest()->paginate(15)
        );
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return $this->createdResponse(
            new CategoryResource($category),
            'Category created successfully'
        );
    }

    public function show(Category $category)
    {
        return $this->successResponse(
            new CategoryResource($category->load('products'))
        );
    }

    public function update(StoreCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return $this->successResponse(
            new CategoryResource($category),
            'Category updated successfully'
        );
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return $this->deletedResponse();
    }
}
