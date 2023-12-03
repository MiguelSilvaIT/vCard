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
            'message' => 'Vcard criado com sucesso',
            'data' => new VcardResource($newVcard)
        ], 200);
    }

    public function piggyBank(Request $request, Vcard $vcard)
    {
        $request->validate([
            'value' => 'required|numeric|min:1',
            'action' => 'required|string'
        ]);
        $piggyBank = json_decode($vcard->custom_data, true);
        if ($request->action == 'save'){
            if($request->value > $vcard->balance){
                return response()->json([
                    'message' => 'Não é possivel guardar mais do que o valor que tem na conta',
                    'data' => ''
                ], 400);
            }
            if (isset($piggyBank['value'])) {
                $piggyBank['value'] += $request->value;
            } else {
                $piggyBank['value'] = $request->value;
            }          
            $vcard->balance -= $request->value;
        } 
        else if ($request->action == 'withdraw'){
            if( !isset($piggyBank['value']) || $request->value > $piggyBank['value']) {
                $piggyBank['value'] = 0;
                return response()->json([
                    'message' => 'Não é possivel retirar mais do que o valor que tem na poupança',
                    'data' => ''
                ], 400);
            }
            $piggyBank['value'] -= $request->value;
            $vcard->balance += $request->value;
        }
        $vcard->custom_data = json_encode(['value' => $piggyBank['value']]);
        $vcard->save();  
        
        VcardResource::$format = 'detailed';
        return response()->json([
            'success' => true,
            'message' => 'Dinheiro guardado com sucesso',
            'data' => new VcardResource($vcard)
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
            'message' => $vcard != null ? 'Vcard exists' : 'Vcard does not exist',
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
            'message' => $isPasswordCorrect ? 'Login bem sucedido' : 'Palavra-passe incorrecta, tente novamente',
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
            'message' => $isConfirmationCodeCorrect ? 'PIN correto' : 'PIN incorreto, tente novamente',
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

    public function myTransactions(Request $request, Vcard $vcard)
    {
        
        $transactions = $vcard->transactions();
        $filter_by_date = null;
        $filter_by_type = null;
        
        if($request->filter_start_date != null && $request->filter_end_date == null){
            $transactions = $vcard->transactions()->where('date', '>=', $request->filter_start_date);
            //return response()->json($transactions);
        }

        if($request->filter_start_date && $request->filter_end_date){
            $transactions = $vcard->transactions()->whereBetween('date', [$request->filter_start_date, $request->filter_end_date]);
        }
        
        if($request->filter_by_type != 'T' && $request->filter_by_type){
            $transactions = $transactions->where('type', $request->filter_by_type);
        }

        if($request->filter_by_value == "value_asc"){
            $transactions = $transactions->orderByRaw('CAST(value AS DECIMAL(10,2))');;
        }
        else if($request->filter_by_value == "value_desc"){
            $transactions = $transactions->orderBy('value',"desc");
        }
        else if($request->filter_by_value == "date_asc"){
            $transactions = $transactions->orderBy('datetime');
        }
        else{
            $transactions = $transactions->orderBy('datetime',"desc");
        }
        $transactions = $transactions->get();
        return response()->json($transactions);
    }
}
