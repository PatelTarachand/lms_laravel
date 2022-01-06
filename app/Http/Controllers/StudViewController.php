<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator, Redirect, Response;
use App\Models\{CourseEnroll, EnrollEntpMember, Enrollments, StudentRemarks, Student};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApproveRegistration;
use App\Mail\RejectRegistration;
use App\Mail\sendRemarkRegistration;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Course;
use Illuminate\Support\Facades\Crypt;
use App\Helper\Helper;
use Config;

class StudViewController extends Controller
{

    public function getCoursesList()
    {
        $data =  DB::table('t_students')
            ->leftJoin('m_courses', 't_students.course_code', '=', 'm_courses.course_code')
            ->where("t_students.row_delete", "=", "0")
            ->orderBy('id', 'asc')
            ->get(["t_students.id", "t_students.first_name", "t_students.last_name", "m_courses.course_name", "t_students.registration_date"]);
        return response()->json($data);
    }

    public function getStudentList($start_date=null, $end_date=null, $status=null, $course_type=null, Request $request)
    {
        $start_date = base64_decode(base64_decode($start_date));
        $end_date = base64_decode(base64_decode($end_date));
        $status = base64_decode(base64_decode($status));
        $course_type = base64_decode(base64_decode($course_type));
        $query =  DB::table(DB::raw('(SELECT @a:= 0) AS a , t_students'))
        ->join('m_courses', 't_students.course_code', '=', 'm_courses.course_code')
        ->select(
            DB::raw('DISTINCT @a:=@a+1 as serial_number'),
            'm_courses.course_code',
            'm_courses.course_name',
            "t_students.id",
            "t_students.first_name",
            "t_students.last_name",
            "m_courses.course_name",
            "t_students.registration_date",
            "t_students.approve_reject_status as apprejstatus",
            "t_students.request_status AS approve_reject_status",

        )
        ->where("t_students.row_delete", "=", "0");

        if($start_date!='undefined' && $end_date!='undefined'){
            $query = $query->where(DB::raw('DATE(t_students.registration_date)'), ">=", DATE($start_date))
            ->where(DB::raw('DATE(t_students.registration_date)'), "<=", DATE($end_date));
        }

        if($status!='undefined'){
            $query =  $query->where('t_students.approve_reject_status', "=", $status);
        }

        if($course_type!='undefined'){
            $query = $query->where('m_courses.course_type', "=", $course_type);
        }
        $data = $query->get(["t_students.id", "t_students.first_name", "t_students.last_name", "m_courses.course_name", "t_students.registration_date", "t_students.approve_reject_status"]);
        return response()->json($data);
    }//fun


    public function getTotalStudentCount()
    {

        $data =  DB::table('t_students')
            ->leftJoin('m_courses', 't_students.course_code', '=', 'm_courses.course_code')
            // ->where("t_students.approve_reject_status", "=", "P")
            ->whereIn("t_students.approve_reject_status", ["R","M"])
            ->where("t_students.row_delete", "=", "0")
            ->orderBy('id', 'asc')
            ->get(["t_students.id", "t_students.first_name", "t_students.last_name", "m_courses.course_name", "t_students.registration_date"]);
        $dataCount['totalstudent_count'] = $data->count();
        //json_encode($dataCount);
        return response()->json($dataCount);
    }



    //     public function getStudentDetails($id,Request $request)
    //     {
    //          $data = Student::where("id",$id)->orderBy('id','asc')
    //                      ->get(["id","first_name","last_name","mother_name", "father_name",
    //                       "gender", "dob", "category", "mobile_no", "email", "aadhar_no", "whatsapp_no", "twitter_id", "facebook_id", "instagram_id",
    //                       "correspondence_country", "correspondence_state", "correspondence_city", "correspondence_address", "correspondence_pin"]);
    //         $data1= json_decode($data, true);
    //          return response()->json($data1);
    //    }


