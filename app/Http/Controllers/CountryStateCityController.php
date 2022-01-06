<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Validator,Redirect,Response;
use App\Models\{Country,State,City};
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Models\District;

class CountryStateCityController extends Controller
{

    public function index()
    {
       $data['countries'] = Country::get(["name","id"]);
       return view('country-state-city',$data);

    }
    public function getState($id,Request $request)
     {
         $id = base64_decode(base64_decode($id));
          $data = State::where("country_id",$id)
                      ->where('record_status','A')
                      ->orderBy('state_name','asc')
                      ->get(["state_id","state_name"]);
          return response()->json($data);
    }
    public function getCity($id,Request $request)
    {
        // $data= City::where("state_id",$id)->orderBy('city_name','asc')
        //             ->get(["city_name","city_id"]);
        $id = base64_decode(base64_decode($id));
        $data = District::select('district_id as city_id','district_name as city_name')->where("state_id",$id)->where('record_status','A')->get();
        return response()->json($data);
    }

    public function getCountry(){
        $data = Country::where("record_status",'A')
                 ->get(["country_id","country_name"]);
       return response()->json($data);
        // $myArr = array("John", "Mary", "Peter", "Sally");

        // $myJSON = json_encode($myArr);

        // return  $myJSON;
    }
    public function getToken(){

        return response()->json(csrf_token());
    }

}
