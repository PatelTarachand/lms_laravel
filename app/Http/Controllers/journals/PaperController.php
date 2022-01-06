<?php

namespace App\Http\Controllers\journals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JrnlMPaperType;
use App\Models\JrnlTPaper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use App\Helper\Helper;
use Config;
class PaperController extends Controller
{
    //==========for paper type==========================
    function getPaperType(){
        $data = JrnlMPaperType::where('row_delete',0)->get();
        return $data;
    }

    function addPaperType(Request $request){
       
        $fobj = json_decode($request->input('paperTypeForm'));
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$fobj);
        $fobj= json_decode($jsonData,true);
        $validater = Validator::make($fobj, [
            'paperType'            =>  'required',
            'paperTypeDesc'        =>  'required',
        ]);
        
        if ($validater->fails())
        return  response()->json(['error' => $validater->errors()->all()], 401);
       
        $ptObj = new JrnlMPaperType;
        $ptObj->paper_type          = $fobj['paperType'];
        $ptObj->description         = $fobj['paperTypeDesc'];
        $ptObj->record_created_on   = date('Y-m-d ');
        $ptObj->record_created_by   = Auth::id();
        $ptObj->save();

        return json_encode(['status'=>'ok','message'=>'New Paper Type has been successfully added.']);
       
    }

    function viewPapertype($paper_type_id){
        $paper_type_id= base64_decode(base64_decode($paper_type_id));
       
        $data = JrnlMPaperType::where('paper_type_id',$paper_type_id)->where('row_delete',0)->get()->first();
        return $data;
    }

    function deletePaperType($paper_type_id){
        $paper_type_id= base64_decode(base64_decode($paper_type_id));
       
        $ptObj    =  JrnlMPaperType::find($paper_type_id);

        $ptObj->record_updated_on     = date('Y-m-d H:i:s'); 
        $ptObj->record_updated_by     = Auth::id();
        $ptObj->row_delete            = 1;

        $message = "The Paper type has been deleted successfully.";
        if($ptObj->save())
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']);  

    }
    //===============end paper type==================


    function getPapersByAuthor($author_id){
        $author_id= base64_decode(base64_decode($author_id));
       
        $data = DB::table('jrnl_t_papers as tp')
        ->join('jrnl_t_journals', 'tp.journal_id', '=', 'jrnl_t_journals.journal_id')
        ->join('jrnl_m_categories', 'tp.journal_category_id', '=', 'jrnl_m_categories.category_id')
        ->join('jrnl_m_paper_types', 'tp.paper_type_id', '=', 'jrnl_m_paper_types.paper_type_id')
        ->select('*')
        ->where('tp.author_user_id',$author_id)
        //->where('paper_status','R')
        ->get();
        return $data;
    }

    function getPapersListByStatus($status){
        $status= base64_decode(base64_decode($status));
        $data = DB::table('jrnl_t_papers as tp')
            ->join('jrnl_t_journals', 'tp.journal_id', '=', 'jrnl_t_journals.journal_id')
            ->join('jrnl_m_categories', 'tp.journal_category_id', '=', 'jrnl_m_categories.category_id')
            ->join('jrnl_m_paper_types', 'tp.paper_type_id', '=', 'jrnl_m_paper_types.paper_type_id')
            ->select('*')
            ->where('tp.row_delete',0)
            ->where('paper_status',$status)
            ->get();
        return $data;
    }

    function getPapersList(){
        $data = DB::table('jrnl_t_papers as tp')
            ->join('jrnl_t_journals', 'tp.journal_id', '=', 'jrnl_t_journals.journal_id')
            ->join('jrnl_m_categories', 'tp.journal_category_id', '=', 'jrnl_m_categories.category_id')
            ->join('jrnl_m_paper_types', 'tp.paper_type_id', '=', 'jrnl_m_paper_types.paper_type_id')
            ->select('*')
            //->where('paper_status',$status)
            ->get();
        return $data;
    }


    function viewPaper($paper_id){
        $paper_id= base64_decode(base64_decode($paper_id));
       
        $data = DB::table('jrnl_t_papers as tp')
        ->join('jrnl_t_journals', 'tp.journal_id', '=', 'jrnl_t_journals.journal_id')
        ->join('jrnl_m_categories', 'tp.journal_category_id', '=', 'jrnl_m_categories.category_id')
        ->join('jrnl_m_paper_types', 'tp.paper_type_id', '=', 'jrnl_m_paper_types.paper_type_id')
        ->select('*')
        ->where('paper_id',$paper_id)
        ->get()->first();
        return $data;
    }

}
