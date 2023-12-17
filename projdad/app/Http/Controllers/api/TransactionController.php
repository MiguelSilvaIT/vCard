<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Notifications\NewNotification;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Transaction::class, 'transaction');
    }
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
        $newTransaction->date = date("Y-m-d");
        $newTransaction->datetime = date("Y-m-d H:i:s");
        // check vcard balance using the relationship between vcard and transaction
        $vcard = $newTransaction->vcard_details;
        $newTransaction->old_balance = $vcard->balance;
        switch($request->type){
            case 'D':
                if($vcard->balance < $request->value){
                    return response()->json([
                        'success' => false,
                        'message' => 'Insuficient funds'
                    ], 200);
                }
                if($vcard->max_debit < $request->value){
                    return response()->json([
                        'success' => false,
                        'message' => 'Max debit exceeded'
                    ], 200);
                }
                $newTransaction->new_balance = $vcard->balance - $request->value;
                if($request->payment_type == 'VCARD'){
                    $newPairTransaction = new Transaction();
                    $newTransaction->pair_vcard = $request->payment_ref;
                    $newTransaction->payment_reference = $request->payment_ref;
                    
                    $newTransaction->save();
                    //FALTA ALTERAR O VALOR DO BALANCE DOS VCARDS ENVOLVIDOS
                    $vcard->balance = $newTransaction->new_balance;
                    $vcard->save();
                    
                    $newPairTransaction->value = $request->value;
                    $newPairTransaction->vcard = $request->payment_ref;
                    $newPairTransaction->pair_vcard = $request->vcard;
                    $newPairTransaction->description = $request->description;
                    $newPairTransaction->date = date("Y-m-d");
                    $newPairTransaction->datetime = date("Y-m-d H:i:s");
                    $newPairTransaction->type = 'C';
                    $pair_vcard = $newPairTransaction->vcard_details;
                    $newPairTransaction->old_balance = $pair_vcard->balance;
                    $newPairTransaction->new_balance = $pair_vcard->balance + $request->value;
                    $newPairTransaction->payment_type = 'VCARD';
                    $newPairTransaction->payment_reference = $request->vcard;
                    $newPairTransaction->pair_transaction = $newTransaction->id;
                    $newPairTransaction->save();
                    //FALTA ALTERAR O VALOR DO BALANCE DOS VCARDS ENVOLVIDOS
                    $pair_vcard->balance = $newPairTransaction->new_balance;
                    $pair_vcard->save();
        
                    //update pair_transaction 
                    $newTransaction->pair_transaction = $newPairTransaction->id;
                    $newTransaction->save();
        
                    //send notification when transaction between vcards is made
                    $custom_data = $pair_vcard->custom_data;
                    $notification["id"] =isset($custom_data['notifications']) ? count($custom_data['notifications']) :0;
                    $notification["message"] = 'Recebeu '.$request->value.'€ recebida de '.$request->vcard;
                    $notification["description"] = $request->description;
                    $notification["read"] = false;
                    $custom_data['notifications'][] = $notification;
        
                    if(isset($pair_vcard->custom_options['notification']) && $pair_vcard->custom_options['notification']){
                        $expoMessage = new NewNotification($notification);
                        $pair_vcard->notify($expoMessage);
                    }
        
                    $pair_vcard->custom_data = $custom_data;
                    $pair_vcard->save();
                    
        
                    if($request->spare_change){
                        $change = round(ceil($request->value) - $request->value,2);
                        if(ceil($request->value)<$vcard->balance ){
                            $custom_data = $vcard->custom_data;
                            $piggyBank = $custom_data['value'] ?? 0;
                            
                            $piggyBank += $change;
                            $custom_data['value'] = $piggyBank;
                            $vcard->custom_data = $custom_data;
                            $vcard->save();
                            return response()->json([
                                'success' => true,
                                'message' => "({$change}€) guardados com sucesso nas suas poupanças",
                                'data' => $newTransaction
                            ], 200);
                        }
                        return response()->json([
                            'success' => true,
                            'message' => " Não possui saldo suficiente para guardar ({$change}€) nas suas poupanças",
                            'data' => $newTransaction
                        ], 200); 
                    }
                    return response()->json([
                        'success' => true,
                        'message' => 'Transaction completed successfully',
                        'data' => $newTransaction
                    ], 200);
                }
                $endpoint = 'debit';
                break;
            case 'C':
                $newTransaction->new_balance = $vcard->balance + $request->value;
                if($request->payment_type == 'VCARD'){
                    return response()->json([
                        'success' => false,
                        'message' => 'Credit transactions not allowed between vcards'
                    ], 200);
                }
                $endpoint = 'credit';
                break;
        }
        $newTransaction->payment_reference = $request->payment_ref;
        $payload = [
            'value' => $request->value,
            'reference' => $request->payment_ref,
            'type' => $request->payment_type
        ];
        $response = Http::post("https://dad-202324-payments-api.vercel.app/api/{$endpoint}", $payload);
        if($response->failed()){
            $message = json_decode($response->body())->message;
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 200);
        }

        $vcard->balance = $newTransaction->new_balance;
        $newTransaction->save();
        $vcard->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Transação realizada com sucesso',
            'data' => $newTransaction
        ], 200);
    }
    
    //update
    public function update( Transaction $transaction, Request $request)
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

    public function getAllMonths()
    {
        $months = Transaction::select(\DB::raw('MONTH(date) as month'))
            ->groupBy(\DB::raw('MONTH(date)'))
            ->orderBy(\DB::raw('MONTH(date)'))
            ->get();
    
        $monthsFormatted = $months->map(function ($item) {
            return [
                'month' => strftime('%B', mktime(0, 0, 0, $item->month, 1)),
            ];
        });
    
        return $monthsFormatted;
    }



    
}
