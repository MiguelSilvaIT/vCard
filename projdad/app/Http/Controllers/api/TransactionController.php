<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;

class TransactionController extends Controller
{
    //index
    public function index()
    {
        return TransactionResource::collection(Transaction::all());
    }

    //show
    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction);
    }

    //store
    public function store(StoreTransactionRequest $request)
    {
        $newTransaction = new Transaction($request->validated());
        $newPairTransaction = new Transaction();
        $newTransaction->date = date("Y-m-d");
        $newTransaction->datetime = date("Y-m-d H:i:s");
        $newTransaction->type = 'D';
        // check vcard balance using the relationship between vcard and transaction
        $newTransaction->old_balance = $newTransaction->vcard_details->balance;
        $newTransaction->new_balance = $newTransaction->vcard_details->balance - $request->value;
        $newTransaction->payment_type = 'VCARD';
        $newTransaction->payment_reference = $request->pair_vcard;
        //fix this problem
        
        $newTransaction->save();
        //FALTA ALTERAR O VALOR DO BALANCE DOS VCARDS ENVOLVIDOS
        // $newTransaction->vcard_details->balance = $newTransaction->new_balance;
        // $newTransaction->vcard_details->save();

        
        $newPairTransaction->value = $request->value;
        $newPairTransaction->vcard = $request->pair_vcard;
        $newPairTransaction->pair_vcard = $request->vcard;
        $newPairTransaction->description = $request->description;
        $newPairTransaction->date = date("Y-m-d");
        $newPairTransaction->datetime = date("Y-m-d H:i:s");
        $newPairTransaction->type = 'C';
        $newPairTransaction->old_balance = $newPairTransaction->pair_vcard_details->balance;
        $newPairTransaction->new_balance = $newPairTransaction->pair_vcard_details->balance + $request->value;
        $newPairTransaction->payment_type = 'VCARD';
        $newPairTransaction->payment_reference = $request->vcard;
        $newPairTransaction->pair_transaction = $newTransaction->id;
        $newPairTransaction->save();
        //FALTA ALTERAR O VALOR DO BALANCE DOS VCARDS ENVOLVIDOS
        // $newPairTransaction->vcard_details->balance = $newPairTransaction->new_balance;
        // $newPairTransaction->vcard_details->save();
        $newTransaction->pair_transaction = $newPairTransaction->id;
        $newTransaction->save();

        return response()->json([
            'success' => true,
            'data' => $newTransaction
        ], 200);
    }

    //update
    public function update(Request $request, Transaction $transaction)
    {
        $transaction->update($request->all());
        return new TransactionResource($transaction);
    }

    //delete
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return response()->json([
            'message' => 'success'
        ], 200);
    }
}
