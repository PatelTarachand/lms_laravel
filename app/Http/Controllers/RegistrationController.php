<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\EnrollController;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isNull;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationMail;
use Illuminate\Support\Facades\Crypt;
use App\Helper\Helper;
use Config;
use phpDocumentor\Reflection\Types\Null_;

class RegistrationController extends Controller
{
    private $aws_bucket_url  = 'https://ts5pftk5zcrik-test.s3.ap-south-1.amazonaws.com/';
    private $storage_disk   = 's3';
    private $storage_root   = 'candiate/';

    function register(Request $request)
    {

        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        $responce =        json_decode(json_decode($request->formData));
        $jsonData = Helper::CryptoJSAesDecrypt($decrypt_key, $responce);

        $data = json_decode($jsonData, true);

        $req = $data;
        //==========validation=============
        $validator = Validator::make(
            $req,
            [
                'firstName'         => 'required',
                'fatherName'        => 'required',
                'motherName'        => 'required',
                'mobileNumber'      => 'required|unique:t_students,mobile_no',
                'emailId'           => 'required|email|unique:t_students,email',
                'aadharNumber'      => 'required|unique:t_students,aadhar_no',
                'addressLine'       => 'required',
                'pinCode'           => 'required',
                'selectCourse'      => 'required',
                'selectYear'        => 'required',
                'selectMonth'       => 'required',
                'selectDay'         => 'required',
                'selectGender'      => 'required',
                'selectCategory'    => 'required',
                'selectCountry'     => 'required',
                'selectState'       => 'required',
                'selectDistrict'    => 'required',
                'selectDisability'  => 'required',

            ],
            [
                'emailId.unique'       =>  'आपका ईमेल आईडी पहले से पंजीकृत है।',
                'mobileNumber.unique'  =>  'आपका मोबाइल नंबर पहले से पंजीकृत है|',
                'aadharNumber.unique'  =>  'आपका आधार नंबर  पहले से पंजीकृत है|',
            ]
        );
        if ($validator->fails())
            return  response()->json(['error' => $validator->errors()->all()], 401);

        //===========validation for files=========
        $validator = Validator::make($request->all(), [
            'candidatePhoto'    => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'aadharFile'        => 'required|file|mimes:jpg,png,jpeg,pdf|max:2048',
        ]);

        //validation check
        if ($validator->fails())
            return  response()->json(['error' => $validator->errors()->all()], 401);

        //=======student model for data binding===
        $stuObj = new Student();

        $stuObj->first_name              = $req['firstName'];
        $stuObj->last_name               = $req['lastName'];
        $stuObj->father_name             = $req['fatherName'];
        $stuObj->mother_name             = $req['motherName'];
        $stuObj->mobile_no               = $req['mobileNumber'];
        $stuObj->email                   = $req['emailId'];
        $stuObj->gender                  = $req['selectGender']['name'];
        $stuObj->category                = $req['selectCategory'];
        $stuObj->aadhar_no               = $req['aadharNumber'];
        $stuObj->course_code             = $req['selectCourse'];

        $stuObj->category               = $req['selectCategory'];
        $stuObj->correspondence_country = $req['selectCountry'];
        $stuObj->correspondence_state   = $req['selectState'];
        $stuObj->correspondence_city    = $req['selectDistrict'];
        $stuObj->correspondence_address = $req['addressLine'];
        $stuObj->correspondence_pin     = $req['pinCode'];
        $stuObj->whatsapp_no             = $req['waNumber'];
        $stuObj->twitter_id              = $req['twitterId'];
        $stuObj->facebook_id             = $req['fbId'];
        $stuObj->instagram_id            = $req['instaId'];
        $stuObj->approve_reject_status   = 'R';
        $stuObj->request_status          = 'Registered';
        $stuObj->record_created_on       = date('Y-m-d H:i:s');
        $stuObj->registration_date       = date('Y-m-d H:i:s');
        $stuObj->client_info             = Helper::clientInfo();

        if (isset($req['selectDisability']))
            $stuObj->is_disability          = $req['selectDisability'];
        if (isset($req['disability']))
            $stuObj->disability_per         = $req['disability'];

        //date of birth concadinate
        $month                  =  $req['selectMonth']['id'];
        $month                  =  (strlen($month) == 1) ? '0' . $month : $month;
        $day                    =  $req['selectDay'];
        $day                    =  (strlen($day) == 1) ? '0' . $day : $day;
        $year                   =  $req['selectYear'];
        $sdate                  =  $year . '-' . $month . '-' . $day;
        $dob                    =  date($sdate);

        $stuObj->password       =  Hash::make($day . $month . $year);
        $stuObj->dob            =  $dob;

        $stuObj->ip = getenv('HTTP_CLIENT_IP') ?:
            getenv('HTTP_X_FORWARDED_FOR') ?:
            getenv('HTTP_X_FORWARDED') ?:
            getenv('HTTP_FORWARDED_FOR') ?:
            getenv('HTTP_FORWARDED') ?:
            getenv('REMOTE_ADDR');


        //insert data into student table
        $sucees =   $stuObj->save();

        if (!empty($sucees)) {
            //get last insert id
            $student_id =  DB::getPdo()->lastInsertId();

            //===========for candidate photo========
            if ($request->hasFile('candidatePhoto')) {
                $extension = $request->candidatePhoto->extension();
                $file = uniqid() . "_" . time() . "_" . $student_id . "." . $extension;
                $path = $this->storage_root . "profile/";
                $request->file('candidatePhoto')->storeAs($path, $file, $this->storage_disk);
                $path   = $this->aws_bucket_url . $path . $file;
                $candidatePhoto =  $path;

                DB::table('t_student_file_uploads')->insert(
                    [
                        'document_name' => 'candidate_photo',
                        'file_path'     => $candidatePhoto,
                        'student_id'    => $student_id,
                        'record_created_on'  => date('Y-m-d H:i:s'),
                        'record_created_by'  =>  $student_id,
                        'record_status' => 'R'
                    ],
                );
            }

            //============for candidate categoryFile=======
            if ($request->hasFile('categoryFile')) {

                $validator = Validator::make($request->all(), [
                    'categoryFile'      => 'file|mimes:jpg,png,jpeg,pdf|max:2048',
                ]);

                //validation check
                if ($validator->fails())
                    return  response()->json(['error' => $validator->errors()->all()], 401);

                $extension = $request->categoryFile->extension();
                $file = uniqid() . "_" . time() . "_" . $student_id . "." . $extension;
                $path = $this->storage_root . "category/";
                $request->file('categoryFile')->storeAs($path, $file, $this->storage_disk);
                $path   = $this->aws_bucket_url . $path . $file;
                $casteFile =  $path;

                DB::table('t_student_file_uploads')->insert(
                    [
                        'document_name' => 'caste_file',
                        'file_path'     => $casteFile,
                        'student_id'    => $student_id,
                        'record_created_on'     => date('Y-m-d H:i:s'),
                        'record_created_by'     =>  $student_id,
                        'record_status' => 'R'
                    ],
                );
            } //if

            //==========for candidate aadharFile upload ===========
            if ($request->hasFile('aadharFile')) {
                $extension = $request->aadharFile->extension();
                $file = "aadhaar_" . uniqid() . "_" . time() . "_" . $student_id . "." . $extension;
                $path = $this->storage_root . "identity_file/";
                $request->file('aadharFile')->storeAs($path, $file, $this->storage_disk);
                $path   = $this->aws_bucket_url . $path . $file;
                $aadharFile =  $path;

                DB::table('t_student_file_uploads')->insert(
                    [
                        'document_name' => 'aadhar_file',
                        'file_path'     =>  $aadharFile,
                        'student_id'    =>  $student_id,
                        'record_created_on' => date('Y-m-d H:i:s'),
                        'record_created_by' =>  $student_id,
                        'record_status' => 'R'
                    ]
                );
            } //if

            //========Candidate Marksheet upload========
            if ($request->hasFile('quafile')) {

                foreach ($request->file('quafile') as $file) {
                    $extension = $file->extension();
                    $filename = uniqid() . "_" . time() . "_" . $student_id . "." . $extension;
                    $path = $this->storage_root . "qualification/";
                    $file->storeAs($path, $filename, $this->storage_disk);
                    $fileArray[]  = $this->aws_bucket_url . $path . $filename;
                }
            }

            $i = 0;
            $qulificationArray = $req['qArray'];
            foreach ($qulificationArray as $val) {

                if (!isset($fileArray))
                    $path = null;
                else
                    $path  = $fileArray[$i];

                //file path store to database
                    // DB::table('t_qualifications')->insert(
                    //     [
                    //         'qualification_code'    => $val['qualification'],
                    //         'qualification_name'    => $val['qualification'],
                    //         'board_name'            => $val['boardName'],
                    //         'passing_year'          => $val['boardPassingYear'],
                    //         'marks'                 => $val['marks'],
                    //         'grade'                 => $val['cgpa'],
                    //         'file_path'             => $path,
                    //         'student_id'            => $student_id,
                    //         'record_status'         => 'R',
                    //         'record_created_on'     => date('Y-m-d H:i:s'),
                    //         'record_created_by'     =>  $student_id
                    //     ]
                    // );
                    $this->insertQualificationInDB($val['qualification'],$val['qualification'],$val['boardName'],
                    $val['boardPassingYear'],$val['marks'],$val['cgpa'],$path,$student_id);
                $i++;
            } //foreach

            //send mail to candidate
            $mailData = array(
                'username' =>  $stuObj->email,
                'password' =>  $day . $month . $year,
            );
            Mail::to($stuObj->email)->send(new RegistrationMail($mailData));

            if ($sucees)
                return  response()->json(['message' => 'Dear Applicant you have Successfully Registered.'], 200);
        }
    } //fun

