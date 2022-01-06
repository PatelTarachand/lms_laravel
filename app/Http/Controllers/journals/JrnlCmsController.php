<?php

namespace App\Http\Controllers\journals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\JrnlSetting;
use Illuminate\Support\Facades\Crypt;
use App\Helper\Helper;
use Config;
class JrnlCmsController extends Controller
{
    private $storage_root   = 'journals/';
    function addAuthorGuidline(Request $request){
        $fobj = json_decode($request->input('authorGuidelineForm'));
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$fobj);
        $fobj= json_decode($jsonData,true);
        // dd($fobj);
        $validater = Validator::make($fobj, [
            'category_id'           =>  'required',
            'journal_id'            =>  'required',
            'guideline'             =>  'required',
            ],
            [
                'category_id.required' => 'The journal category field is required',
                'journal_id.required'  => 'The journal field is required',
            ]
        );
        
        if ($validater->fails())
        return  response()->json(['error' => $validater->errors()->all()], 401);

        $affected = DB::table('jrnl_t_author_guideline')->insert([
            'jrnl_category_id'      => $fobj['category_id'],
            'journal_id'            => $fobj['journal_id'],
            'author_guideline'      => $fobj['guideline'],
            'record_created_by'     => Auth::id(),
            'created_at'            => date('Y-m-d H:i:s')
        ]);

