<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateVcardRequest;
use Illuminate\Http\Request;
use App\Models\Vcard;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\VcardResource;
use App\Http\Requests\StoreVcardRequest;
use Illuminate\Support\Facades\Hash;
use App\Services\Base64Services;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateUserPasswordRequest;
use App\Http\Requests\UpdateVcardconfirmation_codeRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Validator;
use App\Models\DefaultCategory;


class VcardController extends Controller
{
    private function storeBase64AsFile(Vcard $vcard, string $base64String)
    {
        // Store base64 string as file and return the file name
        $targetDir = storage_path('app/public/fotos');
        $newfilename = $vcard->phone_number . "_" . rand(1000, 9999);
        $base64Service = new Base64Services();
        return $base64Service->saveFile($base64String, $targetDir, $newfilename);
    }

    public function index()
    {
        VcardResource::$format = 'detailed';
        return VcardResource::collection(Vcard::all());
    }


    public function show(Vcard $vcard)
    {
        //devolve o vCard correspondente ao phoneNumber recebido
        VcardResource::$format = 'detailed';
        return new VcardResource($vcard);
    }

    public function store(StoreVcardRequest $request)
    {
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
        if ($base64ImagePhoto) {
            $newVcard->photo_url = $this->storeBase64AsFile($newVcard, $base64ImagePhoto);
        }

        $newVcard->save();
        $defaultCategories = DefaultCategory::query()->get();
        foreach ($defaultCategories as $defaultCategory) {
            $category = new \App\Models\Category();
            $category->name = $defaultCategory->name;
            $category->type = $defaultCategory->type;
            $category->vcard = $request->phone_number;
            $category->save();
        }

        //devolve o vCard criado no formato detalhado que mostra: phone, name, photo e balance (formatos no VcardResource.php)
        VcardResource::$format = 'detailed';
        return new VcardResource($newVcard);
    }

    public function piggyBank(Request $request, Vcard $vcard)
    {
        $request->validate([
            'value' => 'required|numeric|min:0.01',
            'action' => 'required|string'
        ]);
        //message for the validation
        $customData = $vcard->custom_data;
        $customData["notifications"] = $customData["notifications"] ?? [];
        $piggyBank = $customData["value"] ?? 0;

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Dinheiro guardado com sucesso',
        //     'data' => $piggyBank
        // ], 200);
        if ($request->action == 'save') {
            if ($request->value > $vcard->balance) {
                return response()->json([
                    'message' => 'Não é possivel guardar mais do que o valor que tem na conta',
                    'data' => ''
                ], 400);
            }
            $piggyBank += $request->value;
            $vcard->balance -= $request->value;
        } else if ($request->action == 'withdraw') {
            if ($request->value > $piggyBank) {
                return response()->json([
                    'message' => 'Não é possivel retirar mais do que o valor que tem na poupança',
                    'data' => ''
                ], 400);
            }
            $piggyBank -= $request->value;
            $vcard->balance += $request->value;
        }

        $customData['value'] = $piggyBank;
        $vcard->custom_data = $customData;
        $vcard->save();

        VcardResource::$format = 'detailed';
        return response()->json([
            'success' => true,
            'message' => 'Dinheiro guardado com sucesso',
            'data' => new VcardResource($vcard)
        ], 200);
    }

    public function updateSettings(Request $request, Vcard $vcard)
    {
        $request->validate([
            'notification' => 'required|boolean',
            'spare_change' => 'required|boolean',
        ]);
        $custom_options = $vcard->custom_options;
        $custom_options['notification'] = $request->notification;
        $custom_options['spare_change'] = $request->spare_change;
        $vcard->custom_options = $custom_options;
        $vcard->save();
        VcardResource::$format = 'detailed';
        return new VcardResource($vcard);
    }

    public function markReadNotifications(Request $request, Vcard $vcard)
    {
        $custom_data = $vcard->custom_data;
        $custom_data['notifications'] = $request->notifications;
        $vcard->custom_data = $custom_data;
        $vcard->save();
        return response()->json([
            'message' => 'success',
            'success' => true
        ], 200);
    }

