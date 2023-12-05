<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DADStoreVcardRequest;
use App\Http\Requests\DADUpdateVcardRequest;
use App\Http\Resources\VcardResource;
use App\Models\Vcard;
use Illuminate\Http\Request;
use App\Services\Base64Services;
use Illuminate\Support\Facades\Storage;
use App\Models\Transaction;
use App\Models\Category;

class DADVcardController extends Controller {
    public function index() {
        return VcardResource::collection(Vcard::all());
    }

    private function storeBase64AsFile(Vcard $vcard, string $base64String) {
        $targetDir = storage_path('app/public/fotos');
        $newfilename = $vcard->phone_number."_".rand(1000, 9999);
        $base64Service = new Base64Services();
        return $base64Service->saveFile($base64String, $targetDir, $newfilename);
    }

    public function store(DADStoreVcardRequest $request) {
        $dataToSave = $request->validated();

        $base64ImagePhoto = array_key_exists("base64ImagePhoto", $dataToSave) ?
            $dataToSave["base64ImagePhoto"] : ($dataToSave["base64ImagePhoto"] ?? null);
        unset($dataToSave["base64ImagePhoto"]);


        $newVcard = new Vcard();
        $newVcard->phone_number = $dataToSave['phone_number'];
        $newVcard->password = $dataToSave['password'];
        $newVcard->confirmation_code = $dataToSave['confirmation_code'];
        $newVcard->name = $dataToSave['name'];
        $newVcard->email = $dataToSave['email'];
        $newVcard->blocked = 0;
        $newVcard->balance = 0;
        $newVcard->max_debit = 5000;

        if($base64ImagePhoto) {
            $newVcard->photo_url = $this->storeBase64AsFile($newVcard, $base64ImagePhoto);
        }

        $newVcard->save();

        VcardResource::$format = 'detailed';
        return response()->json([
            'message' => 'success',
            'data' => new VcardResource($newVcard)
        ], 200);
    }

    public function show($phoneNumber) {
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



    public function destroy($phoneNumber) {
        $vcard = Vcard::findOrFail($phoneNumber);

        if($vcard->balance != 0) {
            return response()->json([
                'message' => 'error',
                'data' => 'Vcard has balance'
            ], 400);
        }
        //confirmar se o vCard tem transações associads
        //se tiver, apagar com soft delete:

        $transactions = Transaction::where('vcard', $phoneNumber)->get();
        $categories = Category::where('vcard', $phoneNumber)->get();
        if($transactions || $categories) {
            foreach($categories as $category) {
                $category->delete();
            }
            foreach($transactions as $transaction) {
                $transaction->delete();
            }
            $vcard->delete();
            return response()->json([
                'message' => 'success',
                'data' => new VcardResource($vcard),
            ], 200);
        } else {
            //se não tiver, apagar com hard delete:
            $vcard->forceDelete();
            return response()->json([
                'message' => 'success',
                'data' => new VcardResource($vcard)
            ], 200);
        }

    }

    public function show_me(Request $request) {
        return new VcardResource($request->vcard());
    }
}