        $message = "The New Author Guideline has been added successfully.";
        if($affected)
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']);
    }

    function addCallForMenuscript(Request $request){
         
        $fobj = json_decode($request->input('callForManuscriptForm'));
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$fobj);
        $fobj= json_decode($jsonData,true);
        $validater = Validator::make($fobj, [
                'category_id'           => 'required',
                'journal_id'            => 'required',
                'introduction'          => 'required',
                'full_details'          => 'required',
                'scope_topics'          => 'required',
            ],
            [
                'category_id.required' => 'The journal category field is required',
                'journal_id.required'  => 'The journal field is required',   
            ]

        );
        
        if ($validater->fails())
        return  response()->json(['error' => $validater->errors()->all()], 401);

        if ($request->hasFile('coverImage')) {
            $extension = $request->coverImage->extension();
            $file = uniqid() . "_" . time(). "." . $extension;
            $path = $this->storage_root . "callformenuscript/";
            $request->file('coverImage')->storeAs($path, $file, env('STORAGE_DISK'));
            $path   = env('AWS_BUCKET_URL') . $path . $file;
        }else
            $path = null;


        $affected = DB::table('jrnl_t_call_for_menuscript')->insert([
            'jrnl_category_id'      => $fobj['category_id'],
            'journal_id'            => $fobj['journal_id'],
            'introduction'          => $fobj['introduction'],
            'details'               => $fobj['full_details'],
            'scope_topic'           => $fobj['scope_topics'],
            'cover_image'           => $path,
            'record_created_by'     => Auth::id(),
            'created_at'            => date('Y-m-d H:i:s')
        ]);

        $message = "The New Call for menuscript has been added successfully.";
        if($affected)
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']); 
    }

    function addEditorialWorkflow(Request $request){
        $fobj = json_decode($request->input('editorialWorkflowForm'));
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$fobj);
        $fobj= json_decode($jsonData,true);
        $validater = Validator::make($fobj, [
            'category_id'           =>  'required',
            'journal_id'            =>  'required',
            'introduction'          =>  'required',
            'workflow'              =>  'required',
            'editorial_policy'      =>  'required',
            'life_cycle'            =>  'required',
            'review_process'        =>  'required',
            'referees_guideline'    =>  'required',
        ]);
        
        if ($validater->fails())
        return  response()->json(['error' => $validater->errors()->all()], 401);

        if ($request->hasFile('coverImage')) {
            $extension = $request->coverImage->extension();
            $file = uniqid() . "_" . time(). "." . $extension;
            $path = $this->storage_root . "callformenuscript/";
            $request->file('coverImage')->storeAs($path, $file, env('STORAGE_DISK'));
            $path   = env('AWS_BUCKET_URL') . $path . $file;
        }else
            $path = null;


        $affected = DB::table('jrnl_t_editorial_workflow')->insert([
            'jrnl_category_id'      => $fobj['category_id'],
            'journal_id'            => $fobj['journal_id'],
            'introduction'          => $fobj['introduction'],
            'workflow'              => $fobj['workflow'],
            'editorial_policy'      => $fobj['editorial_policy'],
            'life_cycle'            => $fobj['life_cycle'],
            'review_process'        => $fobj['review_process'],
            'referees_guideline'    => $fobj['referees_guideline'],
            'record_created_by'     => Auth::id(),
            'created_at'            => date('Y-m-d H:i:s')
        ]);
       
        $message = "The Editorial Workflow has been added successfully.";
        if($affected)
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']); 
    } 

    function getAuthorGuidline($journal_id){
        $journal_id= base64_decode(base64_decode($journal_id));
        $data = DB::table('jrnl_t_author_guideline as ag')
            ->join('jrnl_t_journals as tj', 'ag.journal_id', '=', 'tj.journal_id')
            ->join('jrnl_m_categories as c', 'tj.journal_category_id', '=', 'c.category_id')
            ->select('ag.*', 'c.category_name','c.category_short_name', 'tj.title_name', 'tj.title', 'tj.issn_number', 'tj.cover_image', 'tj.side_image')
            ->where('ag.row_delete',0)
            ->where('ag.journal_id',$journal_id)
            ->get();
        return $data;
    }

    function getCallForMenuscript($journal_id){
        $journal_id= base64_decode(base64_decode($journal_id));
        $data = DB::table('jrnl_t_call_for_menuscript as cfm')
            ->join('jrnl_t_journals as tj', 'cfm.journal_id', '=', 'tj.journal_id')
            ->join('jrnl_m_categories as c', 'tj.journal_category_id', '=', 'c.category_id')
            ->select('cfm.*', 'c.category_name','c.category_short_name', 'tj.title_name', 'tj.title', 'tj.issn_number', 'tj.side_image')
            ->where('cfm.row_delete',0)
            ->where('cfm.journal_id',$journal_id)
            ->get();
        return $data;
    }

    function getEditorialWorkflow($journal_id){
        $journal_id= base64_decode(base64_decode($journal_id));
        $data = DB::table('jrnl_t_editorial_workflow as ew')
        ->join('jrnl_t_journals as tj', 'ew.journal_id', '=', 'tj.journal_id')
        ->join('jrnl_m_categories as c', 'tj.journal_category_id', '=', 'c.category_id')
        ->select('ew.*', 'c.category_name','c.category_short_name', 'tj.title_name', 'tj.title', 'tj.issn_number', 'tj.cover_image', 'tj.side_image')
        ->where('ew.row_delete',0)
        ->where('ew.journal_id',$journal_id)
        ->get();
        return $data;
    }

    function deleteCallForMenuscript($id){
        $id= base64_decode(base64_decode($id));
        
        $affected  = DB::table('jrnl_t_call_for_menuscript')
              ->where('id', $id)
              ->update([
                  'record_updated_by'     => Auth::id(),
                  'row_delete'            => 1,         
                ]);

        $message = "The call for menuscript has been deleted successfully.";
        if($affected)
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']);  

    }

    function deleteAuthorGuideline($id){
        $id= base64_decode(base64_decode($id));
        
        $affected  = DB::table('jrnl_t_author_guideline')
        ->where('id', $id)
        ->update([
            'record_updated_by'     => Auth::id(),
            'row_delete'            => 1,         
          ]);

        $message = "The Author Guideline has been deleted successfully.";
        if($affected)
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']);  
    }

    function deleteEditorialWorkflow($id){
        $id= base64_decode(base64_decode($id));
        
        $affected  = DB::table('jrnl_t_editorial_workflow')
        ->where('id', $id)
        ->update([
            'record_updated_by'     => Auth::id(),
            'row_delete'            => 1,         
          ]);

        $message = "The Editorial Workflow has been deleted successfully.";
        if($affected)
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']);  
    }

    function updateAuthorGuideline(Request $request){
        $fobj = json_decode($request->input('updateGuidelineForm'));
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$fobj);
        $fobj= json_decode($jsonData,true);
        $validater = Validator::make($fobj, [
            'category_id'           =>  'required',
            'journal_id'            =>  'required',
            'guideline'             =>  'required',
            ],
            [
                'category_id.required' => 'The journal category field is required',
                'journal_id.required'  => 'The journal field is required',
            ]
        );
        
        if ($validater->fails())
        return  response()->json(['error' => $validater->errors()->all()], 401);

        $affected = DB::table('jrnl_t_author_guideline')
        ->where('id', $fobj['guideline_id'])
        ->update([
            'jrnl_category_id'      => $fobj['category_id'],
            'journal_id'            => $fobj['journal_id'],
            'author_guideline'      => $fobj['guideline'],
            'record_updated_by'     => Auth::id(),
            'updated_at'            => date('Y-m-d H:i:s')
        ]);

        $message = "The Author Guideline has been updated successfully.";
        if($affected)
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']);
          
    }

    function updateCallForManuscript(Request $request){
          
        $fobj = json_decode($request->input('updateCFMForm'));
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$fobj);
        $fobj= json_decode($jsonData,true);
        // dd($fobj);
        $validater = Validator::make($fobj, [
                'category_id'           => 'required',
                'journal_id'            => 'required',
                'introduction'          => 'required',
                'full_details'          => 'required',
                'scope_topics'          => 'required',
            ],
            [
                'category_id.required' => 'The journal category field is required',
                'journal_id.required'  => 'The journal field is required',   
            ]

        );
        
        if ($validater->fails())
        return  response()->json(['error' => $validater->errors()->all()], 401);

        if ($request->hasFile('coverImage')) {
            $extension = $request->coverImage->extension();
            $file = uniqid() . "_" . time(). "." . $extension;
            $path = $this->storage_root . "callformenuscript/";
            $request->file('coverImage')->storeAs($path, $file, env('STORAGE_DISK'));
            $path   = env('AWS_BUCKET_URL') . $path . $file;

            $affected = DB::table('jrnl_t_call_for_menuscript')
            ->where('id',$fobj['id'])
            ->update([
                'cover_image'      => $path,
            ]);
        }

        $affected = DB::table('jrnl_t_call_for_menuscript')
        ->where('id',$fobj['id'])
        ->update([
            'jrnl_category_id'      => $fobj['jrnl_category_id'],
            'journal_id'            => $fobj['journal_id'],
            'introduction'          => $fobj['introduction'],
            'details'               => $fobj['details'],
            'scope_topic'           => $fobj['scope_topics'],
            'record_created_by'     => Auth::id(),
            'created_at'            => date('Y-m-d H:i:s')
        ]);

        $message = "The Call for menuscript has been updated successfully.";
        if($affected)
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']); 
   
    }

    function updateEditorialWorkflow(Request $request){
        $fobj = json_decode($request->input('updateEditorialWorkflowForm'));
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$fobj);
        $fobj= json_decode($jsonData,true);
        $validater = Validator::make($fobj, [
            'category_id'           =>  'required',
            'journal_id'            =>  'required',
            'introduction'          =>  'required',
            'workflow'              =>  'required',
            'editorial_policy'      =>  'required',
            'life_cycle'            =>  'required',
            'review_process'        =>  'required',
            'referees_guideline'    =>  'required',
        ]);
        
        if ($validater->fails())
        return  response()->json(['error' => $validater->errors()->all()], 401);


        $affected = DB::table('jrnl_t_editorial_workflow')
            ->where('id',$fobj['id'])
            ->update([
                'jrnl_category_id'      => $fobj['category_id'],
                'journal_id'            => $fobj['journal_id'],
                'introduction'          => $fobj['introduction'],
                'workflow'              => $fobj['workflow'],
                'editorial_policy'      => $fobj['editorial_policy'],
                'life_cycle'            => $fobj['life_cycle'],
                'review_process'        => $fobj['review_process'],
                'referees_guideline'    => $fobj['referees_guideline'],
                'record_created_by'     => Auth::id(),
                'created_at'            => date('Y-m-d H:i:s')
            ]);
       
        $message = "The Editorial Workflow has been updated successfully.";
        if($affected)
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']); 
    
    }

    function storeAboutus(Request $request){
        $fobj = json_decode($request->input('aboutForm'));
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        // dd($fobj);
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$fobj);
        $aboutObj= json_decode($jsonData,true);
        $validater = Validator::make($aboutObj, [
            'title'                 =>  'required',
            'description'           =>  'required',
        ]);
        
        if ($validater->fails())
        return  response()->json(['error' => $validater->errors()->all()], 401);

        $affected = DB::table('jrnl_m_settings')
        ->updateOrInsert(
            ['key_name' => 'aboutus'],
            ['value' => $aboutObj ,
            'record_created_by'     => Auth::id(),
            'created_at'            => date('Y-m-d H:i:s'),
            'record_updated_by'    => Auth::id(),
            'updated_at'           => date('Y-m-d H:i:s'),
            ]
        );
            
        $message = "The About us content has been updated successfully.";
        if($affected)
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']); 
 
    }

    function storeContactus(Request $request){
        $fobj = json_decode($request->input('contactForm'));
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$fobj);
        $fobj= json_decode($jsonData,true);
        $validater = Validator::make($fobj, [
            'address1'             =>  'required',
            'mobile_no1'           =>  'required',
            'email_id1'            =>  'required',
        ]);
        
        if ($validater->fails())
        return  response()->json(['error' => $validater->errors()->all()], 401);

        $affected = DB::table('jrnl_m_settings')
        ->updateOrInsert(
            ['key_name' => 'contactus'],
            [
                'value'                => $fobj,
                'record_created_by'    => Auth::id(),
                'created_at'           => date('Y-m-d H:i:s'),
                'record_updated_by'    => Auth::id(),
                'updated_at'           => date('Y-m-d H:i:s'),
            ]
        );
            
        $message = "The Contact us content has been updated successfully.";
        if($affected)
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']); 
    }

    function getJrnlSettings($key){
        $key= base64_decode(base64_decode($key));
        
        $data = JrnlSetting::where('row_delete',0)->where('status','A')->where('key_name',$key)->get()->first();
        return $data;
    }
}