    public function getStudentDetails($id, Request $request)
    {
        $id = base64_decode(base64_decode($id));
        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        $data =  DB::table('t_students')
            ->leftJoin('m_courses', 't_students.course_code', '=', 'm_courses.course_code')
            ->leftJoin('m_states',  'm_states.state_id', '=',  't_students.correspondence_state')
            ->leftJoin('m_countries',  'm_countries.country_id', '=',  't_students.correspondence_country')
            ->leftJoin('m_cities',  'm_cities.city_id', '=',  't_students.correspondence_city')
            ->where("t_students.id", $id)
            ->where("t_students.row_delete", "=", "0")
            ->orderBy('t_students.id', 'asc')
            ->get();
        // ->get(["t_students.id","m_courses.course_name","t_students.first_name","t_students.last_name","t_students.mother_name", "t_students.father_name",
        // "t_students.gender", "t_students.dob", "t_students.category", "t_students.mobile_no", "t_students.email", "t_students.aadhar_no", "t_students.whatsapp_no", "t_students.twitter_id", "t_students.facebook_id", "t_students.instagram_id",
        // "t_students.correspondence_country", "t_students.correspondence_state", "t_students.correspondence_city", "t_students.correspondence_address", "t_students.correspondence_pin"]);

        $data1 = json_decode($data, true);
        $string_json_fromPHP = Helper::CryptoJSAesEncrypt($encrypt_key, json_encode($data1));
        
        return response()->json($string_json_fromPHP);
    }

    public function getStudentQualification($id, Request $request)
    {
        $id = base64_decode(base64_decode($id));
        $data =  DB::table('t_students')
            ->leftJoin('m_courses', 't_students.course_code', '=', 'm_courses.course_code')
            ->leftJoin('t_qualifications', 't_students.id', '=', 't_qualifications.student_id')
            ->where("t_students.id", $id)
            ->where("t_students.row_delete", "=", "0")
            // ->groupBy('t_students.id')
            ->orderBy('t_qualifications.qualification_id', 'asc')
            ->get([
                "t_qualifications.qualification_id", "t_students.id", "m_courses.course_name",
                "t_qualifications.qualification_name", "t_qualifications.board_name", "t_qualifications.passing_year", "t_qualifications.marks", "t_qualifications.grade", "t_qualifications.file_path",
            ]);
        $data1 = json_decode($data, true);
        return response()->json($data1);
    }

    public function getStudentPendingCount()
    {

        $data =  DB::table('t_students')
            ->leftJoin('m_courses', 't_students.course_code', '=', 'm_courses.course_code')
            ->where("t_students.approve_reject_status", "=", "N")
            ->where("t_students.row_delete", "=", "0")
            ->orderBy('id', 'asc')
            ->get(["t_students.id", "t_students.first_name", "t_students.last_name", "m_courses.course_name", "t_students.registration_date"]);
        $dataCount['pending_count'] = $data->count();
        // $data1= json_encode($dataCount);
        return response()->json($dataCount);
    }

    public function StudentApprove($id, Request $request)
    {
        $id = base64_decode(base64_decode($id));
        // For t_students approve_reject_status
        $date = Carbon::now('Asia/Kolkata');
        $stdObj = Student::find($id);
        $stdObj->approve_reject_reason = $request->approve_reject_msg;
        $stdObj->approve_reject_status = 'A';
        $stdObj->request_status = 'Approved';
        $stdObj->approve_reject_date = $date;

        $stdObj->save();

        // get new enroll id
        $course_code = $request->course_code;
        $sid         = $id;
        $cprefix     = substr($course_code, 0, 3);
        $year        =  date('y');

        $sid         = (strlen($sid) == 1) ? '00' . $sid : $sid;
        $sid         = (strlen($sid) == 1) ? '0' . $sid : $sid;

        // entroll_id = course_prefix + year 2 digit + student_id
        $enroll_no  = $cprefix . $year . $sid;

        $enrollment_no = new Enrollments();
        $enrollment_no->student_id = $id;
        $enrollment_no->enroll_id = $enroll_no;
        $enrollment_no->enroll_date = $date;
        $enrollment_no->record_status = 'A';
        $enrollment_no->request_status = 'Approved';
        $enrollment_no->save();

        // For t_course_enroll
        $courseenroll = new CourseEnroll();
        $courseenroll->student_id = $id;
        $courseenroll->enroll_id = $enroll_no;
        //$courseenroll->course_id = $entroll_id;
        $courseenroll->course_code = $request->course_code;
        $courseenroll->record_status = 'A';
        $courseenroll->save();

        // For t_entroll_entp_members
        $entrp_enroll = new EnrollEntpMember();
        $entrp_enroll->student_id = $id;
        $entrp_enroll->entroll_id = $enroll_no;
        // $courseenroll->course_id = $entroll_id;
        $entrp_enroll->course_code = $request->course_code;
        $entrp_enroll->record_status = 'A';
        $entrp_enroll->student_type = 'A';
        $entrp_enroll->save();

        //get course name
        $cobj = Course::where('course_code', $request->course_code)->get()->first();

        $newPass  =   Str::random(8);
        //send Approve mail to candidate
        $mailData = array(
            'username' =>  $enroll_no,
            'password' =>  $newPass,
            'course'   =>  $cobj->course_name,
        );
        Mail::to($stdObj->email)->send(new ApproveRegistration($mailData));

        $newPass   = Hash::make($newPass);
        //update new password
        DB::table('t_student_passwords')
            ->insert([
                'student_id'    => $id,
                'username'      => $enroll_no,
                'old_password'  => $newPass,
                'new_password'  => $newPass,
                'record_created_on' => date('Y-m-d H:i:s'),
                'record_created_by' =>  'admin',
                'record_status'     => 'A',
            ]);

        $data1 = json_decode($stdObj, true);
        return response()->json($data1);
    }

