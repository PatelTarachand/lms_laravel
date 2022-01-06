<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Helper\Helper;
use Config;
class UserAuthController extends Controller
{

    function login(Request $request){
        $encrypt_key=Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key=Config::get('encdec.DECRYPT_KEY');
        $responce=json_decode($request->data);
        //dd($responce);
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$responce);
        $data=json_decode($jsonData);
        $user =  User::where('email', $data->email)->first();

        if($user) {
            if (Hash::check($data->password, $user->password)) {
               $token = $user->createToken('access_token')->accessToken;
                 
               //for admin
               if($user->user_type==1){
                $string_json_fromPHP = Helper::CryptoJSAesEncrypt($encrypt_key,json_encode(['status' => 'success','user_type' => 'admin','token' => $token, 'name' => $user->name, 'id' => $user->id], 200));
                    return response()->json($string_json_fromPHP);
                }else{
                    $string_json_fromPHP = Helper::CryptoJSAesEncrypt($encrypt_key,json_encode(['status' => 'success','user_type' => 'user','token' => $token, 'name' => $user->name, 'id' => $user->id], 200));
                    return response()->json($string_json_fromPHP);
                }
            }
            else{
                $string_json_fromPHP = Helper::CryptoJSAesEncrypt($encrypt_key,json_encode(['status'=>'Unauthorised','error'=>'ID or Password Incorrect']));
                return response()->json($string_json_fromPHP);
            }
        }else{
            $string_json_fromPHP = Helper::CryptoJSAesEncrypt($encrypt_key,json_encode(['status'=>'Unauthorised','error'=>'ID or Password Incorrect']));
            return response()->json($string_json_fromPHP);
        }

    }

    function logout(Request $request){
        $accessToken = auth()->user()->token();
        if(!empty($accessToken)){
            $token= $request->user()->tokens->find($accessToken);
            $token->revoke();
            return response()->json(['status' => 'success','message' => 'You have been successfully logged out.'], 200);
        }else{
            return response()->json(['status' => 'failed','message' => 'You have been successfully logged out.']);
        }
    }

}
