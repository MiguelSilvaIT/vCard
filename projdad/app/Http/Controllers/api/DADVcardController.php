<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DADStoreVcardRequest;
use App\Http\Requests\DADUpdateVcardRequest;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\VcardResource;
use App\Models\Vcard;
use Illuminate\Http\Request;
use App\Services\Base64Services;
use Illuminate\Support\Facades\Storage;
use App\Models\Transaction;
use App\Models\Category;

class DADVcardController extends Controller {
    public function index() {
        //devolve todos os vCards da BD
        return VcardResource::collection(Vcard::all());
    }

    private function storeBase64AsFile(Vcard $vcard, string $base64String) {
        // Store base64 string as file and return the file name
        $targetDir = storage_path('app/public/fotos');
        $newfilename = $vcard->phone_number."_".rand(1000, 9999);
        $base64Service = new Base64Services();
        return $base64Service->saveFile($base64String, $targetDir, $newfilename);
    }

    public function store(DADStoreVcardRequest $request) {
        //validar os dados recebidos
        $dataToSave = $request->validated();

        $base64ImagePhoto = array_key_exists("base64ImagePhoto", $dataToSave) ?
            $dataToSave["base64ImagePhoto"] : ($dataToSave["base64ImagePhoto"] ?? null);
        unset($dataToSave["base64ImagePhoto"]);

        //criar um novo vCard e inserir os dados recebidos
        $newVcard = new Vcard();
        $newVcard->phone_number = $dataToSave['phone_number'];
        $newVcard->password = $dataToSave['password'];
        $newVcard->confirmation_code = $dataToSave['confirmation_code'];
        $newVcard->name = $dataToSave['name'];
        $newVcard->email = $dataToSave['email'];
        $newVcard->blocked = 0;
        $newVcard->balance = 0;
        $newVcard->max_debit = 5000;

        //Guarda o nome da foto do vCard na BD e a foto na pasta storage/app/public/fotos
        if($base64ImagePhoto) {
            $newVcard->photo_url = $this->storeBase64AsFile($newVcard, $base64ImagePhoto);
        }

        $newVcard->save();

        //devolve o vCard criado no formato detalhado que mostra: phone, name, photo e balance (formatos no VcardResource.php)
        VcardResource::$format = 'detailed';
        return response()->json([
            'message' => 'success',
            'data' => new VcardResource($newVcard)
        ], 200);
    }

    public function show($phoneNumber) {
        //devolve o vCard correspondente ao phoneNumber recebido
        $vcard = Vcard::findOrFail($phoneNumber);
        return new VcardResource($vcard);
    }

    public function update(DADUpdateVcardRequest $request, $phoneNumber) {
        $vcard = Vcard::findOrFail($phoneNumber);
        $dataToSave = $request->validated();

        $base64ImagePhoto = array_key_exists("base64ImagePhoto", $dataToSave) ?
            $dataToSave["base64ImagePhoto"] : ($dataToSave["base64ImagePhoto"] ?? null);
        $deletePhotoOnServer = array_key_exists("deletePhotoOnServer", $dataToSave) && $dataToSave["deletePhotoOnServer"];
        unset($dataToSave["base64ImagePhoto"]);
        unset($dataToSave["deletePhotoOnServer"]);

        $vcard->name = $dataToSave['name'];
        $vcard->email = $dataToSave['email'];
        $vcard->password = $dataToSave['password'];
        $vcard->confirmation_code = $dataToSave['confirmation_code'];

        // Delete previous photo file if a new file is uploaded or the photo is to be deleted
        if($vcard->photo_url && ($deletePhotoOnServer || $base64ImagePhoto)) {
            if(Storage::exists('public/fotos/'.$vcard->photo_url)) {
                Storage::delete('public/fotos/'.$vcard->photo_url);
            }
            $vcard->photo_url = null;
        }

        // Create a new photo file from base64 content
        if($base64ImagePhoto) {
            $vcard->photo_url = $this->storeBase64AsFile($vcard, $base64ImagePhoto);
        }
        $vcard->save();
        VcardResource::$format = 'detailed';
        return response()->json([
            'message' => 'success',
            'data' => new VcardResource($vcard)
        ], 200);
    }

    //método que apenas deve estar disponível para o admin
    public function alterBlockedStatus($phoneNumber) {
        //Find the vCard with the given phone number
        $vcard = Vcard::findOrFail($phoneNumber);
        //Invert the blocked status
        if($vcard->blocked == 0) {
            //If the vCard is not blocked, block it
            //And return the vCard with the new status
            $vcard->blocked = 1;
            $vcard->save();
            return response()->json([
                'message' => 'blocked with success',
                'data' => new VcardResource($vcard)
            ], 200);
        } else {
            //If the vCard is blocked, unblock it
            //And return the vCard with the new status
            $vcard->blocked = 0;
            $vcard->save();
            return response()->json([
                'message' => 'unblocked with success',
                'data' => new VcardResource($vcard)
            ], 200);
        }
    }

    //método que apenas deve estar disponível para o admin
    public function updateMaxDebit(Request $request, $phoneNumber) {
        //Find the vCard with the given phone number
        $vcard = Vcard::findOrFail($phoneNumber);
        //Validate the request
        $dataToSave = $request->validated();
        //Update the max_debit
        $vcard->max_debit = $dataToSave['max_debit'];
        $vcard->save();
        //Return the vCard with the new max_debit
        return response()->json([
            'message' => 'success',
            'data' => new VcardResource($vcard)
        ], 200);
    }

    public function destroy($phoneNumber) {
        //vai buscar o vCard com o phoneNumber recebido
        $vcard = Vcard::findOrFail($phoneNumber);
        //confirmar se o vCard tem saldo
        if($vcard->balance != 0) {
            //se tiver, devolve erro
            return response()->json([
                'message' => 'error',
                'data' => 'Vcard has balance'
            ], 400);
        }
        //vai buscar as Transações e Categorias do vCard
        $transactions = Transaction::where('vcard', $phoneNumber)->get();
        $categories = Category::where('vcard', $phoneNumber)->get();
        //se tiver transações, apagar com soft delete:
        if(!$transactions->isEmpty()) {
            //se tiver categorias, apagar com soft delete:
            if(!$categories->isEmpty()){
                foreach($categories as $category) {
                    $category->delete();
                }
            }
            //apagar as transações com soft delete:
            foreach($transactions as $transaction) {
                $transaction->delete();
            }
            //apagar o vCard com soft delete:
            $vcard->delete();
            //devolver o vCard apagado e a mensagem de sucesso "Soft deleted"
            return response()->json([
                'message' => 'Soft deleted',
                'data' => new VcardResource($vcard)
            ], 200);

        } else {
            //se não tiver transações
            //se tiver categorias, apagar com hard delete:
            if($categories){
                foreach($categories as $category) {
                    $category->forceDelete();
                }
            }
            //apagar o vCard com hard delete:
            $vcard->forceDelete();

            //devolver o vCard apagado e a mensagem de sucesso "Hard deleted"
            return response()->json([
                'message' => 'Hard deleted',
                'data' => new VcardResource($vcard)
            ], 200);
        }

    }
}
