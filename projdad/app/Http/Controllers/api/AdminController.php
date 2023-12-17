<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\AdminResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Admin::class, 'admin');
    }
    
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

    public function updatePassword(UpdateUserPasswordRequest $request, Admin $admin)
    {
        //validar os dados recebidos
        $dataToSave = $request->validated();
        //alterar a password do vCard
        if (!Hash::check($dataToSave['current_password'], $admin->password)) {
            //se a password antiga não for igual à que está na BD, devolve erro
            $validator = Validator::make([], []);
            $validator->errors()->add('current_password', 'Current password is not correct');

            return response()->json(['errors' => $validator->errors()], 422);
        }
        $admin->password = $dataToSave['password'];
        $admin->save();
        //devolver o vCard com a nova password
        return response()->json([
            'message' => 'success',
            'data' => new AdminResource($admin)
        ], 200);
    }

    public function getSumOfBalances(Request $request) 
    {
        $sumOfBalances = DB::table('vcards')->sum('balance');
        return $sumOfBalances;
    }

    public function getNumberOfVcards(Request $request) 
    {
        $numberOfVcards = DB::table('vcards')->count();
        return $numberOfVcards;
    }

    public function getNumberOfBlockedVcards(Request $request) 
    {
        $numberOfBlockedVcards = DB::table('vcards')->where('blocked', '=', 1)->count();
        return $numberOfBlockedVcards;
    }

    public function getCountOfCreatedVcardsPerMonth(Request $request)
    {
        $countOfCreatedVcardsPerMonth = DB::table('vcards')
            ->select(DB::raw('count(*) as total'))
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();
        return $countOfCreatedVcardsPerMonth;
    }

  
}
