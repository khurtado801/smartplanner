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
use App\Api\User;
use App\Grade;
use App\Subject;
use App\Theme;
use App\Lesson;
use App\LessonUser;
use App\MapLessonUser;
use App\LearningTargets;
use App\LessonSequence;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiLearningTargetController extends Controller {

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
        $this->lessonObj = new Lesson();
    }

    /*
     * Added By: Karnik 
     * Reason: Get All Learning Targets
     */

    public function getAllLearningTargets(Request $request) {

        $countFilter = count($request->all());
        $learningTargetDetails = DB::table('learningtargets')
                ->select('id', 'name', 'overview_summary', 'standards', 'essential_questions', 'core_ideas', 'academic_vocabulary', 'status')
                ->orderBy('id', 'desc')
                ->where('status', 'Active')
                ->get();


        if (count($learningTargetDetails) > 0) {
            $this->resultapi('1', 'Learning Targets Found', $learningTargetDetails);
        } else {
            $this->resultapi('0', 'No Learning Targets Found', $learningTargetDetails);
        }
    }

    /*
     * Added By: Karnik 
     * Reason: Get All Learning Targets
     */

    public function getLearningTargetById(Request $request) {

        if ($request->all() && count($request->all()) > 0) {
            
            $learningtarget_id = $request->learningtarget_id;
            $learningTargetDetails = $this->learningObj->getLearningTargetById($learningtarget_id);

            if (count($learningTargetDetails) > 0) {
                $this->resultapi('1', 'Learning Targets Found', $learningTargetDetails);
            } else {
                $this->resultapi('0', 'No Learning Targets Found', $learningTargetDetails);
            }
        }
    }

    /*
     * Added By: Amit 
     * Reason: Get All Learning Targets
     */

    public function getLearningTargetByUser(Request $request) {

        if ($request->all() && count($request->all()) > 0) {
            
            $last_lession_id = $request->last_lession_id;

            $list = DB::table('lessons_users as lu')                
                ->select('lu.id')              
                ->join('map_lessons_users as mlu', 'lu.id', '=', 'mlu.ulessons_id')
                ->where('lu.lesson_id', $last_lession_id)         
                ->get();

            if (count($list) > 0) {
                $this->resultapi('1', 'Learning Targets Found', $list);
            } else {
                $this->resultapi('0', 'No Learning Targets Found', $list);
            }
        }
    }

    /*
     * Added By: Amit 
     * Reason: get editor content
     */

    public function getEditorContent(Request $request) {

        if ($request->all() && count($request->all()) > 0) {
            
            $user_data = $this->learningObj->getEditorContent($request);
           
            if (count($user_data) > 0) {
                $this->resultapi('1', 'Editor content', $user_data);
            } else {
                $this->resultapi('0', 'No editor content', $user_data);
            }
            //print_r($modified_data);
            //exit;
        }
    }

     /*
     * Added By: Amit 
     * Reason: get modification tab content
     */

    public function getModificationContent(Request $request) {
        $content = array();
        if ($request->all() && count($request->all()) > 0) {
            
            $last_lession_id = $request->last_lession_id;

            $modified_data = DB::table('lessons_modifications as lm')                
                ->select('lm.content')              
                ->join('lessons as ls', 'ls.id', '=', 'lm.lesson_id')
                ->where('lm.lesson_id', $last_lession_id)         
                ->first();

            //print_r($modified_data);
            if(count($modified_data)>0){
                $content["content"] = $modified_data->content;
            }else{
                $editor_content = $this->learningObj->getEditorContent($request);
//                $merge_content = '<h3>Summary</h3>'.$editor_content["summary"].'<h3>Standards</h3>'.$editor_content["standards"].'<h3>Essential Questions</h3>'.$editor_content["essential_questions"].'<h3>Core Ideas</h3>'.$editor_content["core_ideas"].'<h3>Vocabulary</h3>'.$editor_content["vocabulary"];
                $content_summary = $editor_content["summary"] ? '<h3>Summary</h3>'.$editor_content["summary"] : $editor_content["summary"];
                $content_standards = $editor_content["standards"] ? '<h3>Standards</h3>'.$editor_content["standards"] : $editor_content["standards"];
                $content_essential_questions = $editor_content["essential_questions"] ? '<h3>Essential Questions</h3>'.$editor_content["essential_questions"] : $editor_content["essential_questions"];
                $content_core_ideas = $editor_content["core_ideas"] ? '<h3>Core Ideas</h3>'.$editor_content["core_ideas"] : $editor_content["core_ideas"];
                $content_vocabulary = $editor_content["vocabulary"] ? '<h3>Vocabulary</h3>'.$editor_content["vocabulary"] : $editor_content["vocabulary"];
                // $content_lesson_sequence = $editor_content["lesson_sequence"] ? '<h3>Lesson sequence</h3>'.$editor_content["lesson_sequence"] : $editor_content["lesson_sequence"];
                // $merge_content = $content_summary.$content_standards.$content_essential_questions.$content_core_ideas.$content_vocabulary.$content_lesson_sequence;
                $merge_content = $content_summary.$content_standards.$content_essential_questions.$content_core_ideas.$content_vocabulary;
                $content["content"] = $merge_content;
            }
            
            //print_r($content);
             
           
            if (count($content) > 0) {
                $this->resultapi('1', 'Editor content', $content);
            } else {
                $this->resultapi('0', 'No editor content', $content);
            }
            //print_r($modified_data);
            //exit;
        }
    }

    

    public function resultapi($status, $message, $result = array()) {
        $finalArray['STATUS'] = $status;
        $finalArray['MESSAGE'] = $message;
        $finalArray['DATA'] = $result;
        echo json_encode($finalArray);

        exit;
    }

}
