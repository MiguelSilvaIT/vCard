<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\User;
use App\Models\Vcard;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\StoreUpdateCategoryRequest;






class CategoryController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(Category::all());
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function getCategoriesOfVcard(Request $request, Vcard $vcard)
    {
        return CategoryResource::collection($vcard->categories->sortByDesc('id'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return new CategoryResource($category);
    }

    public function store(StoreUpdateCategoryRequest $request)
    {
        $newCategory = Category::create($request->validated());
        return new CategoryResource($newCategory);
    }
}
