<?php

namespace App\Http\Controllers\journals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JrnlMCategory;
use App\Models\JrnlTJournal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\JrnlTPaper;
use Illuminate\Support\Facades\Crypt;
use App\Helper\Helper;
use Config;
class JournalsController extends Controller
{
    private $storage_root   = 'journals/';

    function getCategory(){
        $data = JrnlMCategory::where('row_delete',0)->get();
        return $data;
    }

    function showCategory($category_id){
        $category_id= base64_decode(base64_decode($category_id));
        $data = JrnlMCategory::where('row_delete',0)->where('category_id',$category_id)->get()->first();
        return $data;
    }

    function getJournalsByCategory($category_id){
        $category_id= base64_decode(base64_decode($category_id));
        $data = JrnlTJournal::where('row_delete',0)->where('journal_category_id',$category_id)->get();
        return $data;
    }
    function getJournalById($journal_id){
        $journal_id= base64_decode(base64_decode($journal_id));
        $journal= JrnlTJournal::select('journal_id','title_name','title','desc', 'issn_number','jrnl_t_journals.cover_image','jrnl_t_journals.side_image','category_name','category_short_name', 'category_id')
        ->join('jrnl_m_categories as c', 'jrnl_t_journals.journal_category_id', '=', 'c.category_id')
        ->where('journal_id',$journal_id)
        ->where('jrnl_t_journals.record_status','A')->get()->first();
       return response()->json($journal);
    }
    // this method use to add new category and update category
    function storeJrnlCategory(Request $request){
       
        if($request->input('updateCategoryForm'))
            $fobj = json_decode($request->input('updateCategoryForm'));
        else
            $fobj = json_decode($request->input('categoryForm'));

        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$fobj);
        $fobj= json_decode($jsonData,true);
        $validater = Validator::make($fobj, [
            'categoryName'            =>  'required',
            'categoryAbbr'            =>  'required',
            ],
            [
                'categoryAbbr.required'    =>  'The category Abbriviation field is required',
            ]
        );  
         
        if ($validater->fails())
        return  response()->json(['error' => $validater->errors()->all()], 401);
        
       
        if(isset($fobj['categoryId'])){
            $mcatObj    =  JrnlMCategory::find($fobj['categoryId']);
            $mcatObj->record_updated_on     = date('Y-m-d H:i:s'); 
            $mcatObj->record_updated_by     = Auth::id();
            $message  = 'The category has been updated successfully.';
        }else{
            $mcatObj    =  new JrnlMCategory;
            $mcatObj->record_created_on     = date('Y-m-d H:i:s'); 
            $mcatObj->record_created_by     = Auth::id();
            $message  = 'The new category has been added successfully.';
        }

        $mcatObj->category_name         = $fobj['categoryName'];
        $mcatObj->category_short_name   = $fobj['categoryAbbr'];
        $mcatObj->category_description  = (isset($fobj['categoryDesc']))?$fobj['categoryDesc']:null;

        if ($request->hasFile('coverImage')) {
          
            $validater = Validator::make ($request->all(),[
                'coverImage'      => 'file|mimes:jpg,png,jpeg|max:2048',
            ]);  
             
            if ($validater->fails())
            return  response()->json(['error' => $validater->errors()->all()], 401);

            $extension = $request->coverImage->extension();
            $file = uniqid() . "_" . time(). "." . $extension;
            $path = $this->storage_root . "category/";
            $request->file('coverImage')->storeAs($path, $file, env('STORAGE_DISK'));
            $path   = env('AWS_BUCKET_URL') . $path . $file;
            $mcatObj->cover_image  =  $path;
        }

