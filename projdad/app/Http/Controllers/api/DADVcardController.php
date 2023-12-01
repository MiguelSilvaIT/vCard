<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DADStoreVcardRequest;
use App\Http\Requests\DADUpdateVcardRequest;
use App\Http\Resources\VcardResource;
use App\Services\Base64Services;
use App\Models\Vcard;

class DADVcardController extends Controller
{
    public function index()
    {
        return VcardResource::collection(Vcard::all());
    }

    public function store(DADStoreVcardRequest $request)
    {   
        $dataToSave = $request->validated();

        $newVcard = new Vcard($dataToSave);
        $newVcard->blocked = 0;
        $newVcard->balance = 0;
        $newVcard->max_debit = 5000;

        $newVcard->save();
        VcardResource::$format = 'detailed';
        return response()->json([
            'message' => 'success',
            'data' => new VcardResource($newVcard)
        ], 200);
    }

    public function show($phoneNumber)
    {
        $vcard = Vcard::findOrFail($phoneNumber);
        return new VcardResource($vcard);
    }

    public function update($phoneNumber)
    {
        $request = new DADUpdateVcardRequest();
        $vcard = Vcard::findOrFail($phoneNumber);
        $vcard->name = $request->name;
        $vcard->email = $request->email;
        $vcard->password = $request->password;
        $vcard->confirmation_code = $request->confirmation_code;
        $vcard->save();
        return response()->json([
            'message' => 'success',
            'data' => new VcardResource($vcard)
        ], 200);
    }

    public function destroy($phoneNumber)
    {
        $vcard = Vcard::findOrFail($phoneNumber);

        if ($vcard->balance != 0) {
            return response()->json([
                'message' => 'error',
                'data' => 'Vcard has balance'
            ], 400);
        }
        //confirmar se o vCard tem transações associads
        //se tiver, apagar com soft delete:
        //Vcard::destroy($phoneNumber);

        //se não tiver, apagar com hard delete:
        //$vcard->forceDelete();
        return response()->json([
            'message' => 'success',
            'data' => new VcardResource($vcard)
        ], 200);
    }
}
