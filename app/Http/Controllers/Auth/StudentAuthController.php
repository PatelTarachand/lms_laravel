<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Enrollments;
use App\Models\CandidatePass;
use Illuminate\Support\Facades\Crypt;
use App\Helper\Helper;
use Config;
class StudentAuthController extends Controller
{
    
    function login(Request $request){
        $encrypt_key=Config::get('encdec.ENCRYPT_KEY');
    $decrypt_key=Config::get('encdec.DECRYPT_KEY');
    $responce=        json_decode($request->data);
       $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$responce);
       $data=json_decode($jsonData);
        $ip  = getenv('HTTP_CLIENT_IP')?:
        getenv('HTTP_X_FORWARDED_FOR')?:
        getenv('HTTP_X_FORWARDED')?:
        getenv('HTTP_FORWARDED_FOR')?:
        getenv('HTTP_FORWARDED')?:
        getenv('REMOTE_ADDR');

        $user = Student::where('email', $data->email)->first();
        if($user) {
            if (Hash::check($data->password, $user->password)) {
                $token =  $user->createToken('student_token')->plainTextToken;
                $user->api_token = $token;
                if($user->approve_reject_status==='A'){

                    DB::table('t_student_logins')->insert(
                        [
                            'student_id'        => $user->id,
                            'login_date_time'   => date('Y-m-d H:i:s'),
                            'login_ip_address'  => $ip,
                            'token_id'          => $user->api_token,
                            'login_attempt'     => 1,
                            'record_created_on' => date('Y-m-d H:i:s'),
                            'record_updated_on' => date('Y-m-d H:i:s'),
                            'record_created_by' => $user->id,
                            'record_updated_by' => $user->id,
                            'login_status'      => 'success'
                        ],
                    );
                    $user->save();
                   // $string_json_fromPHP = $this->CryptoJSAesEncrypt("rajatjogi",json_encode(['status' => 'success','approve_status'=> $user->approve_reject_status,'request_status'=> $user->request_status, 'id'=>$user->id,'name'=>$user->first_name.' '.$user->last_name,'token' => $token], 200));
                    $string_json_fromPHP = Helper::CryptoJSAesEncrypt($encrypt_key,json_encode(['status' => 'success','approve_status'=> $user->approve_reject_status,'request_status'=> $user->request_status, 'id'=>$user->id,'name'=>$user->first_name.' '.$user->last_name,'token' => $token], 200));
                   return response()->json($string_json_fromPHP);
                    
                }
                $user->save();
                DB::table('t_student_logins')->insert(
                    [
                        'student_id'        => $user->id,
                        'username'          => 'not approved',
                        'login_date_time'   => date('Y-m-d H:i:s'),
                        'login_ip_address'  => $ip,
                        'token_id'          => $user->api_token,
                        'login_attempt'     => 1,
                        'record_created_on' => date('Y-m-d H:i:s'),
                        'record_updated_on' => date('Y-m-d H:i:s'),
                        'record_created_by' => $user->id,
                        'record_updated_by' => $user->id,
                        'login_status'      => 'success'
                    ],
                );
               // $string_json_fromPHP = $this->CryptoJSAesEncrypt("rajatjogi", json_encode(['status' => 'success','approve_status'=> $user->approve_reject_status,'request_status'=> $user->request_status,'id'=>$user->id,'name'=>$user->first_name.' '.$user->last_name,'token' => $token]));
                $string_json_fromPHP = Helper::CryptoJSAesEncrypt($encrypt_key, json_encode(['status' => 'success','approve_status'=> $user->approve_reject_status,'request_status'=> $user->request_status,'id'=>$user->id,'name'=>$user->first_name.' '.$user->last_name,'token' => $token]));
               // return response()->json(['status' => 'success','approve_status'=> $user->approve_reject_status,'request_status'=> $user->request_status,'id'=>$user->id,'name'=>$user->first_name,'token' => $token], 200);
              // dd($string_json_fromPHP);
               return response()->json($string_json_fromPHP);
            }else{
                DB::table('t_student_logins')->insert(
                    [
                        'login_date_time'   => date('Y-m-d H:i:s'),
                        'login_ip_address'  => $ip,
                        'login_attempt'     => 1,
                        'record_created_on' => date('Y-m-d H:i:s'),
                        'record_updated_on' => date('Y-m-d H:i:s'),
                        'login_status'      => 'failed|wrong_password'
                    ],
                );
                $string_json_fromPHP = Helper::CryptoJSAesEncrypt($encrypt_key,json_encode(['status'=>'Unauthorised','error'=>'ID or Password Incorrect']));
                return response()->json($string_json_fromPHP);
            }
        }else{

            DB::table('t_student_logins')->insert(
                [
                    'login_date_time'   => date('Y-m-d H:i:s'),
                    'login_ip_address'  => $ip,
                    'login_attempt'     => 1,
                    'record_created_on' => date('Y-m-d H:i:s'),
                    'record_updated_on' => date('Y-m-d H:i:s'),
                    'login_status'      => 'failed|wrong_user'
                ],
            );
            $string_json_fromPHP = Helper::CryptoJSAesEncrypt($encrypt_key,json_encode(['status'=>'Unauthorised','error'=>'ID or Password Incorrect']));
            return response()->json($string_json_fromPHP);
        }
    }

    function candidate_login(Request $request){
        $encrypt_key=Config::get('encdec.ENCRYPT_KEY');
    $decrypt_key=Config::get('encdec.DECRYPT_KEY');
        $ip  = getenv('HTTP_CLIENT_IP')?:
        getenv('HTTP_X_FORWARDED_FOR')?:
        getenv('HTTP_X_FORWARDED')?:
        getenv('HTTP_FORWARDED_FOR')?:
        getenv('HTTP_FORWARDED')?:
        getenv('REMOTE_ADDR');
        $responce=        json_decode($request->data);
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$responce);
        $data=json_decode($jsonData);
       // dd($data);
        $approveUser = CandidatePass::where('username',$data->email)->get()->first();
        if($approveUser) {
            if (Hash::check($data->password, $approveUser->new_password)) {
                $token =  $approveUser->createToken('approve_candidate')->plainTextToken;
                $approveUser->api_token = $token;

                $enrollObj = Enrollments::where('student_id',$approveUser->student_id)->first();
                if(!empty($enrollObj))
                    $enroll_id = $enrollObj->enroll_id;
                else
                    $enroll_id = 'Pending';

                    DB::table('t_student_logins')->insert(
                        [
                            'student_id'        => $approveUser->student_id,
                            'username'          => $enroll_id,
                            'login_date_time'   => date('Y-m-d H:i:s'),
                            'login_ip_address'  => $ip,
                            'token_id'          => $approveUser->api_token,
                            'login_attempt'     => 1,
                            'record_created_on' => date('Y-m-d H:i:s'),
                            'record_updated_on' => date('Y-m-d H:i:s'),
                            'record_created_by' => $approveUser->student_id,
                            'record_updated_by' => $approveUser->student_id,
                            'login_status'      => 'success'
                        ],
                    );

                    $stuObj = Student::where('id',$approveUser->student_id)->first();
                    $approveUser->save();
                    $string_json_fromPHP = Helper::CryptoJSAesEncrypt($encrypt_key,json_encode([
                        'status' => 'success',
                        'approve_status'=> $stuObj->approve_reject_status,
                        'request_status'=> $stuObj->request_status,
                        'enroll_id'     => $enroll_id,
                        'id'            => $stuObj->id,
                        'name'          => $stuObj->first_name.' '.$stuObj->last_name,
                        'token'         => $token
                    ], 200));
                  //  dd($string_json_fromPHP);
                    return response()->json($string_json_fromPHP);

            }else{
                DB::table('t_student_logins')->insert(
                    [
                        'login_date_time'   => date('Y-m-d H:i:s'),
                        'login_ip_address'  => $ip,
                        'login_attempt'     => 1,
                        'record_created_on' => date('Y-m-d H:i:s'),
                        'record_updated_on' => date('Y-m-d H:i:s'),
                        'login_status'      => 'failed|wrong_password'
                    ],
                );
                $string_json_fromPHP = Helper::CryptoJSAesEncrypt($encrypt_key,json_encode(['status'=>'Unauthorised','error'=>'ID or Password Incorrect']));
               // dd($string_json_fromPHP);
                return response()->json($string_json_fromPHP);
            }
        }else{

            DB::table('t_student_logins')->insert(
                [
                    'login_date_time'   => date('Y-m-d H:i:s'),
                    'login_ip_address'  => $ip,
                    'login_attempt'     => 1,
                    'record_created_on' => date('Y-m-d H:i:s'),
                    'record_updated_on' => date('Y-m-d H:i:s'),
                    'login_status'      => 'failed|wrong_user'
                ],
            );
            $string_json_fromPHP = Helper::CryptoJSAesEncrypt($encrypt_key,json_encode(['status'=>'Unauthorised','error'=>'ID or Password Incorrect']));
           //dd($string_json_fromPHP);
            return response()->json( $string_json_fromPHP);
        }
    }

    function logout(Request $request){
        $api_token = $request->user()->api_token;
        if(!empty($api_token)){
            $id        = $request->user()->id;

            $logObj = DB::table('t_student_logins')->where('token_id',$api_token)->get()->first();

            if(!empty($logObj)){
                DB::table('t_student_logins')->where('token_id',$api_token)->update(
                    [
                        'logout_date_time'   => date('Y-m-d H:i:s'),
                        'record_updated_on' => date('Y-m-d H:i:s'),
                        'record_updated_by' => $id,
                    ],
                );
            }

            $stuObj    = Student::find($id);
            $stuObj->api_token  = null;
            $stuObj->save();
            DB::table('personal_access_tokens')->where('tokenable_id',$id)->delete();
            return response()->json(['status' => 'success','message' => 'You have been successfully logged out.'], 200);
        }else{
            return response()->json(['status' => 'failed','message' => 'You have been successfully logged out.']);
        }
    }//fun

    function candidate_logout(Request $request){

       $api_token = $request->user()->api_token;

        if(!empty($api_token)){
            $id    = $request->user()->student_id;

           $logObj = DB::table('t_student_logins')->where('token_id',$api_token)->get()->first();

            if(!empty($logObj)){
                DB::table('t_student_logins')->where('token_id',$api_token)->update(
                    [
                        'logout_date_time'   => date('Y-m-d H:i:s'),
                        'record_updated_on' => date('Y-m-d H:i:s'),
                        'record_updated_by' => $id,
                    ],
                );
            }

            $Obj    = CandidatePass::where('student_id',$id)->first();
            $Obj->api_token  = null;
            $Obj->save();
            DB::table('personal_access_tokens')->where('tokenable_id',$id)->delete();
            return response()->json(['status' => 'success','message' => 'You have been successfully logged out.'], 200);
        }else{
            return response()->json(['status' => 'failed','message' => 'You have been successfully logged out.']);
        }
    }//fun

    // //encryption
    // function CryptoJSAesEncrypt($passphrase, $plain_text){

    //     $salt = openssl_random_pseudo_bytes(256);
    //     $iv = openssl_random_pseudo_bytes(16);
    //     //on PHP7 can use random_bytes() istead openssl_random_pseudo_bytes()
    //     //or PHP5x see : https://github.com/paragonie/random_compat
    //    // dd($salt);
    //     $iterations = 999;  
    //     $key = hash_pbkdf2("sha512", $passphrase, $salt, $iterations, 64);
    
    //     $encrypted_data = openssl_encrypt($plain_text, 'aes-256-cbc', hex2bin($key), OPENSSL_RAW_DATA, $iv);
    
    //     $data = array("key"=> $key, "ciphertext" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "salt" => bin2hex($salt));
    //     return $data;
    // }
    // function CryptoJSAesDecrypt($passphrase, $encrypted){

    //      $salt = $encrypted->salt;
    //      $iv = $encrypted->iv;
    // //     //on PHP7 can use random_bytes() istead openssl_random_pseudo_bytes()
    // //     //or PHP5x see : https://github.com/paragonie/random_compat
    // //    // $salt =   
    //    // dd(hex2bin($salt));
    //      $iterations = 10000;  
    //      $key = hash_pbkdf2("sha512", $passphrase, hex2bin($salt), $iterations, 64);
    //    // dd($key);
    //      $decrypted_data = openssl_decrypt($encrypted->cipherText, 'aes-256-cbc', hex2bin($key), 0, hex2bin($iv));
    
    // //     $data = array("Decrypted text" => $decrypted_data, "iv" => $iv, "salt" => $salt);

    // //dd($decrypted_data);
    //    return $decrypted_data;
    // }
}
