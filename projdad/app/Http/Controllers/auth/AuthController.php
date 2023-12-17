<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Vcard;

class AuthController extends Controller
{
    private function passportAuthenticationData($username, $password) {
        return [
        'grant_type' => 'password',
        'client_id' => env('PASSPORT_CLIENT_ID'),
        'client_secret' => env('PASSPORT_CLIENT_SECRET'),
        'username' => $username,
        'password' => $password,
        'scope' => ''
        ];
    }


    public function login(Request $request)
    {
        $passportData = [
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_PASSWORD_GRANT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_GRANT_SECRET'),
            'username' => $request->username,
            'password' => $request->password,
            'scope'         => '',
        ];

        if($request->has('token')){
            $vcard = Vcard::where('phone_number', $request->username)->first();
            if ($vcard !=null) {
                $custom_options = $vcard->custom_options;
                $custom_options['token'] = $request->token;
                $vcard->custom_options = $custom_options;
                $vcard->save();
            }
        }

        request()->request->add($passportData);

        $request = Request::create(env('PASSPORT_URL') . '/oauth/token', 'POST');
        $response = Route::dispatch($request);
        $errorCode = $response->getStatusCode();

        if (
            $errorCode == '200'
        ) {
            return json_decode((string) $response->content(), true);
        } else {
            return response()->json(
                ['msg' => 'User credentials are invalid'],
                $errorCode
            );
        }
    }

    public function logout(Request $request)
    {
        $accessToken = $request->user()->token();
        $token = $request->user()->tokens->find($accessToken);
        $token->revoke();
        $token->delete();
        return response(['msg' => 'Token revoked'], 200);
    }
}