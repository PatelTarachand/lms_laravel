<?php

namespace App\Http\Controllers\journals;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\JrnlUser;
use App\Models\JrnlAuthorUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use App\Helper\Helper;
use Config;
class JrnlUserAuth extends Controller
{
    private $storage_root   = 'journals/';

    function authorRegister(Request $request){
        $validator =  Validator::make($request->all(),
            [
                'first_name'     => 'required',
                'last_name'      => 'required',
                'email'          => 'required|email|unique:jrnl_t_author_users,email',
                'mobile_no'      => 'required',
                'password'       => 'required',
            ],
        );

       if ($validator->fails())
       return  response()->json(['error' => $validator->errors()->all()], 401);


        $success = DB::table('jrnl_t_author_users')->insert([
           'first_name'     => $request->input('first_name'),
           'last_name'      => $request->input('last_name'),
           'email'          => $request->input('email'),
           'mobile_no'      => $request->input('mobile_no'),
           'password'       => Hash::make($request->input('password')),
           'record_status'  => 'A',
           'record_created_on'=> date('Y-m-d H:i:s'),
           'record_created_by'=>  $request->input('email'),
        ]);
       
        if($success)
        return  response()->json(['status' =>'success'], 200);

    }//authorRegister

    function authorLogin(Request $request){
      
        // $encrypt_key=Config::get('encdec.ENCRYPT_KEY');
        // $decrypt_key=Config::get('encdec.DECRYPT_KEY');
        // $responce=        json_decode($request->data);
        // $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$responce);
        // $data=json_decode($jsonData);

        $ip  = getenv('HTTP_CLIENT_IP')?:
        getenv('HTTP_X_FORWARDED_FOR')?:
        getenv('HTTP_X_FORWARDED')?:
        getenv('HTTP_FORWARDED_FOR')?:
        getenv('HTTP_FORWARDED')?:
        getenv('REMOTE_ADDR');

        $user = JrnlAuthorUser::where('email', $request->input('username'))->first();
       
        if($user) {
            if (Hash::check($request->password, $user->password)) {
               $author_token =  $user->createToken('jnrl_user_token')->plainTextToken;
               
               DB::table('jrnl_t_user_logins')->insert(
                    [
                        'user_id'        => $user->author_id,
                        'login_date_time'   => date('Y-m-d H:i:s'),
                        'login_ip_address'  => $ip,
                        'token_id'          => 'Author-'.$author_token,
                        'login_attempt'     => 1,
                        'record_created_on' => date('Y-m-d H:i:s'),
                        'record_updated_on' => date('Y-m-d H:i:s'),
                        'record_created_by' => $user->user_id,
                        'record_updated_by' => $user->user_id,
                        'login_status'      => 'success'
                    ],
                );
                
                $user->api_token = $author_token;
                $user->save();
                return json_encode([
                    "status"        => "success",
                    "author_id"     => $user->author_id,
                    "author_name"   => $user->first_name.' '.$user->last_name,
                    "user_role"     => 'Author',
                    "user_token"    => $author_token
                ]);
              
            }else{
                DB::table('jrnl_t_user_logins')->insert(
                    [
                        'login_date_time'   => date('Y-m-d H:i:s'),
                        'login_ip_address'  => $ip,
                        'login_attempt'     => 1,
                        'record_created_on' => date('Y-m-d H:i:s'),
                        'record_updated_on' => date('Y-m-d H:i:s'),
                        'login_status'      => 'failed|wrong_password'
                    ],
                );
                return json_encode(["status"=>"failed","message"=>"ID or Password Incorrect"]);
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
            return json_encode(["status"=>"failed","message"=>"ID or Password Incorrect"]);
        }
    }//author login
    
    function adminLogin(Request $request){
       
        // $encrypt_key=Config::get('encdec.ENCRYPT_KEY');
        // $decrypt_key=Config::get('encdec.DECRYPT_KEY');
        // $responce=        json_decode($request->data);
        // $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$responce);
        // $data=json_decode($jsonData);

        $ip  = getenv('HTTP_CLIENT_IP')?:
        getenv('HTTP_X_FORWARDED_FOR')?:
        getenv('HTTP_X_FORWARDED')?:
        getenv('HTTP_FORWARDED_FOR')?:
        getenv('HTTP_FORWARDED')?:
        getenv('REMOTE_ADDR');

        $user = JrnlUser::where('email', $request->input('username'))->first();
       
        if($user) {
            if (Hash::check($request->password, $user->password)) {
               $token =  $user->createToken('jnrl_token')->plainTextToken;
             
               DB::table('jrnl_t_user_logins')->insert(
                    [
                        'user_id'           => $user->user_id,
                        'login_date_time'   => date('Y-m-d H:i:s'),
                        'login_ip_address'  => $ip,
                        'token_id'          => $token,
                        'login_attempt'     => 1,
                        'record_created_on' => date('Y-m-d H:i:s'),
                        'record_updated_on' => date('Y-m-d H:i:s'),
                        'record_created_by' => $user->user_id,
                        'record_updated_by' => $user->user_id,
                        'login_status'      => 'success'
                    ],
                );
                $user->api_token = $token;
                $user->save();
                return json_encode([
                    "status"        => "success",
                    "user_id"       => $user->user_id,
                    "user_name"     => $user->first_name,
                    "user_role"     => $user->user_type_id,
                    "user_designation" => $user->designation,
                    "user_token"    => $token
                ]);

            }else{
                DB::table('jrnl_t_user_logins')->insert(
                    [
                        'login_date_time'   => date('Y-m-d H:i:s'),
                        'login_ip_address'  => $ip,
                        'login_attempt'     => 1,
                        'record_created_on' => date('Y-m-d H:i:s'),
                        'record_updated_on' => date('Y-m-d H:i:s'),
                        'login_status'      => 'failed|wrong_password'
                    ],
                );
                echo "password error";
                //return json_encode(["status"=>"failed","message"=>"ID or Password Incorrect"]);
            }
        }else{

            DB::table('jrnl_t_user_logins')->insert(
                [
                    'login_date_time'   => date('Y-m-d H:i:s'),
                    'login_ip_address'  => $ip,
                    'login_attempt'     => 1,
                    'record_created_on' => date('Y-m-d H:i:s'),
                    'record_updated_on' => date('Y-m-d H:i:s'),
                    'login_status'      => 'failed|wrong_user'
                ],
            );
            return json_encode(["status"=>"failed","message"=>"ID or Password Incorrect"]);
        }
     
    }

    function authorLogout(){
        $author_id      =    Auth::id();
        DB::table('jrnl_t_author_users')
        ->where('author_id',$author_id)
        ->update(['api_token' => '']);
        return json_encode(["status"=>"ok","message"=>"logout Successfully"]);
    }
    function adminLogout(){
        $user_id      =    Auth::id();
        DB::table('jrnl_t_users')
        ->where('user_id',$user_id)
        ->update(['api_token' => '']);
        return json_encode(["status"=>"ok","message"=>"logout Successfully"]);
    }

    function getUserRole(){
        
        $data = DB::table('jrnl_t_user_roles')
            ->select('*')
            ->get();
        return $data;
    }

    function getUserList(){
       $data =  DB::table('jrnl_t_users')
        ->join('jrnl_t_user_roles', 'jrnl_t_users.user_type_id', '=', 'jrnl_t_user_roles.role_preference')
        ->select('jrnl_t_users.*', 'jrnl_t_user_roles.role_name')
        ->get();
        return $data;
    }

    function getEditorList(){
        $data =  DB::table('jrnl_t_users')
        ->join('jrnl_t_user_roles', 'jrnl_t_users.user_type_id', '=', 'jrnl_t_user_roles.role_preference')
        ->select('user_id','prefix','first_name','last_name','address','experience','email','designation','award','publication','jrnl_t_user_roles.role_name','mobile_no','profile_pic')
        ->where('user_type_id',2)//2 for editor
        ->where('jrnl_t_users.row_delete',0)
        ->get();
        return $data;
    }

    function getAuthorList(){
        $data =  DB::table('jrnl_t_authors')
        ->where('row_delete',0)
        ->get();
        return $data;
    }

    function viewAuthor($author_id){
        $author_id= base64_decode(base64_decode($author_id));
        
        $data =  DB::table('jrnl_t_author_users')
        ->where('author_id',$author_id)
        ->where('row_delete',0)
        ->get()->first();
        return $data;
    }

    function viewEditor($user_id){
        $user_id= base64_decode(base64_decode($user_id));
       
        $data =  DB::table('jrnl_t_users')
        ->where('user_id',$user_id)
        ->where('user_type_id',2)//2 for editor
        ->where('row_delete',0)
        ->get()->first();
        return $data;
    }

    function viewUser($user_id){
        $user_id= base64_decode(base64_decode($user_id));
       
        $data =  DB::table('jrnl_t_users')
        ->where('user_id',$user_id)
        ->where('user_type_id', '!=', 2)
        ->where('row_delete',0)
        ->get()->first();
        return $data;
    }

    function addEditorUser(Request $request){
        $formData  =  json_decode($request->input('userForm'),);
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$formData);
        $formData= json_decode($jsonData,true);
        $validator = Validator::make($formData,[
            'prefix'              => 'required',
            'firstName'           => 'required',
            'lastName'            => 'required',
            'gender'              => 'required',
            'contact'             => 'required|unique:jrnl_t_users,mobile_no',
            'email'               => 'required|unique:jrnl_t_users,email',
            'password'            => 'required',
            'designation'         => 'required',
        ]);
        if ($validator->fails())
        return  response()->json(['error' => $validator->errors()->all()], 401);

        $userObj = new JrnlUser;
        $userObj->user_type_id  =   2;//for editor user
        $userObj->prefix        =   $formData['prefix'];
        $userObj->first_name    =   $formData['firstName'];
        $userObj->last_name     =   $formData['lastName'];
        $userObj->gender        =   $formData['gender'];        
        $userObj->email         =   $formData['email'];
        $userObj->password      =   Hash::make($formData['password']);
        $userObj->mobile_no     =   $formData['contact'];
        $userObj->designation   =   $formData['designation'];
        $userObj->address       =   (isset($formData['address']))?$formData['address']:null;
        $userObj->experience    =   (isset($formData['experience']))?$formData['experience']:null;
        $userObj->award         =   (isset($formData['award']))?$formData['award']:null;
        $userObj->publication   =   (isset($formData['total_pub']))?$formData['total_pub']:null;

        if ($request->hasFile('userFile')) {
            $extension = $request->userFile->extension();
            $file = uniqid() . "_" . time(). "." . $extension;
            $path = $this->storage_root . "editor-users/";
            $request->file('userFile')->storeAs($path, $file, env('STORAGE_DISK'));
            $userObj->profile_pic   = env('AWS_BUCKET_URL') . $path . $file;
        }

        if($userObj->save())
            return  response()->json(['message' => 'The new Editor member has been added successfully.'], 200);
        else
        return  response()->json(['message' => 'Something went wrong. please try again later.'], 401);

    }