        if($mcatObj->save())
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']);  
    }

    function deleteJrnlCategory($category_id){
        $category_id= base64_decode(base64_decode($category_id));
       
        $mcatObj    =  JrnlMCategory::find($category_id);

        $mcatObj->record_updated_on     = date('Y-m-d H:i:s'); 
        $mcatObj->record_updated_by     = Auth::id();
        $mcatObj->row_delete            = 1;

        $message = "The category has been deleted successfully.";
        if($mcatObj->save())
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']);  

    }

    //this method use to add new journals and update journals
    function storeJournal(Request $request){
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        if($request->input('updateJournal')){
            $req=json_decode($request->input('updateJournal'));
            $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$req);
            $req= json_decode($jsonData,true);
                $validator =  Validator::make($req,
                [
                    'journalCategory'       => 'required',
                    'issnNumber'            => 'required',
                    'journalName'           => 'required',
                    'journalAbbr'           => 'required',
                    'journalDesc'           => 'required', 
                ],
            );
        }
        else{
            $req=json_decode($request->input('journalForm'));
            $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$req);
            $req= json_decode($jsonData,true);
            $validator =  Validator::make($req,
                [
                    'journalCategory'       => 'required',
                    'issnNumber'            => 'required|unique:jrnl_t_journals,issn_number',
                    'journalName'           => 'required|unique:jrnl_t_journals,title',
                    'journalAbbr'           => 'required|unique:jrnl_t_journals,title_name',
                    'journalDesc'           => 'required', 
                ],
                [
                    'issnNumber.unique'    => 'ISSN number is already registered.', 
                    'title.unique'          =>  'Name of the Journal is already registered.',
                    'title_name.unique'     =>  'Abbreviation of the Journal is already registered.',
                ]
            );
        }
       
        if ($validator->fails())
            return  response()->json(['error' => $validator->errors()->all()], 401);


        if(isset($req['journalId'])){
            $jrnlObj    =  JrnlTJournal::find($req['journalId']);
            $jrnlObj->record_updated_on     = date('Y-m-d H:i:s'); 
            $jrnlObj->record_updated_by     = Auth::id();
            $message  = 'The journal has been updated successfully.';
        }else{
            $jrnlObj    =  new JrnlTJournal;
            $jrnlObj->record_created_on     = date('Y-m-d H:i:s'); 
            $jrnlObj->record_created_by     = Auth::id();
            $message  = 'The new journal has been added successfully.';
        }

        $jrnlObj->journal_category_id   = $req['journalCategory'];
        $jrnlObj->title                 = $req['journalName'];
        $jrnlObj->title_name            = $req['journalAbbr'];
        $jrnlObj->issn_number           = $req['issnNumber'];
        $jrnlObj->desc                  = $req['journalDesc'];
        $jrnlObj->record_status         = 'A';
            
        
        if ($request->hasFile('coverImage')) {
        
            $validater = Validator::make ($request->all(),[
                'coverImage'      => 'file|mimes:jpg,png,jpeg|max:2048',
            ]);  
            
            if ($validater->fails())
            return  response()->json(['error' => $validater->errors()->all()], 401);

            $extension = $request->coverImage->extension();
            $file = uniqid() . "_" . time(). "." . $extension;
            $path = $this->storage_root . "journals-cover/";
            $request->file('coverImage')->storeAs($path, $file, env('STORAGE_DISK'));
            $path   = env('AWS_BUCKET_URL') . $path . $file;
            $jrnlObj->cover_image  =  $path;
        }
        
        if($jrnlObj->save())
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']);  
       
    }

    function deleteJournal($journal_id){
        $journal_id= base64_decode(base64_decode($journal_id));
       
        $jrnlObj    =  JrnlTJournal::find($journal_id);

        $jrnlObj->record_updated_on     = date('Y-m-d H:i:s'); 
        $jrnlObj->record_updated_by     = Auth::id();
        $jrnlObj->row_delete            = 1;

        $message = "The journal has been deleted successfully.";
        if($jrnlObj->save())
            return json_encode(['status'=>'ok','message'=> $message]);
        else
            return json_encode(['status'=>'error','message'=> 'Something went wrong. please try again later']);  
 
    }

    function getJournalCatagory(){
        $category= DB::table('jrnl_m_categories')->select(['category_id','category_name','category_short_name'])->where('record_status','A')->where('row_delete','0')->get();
        return response()->json($category);
    }
    function getJournalSubcatagory(Request $request,$id){
        $id= base64_decode(base64_decode($id));
       
        $category_id=$id;
        $category='';
        if(!empty($category_id)){
        $category= DB::table('jrnl_m_sub_categories')->select(['sub_category_id','sub_category_name','sub_category_short_name'])
        ->where('journal_category_id',$category_id)->where('record_status','A')->get();
        }
        
        return response()->json($category);
    }
    function getJournalList(){
        $category= DB::table('jrnl_t_journals')->where('row_delete',0)->select(['journal_id','title_name','title','issn_number','cover_image'])->where('record_status','A')->orderBy('journal_id','desc')->where('row_delete',0)->get();
        return response()->json($category);
    }
    function getJournalListByCategory($category_id){
        $category_id= base64_decode(base64_decode($category_id));
        $category= DB::table('jrnl_t_journals')->select(['journal_id','title_name','title','issn_number','cover_image'])->where('journal_category_id',$category_id)->where('record_status','A')->get();
        return response()->json($category);
    }
    function getManuscriptList($id){
        $id= base64_decode(base64_decode($id));
        $category= DB::table('jrnl_t_papers as jtp')->select(['jtp.paper_id','jtp.journal_id','jtp.journal_category_id','jmpt.paper_type','jtp.paper_title','jta.first_name','jta.middle_name','jta.last_name','jtp.paper_status','jtp.status', 'jtp.submission_date'])->where('jtp.record_status','A')
        ->join('jrnl_m_paper_types as jmpt','jmpt.paper_type_id','=','jtp.paper_type_id')
        ->join('jrnl_t_journals as jtj', 'jtj.journal_id','=','jtp.journal_id')
        ->join('jrnl_m_categories as jmc', 'jmc.category_id', '=', 'jtj.journal_category_id')
        ->join('jrnl_t_authors as jta','jta.paper_id','=','jtp.paper_id')
        ->where('jtp.record_status','A')

        ->where('jtp.journal_id',$id)->get();
        return response()->json($category);
    }
    function getReadyToPublishManuscriptList($id){
        $id= base64_decode(base64_decode($id));
        $category= DB::table('jrnl_t_papers as jtp')->select(['jtp.paper_id','jtp.journal_id','jtp.journal_category_id','jmpt.paper_type','jtp.paper_title','jta.first_name','jta.middle_name','jta.last_name','jtp.paper_status','jtp.status','jtp.submission_date'])->where('jtp.record_status','A')
        ->join('jrnl_m_paper_types as jmpt','jmpt.paper_type_id','=','jtp.paper_type_id')
        ->join('jrnl_t_journals as jtj', 'jtj.journal_id','=','jtp.journal_id')
        ->join('jrnl_m_categories as jmc', 'jmc.category_id', '=', 'jtj.journal_category_id')
        ->join('jrnl_t_authors as jta','jta.paper_id','=','jtp.paper_id')
        ->where('jta.record_status','A')
        ->where('jmpt.record_status','A')
        // ->where('jtca.record_status','A')
        ->where('jtp.paper_status','RP')
        ->where('jtp.journal_id',$id)->get();
        return response()->json($category);
    }
    function getManuscript($id){
        $id= base64_decode(base64_decode($id));
        $manuscript= DB::table('jrnl_t_papers as jtp')->select([
            'jtp.paper_id',
            'jtp.journal_id',
            'jmpt.paper_type',
            'jmc.category_name as journal_category',
            'jmc.category_short_name as journal_category_short',
            'jtj.title_name as journal_title_short',
            'jtj.title as journal_title',
            'jtj.issn_number as journal_issn',
            'jtp.paper_title',
            'jtp.paper_path',
            'jtp.submission_date',
            'jtp.paper_status',
            'jtp.status',
            'jta.author_id  as author_id',
            'jta.prefix  as author_prefix',
            'jta.first_name  as author_first_name',
            'jta.middle_name  as author_middle_name',
            'jta.last_name  as author_last_name',
            'jta.designation as author_designation',
            'jta.institute as author_institute',
            'jta.department as author_department',
            'jta.contact_no as author_contact_no',
            'jta.address  as author_address',
            'jta.city as author_city',
            'jta.district as author_district',
            'jta.state as author_state',
            'jta.country as author_country',
            'jta.zip as author_zip',
        ])
        ->join('jrnl_m_paper_types as jmpt','jmpt.paper_type_id','=','jtp.paper_type_id')
        ->join('jrnl_t_journals as jtj', 'jtj.journal_id','=','jtp.journal_id')
        ->join('jrnl_m_categories as jmc', 'jmc.category_id', '=', 'jtj.journal_category_id')
        ->join('jrnl_t_authors as jta','jta.paper_id','=','jtp.paper_id')
        ->where('jtp.record_status','A')
        ->where('jtp.paper_id',$id)->get()->first();

        $coAuthor  = DB::table('jrnl_t_co_authors')->select('*')
        ->where('row_delete',0)
        ->where('paper_id',$id)->get();

        return response()->json(['menuscript'=>$manuscript, 'coauthor'=>$coAuthor]);
       }

    function sendRemark(Request $request){
        $request =json_decode(json_decode($request->input('data')));
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$request);
        $request= json_decode($jsonData);
        // $validated =  $validator = Validator::make($request, [
        //     'status'            =>  'required',
        //     'paper_id'         =>  'required',
        //     'remark'               =>  'required',
        //     'isPlagChecked'                 =>  'required',
        //     'plagPersent'           =>  'required',
        // ]);
        
        // if ($validator->fails())
        // return  response()->json(['error' => $validated->errors()->all()], 401);
        // dd($request);
        $remarkObj = JrnlTPaper::where('paper_id',$request->paper_id)
        ->update([
            'paper_status'=>$request->status,
            'is_plag_checked' => $request->isPlagChecked,
            'plag_persent' => $request->plagPersent,
            'status' => $request->status_detail,
        ]);
        // dd($request->status);
        if ($request->status=='DN' || $request->status=='EN') {
            // dd($request->status);
            $success = DB::table('jrnl_t_remark')->insert([
                'paper_id'   =>  $request->paper_id, //default author
                'remark'              => $request->remark,
                'remark_date'          =>date('Y-m-d H:i:s'),
                'remark_status'  => 'A',
                'record_created_on' => date('Y-m-d H:i:s'),
                'record_created_by' =>  'admin',//$request->email,
            ]);
        }
        if ($request->status=='E') 
            $message="Sent to Editor successfully.";
            
        if ($request->status=='DN' || $request->status=='EN') 
            $message="Notification Sent successfully.";

        if ($request->status=='X') 
            $message="Rejected successfully.";

        if ($request->status=='RP') 
            $message="Sent to Publish successfully.";
        
    return response()->json($message);
    }
    function markReviewStatus(Request $request){
        $request =json_decode(json_decode($request->input('data')));
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        $jsonData= Helper::CryptoJSAesDecrypt($decrypt_key,$request);
        $request= json_decode($jsonData);
        $remarkObj = JrnlTPaper::select('paper_status')->where('paper_id',$request->paper_id)
        ->update([
            'paper_status'=>$request->status,
            'status' => $request->status_detail,
        ]);
    return response()->json("Mark as reviewed Successfully.");
    }
    //*****end rajat code */
}
