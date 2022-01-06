<?php

namespace App\Http\Controllers\journals;

use App\Http\Controllers\Controller;
use App\Models\JrnlTAuthor;
use App\Models\JrnlTCoAuthor;
use App\Models\JrnlTPaper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class MenuScript extends Controller
{
    private $storage_root   = 'journals/';

    function addMenuScript(Request $request){
        // print_r($_REQUEST); 
       $validator = Validator::make($request->all(), [
            'paper_type'            =>  'required',
            'jrnl_category'         =>  'required',
            'journal'               =>  'required',
            'title'                 =>  'required',
            'author_name'           =>  'required',
            'author_designation'    =>  'required',
            'author_email'          =>  'required',
            'author_mobile_no'      =>  'required',
            'country_id'            =>  'required',
            'state'                 =>  'required',
            'district'              =>  'required',
            'author_address'        =>  'required',
            'author_pin_code'       =>  'required',
            'menuscript_file'      =>   'file|required|mimes:docx,doc,pdf|max:20048',
        ]);

        if ($validator->fails())
        return  response()->json(['error' => $validator->errors()->all()], 401);
        
        $paperObj = new JrnlTPaper;

        $paperObj->paper_title              = $request->input('title');
        $paperObj->paper_type_id            = $request->input('paper_type');
        $paperObj->journal_id               = $request->input('journal');
        $paperObj->journal_category_id      = $request->input('jrnl_category');
        $paperObj->paper_status             = 'R';
        $paperObj->status                   = 'Registered';
        $paperObj->submission_date          = date('Y-m-d H:i:s');
        $paperObj->record_created_by        = $request->input('author_user_id');
        $paperObj->record_created_on        = date('Y-m-d H:i:s');
        $paperObj->author_user_id           = $request->input('author_user_id');


        if ($request->hasFile('menuscript_file')) {
            $extension = $request->menuscript_file->extension();
            $file = uniqid() . "_" . time(). "." . $extension;
            $path = $this->storage_root . "menuscript-paper/";
            $request->file('menuscript_file')->storeAs($path, $file, env('STORAGE_DISK'));
            $path   = env('AWS_BUCKET_URL') . $path . $file;
            $paperObj->paper_path  =  $path;
        }

        $paperObj->save();

        $paper_id  =   $paperObj->paper_id; 

        //insert to t_authors table
        $tauthorObj     =   new  JrnlTAuthor;
        $tauthorObj->author_user_id         = $request->input('author_user_id');
        $tauthorObj->paper_id               = $paper_id;  
        $tauthorObj->first_name             = $request->input('author_name');
        $tauthorObj->designation            = $request->input('author_designation');
        $tauthorObj->email                  = $request->input('author_email');
        $tauthorObj->address                = $request->input('author_address');
        $tauthorObj->country                = $request->input('country_id');
        $tauthorObj->district               = $request->input('district');
        $tauthorObj->state                  = $request->input('state');
        $tauthorObj->record_created_on      = date('Y-m-d');
        $tauthorObj->record_created_by      = $request->input('author_user_id');
        $tauthorObj->save();

        $coAuthorArray  =   json_decode($request->input('coAuthor'));
        //insert to co_authors table
        if(!empty($coAuthorArray)){
            foreach($coAuthorArray as $row){
                $coauthorObj    =    new JrnlTCoAuthor;
                $coauthorObj->author_user_id        = $request->input('author_user_id');
                $coauthorObj->paper_id              = $paper_id;
                $coauthorObj->full_name             = $row->name;
                $coauthorObj->designation           = $row->designation;
                $coauthorObj->email                 = $row->email;
                $coauthorObj->record_status         = 'A';
                $coauthorObj->record_created_on     = date('Y-m-d H:i:s'); 
                $coauthorObj->record_updated_by     = $request->input('author_user_id');
                $coauthorObj->save(); 
            }
        }
       
       return json_encode(['status'=>'ok','message'=>'Menuscript has been submitted.']);

    }
}