    function updateEditor(Request $request){
        
        $formData  =  json_decode($request->input('updateEditorForm'));
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$formData);
        $formData= json_decode($jsonData,true);
        $validator = Validator::make($formData,[
            'prefix'              => 'required',
            'firstName'           => 'required',
            'lastName'            => 'required',
            'gender'              => 'required',
            'contact'             => 'required',
            'password'            => 'required',
            'designation'         => 'required',
        ]);
        if ($validator->fails())
        return  response()->json(['error' => $validator->errors()->all()], 401);

        $userObj = JrnlUser::find($formData['user_id']);
        $userObj->prefix        =   $formData['prefix'];
        $userObj->first_name    =   $formData['firstName'];
        $userObj->last_name     =   $formData['lastName'];
        $userObj->gender        =   $formData['gender'];        
        $userObj->mobile_no     =   $formData['contact'];
        $userObj->designation   =   $formData['designation'];
        $userObj->address       =   (isset($formData['address']))?$formData['address']:null;
        $userObj->experience    =   (isset($formData['experience']))?$formData['experience']:null;
        $userObj->award         =   (isset($formData['award']))?$formData['award']:null;
        $userObj->publication   =   (isset($formData['total_pub']))?$formData['total_pub']:null;

        if ($request->hasFile('userFile')) {
            $extension = $request->userFile->extension();
            $file = uniqid() . "_" . time(). "." . $extension;
            $path = $this->storage_root . "editor-users/";
            $request->file('userFile')->storeAs($path, $file, env('STORAGE_DISK'));
            $userObj->profile_pic   = env('AWS_BUCKET_URL') . $path . $file;
        }