    public function getStudentApproveList()
    {

        $data =  DB::table('t_students')
            ->leftJoin('m_courses', 't_students.course_code', '=', 'm_courses.course_code')
            ->where("t_students.approve_reject_status", "=", "A")
            ->where("t_students.row_delete", "=", "0")
            ->orderBy('id', 'asc')
            ->get(["t_students.id", "t_students.first_name", "t_students.last_name", "m_courses.course_name", "t_students.registration_date"]);
        return response()->json($data);
    }

    public function getStudentApproveCount()
    {
        $data =  DB::table('t_students')
            ->leftJoin('m_courses', 't_students.course_code', '=', 'm_courses.course_code')
            ->where("t_students.approve_reject_status", "=", "A")
            ->where("t_students.row_delete", "=", "0")
            ->orderBy('id', 'asc')
            ->get(["t_students.id", "t_students.first_name", "t_students.last_name", "m_courses.course_name", "t_students.registration_date"]);
        $dataCount['approved_count'] = $data->count();
        // $data1= json_encode($dataCount);
        return response()->json($dataCount);
    }

    public function StudentEligible($id, Request $request)
    {
        $id = base64_decode(base64_decode($id));
        $date = Carbon::now('Asia/Kolkata');
        $stuObj = Student::find($id);
        $stuObj->approve_reject_reason = $request->approve_reject_msg;
        $stuObj->approve_reject_status = 'E';
        $stuObj->request_status = 'Eligible';
        $stuObj->approve_reject_date = $date;
        $stuObj->save();

        $sData = Student::where('id', $id)->get()->first();
        //get course name
        $cobj = Course::where('course_code', $sData->course_code)->get()->first();

        //send Approve mail to candidate
        $mailData = array(
            'message' =>  $request->approve_reject_msg,
            'course'      => $cobj->course_name,
        );
        Mail::to($stuObj->email)->send(new RejectRegistration($mailData));

        $data1 = json_decode($stuObj, true);
        return response()->json($data1);
    }

    public function StudentReject($id, Request $request)
    {
        $id = base64_decode(base64_decode($id));
        $date = Carbon::now('Asia/Kolkata');
        $stuObj = Student::find($id);
        $stuObj->approve_reject_reason = $request->approve_reject_msg;
        $stuObj->approve_reject_status = 'X';
        $stuObj->request_status = 'Rejected';
        $stuObj->approve_reject_date = $date;
        $stuObj->save();

        $sData = Student::where('id', $id)->get()->first();
        //get course name
        $cobj = Course::where('course_code', $sData->course_code)->get()->first();

        //send Approve mail to candidate
        $mailData = array(
            'message' =>  $request->approve_reject_msg,
            'course'      => $cobj->course_name,
        );
        Mail::to($stuObj->email)->send(new RejectRegistration($mailData));

        $data1 = json_decode($stuObj, true);
        return response()->json($data1);
    }
    public function getStudentRejectCount()
    {

        $data =  DB::table('t_students')
            ->leftJoin('m_courses', 't_students.course_code', '=', 'm_courses.course_code')
            ->where("t_students.approve_reject_status", "=", "X")
            ->orderBy('id', 'asc')
            ->get(["t_students.id", "t_students.first_name", "t_students.last_name", "m_courses.course_name", "t_students.registration_date"]);
        $dataCount['reject_count'] = $data->count();
        // $data1= json_encode($dataCount);
        return response()->json($dataCount);
    }

