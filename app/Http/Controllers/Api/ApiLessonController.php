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
use App\LearningtargetsName;
use App\Lesson;
use App\MapUserTarget;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiLessonController extends Controller {

    private $req;
    private $user;
    private $jwtAuth;

    public function __construct(Request $request, User $user, ResponseFactory $responseFactory, JWTAuth $jwtAuth) {
        header('Content-Type: application/json');
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
        $this->req = $request;
        $this->res = $responseFactory;
        $this->lessonObj = new Lesson();
    }

    /*
     * Added By: Karnik 
     * Reason: Get All Lessons
     */

    public function getAllLessons(Request $request) {

        $countFilter = count($request->all());
        $lessonDetails = DB::table('lessons')
                ->select('id', 'grade_id', 'subject_id', 'theme_id', 'unit_title', 'status')
                ->orderBy('id', 'desc')
                ->where('status', 'Active')
                ->get();


        if (count($lessonDetails) > 0) {
            $this->resultapi('1', 'Lessons Found', $lessonDetails);
        } else {
            $this->resultapi('0', 'No Lessons Found', $lessonDetails);
        }
    }

    /*
     * Added By: Devang Mavani 
     * Reason: Create New Lesson
     */

    public function create(Request $request) {

        DB::beginTransaction();
        //print_r($request->all()); exit;
        $validator = Validator::make($request->all(), [
                    'grade_id' => 'required',
                    'subject_id' => 'required',
                    'theme_id' => 'required',
                    'unit_title' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            $this->resultapi('0', 'Please fill all required data.', $validator->errors()->all());
        }

        if ($request->id === null || $request->id === "") {

            $lesson = Lesson::where('user_id', '=', $request->user_id)->where('grade_id', '=', $request->grade_id)->where('subject_id', '=', $request->subject_id)->where('theme_id', '=', $request->theme_id)->where('unit_title', '=', $request->unit_title)->first();
            // DB::enableQueryLog();

            if ($lesson === null) {
                $lesson = new Lesson;
                $lesson->grade_id = $request->grade_id;
                $lesson->subject_id = $request->subject_id;
                $lesson->theme_id = $request->theme_id;
                $lesson->unit_title = $request->unit_title;
                $lesson->user_id = $request->user_id;
                $lesson->save();

                if ($lesson == true) {
                    DB::commit();
                    $this->resultapi('1', 'Success', array("last_id" => $lesson->id));
                } else {
                    DB::rollBack();
                    $this->resultapi('0', 'Something went wrong with server, Please try again');
                }
            } else {
                $this->resultapi('0', 'Unit title already exist');
            }
        } else {
            $lesson_updated = Lesson::where('id', $request->id)
                    ->update(['grade_id' => $request->grade_id,
                'subject_id' => $request->subject_id,
                'theme_id' => $request->theme_id,
                'unit_title' => $request->unit_title]);
            //print_r($lesson_updated);
            //exit;
            if (count($lesson_updated) > 0) {
                DB::commit();
                $this->resultapi('1', 'Success');
            } else {
                DB::rollBack();
                $this->resultapi('0', 'Something went wrong with server,Please try again');
            }
        }
    }

    /*
     * Added By: Karnik 
     * Reason: Get Lesson Detail
     */

    public function getLessonDetails(Request $request) {

        if ($request->all() && count($request->all()) > 0) {

            $lession_id = $request->lession_id['last_id'];
            
            $lessonDetails = $this->getLessonInfo($lession_id);
            
            if (count($lessonDetails) > 0) {
                //print_r($lessonDetails); exit;
                $grade_id = $lessonDetails[0]->grade_id;
                $subject_id = $lessonDetails[0]->subject_id;
                $theme_id = $lessonDetails[0]->theme_id;
                //exit;

                $lessonDetails[0]->keyconcept_details = DB::table('keyconcepts as kc')
                        ->join('keyconcepts_themes as kct', 'kct.keyconcept_id', '=', 'kc.id')
                        ->select('kc.name as keyconcept_name', 'kc.id as kc_id')
                        //->orderBy('keyconcept_name', 'asc')
                        ->orderBy('order_by', 'asc')
                        ->orderBy('keyconcept_name', 'asc')
                        ->where('kct.theme_id', $theme_id)
                        ->where('kc.status', 'Active')
                        ->get();

                 //print_r($lessonDetails[0]->keyconcept_details);       exit;
                if (count($lessonDetails[0]->keyconcept_details) > 0) {
                    foreach ($lessonDetails[0]->keyconcept_details as $key => $value) {
                        //print_r($value);
                        /*$target_list = DB::table('learningtargets as lt')
                                ->join('learningtargets_keyconcepts as ltkc', 'ltkc.learningtargets_id', '=', 'lt.id')
                                ->join('keyconcepts as kc', 'ltkc.keyconcepts_id', '=', 'kc.id')
                                ->select('lt.name as learningtargets_name', 'kc.id as keyconcept_id', 'kc.name as keyconcept_name', 'lt.id as learningtargets_id')
                                ->where('ltkc.keyconcepts_id', '=', $value->kc_id)
										  ->where('ltkc.theme_id', '=', $theme_id)
                                ->where('ltkc.grade_id', '=', $grade_id)
											->where('ltkc.subject_id', '=', $subject_id)
																									
                                ->where('lt.status', 'Active')
                                ->orderBy('learningtargets_name', 'asc')
                                ->groupBy('lt.id')
                                ->get();*/
                        $target_list = DB::table('learningtargets as lt')
                                ->join('learningtargets_keyconcepts as ltkc', 'ltkc.learningtargets_id', '=', 'lt.id')
                                ->join('keyconcepts as kc', 'ltkc.keyconcepts_id', '=', 'kc.id')
                                ->join('learningtargets_name as lt_name', 'lt.learningtargetsName_id', '=', 'lt_name.id')
                                ->select('lt_name.name as learningtargets_name', 'kc.id as keyconcept_id', 'kc.name as keyconcept_name', 'lt.id as learningtargets_id')
                                ->where('ltkc.keyconcepts_id', '=', $value->kc_id)
                                ->where('ltkc.theme_id', '=', $theme_id)
                                ->where('ltkc.grade_id', '=', $grade_id)
                                ->where('ltkc.subject_id', '=', $subject_id)
                                ->where('lt.status', 'Active')
                                ->orderBy('learningtargets_name', 'asc')
                                ->groupBy('lt.id')
                                ->get();

                        if(count($target_list)>0){
                            $lessonDetails[0]->keyconcept_details[$key]->target_list = $target_list;
                        }else{
                            $lessonDetails[0]->keyconcept_details[$key] = array();
                        }
                        //print_r($target_list);
                    }
                }
            }
            foreach ($lessonDetails[0]->keyconcept_details as $key => $value) {
                if(count($value)<=0){
                    unset($lessonDetails[0]->keyconcept_details[$key]);
                }
            }
            
        }
        if (count($lessonDetails) > 0) {
                $this->resultapi('1', 'Lessons Found', $lessonDetails);
            } else {
                $this->resultapi('0', 'No Lessons Found', $lessonDetails);
            }
    }

    /*
     * Added By: Karnik 
     * Reason: Get Learning Target Detail
     */

    public function getLearningTargetDetails(Request $request) {

        if ($request->all() && count($request->all()) > 0) {

            $lession_id = $request->lession_id;

            $lessonDetails = $this->getLessonInfo($lession_id);

            if (count($lessonDetails) > 0) {
                //print_r($lessonDetails); exit;
                $grade_id = $lessonDetails[0]->grade_id;
                $subject_id = $lessonDetails[0]->subject_id;
                $theme_id = $lessonDetails[0]->theme_id;
                //exit;

                $keyconcept_id = $request->keyconcept_id;
                $lessonDetails[0]->learning_target_details = DB::table('learningtargets as lt')
                        ->join('learningtargets_keyconcepts as ltkc', 'ltkc.learningtargets_id', '=', 'lt.id')
                        ->join('keyconcepts as kc', 'ltkc.keyconcepts_id', '=', 'kc.id')
                        // Need to changes after done
                        ->join('learningtargets_name as lt_name', 'lt.learningtargetsName_id', '=', 'lt_name.id')
                        ->select('lt_name.name as learningtargets_name', 'kc.id as keyconcept_id', 'kc.name as keyconcept_name', 'lt.id as learningtargets_id')
//                        ->select('lt.name as learningtargets_name', 'kc.id as keyconcept_id', 'kc.name as keyconcept_name', 'lt.id as learningtargets_id')
                        //->groupby('keyconcept_name')
                        ->orderBy('learningtargets_name', 'asc')
                        ->where('ltkc.keyconcepts_id', '=', $keyconcept_id)
                        ->where('ltkc.theme_id', '=', $theme_id)
                        ->where('ltkc.grade_id', '=', $grade_id)
                        ->where('ltkc.subject_id', '=', $subject_id)
                        ->where('lt.status', '=', "Active")
                        ->get();
            }
            if (count($lessonDetails) > 0) {
                $this->resultapi('1', 'Lessons Found', $lessonDetails);
            } else {
                $this->resultapi('0', 'No Lessons Found', $lessonDetails);
            }
        }
    }

    /*
     * Added By: Karnik 
     * Reason: Get Key Concepts Detail
     */

    public function getLessonInfo($lession_id) {
        
        $lessonInfo = DB::table('lessons as ls')
                ->join('grades as gr', 'ls.grade_id', '=', 'gr.id')
                ->join('subjects as sbj', 'ls.subject_id', '=', 'sbj.id')
                ->join('themes as th', 'ls.theme_id', '=', 'th.id')
                ->select('gr.id as grade_id', 'gr.name as grade_name', 'sbj.id as subject_id', 'sbj.name as subject_name', 'th.id as theme_id', 'th.name as theme_name', 'ls.unit_title as unit_title', 'ls.status')
                ->orderBy('ls.id', 'desc')
                ->where('ls.id', '=', $lession_id)
                ->where('ls.status', 'Active')
                ->take(1)
                ->get();

        return $lessonInfo;
    }

    /*
     * Added By: Karnik 
     * Reason: Get All Lessons
     */

    public function getMyAllLessons(Request $request) {

        $user_id = $request->user_id;
        $countFilter = count($request->all());
        $myLessons = DB::table('lessons')
                ->select('id', 'grade_id', 'subject_id', 'theme_id', 'unit_title', 'user_id', 'status')
                ->orderBy('id', 'desc')
                ->where('user_id', $user_id)
                ->where('status', 'Active')
                ->where('deleted_at', null)
                ->get();
//        $myLessons = DB::table('lessons as les')
//                ->leftjoin('map_lessons_users as mlu', 'les.id', '=', 'mlu.ulessons_id')
//                ->select('les.id','unit_title', 'user_id', 'status')
//                ->orderBy('id', 'desc')
//                ->where('user_id', $user_id)
//                ->where('status', 'Active')
//                ->where('deleted_at', 'null')
//                ->get();

        if (count($myLessons) > 0) {
            $this->resultapi('1', 'Lessons Found', $myLessons);
        } else {
            $this->resultapi('0', 'No Lessons Found', $myLessons);
        }
    }

    /*
     * Added By: Amit 
     * Reason: Get Fixed content like summary, core ides etc...
     */

    public function getFixedcontent(Request $request) {

        if ($request->all() && count($request->all()) > 0) {

            $last_lession_id = $request->last_lession_id;
            $summary_list = $this->lessonObj->getSummary($last_lession_id);
            $standard_list = $this->lessonObj->getStandard($last_lession_id);
            $essential_list = $this->lessonObj->getEssaential($last_lession_id);
            $coreideas_list = $this->lessonObj->getCoreideas($last_lession_id);
            $vocabulary_list = $this->lessonObj->getVocabulary($last_lession_id);
            $lesson_sequence_list = $this->lessonObj->getLessonSequence($last_lession_id);

            if (count($summary_list) > 0) {
                $this->resultapi('1', 'Learning Targets Found', array("summary" => $summary_list, "standard" => $standard_list, "essential" => $essential_list, "coreideas" => $coreideas_list, "vocabulary" => $vocabulary_list));
            } else {
                $this->resultapi('0', 'No Learning Targets Found', $summary_list);
            }
        }
    }

    /*
     * Added By: Amit 
     * Reason: Save summary box records, call from left side
     */

    public function saveSummary(Request $request) {

        /* Add select summary box data to database */

        if (count($request->summary_storage) > 0) {

            MapUserTarget::where('ulessons_id', $request->lession_id)
                    ->where('type', 'summary')
                    ->delete();

            foreach ($request->summary_storage as $summary_val) {
                //print_r($summary_val);
                $maptarget = new MapUserTarget;
                $maptarget->type = "summary";
                $maptarget->targetids = $summary_val["target_id"];
                $maptarget->key_concept_id = $summary_val["keyconcept_id"];
                $maptarget->ulessons_id = $request->lession_id;
                $maptarget->save();

                if ($maptarget == true) {
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            }
            if ($maptarget == true) {
                $this->resultapi('1', 'Success');
            } else {
                $this->resultapi('0', 'Something went wrong with server,Please try again');
            }
        } else {
            $this->resultapi('0', 'Please select summary');
        }

        /* Summary box end */
    }

    /*
     * Added By: Amit 
     * Reason: Save summary box records, call from left side
     */

    public function saveStandrad(Request $request) {

        if (count($request->standrad_storage) > 0) {

            MapUserTarget::where('ulessons_id', $request->lession_id)
                    ->where('type', 'standard')
                    ->delete();

            foreach ($request->standrad_storage as $standrad_val) {
                //print_r($summary_val);
                $maptarget = new MapUserTarget;
                $maptarget->type = "standard";
                $maptarget->targetids = $standrad_val["target_id"];
                $maptarget->key_concept_id = $standrad_val["keyconcept_id"];
                $maptarget->ulessons_id = $request->lession_id;
                $maptarget->save();

                if ($maptarget == true) {
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            }
            if ($maptarget == true) {
                $this->resultapi('1', 'Success');
            } else {
                $this->resultapi('0', 'Something went wrong with server,Please try again');
            }
        } else {
            $this->resultapi('0', 'Please select standard');
        }
    }

    /*
     * Added By: Amit 
     * Reason: Save Essential box records, call from left side
     */

    public function saveEssential(Request $request) {

        if (count($request->essential_storage) > 0) {

            MapUserTarget::where('ulessons_id', $request->lession_id)
                    ->where('type', 'essential')
                    ->delete();

            foreach ($request->essential_storage as $essential_val) {
                //print_r($summary_val);
                $maptarget = new MapUserTarget;
                $maptarget->type = "essential";
                $maptarget->targetids = $essential_val["target_id"];
                $maptarget->key_concept_id = $essential_val["keyconcept_id"];
                $maptarget->ulessons_id = $request->lession_id;
                $maptarget->save();

                if ($maptarget == true) {
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            }
            if ($maptarget == true) {
                $this->resultapi('1', 'Success');
            } else {
                $this->resultapi('0', 'Something went wrong with server,Please try again');
            }
        } else {
            $this->resultapi('0', 'Please select essential');
        }
    }

    /*
     * Added By: Amit 
     * Reason: Save CoreIdeas box records, call from left side
     */

    public function saveCoreIdeas(Request $request) {

        if (count($request->coreideas_storage) > 0) {

            MapUserTarget::where('ulessons_id', $request->lession_id)
                    ->where('type', 'coreideas')
                    ->delete();

            foreach ($request->coreideas_storage as $coreideas_val) {
                //print_r($summary_val);
                $maptarget = new MapUserTarget;
                $maptarget->type = "coreideas";
                $maptarget->targetids = $coreideas_val["target_id"];
                $maptarget->key_concept_id = $coreideas_val["keyconcept_id"];
                $maptarget->ulessons_id = $request->lession_id;
                $maptarget->save();

                if ($maptarget == true) {
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            }
            if ($maptarget == true) {
                $this->resultapi('1', 'Success');
            } else {
                $this->resultapi('0', 'Something went wrong with server,Please try again');
            }
        } else {
            $this->resultapi('0', 'Please select coreideas');
        }
    }

    /*
     * Added By: Amit 
     * Reason: Save CoreIdeas box records, call from left side
     */

    public function saveVocabulary(Request $request) {

        if (count($request->vocabulary_storage) > 0) {

            MapUserTarget::where('ulessons_id', $request->lession_id)
                    ->where('type', 'vocabulary')
                    ->delete();

            foreach ($request->vocabulary_storage as $vocabulary_val) {
                //print_r($summary_val);
                $maptarget = new MapUserTarget;
                $maptarget->type = "vocabulary";
                $maptarget->targetids = $vocabulary_val["target_id"];
                $maptarget->key_concept_id = $vocabulary_val["keyconcept_id"];
                $maptarget->ulessons_id = $request->lession_id;
                $maptarget->save();

                if ($maptarget == true) {
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            }
            if ($maptarget == true) {
                $this->resultapi('1', 'Success');
            } else {
                $this->resultapi('0', 'Something went wrong with server,Please try again');
            }
        } else {
            $this->resultapi('0', 'Please select coreideas');
        }
    }

    public function saveLessonSequence(Request $request) {

        if (count($request->lesson_sequence_storage) > 0) {

            MapUserTarget::where('ulessons_id', $request->lession_id)
                    ->where('type', 'vocabulary')
                    ->delete();

            foreach ($request->vocabulary_storage as $vocabulary_val) {
                //print_r($summary_val);
                $maptarget = new MapUserTarget;
                $maptarget->type = "vocabulary";
                $maptarget->targetids = $vocabulary_val["target_id"];
                $maptarget->key_concept_id = $vocabulary_val["keyconcept_id"];
                $maptarget->ulessons_id = $request->lession_id;
                $maptarget->save();

                if ($maptarget == true) {
                    DB::commit();
                } else {
                    DB::rollBack();
                }
            }
            if ($maptarget == true) {
                $this->resultapi('1', 'Success');
            } else {
                $this->resultapi('0', 'Something went wrong with server,Please try again');
            }
        } else {
            $this->resultapi('0', 'Please select coreideas');
        }
    }

    /*
     * Added By: Karnik 
     * Reason: Get Key Concepts Detail
     */

    public function getLessonsUserData(Request $request) {
        $lession_id = $request->lesson_id;

        $lesson_userInfo = DB::table('lessons as ls')
                ->leftJoin('lessons_users as lsu', 'ls.id', '=', 'lsu.lesson_id')
                ->join('grades as gr', 'ls.grade_id', '=', 'gr.id')
                ->join('subjects as sbj', 'ls.subject_id', '=', 'sbj.id')
                ->join('themes as th', 'ls.theme_id', '=', 'th.id')
                ->select('gr.id as grade_id', 'gr.name as grade_name', 'sbj.id as subject_id', 'sbj.name as subject_name', 'th.id as theme_id', 'th.name as theme_name', 'ls.unit_title as unit_title', 'ls.status')
                ->orderBy('ls.id', 'desc')
                ->where('ls.id', '=', $lession_id)
                ->first();

        if (count($lesson_userInfo) > 0) {
            $subjectlist = Subject::getSubjectsByGrade($lesson_userInfo->grade_id);
            
            $lesson_userInfo->subject_list = $subjectlist;

            $themelist = Theme::getThemesByGradeAndSubjects($lesson_userInfo->grade_id, $lesson_userInfo->subject_id);

            $lesson_userInfo->theme_list = $themelist;
        }
        //print_r($lesson_userInfo);
        //exit;
        $this->resultapi('1', 'Success', $lesson_userInfo);
        //return (array)$lesson_userInfo;
    }

    public function resultapi($status, $message, $result = array()) {
        $finalArray['STATUS'] = $status;
        $finalArray['MESSAGE'] = $message;
        $finalArray['DATA'] = $result;
        echo json_encode($finalArray);
        exit;
    }

}