    /*
        candidate update registration
        ------------------------------
    */

    function updateRegistration(Request $request)
    {

        $encrypt_key = Config::get('encdec.ENCRYPT_KEY');
        $decrypt_key = Config::get('encdec.DECRYPT_KEY');
        $responce =  json_decode(json_decode($request->formData));
        $jsonData = Helper::CryptoJSAesDecrypt($decrypt_key, $responce);

        $req = json_decode($jsonData, true);
        $student_id  = $request->user()->id;
        if (!empty($student_id)) {
            //==========validation=============
            $validator = Validator::make(
                $req,
                [
                    "first_name"              => 'required',
                    "father_name"             => 'required',
                    "mother_name"             => 'required',
                    "mobile_no"               => 'required',
                    "correspondence_address"  => 'required',
                    "correspondence_pin"      => 'required',
                    "course_name"             => 'required',
                    "gender"                  => 'required',
                    "category"                => 'required',
                    "correspondence_country"  => 'required',
                    "correspondence_state"    => 'required',
                    "correspondence_city"     => 'required',
                ]
            );

            if ($validator->fails())
                return  response()->json(['error' => $validator->errors()->all()], 401);
            //=======student model for data binding===
            $stuObj =    Student::find($student_id);

            $stuObj->first_name              = $req['first_name'];
            $stuObj->last_name               = $req['last_name'];
            $stuObj->father_name             = $req['father_name'];
            $stuObj->mother_name             = $req['mother_name'];
            $stuObj->mobile_no               = $req['mobile_no'];
            $stuObj->gender                  = $req['gender'];
            $stuObj->category                = $req['category'];
            // $stuObj->aadhar_no               = $req['aadhar_no'];
            $stuObj->course_code             = $req['course_name'];
            
            if($req["approve_reject_status"]==="R" || $req["approve_reject_status"]==="M"){
            $stuObj->approve_reject_status  =   "M";
            $stuObj->request_status         =   "Modified";
            }   
            if($req["approve_reject_status"]==="N" || $req["approve_reject_status"]==="U"){
            $stuObj->approve_reject_status  =   "U";
            $stuObj->request_status         =   "Updated";
            }
            $stuObj->correspondence_country  = $req['correspondence_country'];
            $stuObj->correspondence_state    = $req['correspondence_state'];
            $stuObj->correspondence_city     = $req['correspondence_city'];
            $stuObj->correspondence_address  = $req['correspondence_address'];
            $stuObj->correspondence_pin      = $req['correspondence_pin'];
            $stuObj->whatsapp_no             = $req['whatsapp_no'];
            $stuObj->twitter_id              = $req['twitter_id'];
            $stuObj->facebook_id             = $req['facebook_id'];
            $stuObj->instagram_id            = $req['instagram_id'];
            $stuObj->record_updated_on       = date('Y-m-d H:i:s');
            $stuObj->client_info             = Helper::clientInfo();


            $stuObj->ip = getenv('HTTP_CLIENT_IP') ?:
                getenv('HTTP_X_FORWARDED_FOR') ?:
                getenv('HTTP_X_FORWARDED') ?:
                getenv('HTTP_FORWARDED_FOR') ?:
                getenv('HTTP_FORWARDED') ?:
                getenv('REMOTE_ADDR');

            //update data into student table
            $sucees =   $stuObj->save();

            //===========for candidate photo========
            if ($request->hasFile('candidatePhoto')) {

                $validator = Validator::make($request->all(), [
                    'candidatePhoto'    => 'image|mimes:jpeg,png,jpg|max:2048',
                ]);

                //validation check
                if ($validator->fails())
                    return  response()->json(['error' => $validator->errors()->all()], 401);

                $sfileObj = DB::table('t_student_file_uploads')
                    ->select()
                    ->where(["student_id" => $student_id])
                    ->where('document_name', 'candidate_photo')
                    ->get()->first();

                $extension = $request->candidatePhoto->extension();
                $file = uniqid() . "_" . time() . "_" . $student_id . "." . $extension;
                $path = $this->storage_root . "profile/";
                $request->file('candidatePhoto')->storeAs($path, $file, $this->storage_disk);
                $path   = $this->aws_bucket_url . $path . $file;
                $candidatePhoto =  $path;

                DB::table('t_student_file_uploads')->where('id', $sfileObj->id)->update(
                    [
                        'file_path'          => $candidatePhoto,
                        'record_updated_on'  => date('Y-m-d H:i:s'),
                        'record_updated_by'  =>  $student_id,
                    ],
                );
            }

            //============for candidate categoryFile=======
            if ($request->hasFile('categoryFile')) {

                $validator = Validator::make($request->all(), [
                    'categoryFile'      => 'file|mimes:jpg,png,jpeg,pdf|max:2048',
                ]);

                //validation check
                if ($validator->fails())
                    return  response()->json(['error' => $validator->errors()->all()], 401);

                $sfileObj = DB::table('t_student_file_uploads')
                    ->select()
                    ->where(["student_id" => $student_id])
                    ->where('document_name', 'caste_file')
                    ->get()->first();

                $extension = $request->categoryFile->extension();
                $file = uniqid() . "_" . time() . "_" . $student_id . "." . $extension;
                $path = $this->storage_root . "category/";
                $request->file('categoryFile')->storeAs($path, $file, $this->storage_disk);
                $path   = $this->aws_bucket_url . $path . $file;
                $casteFile =  $path;

                if(!empty($sfileObj)){
                    DB::table('t_caste_filestudent_file_uploads')->where('id', $sfileObj->id)->update(
                        [
                            'file_path'     => $casteFile,
                            'record_updated_on'  => date('Y-m-d H:i:s'),
                            'record_updated_by'  =>  $student_id,
                        ],
                    );
                }else{
                    DB::table('t_student_file_uploads')->insert(
                    [
                        'document_name' => 'caste_file',
                        'file_path'     => $casteFile,
                        'student_id'    => $student_id,
                        'record_created_on'     => date('Y-m-d H:i:s'),
                        'record_created_by'     =>  $student_id,
                        'record_status' => 'U'
                    ],
                    );
                }

            } //if

            //==========for candidate aadharFile upload ===========
            if ($request->hasFile('aadharFile')) {
                $validator = Validator::make($request->all(), [
                    'aadharFile'        => 'file|mimes:jpg,png,jpeg,pdf|max:2048',
                ]);

                //validation check
                if ($validator->fails())
                    return  response()->json(['error' => $validator->errors()->all()], 401);

                $sfileObj = DB::table('t_student_file_uploads')
                    ->select()
                    ->where(["student_id" => $student_id])
                    ->where('document_name', 'aadhar_file')
                    ->get()->first();
                $extension = $request->aadharFile->extension();
                $file = "aadhaar_" . uniqid() . "_" . time() . "_" . $student_id . "." . $extension;
                $path = $this->storage_root . "identity_file/";
                $request->file('aadharFile')->storeAs($path, $file, $this->storage_disk);
                $path   = $this->aws_bucket_url . $path . $file;
                $aadharFile =  $path;

                DB::table('t_student_file_uploads')->where('id', $sfileObj->id)->update(
                    [
                        'file_path'          => $aadharFile,
                        'record_updated_on'  => date('Y-m-d H:i:s'),
                        'record_updated_by'  =>  $student_id,
                    ],
                );
            } //if

            //============for 10th marksheet file upload  =======
            if ($request->hasFile('marksheetFile10')) {

                $validator = Validator::make($request->all(), [
                    'marksheetFile10'      => 'file|mimes:jpg,png,jpeg,pdf|max:2048',
                ]);

                //validation check
                if ($validator->fails())
                    return  response()->json(['error' => $validator->errors()->all()], 401);

                $extension = $request->marksheetFile10->extension();
                $file = uniqid() . "_" . time() . "_" . $student_id . "." . $extension;
                $path = $this->storage_root . "qualification/";
                $request->file('marksheetFile10')->storeAs($path, $file, $this->storage_disk);
                $path   = $this->aws_bucket_url . $path . $file;
                $marksheetFile10 =  $path;
            }
            //============ for 12th marksheet upload===========
            if ($request->hasFile('marksheetFile12')) {
                $validator = Validator::make($request->all(), [
                    'marksheetFile12'      => 'file|mimes:jpg,png,jpeg,pdf|max:2048',
                ]);

                //validation check
                if ($validator->fails())
                    return  response()->json(['error' => $validator->errors()->all()], 401);

                $extension = $request->marksheetFile12->extension();
                $file = uniqid() . "_" . time() . "_" . $student_id . "." . $extension;
                $path = $this->storage_root . "qualification/";
                $request->file('marksheetFile12')->storeAs($path, $file, $this->storage_disk);
                $path   = $this->aws_bucket_url . $path . $file;
                $marksheetFile12 =  $path;
            }
            
            if(array_key_exists('qualification_id10',$req) && $req['qualification_id10']==null){

                if(isset($marksheetFile10))
                    $marksheet = $marksheetFile10;
                else
                    $marksheet = null;
                if($req['boardName10']!=null && $req['boardPassingYear10']!=null){
                   
                    // DB::table('t_qualifications')->insert(
                    //         [
                    //             'qualification_code'    => $req['qualification10'],
                    //             'qualification_name'    => $req['qualification10'],
                    //             'board_name'            => $req['boardName10'],
                    //             'passing_year'          => $req['boardPassingYear10'],
                    //             'marks'                 => $req['marks10'],
                    //             'grade'                 => $req['cgpa10'],
                    //             'student_id'            => $student_id,
                    //             'file_path'             => $marksheet,
                    //             'record_status'         => 'R',
                    //             'record_created_on'     => date('Y-m-d H:i:s'),
                    //             'record_created_by'     => $student_id
                    //         ]
                    //     );
                    $this->insertQualificationInDB($req['qualification10'],$req['qualification10'],$req['boardName10'],
                    $req['boardPassingYear10'],$req['marks10'],$req['cgpa10'],$marksheet,$student_id);
                    
                }
            }else if(array_key_exists('qualification_id10',$req) && $req['qualification_id10']!=null){
                
                // DB::table('t_qualifications')->where('qualification_id', $req['qualification_id10'])->update(
                //     [
                //         'qualification_code'    => $req['qualification10'],
                //         'qualification_name'    => $req['qualification10'],
                //         'board_name'            => $req['boardName10'],
                //         'passing_year'          => $req['boardPassingYear10'],
                //         'marks'                 => $req['marks10'],
                //         'grade'                 => $req['cgpa10'],
                //         'student_id'            => $student_id,
                //         'record_status'         => 'R',
                //         'record_created_on'     => date('Y-m-d H:i:s'),
                //         'record_created_by'     => $student_id
                //     ]
                // );
                $this->updateQualificationInDB($req['qualification_id10'], $req['qualification10'],$req['qualification10'],$req['boardName10'],
                $req['boardPassingYear10'],$req['marks10'],$req['cgpa10'],$student_id);
                if(isset($marksheetFile10)){
                    // DB::table('t_qualifications')->where('qualification_id', $req['qualification_id10'])->update(
                    //     [
                    //         'file_path'    => $marksheetFile10
                    //     ]
                    // );
                    $this->updateQualificationFilePath($req['qualification_id10'], $marksheetFile10);
                }
            }
            // for 12 qualification update
            if(array_key_exists('qualification_id12',$req) && $req['qualification_id12']==null){
                if(isset($marksheetFile12))
                    $marksheet = $marksheetFile12;
                else
                    $marksheet = null;
                    
                if($req['boardName12']!=null && $req['boardPassingYear12']!=null){
                    // DB::table('t_qualifications')->insert(
                    //     [
                    //         'qualification_code'    => $req['qualification12'],
                    //         'qualification_name'    => $req['qualification12'],
                    //         'board_name'            => $req['boardName12'],
                    //         'passing_year'          => $req['boardPassingYear12'],
                    //         'marks'                 => $req['marks12'],
                    //         'grade'                 => $req['cgpa12'],
                    //         'file_path'             => $marksheet,
                    //         'student_id'            => $student_id,
                    //         'record_status'         => 'R',
                    //         'record_created_on'     => date('Y-m-d H:i:s'),
                    //         'record_created_by'     => $student_id
                    //     ]
                    // );
                    $this->insertQualificationInDB($req['qualification12'],$req['qualification12'],$req['boardName12'],
                    $req['boardPassingYear12'],$req['marks12'],$req['cgpa12'],$marksheet,$student_id);
                }
            }else if(array_key_exists('qualification_id12',$req) && $req['qualification_id12']!=null){

                // DB::table('t_qualifications')->where('qualification_id', $req['qualification_id12'])->update(
                //     [
                //         'qualification_code'    => $req['qualification12'],
                //         'qualification_name'    => $req['qualification12'],
                //         'board_name'            => $req['boardName12'],
                //         'passing_year'          => $req['boardPassingYear12'],
                //         'marks'                 => $req['marks12'],
                //         'grade'                 => $req['cgpa12'],
                //         'student_id'            => $student_id,
                //         'record_status'         => 'R',
                //         'record_created_on'     => date('Y-m-d H:i:s'),
                //         'record_created_by'     => $student_id
                //     ]
                // );
                $this->updateQualificationInDB($req['qualification_id12'], $req['qualification12'],$req['qualification12'],$req['boardName12'],
                $req['boardPassingYear12'],$req['marks12'],$req['cgpa12'],$student_id);
                if(isset($marksheetFile12)){
                    // DB::table('t_qualifications')->where('qualification_id', $req['qualification_id12'])->update(
                    //     [
                    //         'file_path'    => $marksheetFile12
                    //     ]
                    // );
                    $this->updateQualificationFilePath($req['qualification_id12'], $marksheetFile12);
                }
            }
            if ($sucees)
                $string_json_fromPHP = Helper::CryptoJSAesEncrypt($encrypt_key, json_encode(['message' => 'Your profile has been successfully updated.'], 200));
            return  response()->json($string_json_fromPHP);
        } //if

    } //fun

