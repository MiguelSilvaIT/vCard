<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Vcard;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\StoreUpdateCategoryRequest;
use Illuminate\Support\Facades\Validator;



class CategoryController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');
    }

    public function index()
    {
        return CategoryResource::collection(Category::all());
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    // public function getCategoriesOfVcard(Request $request, Vcard $vcard)
    // {
    //     return CategoryResource::collection($vcard->categories->sortByDesc('id'));
    // }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $existingCategory = Category::withTrashed()
                                ->where('vcard', $request->vcard)
                                ->where('type', $request->type)
                                ->where('name', $request->name)
                                ->first();
        if ($existingCategory !== null && $existingCategory->id !== $category->id) {
            if ($existingCategory->trashed()) {
                $validator = Validator::make([], []);
                $validator->errors()->add('name', 'A category with this name and type already exists.');

                return response()->json(['errors' => $validator->errors()], 422);
            }
            $existingCategory->update($request->validated());
            return new CategoryResource($existingCategory);
        }

        // The category does not exist or it's the current category, so you can update it
        $category->update($request->validated());

        return new CategoryResource($category);
    }

    public function store(StoreUpdateCategoryRequest $request)
    {
        $existingCategory = Category::withTrashed()
                    ->where('vcard', $request->vcard)
                    ->where('type', $request->type)
                    ->where('name', $request->name)
                    ->first();
        if ($existingCategory !== null) {
            if ($existingCategory->trashed()) {
                $existingCategory->restore();
                return new CategoryResource($existingCategory);
            }
            else{
                $validator = Validator::make([], []);
                $validator->errors()->add('name', 'A category with this name and type already exists.');
                return response()->json(['errors' => $validator->errors()], 422);
            }
        }
        $newCategory = Category::create($request->validated());
        return new CategoryResource($newCategory);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->noContent();
    }
}