        if($userObj->save())
            return  response()->json(['message' => 'The Editor member has been updated successfully.'], 200);
        else
        return  response()->json(['message' => 'Something went wrong. please try again later.'], 401);
    
    }

    function deleteUser($user_id){
        $user_id= base64_decode(base64_decode($user_id));
       
        $userObj    =  JrnlUser::find($user_id);

        $userObj->record_updated_on     = date('Y-m-d H:i:s'); 
        $userObj->record_updated_by     = Auth::id();
        $userObj->row_delete            = 1;

        $message = "The User has been deleted successfully.";
        if($userObj->save())
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']);  

    }

    function addNewUser(Request $request){
        $formData  =  json_decode($request->input('userForm'));
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$formData);
        $formData= json_decode($jsonData,true);
        $validator = Validator::make($formData,[
            'prefix'              => 'required',
            'firstName'           => 'required',
            'lastName'            => 'required',
            'gender'              => 'required',
            'contact'             => 'required|unique:jrnl_t_users,mobile_no',
            'email'               => 'required|unique:jrnl_t_users,email',
            'password'            => 'required',
            'designation'         => 'required',
        ]);
        if ($validator->fails())
        return  response()->json(['error' => $validator->errors()->all()], 401);

        $userObj = new JrnlUser;
        $userObj->user_type_id  =   3;//for Dashboard manager
        $userObj->prefix        =   $formData['prefix'];
        $userObj->first_name    =   $formData['firstName'];
        $userObj->last_name     =   $formData['lastName'];
        $userObj->gender        =   $formData['gender'];        
        $userObj->email         =   $formData['email'];
        $userObj->password      =   Hash::make($formData['password']);
        $userObj->mobile_no     =   $formData['contact'];
        $userObj->designation   =   $formData['designation'];
        $userObj->address       =   (isset($formData['address']))?$formData['address']:null;
    
        if ($request->hasFile('userFile')) {
            $extension = $request->userFile->extension();
            $file = uniqid() . "_" . time(). "." . $extension;
            $path = $this->storage_root . "editor-users/";
            $request->file('userFile')->storeAs($path, $file, env('STORAGE_DISK'));
            $userObj->profile_pic   = env('AWS_BUCKET_URL') . $path . $file;
        }

        if($userObj->save())
            return  response()->json(['message' => 'The new User has been added successfully.'], 200);
        else
        return  response()->json(['message' => 'Something went wrong. please try again later.'], 401);  
    }

    function updateAuthorProfile(){
        /*
        $formData  =  json_decode($request->input('userForm'), true);
        $validator = Validator::make($formData,[
            'prefix'              => 'required',
            'firstName'           => 'required',
            'lastName'            => 'required',
            'gender'              => 'required',
            'contact'             => 'required|unique:jrnl_t_users,mobile_no',
            'email'               => 'required|unique:jrnl_t_users,email',
            'password'            => 'required',
            'designation'         => 'required',
        ]);
        if ($validator->fails())
        return  response()->json(['error' => $validator->errors()->all()], 401);

        $userObj = new JrnlUser;
        $userObj->user_type_id  =   3;//for Dashboard manager
        $userObj->prefix        =   $formData['prefix'];
        $userObj->first_name    =   $formData['firstName'];
        $userObj->last_name     =   $formData['lastName'];
        $userObj->gender        =   $formData['gender'];        
        $userObj->email         =   $formData['email'];
        $userObj->password      =   Hash::make($formData['password']);
        $userObj->mobile_no     =   $formData['contact'];
        $userObj->designation   =   $formData['designation'];
        $userObj->address       =   (isset($formData['address']))?$formData['address']:null;
    
        if ($request->hasFile('userFile')) {
            $extension = $request->userFile->extension();
            $file = uniqid() . "_" . time(). "." . $extension;
            $path = $this->storage_root . "editor-users/";
            $request->file('userFile')->storeAs($path, $file, env('STORAGE_DISK'));
            $userObj->profile_pic   = env('AWS_BUCKET_URL') . $path . $file;
        }

        if($userObj->save())
            return  response()->json(['message' => 'The new User has been added successfully.'], 200);
        else
        return  response()->json(['message' => 'Something went wrong. please try again later.'], 401);  
    */
    }
}

// $string_json_fromPHP = Helper::CryptoJSAesEncrypt($encrypt_key,json_encode(['status'=>'Unauthorised','error'=>'ID or Password Incorrect']));
// return response()->json($string_json_fromPHP);

