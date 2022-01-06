<?php

namespace App\Http\Controllers\journals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JrnlNotice;

class NotificationController extends Controller
{
    function getAuthorRemark($author_id){
        $author_id= base64_decode(base64_decode($author_id));
       
        $data = DB::table('jrnl_t_remark')->where('receiver_author_id',$author_id)->get();
        return $data;
    }

    function getAuthorRemarkCount($author_id){
        $author_id= base64_decode(base64_decode($author_id));
       
        $data = DB::table('jrnl_t_remark')->where('receiver_author_id',$author_id)->count();
        return $data;
    }

    function getNoticeList(){
        $data = JrnlNotice::where('row_delete',0)->where('status','A')->orderBy('notice_id','desc')->get();
        return $data;
    }

    function viewNotice($notice_id){
        $notice_id= base64_decode(base64_decode($notice_id));
       
        $data = JrnlNotice::where('notice_id',$notice_id)->where('row_delete',0)->where('status','A')->get()->first();
        return $data; 
    }
}
