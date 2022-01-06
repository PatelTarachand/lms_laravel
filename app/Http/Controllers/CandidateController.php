<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Qualification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\{Student,StudentRemarks};
use Illuminate\Support\Facades\Crypt;
use App\Helper\Helper;
use Config;

class CandidateController extends Controller
{
    function getProfile($id,Request $request){
        $id = base64_decode(base64_decode($id));
        $encrypt_key=Config::get('encdec.ENCRYPT_KEY');
    $decrypt_key=Config::get('encdec.DECRYPT_KEY');
        $studentData  =  Student::leftJoin('m_courses', 't_students.course_code', '=', 'm_courses.course_code')
            ->where('t_students.id',$id)->select([
            't_students.first_name','t_students.last_name','t_students.mother_name','t_students.father_name','t_students.dob','t_students.gender','t_students.category','t_students.mobile_no',
            't_students.email','t_students.correspondence_country','t_students.correspondence_state','t_students.correspondence_city','t_students.correspondence_address',
            'correspondence_pin','t_students.whatsapp_no','t_students.twitter_id','t_students.instagram_id','t_students.facebook_id','t_students.course_code','t_students.is_disability','t_students.disability_per',
            'm_courses.course_type', 'm_courses.course_name', 't_students.approve_reject_status', 't_students.request_status'
            ])->get()->first();
        $aadharObj  =   Student::where('id',$id)->select(['aadhar_no'])->get()->first();
        $adhaarNo = 'XXXXXXXX'.substr($aadharObj->aadhar_no,-4);
        $studentData->aadhar_no = $adhaarNo;
        $string_json_fromPHP = Helper::CryptoJSAesEncrypt($encrypt_key,json_encode($studentData));
        return  $string_json_fromPHP;
    }

    function getQualificationById($sid){
        $sid = base64_decode(base64_decode($sid));
        $qua = DB::table('t_qualifications')->where("student_id",$sid)->orderBy('qualification_name')->get();
        return response()->json(["Qualification" => $qua]);
    }

    function getCandidateDocumentById($sid,$docType='candidate_photo'){
        $sid = base64_decode(base64_decode($sid));
        $docType = base64_decode(base64_decode($docType));
        $dataObj = DB::table('t_student_file_uploads')->where('document_name',$docType)->where("student_id",$sid)->get()->first();
        if(!empty($dataObj))
            $path =  $dataObj->file_path;
        else
            $path = null;
        return response()->json(["file_path" => $path]);
    }

    function getCandidateEnrollNumber($sid){
        $sid = base64_decode(base64_decode($sid));
        $enroll_number = DB::table('t_students')
              ->leftJoin('t_enrollments', 't_enrollments.student_id', '=', 't_students.id')
              ->select('*', 't_students.first_name',
                DB::raw('(CASE
                WHEN t_enrollments.enroll_id IS NULL THEN "Pending"
                ELSE t_enrollments.enroll_id
                END ) AS enroll_id' )
                )
               ->where("t_students.id",$sid)
               ->get();
        return response()->json($enroll_number);
    }

    function getApprovedCandidateDetails($id, Request $request)
    {
        $id = base64_decode(base64_decode($id));
        $data =  DB::table('t_students')
            ->leftJoin('m_courses', 't_students.course_code', '=', 'm_courses.course_code')
            ->leftJoin('m_states',  'm_states.state_id', '=',  't_students.correspondence_state')
            ->leftJoin('m_countries',  'm_countries.country_id', '=',  't_students.correspondence_country')
            ->leftJoin('m_cities',  'm_cities.city_id', '=',  't_students.correspondence_city')
            ->leftJoin('t_enrollments',  't_enrollments.student_id', '=',  't_students.id')
            ->leftJoin('t_enroll_entp_members',  't_enroll_entp_members.student_id', '=',  't_students.id')
            ->where("t_students.id", $id)
            ->where("t_students.row_delete", "=", "0")
            ->where("t_enrollments.record_status", "=", "A")
            ->where("t_enroll_entp_members.record_status", "=", "A")
            ->orderBy('t_students.id', 'asc')
            ->get();
        // ->get(["t_students.id","m_courses.course_name","t_students.first_name","t_students.last_name","t_students.mother_name", "t_students.father_name",
        // "t_students.gender", "t_students.dob", "t_students.category", "t_students.mobile_no", "t_students.email", "t_students.aadhar_no", "t_students.whatsapp_no", "t_students.twitter_id", "t_students.facebook_id", "t_students.instagram_id",
        // "t_students.correspondence_country", "t_students.correspondence_state", "t_students.correspondence_city", "t_students.correspondence_address", "t_students.correspondence_pin"]);

        $data1 = json_decode($data, true);
        return response()->json($data1);
    }
    function getNotificationList($id ,Request $request){
        $id = base64_decode(base64_decode($id));
        $data = StudentRemarks::leftJoin('t_students', 't_students.id', '=', 't_student_remark.student_id')
        ->where("t_student_remark.student_id",$id)
        ->select('t_student_remark.remark','t_student_remark.remark_date')->get();
        return response()->json($data);
    }

}
