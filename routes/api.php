<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\EnrollController;
use App\Http\Controllers\CountryStateCityController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudViewController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\ApprovedCandidateController;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationMail;

/*---------------------------------------------------------------------
    =====================Admin (User)=================================
---------------------------------------------------------------------*/

Route::post('userlogin',[App\Http\Controllers\Auth\UserAuthController::class,'login']);

Route::middleware('auth:admin')->group(function () {
    Route::get('get-candidate-document/{id}/{document_type}',[CandidateController::class,'getCandidateDocumentById']);
    Route::get('get-totalstudent-count', [StudViewController::class, 'getTotalStudentCount']);
    Route::get('get-student-list/{start_date}/{end_date}/{status}/{course_type}', [StudViewController::class, 'getStudentList']);
    Route::get('get-student-details/{id}', [StudViewController::class, 'getStudentDetails']);
    Route::get('get-student-qualification/{id}', [StudViewController::class, 'getStudentQualification']);

    Route::get('get-student-count-coursewise', [StudViewController::class, 'getStudentCountCourseWise']);
    Route::get('get-student-status-count-coursewise/{id}/{id1}', [StudViewController::class, 'getStudentStatusCountCourseWise']);

    //Route::get('get-student-list-coursewise/{course_code}', [StudViewController::class, 'getStudentListCourseWise']);
    Route::post('get-student-list-coursewise/{course_code}/{status_id}/{status_id1}/{start_date}/{end_date}', [StudViewController::class, 'getStudentListCourseWise']);

    Route::get('get-student-list-coursestatuswise/{course_code}/{status_id }', [StudViewController::class, 'getStudentListCourseStatusWise']);

    //Route::get('get-student-pending-list', [StudViewController::class, 'getStudentApproveList']);
    Route::get('get-student-pending-count', [StudViewController::class, 'getStudentPendingCount']);

    Route::post('student-approved/{id}', [StudViewController::class, 'StudentApprove']);
    Route::get('get-student-approved-list', [StudViewController::class, 'getStudentApproveList']);
    Route::get('get-student-approved-count', [StudViewController::class, 'getStudentApproveCount']);

    Route::post('student-reject/{id}', [StudViewController::class, 'StudentReject']);
    Route::get('get-student-reject-count', [StudViewController::class, 'getStudentRejectCount']);

    Route::get('get-student-eligible-count', [StudViewController::class, 'getStudentEligibleCount']);

    Route::get('get-student-feepaid-count', [StudViewController::class, 'getStudentFeePaidCount']);

    Route::get('get-student-modified-count', [StudViewController::class, 'getStudentModifiedCount']);

    Route::post('student-eligible/{id}', [StudViewController::class, 'StudentEligible']);
    Route::post('student-send-alert/{id}', [StudViewController::class, 'StudentSendAlert']);

    Route::post('userlogout',[App\Http\Controllers\Auth\UserAuthController::class,'logout']);

    Route::post('profile',[App\Http\Controllers\UserController::class,'getUserProfile']);
    Route::get('get-candidate-document/{id}',[StudViewController::class,'getCandidateDocumentByIdAdminSide']);
    Route::get('get-student-list-course-wise-excel/{course_code}/{status_id}/{status_id1}/{start_date}/{end_date}',[StudViewController::class,'getStudentListCourseWiseExcel']);
    Route::get('get-all-student-list-excel/{start_date}/{end_date}/{status}/{course_type}', [StudViewController::class, 'getStudentListExcel']);
    
  });
/*==================================End User================================*/


/*---------------------------------------------------------------------
    =====================Candidate (Student)===========================
----------------------------------------------------------------------*/
//----------registration---------
Route::post('register',[RegistrationController::class,'register']);
Route::post('upload-file',[RegistrationController::class,'uploadFile']);
// Route::post('register',[RegistrationController::class,'register']);

//------- student Auth ------------
Route::post('login',[StudentAuthController::class,'login']);

//Route::post('candidate-enrollno/{id}',[CandidateController::class,'getCandidateEnrollNumber']);
Route::middleware('auth:candidate')->group(function () {
    Route::get('candidate-profile/{id}',[CandidateController::class,'getProfile']);
    Route::get('candidate-qualification/{id}',[CandidateController::class,'getQualificationById']);
    Route::get('candidate-document/{id}/{document_type}',[CandidateController::class,'getCandidateDocumentById']);
    Route::post('candidate-enrollno/{id}',[CandidateController::class,'getCandidateEnrollNumber']);
    Route::post('candidate-update',[RegistrationController::class,'updateRegistration']);

    Route::get('approved-candidate-details/{id}',[CandidateController::class,'getApprovedCandidateDetails']);
   // Route::get('get-appcandidate-details/{id}', [StudViewController::class, 'getStudentDetails']);
    Route::get('get-appcandidate-qualification/{id}', [StudViewController::class, 'getStudentQualification']);
    Route::post('logout',[StudentAuthController::class,'logout']);
});
Route::get('get-notifications/{id}', [CandidateController::class, 'getNotificationList']);
   
