<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoryDefault;
use App\Models\User;
use App\Models\Vcard;
use App\Http\Resources\CategoryDefaultResource;
use App\Http\Requests\UpdateCategoryDefaultRequest;
use App\Http\Requests\StoreUpdateCategoryDefaultRequest;





class CategoryDefaultController extends Controller
{
    public function index()
    {
        return CategoryDefaultResource::collection(CategoryDefault::all());
    }

    public function show(CategoryDefault $category)
    {
        return new CategoryDefaultResource($category);
    }

    public function update(StoreUpdateCategoryDefaultRequest $request, CategoryDefault $category)
    {
        $category->update($request->validated());
        return new CategoryDefaultResource($category);
    }

    public function store(StoreUpdateCategoryDefaultRequest $request)
    {
        //dump("..INICIO.." + $request + "..FIM..");
        $newCategoryDefault = CategoryDefault::create($request->validated());
        return new CategoryDefaultResource($newCategoryDefault);
    }


    public function destroy(CategoryDefault $category)
    {
        $category->delete();
        return response()->noContent();
    }
}