    function uploadFile(Request $request){

        if ($request->hasFile('marksheetFile10')) {
            $file = $request->marksheetFile10;
            //===========validation for files=========
            $validator = Validator::make($request->all(), [
                'marksheetFile10'       => 'required|file|mimes:jpg,png,jpeg,pdf|max:2048',
            ]);
            //validation check
            if ($validator->fails())
                return  response()->json(['error' => $validator->errors()->all()], 401);
 
            $extension = $file->extension();


            $filename  = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            $filename = "10_".uniqid() . "_" . time() . "." . $extension;
            $path = $this->storage_root . "temp_qualification/";
            $file->storeAs($path, $filename, $this->storage_disk);
            $file  = $this->aws_bucket_url . $path . $filename;
            return response()->json($file, 200);
        }
        elseif ($request->hasFile('marksheetFile12')) {
            $file = $request->marksheetFile12;
            //===========validation for files=========
            $validator = Validator::make($request->all(), [
                'marksheetFile12'       => 'required|file|mimes:jpg,png,jpeg,pdf|max:2048',
            ]);
            //validation check
            if ($validator->fails())
                return  response()->json(['error' => $validator->errors()->all()], 401);

            $extension = $file->extension();
            $filename  = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = "12_".uniqid() . "_" . time() . "." . $extension;
            $path = $this->storage_root . "temp_qualification/";
            $file->storeAs($path, $filename, $this->storage_disk);
            $file  = $this->aws_bucket_url . $path . $filename;
            return  response()->json($file, 200);
        }
        elseif ($request->hasFile('categoryFile')) {
            $file = $request->categoryFile;
            //===========validation for files=========
            $validator = Validator::make($request->all(), [
                'categoryFile'       => 'required|file|mimes:jpg,png,jpeg,pdf|max:2048',
            ]);
            //validation check
            if ($validator->fails())
                return  response()->json(['error' => $validator->errors()->all()], 401);

            $extension = $file->extension();
            $filename  = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = "cast_".uniqid() . "_" . time() . "." . $extension;
            $path = $this->storage_root . "temp_category/";
            $file->storeAs($path, $filename, $this->storage_disk);
            $file  = $this->aws_bucket_url . $path . $filename;
            return  response()->json($file, 200);
        }
        elseif ($request->hasFile('aadharFile')) {
            $file = $request->aadharFile;
            //===========validation for files=========
            $validator = Validator::make($request->all(), [
                'aadharFile'       => 'required|file|mimes:jpg,png,jpeg,pdf|max:2048',
            ]);
            //validation check
            if ($validator->fails())
                return  response()->json(['error' => $validator->errors()->all()], 401);

            $extension = $file->extension();
            $filename  = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = "aadhaar_".uniqid() . "_" . time() . "." . $extension;
            $path = $this->storage_root . "temp_identity_file/";
            $file->storeAs($path, $filename, $this->storage_disk);
            $file  = $this->aws_bucket_url . $path . $filename;
            return  response()->json($file, 200);
        }
        elseif ($request->hasFile('candidatePhoto')) {
            $file = $request->candidatePhoto;
            //===========validation for files=========
            $validator = Validator::make($request->all(), [
                'candidatePhoto'       => 'required|file|mimes:jpg,png,jpeg|max:2048',
            ]);
            //validation check
            if ($validator->fails())
                return  response()->json(['error' => $validator->errors()->all()], 401);

            $extension = $file->extension();
            $filename  = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = "photo_".uniqid() . "_" . time() . "." . $extension;
            $path = $this->storage_root . "temp_profile/";
            $file->storeAs($path, $filename, $this->storage_disk);
            $file  = $this->aws_bucket_url . $path . $filename;
            return  response()->json($file, 200);
        }
    }
    
