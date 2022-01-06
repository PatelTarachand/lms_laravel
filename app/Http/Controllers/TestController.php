<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use app\Http\Controllers\EnrollController;

class TestController extends Controller
{

    public function show(Request $request)
    {
    //     $enrollObj  = new EnrollController();
    //    echo  $enroll_no  = $enrollObj->getNewEntrollId('CD1001',12);

        // $filename = "action_img.png";
        // $path =  storage_path('app/public/' . $filename);

        // return Storage::get($path);

    }

    function file_upload(Request $request){

        $file = $request->file('profile');
        $originalname = $file->getClientOriginalName();
        $request->file('profile')->storeAs('boss/', $originalname,'local');
        // $path = $request->file('profile')->store(
        //     'avatars/', 'local'
        // );
        // echo $path;
        //echo Storage::putFileAs('avatars',new File('profile'), $request->file('profile'));

        // echo "<pre>";
        // print_r($_FILES);

//      $path =   $request->file('myfile')->store('myfile','s3');
  //    dd($path);

    }

    function view_file(){
        echo Hash::make('123');
      // $url =  Storage::disk('s3')->url('beautiful_natural_scenery_04_hd_pictures_166229.jpg');

        // $url =  "https://meity-trainingform-2021.atmanirbharproject.com.s3.ap-south-1.amazonaws.com/beautiful_natural_scenery_04_hd_pictures_166229.jpg";

        // echo '<img src="'.$url.'">';
    }

    /*
    function test(Request $request){
        echo "hello";
       $user= Student::where('email', $request->email)->first();

        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
           // $user = Auth::user();
           // dd($user);
            echo $user->createToken('MyApp')-> accessToken;
            // return response()->json(['success' => $success], $this-> successStatus);
        }
        else{
            //return response()->json(['error'=>'Unauthorised'], 401);
        }
    }

    public function register(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'firstName' => 'required',

        // ]);
         //$x = $request->file('candidatePhoto');
        print_r($_FILES);
        //print_r());
        $x = json_decode($_REQUEST['formData'],true);
            echo $x['firstName'];
        //echo json_decode($request->formData);
        //dd($_REQUEST['formdata']);
         //   echo $_REQUEST['firstName'];
    //    echo $request->input('firstName');
    //    echo $request->input('marksheetFile10');
    //    echo "hello bhai";
    }
    */
}
