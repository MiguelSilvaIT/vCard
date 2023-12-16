<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DefaultCategory;
use App\Http\Resources\DefaultCategoryResource;
use App\Http\Requests\DefaultCategoryRequest;

class DefaultCategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(DefaultCategory::class, 'App\Models\DefaultCategory');
    }

    public function index()
    {
        return DefaultCategoryResource::collection(DefaultCategory::all());
    }

    public function show(DefaultCategory $defaultCategory)
    {
        return new DefaultCategoryResource($defaultCategory);
    }

    public function update(DefaultCategoryRequest $request, DefaultCategory $defaultCategory)
    {
        $defaultCategory->update($request->validated());

        return new DefaultCategoryResource($defaultCategory);
    }

    public function store(DefaultCategoryRequest $request)
    {
        $newCategoryDefault = DefaultCategory::create($request->validated());
        return new DefaultCategoryResource($newCategoryDefault);
    }


    public function destroy(DefaultCategory $defaultCategory)
    {
        $defaultCategory->delete();
        return response()->json([
            'success' => 'true',
            'data' => new DefaultCategoryResource($defaultCategory)
        ], 200);
    }
}