    function insertQualificationInDB(String $qualificationCode, String $qualificationName, String $boardName, String $passingYear
        , String $marks = null, String $cgpa = null, String $filePath, String $studentId){
        if(!empty($marks) && empty($cgpa)){
            DB::table('t_qualifications')->insert(
                [
                    'qualification_code'    => $qualificationCode,
                    'qualification_name'    => $qualificationName,
                    'board_name'            => $boardName,
                    'passing_year'          => $passingYear,
                    'marks'                 => $marks,
                    'grade'                 => null,
                    'student_id'            => $studentId,
                    'file_path'             => $filePath,
                    'record_status'         => 'R',
                    'record_created_on'     => date('Y-m-d H:i:s'),
                    'record_created_by'     => $studentId
                ]
            );
        }
        elseif(empty($marks) && !empty($cgpa)){
            DB::table('t_qualifications')->insert(
                [
                    'qualification_code'    => $qualificationCode,
                    'qualification_name'    => $qualificationName,
                    'board_name'            => $boardName,
                    'passing_year'          => $passingYear,
                    'marks'                 => null,
                    'grade'                 => $cgpa,
                    'student_id'            => $studentId,
                    'file_path'             => $filePath,
                    'record_status'         => 'R',
                    'record_created_on'     => date('Y-m-d H:i:s'),
                    'record_created_by'     => $studentId
                ]
            );
        }
        elseif(!empty($marks) && !empty($cgpa)){
            DB::table('t_qualifications')->insert(
                [
                    'qualification_code'    => $qualificationCode,
                    'qualification_name'    => $qualificationName,
                    'board_name'            => $boardName,
                    'passing_year'          => $passingYear,
                    'marks'                 => $marks,
                    'grade'                 => $cgpa,
                    'student_id'            => $studentId,
                    'file_path'             => $filePath,
                    'record_status'         => 'R',
                    'record_created_on'     => date('Y-m-d H:i:s'),
                    'record_created_by'     => $studentId
                ]
            );
        }
       
    }