    public function getStudentEligibleCount()
    {

        $data =  DB::table('t_students')
            ->leftJoin('m_courses', 't_students.course_code', '=', 'm_courses.course_code')
            ->where("t_students.approve_reject_status", "=", "E")
            ->orderBy('id', 'asc')
            ->get(["t_students.id", "t_students.first_name", "t_students.last_name", "m_courses.course_name", "t_students.registration_date"]);
        $dataCount['eligible_count'] = $data->count();
        // $data1= json_encode($dataCount);
        return response()->json($dataCount);
    }

    public function getStudentFeePaidCount()
    {

        $data =  DB::table('t_students')
            ->leftJoin('m_courses', 't_students.course_code', '=', 'm_courses.course_code')
            ->where("t_students.approve_reject_status", "=", "F")
            ->orderBy('id', 'asc')
            ->get(["t_students.id", "t_students.first_name", "t_students.last_name", "m_courses.course_name", "t_students.registration_date"]);
        $dataCount['feepaidstud_count'] = $data->count();
        // $data1= json_encode($dataCount);
        return response()->json($dataCount);
    }

    public function getStudentModifiedCount()
    {

        $data =  DB::table('t_students')
            ->join('m_courses', 't_students.course_code', '=', 'm_courses.course_code')
            ->where("t_students.approve_reject_status", "=", "U")
            ->orderBy('id', 'asc')
            ->get(["t_students.id", "t_students.first_name", "t_students.last_name",  "t_students.registration_date"]);
        $dataCount['modifiedstud_count'] = $data->count();
        // $data1= json_encode($dataCount);
        return response()->json($dataCount);
    }


    public function StudentSendAlert($id, Request $request)
    {
        $id = base64_decode(base64_decode($id));
        // echo $_REQUEST;
        $date = Carbon::now('Asia/Kolkata');
        $send_alert = Student::find($id);
        $send_alert->approve_reject_reason = $request->approve_reject_msg;
        // $input = $request->all();
        // $reject->fill($input)->save();
        $send_alert->approve_reject_status = 'N';
        $send_alert->request_status = 'Pending';
        $send_alert->approve_reject_date = $date;
        $send_alert->save();

        $add_remarks = new StudentRemarks();
        $add_remarks->student_id = $request->id;
        $add_remarks->remark = $request->approve_reject_msg;
        $add_remarks->remark_date = $date;
        $add_remarks->remark_status = 'A';
        $add_remarks->save();

        $sData = Student::where('id', $id)->get()->first();
        //get course name
        $cobj = Course::where('course_code', $sData->course_code)->get()->first();

        //send Approve mail to candidate
        $mailData = array(
            'message' =>  $request->approve_reject_msg,
            'course'      => $cobj->course_name,
        );

        Mail::to($send_alert->email)->send(new sendRemarkRegistration($mailData));


        //$data1 = json_decode($send_alert, true);
        $data2 = json_decode($add_remarks, true);
        return response()->json($data2);
    }

