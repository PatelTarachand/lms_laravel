<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnrollController extends Controller
{
    function getNewEntrollId($coure_code,$student_id){
        $coure_code = base64_decode(base64_decode($coure_code));
        $student_id = base64_decode(base64_decode($student_id));
        $course_code = $coure_code;
        $sid         = $student_id;
        $cprefix     = substr($course_code,0,3);
        $year        =  date('y');

        $sid         = (strlen($sid)==1)?'00'.$sid:$sid;
        $sid         = (strlen($sid)==1)?'0'.$sid:$sid;

        // entroll_id = course_prefix + year 2 digit + student_id
        $entroll_id  = $cprefix.$year.$sid;

        return $entroll_id;
    }
}