    public function checkPhoneNumber(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|digits:9|starts_with:9',
        ]);

        $vcard = Vcard::withTrashed()->where('phone_number', $request->phone_number)->first();
        if (isset($vcard) && $vcard->trashed()){
            return response()->json([
                'existsVcard' => $vcard != null,
                'deleted' => true,
                'message' =>'Vcard foi apagado não pode ser usado',
            ]);
        }
        return response()->json([
            'existsVcard' => $vcard != null,
            'message' => $vcard != null ? 'Vcard existe' : 'Vcard não existe',
        ]);
    }

    public function confirmDelete(Request $request, Vcard $vcard)
    {
        $request->validate([
            'password' => 'required|string',
            'confirmation_code' => 'required|string',
        ]);

        $isPasswordCorrect = Hash::check($request->password, $vcard->password);
        $isConfirmationCodeCorrect = Hash::check($request->confirmation_code, $vcard->confirmation_code);
        if ($isPasswordCorrect && $isConfirmationCodeCorrect) {
            return response()->json([
                'confirmPassword' => true,
                'message' => 'Delete vCard authorized',
            ], 200);
        }
        return response()->json([
            'confirmPassword' => false,
            'message' => 'Delete vCard unauthorized',
        ], 400);
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

    public function update(UpdateVcardRequest $request, Vcard $vcard)
    {
        $dataToSave = $request->validated();

        $base64ImagePhoto = array_key_exists("base64ImagePhoto", $dataToSave) ?
            $dataToSave["base64ImagePhoto"] : ($dataToSave["base64ImagePhoto"] ?? null);
        $deletePhotoOnServer = array_key_exists("deletePhotoOnServer", $dataToSave) && $dataToSave["deletePhotoOnServer"];
        unset($dataToSave["base64ImagePhoto"]);
        unset($dataToSave["deletePhotoOnServer"]);

        $vcard->name = $dataToSave['name'];
        $vcard->email = $dataToSave['email'];

        // Delete previous photo file if a new file is uploaded or the photo is to be deleted
        if ($vcard->photo_url && ($deletePhotoOnServer || $base64ImagePhoto)) {
            if (Storage::exists('public/fotos/' . $vcard->photo_url)) {
                Storage::delete('public/fotos/' . $vcard->photo_url);
            }
            $vcard->photo_url = null;
        }

        // Create a new photo file from base64 content
        if ($base64ImagePhoto) {
            $vcard->photo_url = $this->storeBase64AsFile($vcard, $base64ImagePhoto);
        }
        $vcard->save();
        VcardResource::$format = 'detailed';
        return response()->json([
            'message' => 'success',
            'data' => new VcardResource($vcard)
        ], 200);
    }

    public function update_max_debit(Request $request, Vcard $vcard)
    {
        $request->validate([
            'max_debit' => 'required|numeric|min:0.01',
        ]);
        $vcard->max_debit = $request->max_debit;
        $vcard->save();
        return response()->json([
            'success' => true,
            'data' => new VcardResource($vcard)
        ], 200);
    }

    //método que apenas deve estar disponível para o admin
    public function alterBlockedStatus(Vcard $vcard)
    {
        //Invert the blocked status
        $vcard->blocked = !$vcard->blocked;
        $vcard->save();
        return response()->json([
            'message' => 'Altered with success',
            'data' => new VcardResource($vcard)
        ], 200);
    }

    public function updatePassword(UpdateUserPasswordRequest $request, Vcard $vcard)
    {
        //validar os dados recebidos
        $dataToSave = $request->validated();
        //alterar a password do vCard
        if (!Hash::check($dataToSave['current_password'], $vcard->password)) {
            //se a password antiga não for igual à que está na BD, devolve erro
            $validator = Validator::make([], []);
            $validator->errors()->add('current_password', 'Current password is not correct');

            return response()->json(['errors' => $validator->errors()], 422);
        }
        $vcard->password = $dataToSave['password'];
        $vcard->save();
        //devolver o vCard com a nova password
        return response()->json([
            'message' => 'success',
            'data' => new VcardResource($vcard)
        ], 200);
    }

    public function updateconfirmation_code(UpdateVcardconfirmation_codeRequest $request, Vcard $vcard)
    {
        //validar os dados recebidos
        $dataToSave = $request->validated();
        //alterar o pin do vCard
        if (!Hash::check($dataToSave['oldconfirmation_code'], $vcard->confirmation_code)) {
            //se o pin antigo não for igual ao que está na BD, devolve erro
            $validator = Validator::make([], []);
            $validator->errors()->add('oldconfirmation_code', 'Old confirmation code is not correct');

            return response()->json(['errors' => $validator->errors()], 422);
        }
        $vcard->confirmation_code = $dataToSave['confirmation_code'];
        $vcard->save();
        //devolver o vCard com o novo pin
        return response()->json([
            'message' => 'success',
            'data' => new VcardResource($vcard)
        ], 200);
    }

    public function destroy(Request $request, Vcard $vcard)
    {
        //confirmar se o vCard tem saldo
        if ($vcard->balance != 0) {
            //se tiver, devolve erro
            return response()->json([
                'success' => false,
                'message' => 'Vcard has balance'
            ], 400);
        }
        //vai buscar as Transações e Categorias do vCard
        $transactions = $vcard->transactions;
        // $pairTransactions = $vcard->pairTransactions;
        $categories = $vcard->categories;
        //se tiver transações, apagar com soft delete:
        if (!$transactions->isEmpty()) {
            //se tiver categorias, apagar com soft delete:
            if (!$categories->isEmpty()) {
                foreach ($categories as $category) {
                    $category->delete();
                }
            }
            //apagar as transações com soft delete:
            foreach ($transactions as $transaction) {
                $transaction->delete();
            }
            //apagar o vCard com soft delete:
            $vcard->delete();
            //devolver o vCard apagado e a mensagem de sucesso "Soft deleted"
            return response()->json([
                'success' => 'true',
                'message' => 'Soft deleted',
                'data' => new VcardResource($vcard)
            ], 200);
        } else {
            //se não tiver transações
            //se tiver categorias, apagar com hard delete:
            if (!$categories->isEmpty()) {
                foreach ($categories as $category) {
                    $category->forceDelete();
                }
            }
            if ($request->taes) {
                $vcard->delete();
                return response()->json([
                    'success' => 'true',
                    'message' => 'Soft deleted',
                    'data' => new VcardResource($vcard)
                ], 200);
            }
            //apagar o vCard com hard delete:
            $vcard->forceDelete();
            //devolver o vCard apagado e a mensagem de sucesso "Hard deleted"
            return response()->json([
                'success' => 'true',
                'message' => 'Hard deleted',
                'data' => new VcardResource($vcard)
            ], 200);
        }
    }

    public function myTransactions(Request $request, Vcard $vcard)
    {

        $transactions = $vcard->transactions();

        if ($request->filter_start_date != null && $request->filter_end_date == null) {
            $transactions = $transactions->where('date', '>=', $request->filter_start_date);
            //return response()->json($transactions);
        } else if ($request->filter_start_date && $request->filter_end_date) {
            $transactions = $transactions->whereBetween('date', [$request->filter_start_date, $request->filter_end_date]);
        }

        if ($request->filter_by_type != 'T' && $request->filter_by_type) {
            $transactions = $transactions->where('type', $request->filter_by_type);
        }
        if ($request->filter_payment_type != null) {
            $transactions = $transactions->where('payment_type', $request->filter_payment_type);
        }
        switch ($request->filter_by_value) {
            case 'value_asc':
                $transactions = $transactions->orderByRaw('CAST(value AS DECIMAL(10,2))');
                ;
                break;
            case 'value_desc':
                $transactions = $transactions->orderBy('value', "desc");
                break;
            case 'date_asc':
                $transactions = $transactions->orderBy('datetime');
                break;
            default:
                $transactions = $transactions->orderBy('datetime', "desc");
                break;
        }
        $transactions = $transactions->get();
        return response()->json($transactions);
    }

    public function getCategoriesOfVcard(Request $request, Vcard $vcard)
    {
        if ($request->transaction_type && in_array($request->transaction_type, ['C', 'D'])) {
            $categories = $vcard->categories->where('type', $request->transaction_type);
        } else {
            $categories = $vcard->categories;
        }
        return CategoryResource::collection($categories);
    }



    //get sum of debit transactions of a vcard per month
    public function getDebitTransactionsTotal(Request $request, Vcard $vcard)
    {
        $total = DB::table('transactions')
            ->where('vcard', $vcard->phone_number)
            ->where('type', 'D')
            ->sum('value');

        return response()->json($total);

    }

    //get sum of credit transactions of a vcard per month
    public function getDebitTransactionsTotalByMonth(Request $request, Vcard $vcard, $month)
    {
        $date = date_parse($month);
        
         $total = DB::table('transactions')
             ->where('vcard', $vcard->phone_number)
             ->where('type', 'D')
             ->whereMonth('date' , $date['month'])
            ->sum('value');

        return $total;

    }

    public function getCreditTransactionsTotal(Request $request, Vcard $vcard)
    {
        $total = DB::table('transactions')
            ->where('vcard', $vcard->phone_number)
            ->where('type', 'C')
            ->sum('value');

        return response()->json($total);

    }

    //get sum of credit transactions of a vcard per month
    public function getCreditTransactionsTotalByMonth(Request $request, Vcard $vcard, $month)
    {
        $date = date_parse($month);
        
         $total = DB::table('transactions')
             ->where('vcard', $vcard->phone_number)
             ->where('type', 'C')
             ->whereMonth('date' , $date['month'])
            ->sum('value');

        return $total;

    }

    public function getTotalTransactions(Request $request, Vcard $vcard)
    {
        
         $total = DB::table('transactions')
             ->where('vcard', $vcard->phone_number)
            ->count();

        return $total;

    }

    public function getTotalTransactionsByMonth(Request $request, Vcard $vcard, $month)
    {

        $date = date_parse($month);

        
         $total = DB::table('transactions')
             ->where('vcard', $vcard->phone_number)
             ->whereMonth('date' , $date['month'])
            ->count();

        return $total;

    }

    public function getBalanceInfo(Request $request, Vcard $vcard)
    {
        
         $minBalance = DB::table('transactions')
             ->where('vcard', $vcard->phone_number)
             ->min('old_balance');

        $maxBalance = DB::table('transactions')
                ->where('vcard', $vcard->phone_number)
                ->max('old_balance');
        
        $allBalances = DB::table('transactions')
                ->where('vcard', $vcard->phone_number)
                ->select('old_balance', 'date')
                ->orderBy('datetime')
                ->get();
        
        $response = [
            'minBalance' => $minBalance,
            'maxBalance' => $maxBalance,
            'allBalances' => $allBalances
        ];
        

        return $response;

    }

}
