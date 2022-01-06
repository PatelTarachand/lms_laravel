<?php

namespace App\Http\Controllers\journals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JrnlTPaper;
use App\Models\JrnlMCategory;
use App\Models\JrnlMPaperType;
use App\Models\JrnlTAuthor;
use App\Models\JrnlUser;
use App\Models\JrnlTJournal;

class ReportController extends Controller
{
    function countPapers($author_id,$paper_status){
      $author_id= base64_decode(base64_decode($author_id));
      $paper_status= base64_decode(base64_decode($paper_status));
       
       return  JrnlTPaper::where('row_delete',0)->where('author_user_id',$author_id)->where('paper_status',$paper_status)->count();
    }

    function ListPapersStatusWise($author_id,$paper_status){
        return  JrnlTPaper::where('row_delete',0)->where('author_user_id',$author_id)->where('paper_status',$paper_status)->get();
    }

   function customeCountReport(){
      $totalJournalCategory = JrnlMCategory::where('row_delete',0)->count();
      $totalPaperType       = JrnlMPaperType::where('row_delete',0)->count();
      $totalJournal         = JrnlTJournal::where('row_delete',0)->count();
      $totalAuthor          = JrnlTAuthor::where('row_delete',0)->count();
      $totalSubmitted       = JrnlTPaper::where('row_delete',0)->count();
      $totalSentToEditor    = JrnlTPaper::where('row_delete',0)->where('paper_status','E')->count();
      $TotalReadyToPublish  = JrnlTPaper::where('row_delete',0)->where('paper_status','RP')->count();
      $Totalpublished       = JrnlTPaper::where('row_delete',0)->where('paper_status','P')->count();
      $TotalNotifyAuthor    = JrnlTPaper::where('row_delete',0)->whereIn('paper_status', ['DN','EN'])->count();
      $totalEditor          = JrnlUser::where('row_delete',0)->where('user_type_id',2)->count();

      $data =     array(
               'totalJournalCategory'     => $totalJournalCategory,
               'totalPaperType'           => $totalPaperType,
               'totalJournal'             => $totalJournal,
               'totalAuthor'              => $totalAuthor,
               'totalSubmitted'           => $totalSubmitted,
               'totalSentToEditor'        => $totalSentToEditor,
               'totalReadyToPublish'     => $TotalReadyToPublish,
               'totalpublished'          => $Totalpublished,
               'totalNotifyAuthor'       =>  $TotalNotifyAuthor,
               'totalEditor'             => $totalEditor,
      );

      return json_encode($data);
   }
}
