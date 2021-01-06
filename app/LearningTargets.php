<?php

namespace App;

//use DB;
use Illuminate\Database\Eloquent\Model;
use File;
use DB;


Class LearningTargets extends Model {

    protected $table = 'learningtargets';

    function getLearningTargetById($learningtarget_id){// print_r($request);
    	//$learningtarget_id = $request->learningtarget_id;
      	//print_r($learningtarget_id); exit;

        //echo "ddd";
        // $learningTargetDetails = DB::table('learningtargets as lt')
        //         ->select('lt.id', 'lt.name', 'lt.overview_summary', 'lt.standards', 'lt.essential_questions', 'lt.core_ideas', 'lt.academic_vocabulary','lk.keyconcepts_id')
        //         ->join('learningtargets_keyconcepts as lk', 'lk.learningtargets_id', '=', 'lt.id')
        //         ->whereIn('lt.id', $learningtarget_id)
        //         ->where('status', 'Active')
        //         ->groupBy('lt.id') 
        //         ->get();
        $learningTargetDetails = DB::table('learningtargets as lt')
                ->join('learningtargets_keyconcepts as lk', 'lk.learningtargets_id', '=', 'lt.id')
                ->join('keyconcepts as kc', 'kc.id', '=', 'lk.keyconcepts_id')
                ->join('learningtargets_name as lt_name', 'lt.learningtargetsName_id', '=', 'lt_name.id')
                ->select('lt.id', 'lt_name.name', 'lt.overview_summary', 'lt.standards', 'lt.essential_questions', 'lt.core_ideas', 'lt.academic_vocabulary','lk.keyconcepts_id','kc.name as kc_name')
                ->whereIn('lt.id', $learningtarget_id)
                ->where('lt.status', 'Active')
                ->groupBy('lt.id') 
                ->get();
                //print_r($learningTargetDetails);
        return $learningTargetDetails;
    }

    function getLearningTargetByUser($learningtarget_id){

        $list = DB::table('lessons_users as lu')                
                ->select('lu.summary','lu.standards','lu.essential_questions')              
                ->join('map_lessons_users as mlu', 'lu.id', '=', 'mlu.ulessons_id')
                ->join('learningtargets as lt', 'lt.id', '=', 'mlu.ulessons_id')
                ->whereIn('mlu.learningtarget_id', $learningtarget_id)         
                ->get();
                
       // print_r($learningTargetDetails);exit;        
        return $list;
    }
    function createEditorcontent($list,$column){        
        $html = '';
        if(count($list)>0){
            foreach ($list as $key => $value) { //print_r($value);exit;
                $html .= '<div class="result-discription">';
                $html .= '<h4>'.$value->name.'</h4>';
                $html .= '<p>'.$value->$column.'</p>';
                $html .= '</div>';
            }
        }
        return $html;
    }
    function getEditorContent($request){
        
        $this->lessonObj = new Lesson();

        $last_lession_id = $request->last_lession_id;
        $modified_data = DB::table('lessons_users as lu')                
            ->select('lu.id','lu.summary','lu.standards','lu.essential_questions','lu.core_ideas','lu.vocabulary','lu.lesson_sequence')
            ->join('lessons as ls', 'ls.id', '=', 'lu.lesson_id')
            ->where('lu.lesson_id', $last_lession_id)         
            ->first();
        
        //print_r($modified_data);
        if(count($modified_data)==0){
            $modified_data = array('summary'=>"","standards"=>"","essential_questions"=>"","core_ideas"=>"","vocabulary"=>"","lesson_sequence"=>"");
            $modified_data = (object) $modified_data;
            //print_r($modified_data);
        }
        $user_data = array();
        
        if($modified_data->summary!=""){
            $user_data["summary"] = $modified_data->summary;
        }else{
            $summary_list = $this->lessonObj->getSummary($last_lession_id);
            $user_data["summary"] = $this->createEditorcontent($summary_list,'overview_summary');
        }
        // echo 'test';exit;
        if($modified_data->standards!=""){
            $user_data["standards"] = $modified_data->standards;
        }else{
            //echo $last_lession_id;
            $standard_list = $this->lessonObj->getStandard($last_lession_id);
            $user_data["standards"] = $this->createEditorcontent($standard_list,'standards');
        }

        if($modified_data->essential_questions!=""){
            $user_data["essential_questions"] = $modified_data->essential_questions;
        }else{
           $essential_list = $this->lessonObj->getEssaential($last_lession_id);
           $user_data["essential_questions"] = $this->createEditorcontent($essential_list,'essential_questions');
        }

        if($modified_data->core_ideas!=""){
            $user_data["core_ideas"] = $modified_data->core_ideas;
        }else{
            $coreideas_list = $this->lessonObj->getCoreideas($last_lession_id);
            $user_data["core_ideas"] = $this->createEditorcontent($coreideas_list,'core_ideas');
        }

        if($modified_data->vocabulary!=""){
            $user_data["vocabulary"] = $modified_data->vocabulary;
        }else{
           $vocabulary_list = $this->lessonObj->getVocabulary($last_lession_id);
           $user_data["vocabulary"] = $this->createEditorcontent($vocabulary_list,'academic_vocabulary'); 
        }
        //! get lesson sequence
        // if($modified_data->lesson_sequence!=""){
        //     $user_data["lesson_sequence"] = $modified_data->lesson_sequence;
        // }else{
        //    $lesson_sequence_list = $this->lessonObj->getLessonSequence($last_lession_id);
        //    $user_data["lesson_sequence"] = $this->createEditorcontent($lesson_sequence_list,'lesson_sequence'); 
        // }
        return $user_data;
    }
}

?>
