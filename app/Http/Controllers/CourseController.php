<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function getCourse()
    {
        
         $data = Course::where("row_delete",0)
                    ->orderBy('course_code','asc')
                     ->get();
         return response()->json($data);
    }
    public function getCourseType($id,Request $request)
    {
     $id = base64_decode(base64_decode($id));
         $data = Course::where("row_delete",0)
                    ->where("course_type",$id)
                    ->orderBy('id','asc')
                     ->get();
         return response()->json($data);
    }
}
