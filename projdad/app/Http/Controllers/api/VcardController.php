<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vcard;
use App\Http\Resources\VcardResource;
use App\Http\Requests\StoreVcardRequest;
use Illuminate\Support\Facades\Hash;


class VcardController extends Controller
{
    public function show(Vcard $vcard)
    {
        VcardResource::$format = 'detailed';
        return new VcardResource($vcard);
    }

    public function index()
    {
        return VcardResource::collection(Vcard::all());
    }

    //create new vcard with phone,email,name,password,confirmation_code,balance
    public function store(StoreVcardRequest $request)
    {

        $newVcard = new Vcard($request->validated());
        $newVcard->name = "TAES";
        $newVcard->email = "taes@gmail.com";
        $newVcard->blocked = 0;

        $newVcard->save();

        VcardResource::$format = 'detailed';
        return response()->json([
            'message' => 'success',
            'data' => new VcardResource($newVcard)
        ], 200);
    }
    public function checkPhoneNumber(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|min:9|max:9',
        ]);

        $vcard = Vcard::where('phone_number', $request->phone_number)->first();
        return response()->json([
            'existsVcard' => $vcard != null,
        ]);
    }

    public function checkPassword(Request $request, Vcard $vcard)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $isPasswordCorrect = Hash::check($request->password, $vcard->password);
        return response()->json([
            'isPasswordCorrect' => $isPasswordCorrect,
        ]);
    }

    public function checkConfirmationCode(Request $request, Vcard $vcard)
    {
        $request->validate([
            'confirmation_code' => 'required|string',
        ]);

        $isConfirmationCodeCorrect = Hash::check($request->confirmation_code, $vcard->confirmation_code);
        return response()->json([
            'isConfirmationCodeCorrect' => $isConfirmationCodeCorrect,
        ]);
    }

    public function update(Request $request, Vcard $vcard)
    {
        $vcard->update($request->all());
        return new VcardResource($vcard);
    }

    public function destroy(Vcard $vcard)
    {
        $vcard->transactions()->detach();
        $vcard->delete();
        return new VcardResource($vcard);
    }

    public function myTransactions(Vcard $vcard)
    {
        $transactions = $vcard->transactions()->orderBy('date', 'desc')->get();
        return response()->json($transactions);
    }
}