/* ========================end candidate===========================*/

  /*---------------------------------------------------------------------
    =====================Approved Candidate (Student)===========================
  ----------------------------------------------------------------------*/
Route::post('candidate-login',[StudentAuthController::class,'candidate_login']);
Route::middleware('auth:approved_candidate')->group(function () {
  Route::get('approved-candidate-profile',[CandidateController::class,'getProfile']);
  Route::get('approved-candidate-qualification/{id}',[CandidateController::class,'getQualificationById']);
  Route::get('approved-candidate-document/{id}/{document_type}',[CandidateController::class,'getCandidateDocumentById']);
  Route::post('approved-candidate-enrollno/{id}',[CandidateController::class,'getCandidateEnrollNumber']);
  Route::post('approved-candidate-update',[RegistrationController::class,'updateRegistration']);

  Route::get('approved-candidate-details/{id}',[CandidateController::class,'getApprovedCandidateDetails']);
 // Route::get('get-appcandidate-details/{id}', [StudViewController::class, 'getStudentDetails']);
  Route::get('get-appcandidate-qualification/{id}', [StudViewController::class, 'getStudentQualification']);
  Route::post('candidate-logout',[StudentAuthController::class,'candidate_logout']);
});
/* ========================end of approved candidate============================ */



  /*---------------------------------------------------------------------
    =====================Common routes===========================
----------------------------------------------------------------------*/
Route::get('get-course', [CourseController::class, 'getCourse']);
Route::get('get-course-type/{id}', [CourseController::class, 'getCourseType']);
Route::get('get-country', [CountryStateCityController::class, 'getCountry']);
Route::get('country-state-city', [CountryStateCityController::class, 'index']);
Route::post('get-states-by-country/{id}', [CountryStateCityController::class, 'getState']);
Route::post('get-cities-by-state/{id}', [CountryStateCityController::class, 'getCity']);

/* ========================end Common routes===========================*/


Route::get('access_denied',function(){
    return response()->json(['status'=>'Unauthorised']);
})->name('login');



/*================journals=============================*/
use App\Http\Controllers\journals\JrnlUserAuth;
use App\Http\Controllers\journals\MenuScript;
use App\Http\Controllers\journals\PaperController;
use App\Http\Controllers\journals\JournalsController;
use App\Http\Controllers\journals\ReportController;
use App\Http\Controllers\journals\VolumeController;
use App\Http\Controllers\journals\JrnlCmsController;
use App\Http\Controllers\journals\NotificationController;

Route::prefix('journals')->group(function () {
  Route::post('/user-signup',[JrnlUserAuth::class,'authorRegister']);
  Route::post('/user-login',[JrnlUserAuth::class,'authorLogin']);
  Route::post('/login',[JrnlUserAuth::class,'adminLogin']);
  Route::get('/get-papertype', [PaperController::class,'getPaperType']);
  Route::get('/get-journal-category', [JournalsController::class,'getJournalCatagory']);
  Route::get('/get-journal-subcategory/{id}', [JournalsController::class,'getJournalSubcatagory']);
  Route::get('/get-journal-list', [JournalsController::class,'getJournalList']);
  Route::get('/get-journal-list/{category_id}', [JournalsController::class,'getJournalListByCategory']);
  Route::get('/get-manuscript-list/{id}', [JournalsController::class,'getManuscriptList']);
  Route::get('/get-manuscript/{id}', [JournalsController::class,'getManuscript']);
  Route::get('/get-category', [JournalsController::class,'getCategory']);
  Route::get('/get-journals/{category_id}', [JournalsController::class,'getJournalsByCategory']);
  Route::get('/get-papers/{author_id}', [PaperController::class,'getPapersByAuthor']);
  Route::get('/get-author-guideline/{journal_id}',[JrnlCmsController::class,'getAuthorGuidline']);
  Route::get('/get-call-for-menuscript/{journal_id}',[JrnlCmsController::class,'getCallForMenuscript']);
  Route::get('/get-editorial-workflow/{journal_id}',[JrnlCmsController::class,'getEditorialWorkflow']);
  Route::get('/show-category/{category_id}', [JournalsController::class,'showCategory']);
  Route::get('/get-author-remark/{author_id}',[NotificationController::class,'getAuthorRemark']);
  Route::get('/get-author-remark-count/{author_id}',[NotificationController::class,'getAuthorRemarkCount']);
  Route::get('/view-paper/{paper_id}',[PaperController::class,'viewPaper']);
  Route::get('/get-jrnl-setting/{key}',[JrnlCmsController::class,'getJrnlSettings']);
  Route::get('/papers-status-wise/{author_id}/{paper_status}', [ReportController::class,'ListPapersStatusWise']);
});



