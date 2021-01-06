<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Support\Facades\Hash;
use App\EmailTemplates;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Mail;
use Session;
use App\Api\User;
use App\Grade;
use App\Subject;
use App\Theme;
use App\Lesson;
use App\LessonUser;
use App\MapLessonUser;
use App\LessonsModification;
use App\MapUserTarget;
use App\LearningTargets;
use App\TempPdf;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use mikehaertl\wkhtmlto\Pdf;

class ApiLessonUserController extends Controller {

    private $req;
    private $user;
    private $jwtAuth;

    public function __construct(Request $request, User $user, ResponseFactory $responseFactory, JWTAuth $jwtAuth) {
        header('Content-Type: application/json');
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
        $this->req = $request;
        $this->res = $responseFactory;
        $this->learningObj = new LearningTargets();
    }

     /*
     * Added By: Karnik 
     * Reason: save modification tab
     */

    public function saveModificationTab(Request $request) {
        //print_r($request->all()); //exit;
        DB::beginTransaction();


        $modification = LessonsModification::where('user_id', '=', $request->user_id)->where('lesson_id', '=', $request->lesson_id)->first();
        //$userlesson = LessonUser::where('user_id', '=', $request->user_id)->first();
        // DB::enableQueryLog();

        if ($modification === null) {
            $modification = new LessonsModification;
            $modification->lesson_id = $request->lesson_id;
            $modification->user_id = $request->user_id;
            $modification->content = $request->content;            
            $modification->save();

            if ($modification == true) {
                DB::commit();
                $this->resultapi('1', 'Modifications template saved!', array("last_id" => $modification->id));
            } else {
                DB::rollBack();
                $this->resultapi('0', 'Something went wrong with server,Please try again');
            }
        } else {
            //$modification_update = new LessonUser;
            $modification_update = LessonsModification::where('lesson_id', '=', $request->lesson_id)
                    ->where('user_id', '=', $request->user_id)
                    ->first();
            //print_r($modification_update); exit;
            if (!empty($modification_update)) { //print_r($request->content);
                $modification_update->content = $request->content;
                $modification_update->save();
            }
            if ($modification_update == true) {
                DB::commit();
                $this->resultapi('1', 'Modifications template saved!', array("last_id" => $modification_update->id));
            } else {
                DB::rollBack();
                $this->resultapi('0', 'Something went wrong with server,Please try again');
            }
        }
    }


    /*
     * Added By: Karnik 
     * Reason: Create Lessons By User
     */

    public function createLessonByUser(Request $request) {
        //print_r($request->all()); //exit;
        DB::beginTransaction();


        $userlesson = LessonUser::where('user_id', '=', $request->user_id)->where('lesson_id', '=', $request->lesson_id)->first();
        


        if ($userlesson === null) {
            $userlesson = new LessonUser;
            $userlesson->lesson_id = $request->lesson_id;
            $userlesson->user_id = $request->user_id;
            $userlesson->summary = $request->summary;
            $userlesson->standards = $request->standards;
            $userlesson->essential_questions = $request->essential_questions;
            $userlesson->core_ideas = $request->core_ideas;
            $userlesson->vocabulary = $request->vocabulary;
            $userlesson->lesson_sequence = $request->lesson_sequence;
            $userlesson->save();

            $mapuserlesson = new MapLessonUser;
            $mapuserlesson->ulessons_id = $userlesson->id;
            $mapuserlesson->keyconcept_id = implode(',', $request->keyconcept_id);
            $mapuserlesson->learningtarget_id = implode(',', $request->learningtarget_id);
            $mapuserlesson->save();

            // Delete content
            LessonsModification::where('lesson_id', $request->lesson_id)->delete();
            MapUserTarget::where('ulessons_id',$request->lesson_id)->whereNotIn('targetids',$request->learningtarget_id)->delete();

            if ($userlesson == true) {
                DB::commit();
                $this->resultapi('1', 'Outline template saved!', array("last_id" => $userlesson->id));
            } else {
                DB::rollBack();
                $this->resultapi('0', 'Something went wrong with server,Please try again');
            }
        } else {
            //$userlesson_update = new LessonUser;
            $userlesson_update = LessonUser::where('lesson_id', '=', $request->lesson_id)
                    ->where('user_id', '=', $request->user_id)
                    ->first();
            //print_r($userlesson_update); exit;
            if (!empty($userlesson_update)) {
                $userlesson_update->summary = $request->summary;
                $userlesson_update->standards = $request->standards;
                $userlesson_update->essential_questions = $request->essential_questions;
                $userlesson_update->core_ideas = $request->core_ideas;
                $userlesson_update->vocabulary = $request->vocabulary;
                $userlesson_update->lesson_sequence = $request->lesson_sequence; 
                $userlesson_update->save();
            }

            // Delete content
            LessonsModification::where('lesson_id', $request->lesson_id)->delete();
            MapUserTarget::where('ulessons_id',$request->lesson_id)->whereNotIn('targetids',$request->learningtarget_id)->delete();

            if ($userlesson_update == true) {
                DB::commit();
                $this->resultapi('1', 'Outline template saved!', array("last_id" => $userlesson_update->id));
            } else {
                DB::rollBack();
                $this->resultapi('0', 'Something went wrong with server,Please try again');
            }
        }
    }

    /*
     * Added By: Karnik 
     * Reason: Get Key Concepts Detail
     */

