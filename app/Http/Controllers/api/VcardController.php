<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vcard;
use App\Http\Resources\VcardResource;
class VcardController extends Controller
{
    public function show(Vcard $vcard)
    {
        return new VcardResource($vcard);
    }
    // public function getVcardTransactions(Request $request, Vcard $vcard)
    // {
    //     /   /TaskResource::$format = 'detailed';
    //     TaskResource::$format = 'default';
    //     if (!$request->has('include_assigned')) {
    //         return TaskResource::collection($user->tasks->sortByDesc('id'));
    //     } else {
    //         return TaskResource::collection($user->tasks->merge($user->assigedTasks)->sortByDesc('id'));
    //     }
    // }
    // public function store(StoreUpdateTaskRequest $request)
    // {
    //     $newTask = Task::create($request->validated());
    //     return new TaskResource($newTask);
    // }

    // public function update(StoreUpdateTaskRequest $request, Task $task)
    // {
    //     $task->update($request->validated());
    //     return new TaskResource($task);
    // }

    public function destroy(Vcard $vcard)
    {
        $vcard->transactions()->detach();
        $vcard->delete();
        return new VcardResource($vcard);
    }
}