/*===================Journals Author=================*/
Route::middleware('auth:jnrl_author')->prefix('journals')->group(function () {
  Route::post('/add-menuscript',[MenuScript::class,'addMenuScript']);
  Route::post('/user-logout',[JrnlUserAuth::class,'authorLogout']);
  Route::get('/count-papers/{author_id}/{paper_status}', [ReportController::class,'countPapers']);
  Route::get('/view-paper/{paper_id}',[PaperController::class,'viewPaper']);
  Route::get('/view-author/{author_id}',[JrnlUserAuth::class,'viewAuthor']);
  Route::get('/update-author-profile',[JrnlUserAuth::class,'updateAuthorProfile']);
});
  

/*===================Journals admin=================*/
Route::middleware('auth:jnrl_admin')->prefix('journals')->group(function () {
  Route::post('/logout',[JrnlUserAuth::class,'adminLogout']);
  Route::get('/custome-count-reports',[ReportController::class,'customeCountReport']);
  Route::post('/send-remark', [JournalsController::class,'sendRemark']);
  Route::post('/mark-review-status', [JournalsController::class,'markReviewStatus']);
  Route::get('/get-rtp-manuscript-list/{id}', [JournalsController::class,'getReadyToPublishManuscriptList']);
 
  Route::post('/register-volume',[VolumeController::class,'registerVolume']);
  Route::get('/get-volume-list/{journal_id}',[VolumeController::class,'getVolumeList']);
  Route::get('/get-volume-list-short/{journal_id}',[VolumeController::class,'getVolumeShortList']);
  Route::get('/get-journal/{journal_id}', [JournalsController::class,'getJournalById']);
  Route::post('/publish-volume',[VolumeController::class,'publishVolume']);
  Route::post('/add-author-guideline',[JrnlCmsController::class,'addAuthorGuidline']);
  Route::post('/add-call-for-menuscript',[JrnlCmsController::class,'addCallForMenuscript']);
  Route::post('/add-editorial-workflow',[JrnlCmsController::class,'addEditorialWorkflow']);
  Route::post('/register-paper-type',[PaperController::class,'addPaperType']);
  Route::get('/get-paper-list/{status}',[PaperController::class,'getPapersListByStatus']);
  Route::get('/get-author-remark',[NotificationController::class,'getAuthorRemark']);
  Route::get('/get-author-list',[JrnlUserAuth::class,'getAuthorList']);
  Route::post('/add-new-editor',[JrnlUserAuth::class,'addEditorUser']);
  Route::get('/get-paper-list',[PaperController::class,'getPapersList']);
 
  Route::get('/view-editor/{user_id}',[JrnlUserAuth::class,'viewEditor']);
  Route::get('/view-user/{user_id}',[JrnlUserAuth::class,'viewUser']);
  Route::get('/get-notice-list',[NotificationController::class,'getNoticeList']);
  Route::get('/view-notice/{notice_id}',[NotificationController::class,'viewNotice']);
  
  Route::post('/register-category',[JournalsController::class,'storeJrnlCategory']);
  Route::post('/update-category',[JournalsController::class,'storeJrnlCategory']);
  Route::get('/delete-category/{category}',[JournalsController::class,'deleteJrnlCategory']);
  Route::post('/register-journal',[JournalsController::class,'storeJournal']);
  Route::post('/update-journal', [JournalsController::class,'storeJournal']);
  Route::get('/delete-journal/{journal_id}', [JournalsController::class,'deleteJournal']);

  Route::get('/delete-call-for-manuscript/{id}',[JrnlCmsController::class,'deleteCallForMenuscript']);
  Route::get('/delete-author-guideline/{id}',[JrnlCmsController::class,'deleteAuthorGuideline']);
  Route::get('/delete-editorial-workflow/{id}',[JrnlCmsController::class,'deleteEditorialWorkflow']);
  Route::post('/update-author-guideline',[JrnlCmsController::class,'updateAuthorGuideline']);
  Route::post('/update-call-for-manuscript',[JrnlCmsController::class,'updateCallForManuscript']);
  Route::post('/update-editorial-workflow',[JrnlCmsController::class,'updateEditorialWorkflow']);
  Route::post('/register-aboutus',[JrnlCmsController::class,'storeAboutus']);
  Route::post('/register-contactus',[JrnlCmsController::class,'storeContactus']);
  Route::get('/view-paper-type/{paper_type_id}', [PaperController::class,'viewPapertype']);
  Route::get('/delete-paper-type/{paper_type_id}', [PaperController::class,'deletePaperType']);
  Route::post('/update-editor',[JrnlUserAuth::class,'updateEditor']);
  Route::get('/delete-editor/{user_id}',[JrnlUserAuth::class,'deleteUser']);
  Route::post('/add-new-user',[JrnlUserAuth::class,'addNewUser']);

  Route::get('/get-user-role',[JrnlUserAuth::class,'getUserRole']);
  Route::get('/get-user-list',[JrnlUserAuth::class,'getUserList']);
  Route::get('/get-editor-list',[JrnlUserAuth::class,'getEditorList']);

});



/*=====================testing Routes====================*/
use App\Http\Controllers\TestController;
 Route::get('pass',[TestController::class,'view_file']);