    public function getUserlesson(Request $request) {

        $lessonInfo = DB::table('lessons_users as lu')                
                ->leftJoin('map_users_targets as mut', 'lu.lesson_id', '=', 'mut.ulessons_id')
                ->select(DB::raw("GROUP_CONCAT(mut.targetids) AS learningtarget_id"),DB::raw("GROUP_CONCAT(mut.key_concept_id) AS keyconcept_id"))
                ->where('lu.lesson_id', '=', $request->lesson_id)
                ->where('lu.status', "Draft")
                ->groupBy('mut.ulessons_id')
                ->first();

        if (count($lessonInfo) > 0) {
            $learningids = explode(",", $lessonInfo->learningtarget_id);

            $learningTargetDetails = $this->learningObj->getLearningTargetById($learningids);

            $this->resultapi('1', 'Success', array("user_lessons" => $lessonInfo, "results" => $learningTargetDetails));
        } else {
            $this->resultapi('0', 'Something went wrong with server,Please try again');
        }


        //return  (array) $lessonInfo; 
    }

    public function createTempPdfData(Request $request) {
        //echo $request->user_id; exit;
        $pdfdata = TempPdf::where('user_id', '=', $request->user_id)->where('lesson_id', '=', $request->lesson_id)->first();
        $lesson = Lesson::find($request->lesson_id);
        //print_r($pdfdata);exit;
        $rand = rand(50000, 5225525);
       
        // You can pass a filename, a HTML string, an URL or an options array to the constructor
       
       $pdf = new Pdf(array(
            //'binary' => '/usr/bin/xvfb-run -- /usr/local/bin/wkhtmltopdf',
            //'ignoreWarnings' => true,
                'binary' => '/usr/local/bin/wkhtmltoimage',
                'ignoreWarnings' => true,

            

        ));
         
        $pdf->addPage(config('constant.base_url_new') . 'lessons/pdf/' . $request->lesson_id); 
        
       /* $pdf = new Pdf(config('constant.base_url_new').'lessons/pdf/'.$request->lesson_id);
        
        // On some systems you may have to set the path to the wkhtmltopdf executable
        $pdf->binary = '~/wkhtmltox/bin/wkhtmltopdf';*/

        if (!$pdf->saveAs('storage/pdf/'.$lesson->unit_title.'.pdf')) {
             echo $pdf->getError();
        }
        $pdf->send($lesson->unit_title.'.pdf');

        exit;
    }

    public function saveTempPdfData(Request $request) {

        $temppdfdata = TempPdf::where('user_id', '=', $request->user_id)->where('lesson_id', '=', $request->lesson_id)->first();

        if ($temppdfdata === null) {
            $temppdfdata = new TempPdf;
            $temppdfdata->lesson_id = $request->lesson_id;
            $temppdfdata->user_id = $request->user_id;
            $temppdfdata->temp_content = $request->temp_content;
            $temppdfdata->save();

            if ($temppdfdata == true) {
                DB::commit();
                $this->resultapi('1', 'Success', array("last_id" => $temppdfdata->id));
            } else {
                DB::rollBack();
                $this->resultapi('0', 'Something went wrong with server,Please try again');
            }
        } else {
            $temppdfdata_update = TempPdf::where('lesson_id', $request->lesson_id)
                    ->where('user_id', $request->user_id)
                    ->first();
            //printF($temppdfdata_update);        

            if (!empty($temppdfdata_update)) { 
                
                $temppdfdata_update->temp_content = $request->temp_content;
                $temppdfdata_update->save();
            }
            if ($temppdfdata_update == true) { 
                DB::commit();
                $this->resultapi('1', 'Success', array("last_id" => $temppdfdata_update->id));
            } else { 
                //DB::rollBack();
                $this->resultapi('0', 'Something went wrong with server,Please try again');
            }
        }
    }

    /*
     * Added By: Karnik 
     * Reason: temp pdf data
     */

    public function getTempPdf(Request $request) {

        $getpdfdata = DB::table('temp_pdf_data as tpdf')
                ->select('tpdf.summary', 'tpdf.standards', 'tpdf.essential_questions', 'tpdf.core_ideas', 'tpdf.vocabulary')
                ->where('tpdf.lesson_id', '=', $request->lesson_id)
                ->where('tpdf.user_id', '=', $request->user_id)
                ->first();

        if (count($getpdfdata) > 0) {
            $this->resultapi('1', 'Success', array("results" => $getpdfdata));
        } else {
            $this->resultapi('0', 'Something went wrong with server,Please try again');
        }
    }

    /*
     * Added By: Karnik 
     * Reason: Get Key Concepts Detail
     */

    public function deleteUserlesson(Request $request) {
        //print_r($request->lesson_id); exit;
        $lesson = Lesson::where('id', $request->lesson_id)->delete();
        LessonUser::where('lesson_id', $request->lesson_id)->delete();
        MapLessonUser::where('ulessons_id', $request->lesson_id)->delete();
        MapUserTarget::where('ulessons_id', $request->lesson_id)->delete();
        TempPdf::where('lesson_id', $request->lesson_id)->delete();

        if ($lesson === 1) {
            DB::commit();
            $this->resultapi('1', 'Success', $lesson);
        } else {
            DB::rollBack();
            $this->resultapi('0', 'Something went wrong with server,Please try again');
        }
    }

    function resultapi($status, $message, $result = array()) {
        $finalArray['STATUS'] = $status;
        $finalArray['MESSAGE'] = $message;
        $finalArray['DATA'] = $result;
        echo json_encode($finalArray);

        exit;
    }

}
