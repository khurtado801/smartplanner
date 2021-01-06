<?php

//require __DIR__ . '/vendor/autoload.php';

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use mikehaertl\wkhtmlto\Pdf;
use Illuminate\Support\Facades\Validator;
use App\Grade;
use App\Subject;
use App\GradesSubjects;
use App\Theme;
use App\GradesSubjectsTheme;
use App\KeyConcept;
use App\TempCsv;
use App\KeyConceptThemes;
use App\LearningTargets;
use App\LearningtargetsName;
use App\LearningTargetsKeyConcept;
use Session;


class DashboardController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        // You can pass a filename, a HTML string, an URL or an options array to the constructor
        
        $subjects = Subject::where('status', 'Active')
            ->orderby('name','ASC')->lists('name', 'id')->all();
        return view('admin/Dashboard/index', ['title_for_layout' => 'Dashboard', 'subjects' => $subjects]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    public function csvUpload(Request $request) {
        /* Theme::truncate();
        GradesSubjectsTheme::truncate();
        KeyConcept::truncate();
        KeyConceptThemes::truncate();
        LearningTargets::truncate();
        LearningTargetsKeyConcept::truncate();
        LearningtargetsName::truncate();
        exit; */
        //truncate old tables
        //echo date('Y-m-d H:i:s');
        //print_r($);
        $subject_id = $request->subject_id;
        //print_r($request->subject_id);
        //exit;

        $messages = array(
            'file_path.mimetypes' => 'Please import csv file',
        );
        $validator = Validator::make($request->all(), [
                    'file_path' => 'required|mimetypes:text/plain,application/vnd.ms-excel,text/csv,text/tsv',
                        ], $messages);
        if ($validator->fails()) {
            return redirect('admin/dashboard')
                            ->withInput()
                            ->withErrors($validator);
        }
        $file = $request->file('file_path');

        /* Rearrange this array to change the search priority of delimiters */
        $delimiters = array(//'tab'       => "\t",
            'comma' => ",",
            'zem' => ":",
            'semicolon' => ";"
        );

        $handle = file($file);    # Grabs the CSV file, loads into array

        $line = array();            # Stores the count of delimiters in each row

        $valid_delimiter = array(); # Stores Valid Delimiters
        # Count the number of Delimiters in Each Row
        for ($i = 1; $i < 10; $i++) {
            foreach ($delimiters as $key => $value) {
                //print_r($value);
                $line[$key][$i] = count(explode($value, $handle[$i])) - 1;
            }
        }
        //print_r($line); exit;
        # Compare the Count of Delimiters in Each line
        foreach ($line as $delimiter => $count) {

            # Check that the first two values are not 0
            if ($count[1] > 0 and $count[2] > 0) {
                $match = true;

                $prev_value = '';
                foreach ($count as $value) {

                    if ($prev_value != '')
                        $match = ( $prev_value == $value and $match == true ) ? true : false;

                    $prev_value = $value;
                }
            } else {
                $match = false;
            }

            if ($match == true)
                $valid_delimiter[] = $delimiter;
        }//foreach
        //print_r($valid_delimiter); exit;
        # Set Default delimiter to comma
        if (isset($valid_delimiter[0])) {
            $delimiter = ( $valid_delimiter[0] != '' ) ? $valid_delimiter[0] : "comma";
            $import_delimiter = $delimiters[$delimiter];
        } else {
            $import_delimiter = ' ';
        }

        $file = $request->file('file_path');
        $file_read = fopen($file, "r");
        $first_row = 0;
        $uniqueCodes = "List Of unique codes";

        //$this->data = new LearningTargetsKeyConcept();
        

        $import_delimiter = ",";
        while (!feof($file_read)) {
            $data = fgetcsv($file_read, null, $import_delimiter);
           // print_r($data);
            $essential_questions = trim($data[6]);
            if ($essential_questions) {
               $essential_questions = str_replace("^", "<br/>", $essential_questions);
            }else{
                $essential_questions = '';
            }
            $overview = trim($data[4]);
            if ($overview) {
               $overview = str_replace("^", "<br/>", $overview);
            }else{
                $overview = '';
            }
            $standards = trim($data[5]);
            if ($standards) {
               $standards = str_replace("^", "<br/>", $standards);
            }else{
                $standards = '';
            }
             $core_ideas = trim($data[7]);
            if ($core_ideas) {
               $core_ideas = str_replace("^", "<br/>", $core_ideas);
            }else{
                $core_ideas = '';
            }
            
            $academic_vocabulary = trim($data[8]);
            if ($academic_vocabulary) {
               $academic_vocabulary = str_replace("^", "<br/>", $academic_vocabulary);
            }else{
                $academic_vocabulary = '';
            }

            $learning_target = trim($data[3]);
            if ($learning_target) {
               $learning_target = str_replace("^", "<br/>", $learning_target);
            }else{
                $learning_target = '';
            }
                      
            if ($first_row != 0) {
               // echo "table row=>".$first_row."<br/>";
                if($data[0]!="" && $data[1]!="" && $data[2]!="" && $data[3]!=""){
                    $temp = new TempCsv;
                   
                   // echo "db row=>".$first_row."<br/><br/>";
                    $temp->subject_id = $subject_id;
                    $temp->grade = $data[0];
                    $temp->theme = $data[1];
                    $temp->key_concept = $data[2];
                    $temp->learning_target = $learning_target;
                    $temp->overview = $overview;
                    $temp->standards = $standards;
                    $temp->essential_questions = $essential_questions;
                    $temp->core_ideas = $core_ideas;
                    $temp->academic_vocabulary = $academic_vocabulary;
                    $temp->save();  
                }               
            }
            /*if ($first_row > 50) {
                exit;
            }*/
            
            $first_row++;
        } //exit;
        fclose($file_read);
        //echo date('Y-m-d H:i:s');
        $msg = 'Data Imported Successfully.';
        Session::flash('success_msg', $msg);
        return redirect('/admin/learningtargets');
    }
    function waitAndDate($time){
       // sleep((int)$time);
        //printf('%d secs; %s', $time, shell_exec('date'));
        exit;
    }
    function excutemultiple(){
        $start = microtime(true);
        $mh = curl_multi_init();
        $handles = array();


        // create several requests
        for ($i = 0; $i < 5; $i++) {
            $ch = curl_init();
            $rand = rand(5,25);
            //echo "http://localhost/smartplanner/admin/waitAndDate/".$rand;
            //exit;
             // just making up data to pass to script
            curl_setopt($ch, CURLOPT_URL, "http://localhost/smartplanner/waitAndDate/".$rand);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 100);
            //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 


            curl_multi_add_handle($mh, $ch);
            $handles[] = $ch;
           // print_r($ch);exit;
        }
        

        // execute requests and poll periodically until all have completed
        $isRunning = null;
        do {
            curl_multi_exec($mh, $isRunning);
            usleep(2500);
        } while ($isRunning > 0);

        // fetch output of each request
        $outputs = array();
        for ($i = 0; $i < count($handles); $i++) {
            $outputs[$i] = trim(curl_multi_getcontent($handles[$i]));
            curl_multi_remove_handle($mh, $handles[$i]);
        }

        curl_multi_close($mh);

        print_r($outputs);
        printf("Elapsed time: %.2f seconds\n", microtime(true) - $start);
        exit;
    }

     public function executeProcess(Request $request) {
        /*Theme::truncate();
       GradesSubjectsTheme::truncate();
       KeyConcept::truncate();
       KeyConceptThemes::truncate();
       LearningTargets::truncate();
       LearningTargetsKeyConcept::truncate();
       LearningtargetsName::truncate();
        exit;*/
        /*Theme::truncate();
        GradesSubjectsTheme::truncate();
        KeyConcept::truncate();
        KeyConceptThemes::truncate();
        LearningTargets::truncate();
        LearningTargetsKeyConcept::truncate();
        exit;*/
        //exit;
        $time_start = microtime(true);
        //$list = TempCsv::where('status','0')->limit(10)->get();
        //$list = TempCsv::groupBy('grade')->where('status','0')->where('theme','Geometry')->limit(25)->get();
        $list = TempCsv::where('status','0')->limit(25)->get();
        $first_row = 0;
        //print_r($list);
        //exit;*/
        //$this->data = new LearningTargetsKeyConcept();
        $old_grade = "";
        $new_grade = "";
        $old_theme = "";
        $new_theme = "";

        foreach ($list as $key => $record) {
            $grade_id = Grade::select('id')->where('name', trim($record->grade))->first();
            $subject_id = $record->subject_id;
            $theme_record = Theme::select('id')->where('name', trim($record->theme))->first();
            $learningtargetsName_record = LearningtargetsName::select('id')->where('name', trim($record->learning_target))->first();
           /* echo "hre";
            print_r($theme_record);
            exit;*/
            $keyconcept_order_by = "";
            $keyconcept_name = trim($record->key_concept);
           /* if($record->key_concept != ""){
                $keyconcept_tmp_data = explode(" ",trim($record->key_concept), 2);
                $keyconcept_order_by = $keyconcept_tmp_data[0];
                $keyconcept_name = $keyconcept_tmp_data[1];
                $keyconcept_record = KeyConcept::select('id')->where('name', trim($keyconcept_name))->first();
            }*/
            
           if($keyconcept_order_by == ""){
               $keyconcept_record = KeyConcept::select('id')->where('name', trim($keyconcept_name))->first();
           }
            
            //$keyconcept_record = KeyConcept::select('id')->where('name', trim($keyconcept_name))->first();
            //$learningtarget_record = LearningTargets::select('id')->where('name', trim($record->learning_target))->first();
            //print_r($theme_id);exit;
            $new_grade = $grade_id->id;

            if ($theme_record == '') {
                $theme = new Theme;
                $theme->name = $record->theme;
                $theme->status = 'Active';
                $theme->save();
                //last inserted id
                $theme_id = $theme->id;

                if ($grade_id != '' && $subject_id != '') {
                    $gs_theme = new GradesSubjectsTheme;
                    $gs_theme->grade_id = $grade_id->id;
                    $gs_theme->subject_id = $subject_id;
                    $gs_theme->theme_id = $theme_id;
                    $gs_theme->save();
                }
            } else {
                $theme_id = $theme_record->id;

                $themesubject = GradesSubjectsTheme::where('grade_id',$grade_id->id)
                    ->where('subject_id',$subject_id)
                    ->where('theme_id',$theme_id)
                    ->get()
                    ->count();

                if ($themesubject==0) {
                    $gs_theme = new GradesSubjectsTheme;
                    $gs_theme->grade_id = $grade_id->id;
                    $gs_theme->subject_id = $subject_id;    
                    $gs_theme->theme_id = $theme_id;
                    $gs_theme->save();
                }
            }
            $new_theme = $theme_id;

            if ($keyconcept_record == '') {
                $keyconcept = new KeyConcept;
                $keyconcept->order_by = trim($keyconcept_order_by);
                $keyconcept->name = trim($keyconcept_name);
                $keyconcept->status = 'Active';
                $keyconcept->save();
                //last inserted id
                $keyconcept_id = $keyconcept->id;

                if ($theme_id != '') {
                    $keyconcept_theme = new KeyConceptThemes;
                    $keyconcept_theme->keyconcept_id = $keyconcept_id;
                    $keyconcept_theme->theme_id = $theme_id;
                    $keyconcept_theme->save();
                }
            } else {
                
                $keyconcept_id = $keyconcept_record->id;
                $keyconcepttheme_count = KeyConceptThemes::where('keyconcept_id',$keyconcept_id)                    
                    ->where('theme_id',$theme_id)
                    ->get()
                    ->count();

                if ($keyconcepttheme_count == 0) {
                    $keyconcept_theme = new KeyConceptThemes;
                    $keyconcept_theme->keyconcept_id = $keyconcept_id;
                    $keyconcept_theme->theme_id = $theme_id;
                    $keyconcept_theme->save();
                }
            }
            
            //**********************//
            
            if ($learningtargetsName_record == '') {
                $learningtargetsName_record = new LearningtargetsName;
                $learningtargetsName_record->name = $record->learning_target;
                $learningtargetsName_record->status = 'Active';
                $learningtargetsName_record->save();
                //last inserted id
                $learningtargetsName_record_id = $learningtargetsName_record->id;

//                if ($grade_id != '' && $subject_id != '') {
//                    $gs_theme = new GradesSubjectsTheme;
//                    $gs_theme->grade_id = $grade_id->id;
//                    $gs_theme->subject_id = $subject_id;
//                    $gs_theme->theme_id = $theme_id;
//                    $gs_theme->save();
//                }
            } else {
                $learningtargetsName_record_id = $learningtargetsName_record->id;

//                $themesubject = GradesSubjectsTheme::where('grade_id',$grade_id->id)
//                    ->where('subject_id',$subject_id)
//                    ->where('theme_id',$theme_id)
//                    ->get()
//                    ->count();

//                if ($themesubject==0) {
//                    $gs_theme = new GradesSubjectsTheme;
//                    $gs_theme->grade_id = $grade_id->id;
//                    $gs_theme->subject_id = $subject_id;    
//                    $gs_theme->theme_id = $theme_id;
//                    $gs_theme->save();
//                }
            }
            $new_learningtargetsName = $learningtargetsName_record_id;
            //**********************//
//            if ($learningtarget_record == '') {
                $learningtarget = new LearningTargets;
//                $learningtarget->name = trim($record->learning_target);
                $learningtarget->learningtargetsName_id = $new_learningtargetsName;
                
                $learningtarget->overview_summary = trim($record->overview);
                $learningtarget->standards = trim($record->standards);
                $learningtarget->essential_questions = trim($record->essential_questions);
                $learningtarget->core_ideas = trim($record->core_ideas);
                $learningtarget->academic_vocabulary = trim($record->academic_vocabulary);
                $learningtarget->status = 'Active';
                $learningtarget->save();
                //last inserted id
                $learningtarget_id = $learningtarget->id;

                if ($keyconcept_id != '') {
                    $learningtargets_keyconcept = new LearningTargetsKeyConcept;
                    $learningtargets_keyconcept->learningtargets_id = $learningtarget_id;
                    $learningtargets_keyconcept->keyconcepts_id = $keyconcept_id;
							$learningtargets_keyconcept->theme_id = $theme_id;
                    $learningtargets_keyconcept->grade_id = $grade_id->id;
							$learningtargets_keyconcept->subject_id = $subject_id;
                    $learningtargets_keyconcept->save();
                }
//            }else{
//               
//                $learningcountkeyconcept_count = LearningTargetsKeyConcept::where('learningtargets_id',$learningtarget_record->id)                    
//                    ->where('keyconcepts_id',$keyconcept_id)
//						  ->where('theme_id',$theme_id)
//                    ->where('grade_id',$grade_id->id)
//							 ->where('subject_id',$subject_id)
//                    ->get()
//                    ->count();
//
//                if ($learningcountkeyconcept_count == 0) {
//                    $learningtargets_keyconcept = new LearningTargetsKeyConcept;
//                    $learningtargets_keyconcept->learningtargets_id = $learningtarget_record->id;
//                    $learningtargets_keyconcept->keyconcepts_id = $keyconcept_id;
//							$learningtargets_keyconcept->theme_id = $theme_id;
//                    $learningtargets_keyconcept->grade_id = $grade_id->id;
//						$learningtargets_keyconcept->subject_id = $subject_id;
//                    $learningtargets_keyconcept->save();
//                }
//
//            }

            $csv_record = TempCsv::find($record->id);
            if(count($csv_record) > 0) {
                $csv_record->delete();
            }
            

            $old_theme = $theme_id;
            $old_grade = $grade_id->id; 

        }
        /*echo "<pre>";
        print_r($grade_id);
        exit; */
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $execution_time = ($time_end - $time_start)/60;
        //echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';
        //echo "Process Time: {$time}";
        //$procced_record = TempCsv::where('status','1')->get()->count();
        //$total_record = TempCsv::all()->count();
        $pending_record = TempCsv::where('status','0')->get()->count();
        echo json_encode(array('pending'=>$pending_record));
        exit;
        //Session::flash('success_msg', $msg);
        //return redirect('/admin/dashboard');
    }

}