    function updateQualificationInDB(String $qualificationId, String $qualificationCode, String $qualificationName, String $boardName, String $passingYear
        , String $marks = null, String $cgpa = null, String $studentId){
        if(!empty($marks) && empty($cgpa)){
            DB::table('t_qualifications')->where('qualification_id', $qualificationId)->update(
                [
                    'qualification_code'    => $qualificationCode,
                    'qualification_name'    => $qualificationName,
                    'board_name'            => $boardName,
                    'passing_year'          => $passingYear,
                    'marks'                 => $marks,
                    'grade'                 => null,
                    'student_id'            => $studentId,
                    'record_status'         => 'R',
                    'record_created_on'     => date('Y-m-d H:i:s'),
                    'record_created_by'     => $studentId
                ]
            );
        }
        elseif(empty($marks) && !empty($cgpa)){
            DB::table('t_qualifications')->where('qualification_id', $qualificationId)->update(
                [
                    'qualification_code'    => $qualificationCode,
                    'qualification_name'    => $qualificationName,
                    'board_name'            => $boardName,
                    'passing_year'          => $passingYear,
                    'marks'                 => null,
                    'grade'                 => $cgpa,
                    'student_id'            => $studentId,
                    'record_status'         => 'R',
                    'record_created_on'     => date('Y-m-d H:i:s'),
                    'record_created_by'     => $studentId
                ]
            );
        }
        elseif(!empty($marks) && !empty($cgpa)){
            DB::table('t_qualifications')->where('qualification_id', $qualificationId)->update(
                [
                    'qualification_code'    => $qualificationCode,
                    'qualification_name'    => $qualificationName,
                    'board_name'            => $boardName,
                    'passing_year'          => $passingYear,
                    'marks'                 => $marks,
                    'grade'                 => $cgpa,
                    'student_id'            => $studentId,
                    'record_status'         => 'R',
                    'record_created_on'     => date('Y-m-d H:i:s'),
                    'record_created_by'     => $studentId
                ]
            );
        }
       
    }
    function updateQualificationFilePath(String $qualificationId, String $filePath){
        DB::table('t_qualifications')->where('qualification_id', $qualificationId)->update(
            [
                'file_path'    => $filePath
            ]
        );
    }
}
