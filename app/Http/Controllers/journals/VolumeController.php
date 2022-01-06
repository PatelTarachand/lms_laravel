<?php

namespace App\Http\Controllers\journals;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\JrnlTVolume;
use App\Models\JrnlTPaper;
use App\Models\JrnlTPublishPaper;
use Illuminate\Support\Facades\Crypt;
use App\Helper\Helper;
use Config;
class VolumeController extends Controller
{
    private $aws_bucket_url  = '';//'https://ts5pftk5zcrik-test.s3.ap-south-1.amazonaws.com/';
    private $storage_disk   = 'local';
    private $storage_root   = 'journals/'; 

    function registerVolume(Request $request){
        $req=json_decode($request->input('volumeForm'));
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$req);
        $req= json_decode($jsonData,true);
        $validator = Validator::make($req, [
            'journal_id'      => 'required',
            'volumeName'      => 'required',
            'volumeDesc'      => 'required',
        ]);

        //validation check
        if ($validator->fails())
            return  response()->json(['error' => $validator->errors()->all()], 401);


         //============for candidate coverImage=======
         $coverImg= $request->file('coverImage');
         //dd($file);
         $path='';
         if ($request->hasFile('coverImage')) {
            
            $validator = Validator::make($request->all(), [
                'coverImage'      => 'file|mimes:jpg,png,jpeg,pdf|max:2048',
            ]);

            //validation check
            if ($validator->fails())
                return  response()->json(['error' => $validator->errors()->all()], 401);


            $extension = $request->coverImage->extension();
            $file = uniqid() . "_" . time() . "_." . $extension;
            $path = $this->storage_root . "volume_cover_image/";
            $request->file('coverImage')->storeAs($path, $file, $this->storage_disk);
            $path   = $this->aws_bucket_url . $path . $file;
        } //if
        
        // dd($req);
    $success = JrnlTVolume::insert([
        'journal_id'   =>  $req['journal_id'], //default author
        'volume_name'              => $req['volumeName'],
        'volume_details'          => $req['volumeDesc'],
        // 'publish_date'       => date('Y-m-d H:i:s'),
        'reg_date'       => date('Y-m-d H:i:s'),
        'cover_image'   => $path,
        'status'  => 'N',
        'record_status'  => 'A',
        'record_created_on' => date('Y-m-d H:i:s'),
        'record_created_by' =>  'admin',//$request->email,
     ]);

    
    if($success)
    return  response()->json(['status'=>'ok','message' =>'Your Data saved successfully'], 200);
    
    }
    function getVolumeList($journal_id){
        $journal_id= base64_decode(base64_decode($journal_id));
      
        $category= DB::table('jrnl_t_volumes')->select(['volume_id','journal_id','volume_name','volume_details','volume_path','reg_date','publish_date','cover_image', 'status', 'file_name'])
        ->where('journal_id',$journal_id)
        ->where('record_status','A')->get();
       return response()->json($category);
    }
    function getVolumeShortList($journal_id){
        $journal_id= base64_decode(base64_decode($journal_id));
      
        $category= DB::table('jrnl_t_volumes')->select(['volume_id','volume_name','journal_id','reg_date'])
        ->where('journal_id',$journal_id)
        ->where('status','N')
        ->where('record_status','A')->get();
       return response()->json($category);
    }

    function publishVolume(Request $request){
        $req= json_decode($request->input('volume_form'));
        //dd($req->manuscript_list);
        $manuscript_list = $req->manuscript_list;
        $journal_ids=array();
        $manuscript_ids=array();
        $journal_category_ids=[];
        foreach ($manuscript_list as $manuscript) {
            // dd($manuscript);
            if ($manuscript->checked) {
                array_push($journal_ids,$manuscript->journal_id);
                array_push($manuscript_ids,$manuscript->paper_id);
            }    
        }
        // dd($manuscript_ids);
        $paper = JrnlTPaper::select(array(
        'paper_title',
        'paper_type_id', 
        'journal_id', 
        'journal_category_id',
        'abstract',
        'paper_path', 
        'submission_date',
       'is_plag_checked',
       'plag_persent',
        ))
        ->whereIn('journal_id',$journal_ids)
        ->whereIn('paper_id',$manuscript_ids)
        ->where('paper_status','RP')
        ->where('record_status','A');
        //  $json = json_encode($paper);
        //  dd(json_decode($json));
        $success= JrnlTPublishPaper::insertUsing([
        'paper_title',
        'paper_type_id', 
        'journal_id', 
        'journal_category_id',
        'abstract',
        'paper_path', 
        'submission_date',
        'is_plag_checked',
        'plag_persent',
        ], $paper);
        dd($success);
        return  response()->json(['status' =>'Your Data saved successfully'], 200);
    }
}