    public function getStudentCountCourseWise()
    {

        $data =  DB::table('m_courses')
            ->leftJoin('t_students', 't_students.course_code', '=', 'm_courses.course_code')
            ->select('m_courses.course_code', 'm_courses.course_name', DB::raw('count(t_students.course_code) as student_count, 
             t_students.approve_reject_status,
            t_students.request_status'))
            //->where("t_students.approve_reject_status", "=", "N")
            ->where("t_students.row_delete", "=", "0")
            ->groupBy('m_courses.course_code', 'm_courses.course_name', 't_students.approve_reject_status')
            ->get();
        // $dataCount['course_count'] = $data->count();
        $data1 = json_decode($data);
        return response()->json($data);
    }

    public function getStudentStatusCountCourseWise($id, $id1, Request $request)
    {
        $id = base64_decode(base64_decode($id));
        $id1 = base64_decode(base64_decode($id1));
        $data =  DB::table('m_courses')
            ->leftJoin('t_students', 't_students.course_code', '=', 'm_courses.course_code')
            ->select('m_courses.course_code', 'm_courses.course_name', DB::raw('count(t_students.course_code) as student_count'))
            //  ->where("t_students.approve_reject_status" , "=" , "0")
            ->whereIn("t_students.approve_reject_status", [ $id, $id1])
            ->where("t_students.row_delete", "=", "0")
            ->groupBy('m_courses.course_code', 'm_courses.course_name')
            ->get();
        // $dataCount['course_count'] = $data->count();
        $data1 = json_decode($data, true);
        return response()->json($data);
    }

    public function getStudentListCourseStatusWise($course_code, $status_id, Request $request)
    {
        $course_code = base64_decode(base64_decode($course_code));
        $status_id = base64_decode(base64_decode($status_id));
        $data =  DB::table('m_courses')
            ->leftJoin('t_students', 't_students.course_code', '=', 'm_courses.course_code')
            ->select(
                'm_courses.course_code',
                'm_courses.course_name',
                't_students.id',
                't_students.first_name',
                't_students.last_name',
                "t_students.registration_date",
                "t_students.approve_reject_status",
                "t_students.request_status"
            )
            ->where("m_courses.course_status", "=", "A")
            ->where("t_students.course_code", $course_code)
            ->where("t_students.approve_reject_status", "=", $status_id)
            ->where("t_students.row_delete", "=", "0")
            ->orderBy('t_students.id', 'asc')
            ->get();

        $data1 = json_decode($data, true);
        return response()->json($data);
    }

    public function getStudentListCourseWise( $course_code, $status_id, $status_id1, $start_date=null, $end_date=null, Request $request)
    {
        $start_date = base64_decode(base64_decode($start_date));
        $end_date = base64_decode(base64_decode($end_date));
        $course_code = base64_decode(base64_decode($course_code));
        $status_id = base64_decode(base64_decode($status_id));
        $status_id1 = base64_decode(base64_decode($status_id1));
        $query =  DB::table('m_courses')
            ->leftJoin('t_students', 't_students.course_code', '=', 'm_courses.course_code')
            ->select(
                'm_courses.course_code',
                'm_courses.course_name',
                't_students.id',
                't_students.first_name',
                't_students.last_name',
                "t_students.registration_date",
                "t_students.record_updated_on",
                "t_students.approve_reject_status as apprej_id",
                "t_students.approve_reject_status",
                "t_students.request_status"
            )
            ->where("m_courses.course_status", "=", "A")
            ->whereIn("t_students.approve_reject_status", [$status_id, $status_id1])
            ->where("t_students.course_code", $course_code)
            ->where("t_students.row_delete", "=", "0");

            if($start_date!='undefined' && $end_date!='undefined'){
                $query = $query->where(DB::raw('DATE(t_students.registration_date)'), ">=", DATE($start_date))
                ->where(DB::raw('DATE(t_students.registration_date)'), "<=", DATE($end_date));
            }
            $data = $query->get();

        $data1 = json_decode($data, true);
        return response()->json($data);
    }

    public function getStudentListCourseWiseExcel( $course_code, $status_id, $status_id1, $start_date=null, $end_date=null, Request $request)
    {
        $start_date = base64_decode(base64_decode($start_date));
        $end_date = base64_decode(base64_decode($end_date));
        $course_code = base64_decode(base64_decode($course_code));
        $status_id = base64_decode(base64_decode($status_id));
        $status_id1 = base64_decode(base64_decode($status_id1));
        $query =  DB::table('m_courses')
            ->leftJoin('t_students', 't_students.course_code', '=', 'm_courses.course_code')
            ->leftJoin('m_countries', 't_students.correspondence_country', '=', 'm_countries.country_id')
            ->leftJoin('m_states', 't_students.correspondence_state', '=', 'm_states.state_id')
            ->leftJoin('m_district', 't_students.correspondence_city', '=', 'm_district.district_id')
            ->select(
                'm_courses.course_code',
                'm_courses.course_name',
                "t_students.registration_date",
                't_students.first_name',
                't_students.last_name',
                't_students.father_name',
                't_students.mother_name',
                't_students.dob',
                't_students.gender',
                't_students.mobile_no',
                't_students.email',
                'm_countries.country_name',
                'm_states.state_name',
                'm_district.district_name',
                't_students.correspondence_address',
                't_students.correspondence_pin',
                't_students.whatsapp_no',
                't_students.twitter_id',
                't_students.facebook_id',
                't_students.instagram_id',
                't_students.disability_per',
                't_students.approve_reject_reason',
                't_students.approve_reject_date',
                "t_students.request_status"
            )
            ->where("m_courses.course_status", "=", "A")
            ->whereIn("t_students.approve_reject_status", [$status_id, $status_id1])
            ->where("t_students.course_code", $course_code)
            ->where("t_students.row_delete", "=", "0");

            if($start_date!='undefined' && $end_date!='undefined'){
                $query = $query->where(DB::raw('DATE(t_students.registration_date)'), ">=", DATE($start_date))
                ->where(DB::raw('DATE(t_students.registration_date)'), "<=", DATE($end_date));
            }
            $data = $query->get();

        $data1 = json_decode($data, true);
        return response()->json($data);
    }


    public function getStudentListExcel($start_date=null, $end_date=null, $status=null, $course_type=null, Request $request)
    {
        $start_date = base64_decode(base64_decode($start_date));
        $end_date = base64_decode(base64_decode($end_date));
        $status = base64_decode(base64_decode($status));
        $course_type = base64_decode(base64_decode($course_type));
        $query =  DB::table(DB::raw('(SELECT @a:= 0) AS a , t_students'))
        ->join('m_courses', 't_students.course_code', '=', 'm_courses.course_code')
        ->leftJoin('m_countries', 't_students.correspondence_country', '=', 'm_countries.country_id')
            ->leftJoin('m_states', 't_students.correspondence_state', '=', 'm_states.state_id')
            ->leftJoin('m_district', 't_students.correspondence_city', '=', 'm_district.district_id')
            ->select(
                DB::raw('DISTINCT @a:=@a+1 as serial_number'),
                'm_courses.course_code',
                'm_courses.course_name',
                "t_students.registration_date",
                't_students.first_name',
                't_students.last_name',
                't_students.father_name',
                't_students.mother_name',
                't_students.dob',
                't_students.gender',
                't_students.mobile_no',
                't_students.email',
                'm_countries.country_name',
                'm_states.state_name',
                'm_district.district_name',
                't_students.correspondence_address',
                't_students.correspondence_pin',
                't_students.whatsapp_no',
                't_students.twitter_id',
                't_students.facebook_id',
                't_students.instagram_id',
                't_students.disability_per',
                't_students.approve_reject_reason',
                't_students.approve_reject_date',
                "t_students.request_status"
            )
        ->where("t_students.row_delete", "=", "0");

        if($start_date!='undefined' && $end_date!='undefined'){
            $query = $query->where(DB::raw('DATE(t_students.registration_date)'), ">=", DATE($start_date))
            ->where(DB::raw('DATE(t_students.registration_date)'), "<=", DATE($end_date));
        }

        if($status!='undefined'){
            $query =  $query->where('t_students.approve_reject_status', "=", $status);
        }

        if($course_type!='undefined'){
            $query = $query->where('m_courses.course_type', "=", $course_type);
        }
        $data = $query->get(["t_students.id", "t_students.first_name", "t_students.last_name", "m_courses.course_name", "t_students.registration_date", "t_students.approve_reject_status"]);
        return response()->json($data);
    }//fun

    function getCandidateDocumentByIdAdminSide($sid)
    {
        $sid = base64_decode(base64_decode($sid));
        $qua = DB::table('t_student_file_uploads')->where("student_id", $sid)->get();
        return response()->json(["candidate_doc" => $qua]);
    }
}
