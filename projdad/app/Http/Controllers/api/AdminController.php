<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Models\Admin;
use App\Http\Resources\AdminResource;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        //devolve todos os admins da BD
        return AdminResource::collection(Admin::all());
    }

    public function show(Admin $admin)
    {
        //devolve um admin especifico
        return new AdminResource($admin);
    }

    public function store(StoreAdminRequest $request)
    {
        $dataToSave = $request->validated();

        $newAdmin = new Admin();
        $newAdmin->name = $dataToSave['name'];
        $newAdmin->email = $dataToSave['email'];
        $newAdmin->password = $dataToSave['password'];

        $newAdmin->save();

        return response()->json([
            'message' => 'Admin criado com sucesso',
            'data' => new AdminResource($newAdmin)
        ], 200);
    }

    public function update(UpdateAdminRequest $request, Admin $admin)
    {
        $dataToSave = $request->validated();

        $admin->name = $dataToSave['name'];
        $admin->email = $dataToSave['email'];

        $admin->save();

        return response()->json([
            'message' => 'Admin atualizado com sucesso',
            'data' => new AdminResource($admin)
        ], 200);
    }

    public function destroy(Admin $admin)
    {
        $admin->delete();

        return response()->json([
            'message' => 'Admin eliminado com sucesso'
        ], 200);
    }

    public function updatePassword (UpdateUserPasswordRequest $request, Admin $admin) {
        //validar os dados recebidos
        $dataToSave = $request->validated();
        //alterar a password do vCard
        if(!Hash::check($dataToSave['oldpassword'], $admin->password)) {
            //se a password antiga não for igual à que está na BD, devolve erro
            return response()->json([
                'message' => 'error',
                'data' => 'Old password is not correct'
            ], 400);
        }
        $admin->password = $dataToSave['password'];
        $admin->save();
        //devolver o vCard com a nova password
        return response()->json([
            'message' => 'success',
            'data' => new AdminResource($admin)
        ], 200);
    }

  
}


