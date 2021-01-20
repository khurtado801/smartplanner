<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\TempPdf;
use App\User;
use App\UserSubscribe;
use App\Grade;
use App\Subject;
use App\Theme;
use App\Lesson;
use App\LessonUser;
use App\LearningTargets;
Use DB;
use App\PaymentPlan;
use App\TransactionHistory;

class HomeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        // $this->middleware('auth');
        $this->learningObj = new LearningTargets();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id) {
        $pdfdata = TempPdf::where('temp_pdf_data.lesson_id', '=', $id)->first();
        $lesson_data = Lesson::where('lessons.id', '=', $id)
                ->select('gr.name as grade_name', 'sub.name as subject_name', 'th.name as theme_name', 'lessons.unit_title as unit_title')
                ->join('grades as gr', 'gr.id', '=', 'lessons.grade_id')
                ->join('subjects as sub', 'sub.id', '=', 'lessons.subject_id')
                ->join('themes as th', 'th.id', '=', 'lessons.theme_id')
                ->first();

        if (count($pdfdata) == 0) {
            $pdfdata = (object) array();
            $request = array("last_lession_id" => $id);
            $editor_content = $this->learningObj->getEditorContent((object) $request);
            $content_summary = $editor_content["summary"] ? '<h3>Summary</h3>' . $editor_content["summary"] : $editor_content["summary"];
            $content_standards = $editor_content["standards"] ? '<h3>Standards</h3>' . $editor_content["standards"] : $editor_content["standards"];
            $content_essential_questions = $editor_content["essential_questions"] ? '<h3>Essential Questions</h3>' . $editor_content["essential_questions"] : $editor_content["essential_questions"];
            $content_core_ideas = $editor_content["core_ideas"] ? '<h3>Core Ideas</h3>' . $editor_content["core_ideas"] : $editor_content["core_ideas"];
            $content_vocabulary = $editor_content["vocabulary"] ? '<h3>Vocabulary</h3>' . $editor_content["vocabulary"] : $editor_content["vocabulary"];
            // $content_lesson_sequence = $editor_content["lesson_sequence"] ? '<h3>Lesson Sequence</h3>' . $editor_content["lesson_sequence"] : $editor_content["lesson_sequence"];
            // $merge_content = $content_summary . $content_standards . $content_essential_questions . $content_core_ideas . $content_vocabulary.$content_lesson_sequence;
            $merge_content = $content_summary . $content_standards . $content_essential_questions . $content_core_ideas . $content_vocabulary;
            $pdfdata->temp_content = $merge_content;

            //print_r($content);
        }
        //print_r($lesson_data); exit;
        return view('admin/Cms/pdf')->with('pdfdata', $pdfdata)
                        ->with('lesson_data', $lesson_data);
    }

    public function invoice($id) {
//        $invoicepdfdata = User::where('users.id', '=', $id)
//                ->join('user_subscribe as us','users.id','=','us.user_id')
//                ->join('payment_plan as pp','us.plan_id','=','pp.id')
//                ->first();

        $invoicepdfdata = UserSubscribe::where('user_subscribe.id', '=', $id)
                ->join('users as us', 'us.id', '=', 'user_subscribe.user_id')
                ->join('payment_plan as pp', 'pp.id', '=', 'user_subscribe.plan_id')
                ->first();

        return view('admin/Cms/payment_invoice')->with('invoicepdfdata', $invoicepdfdata);
    }

    public function checkLastStatus() {

        $last_status = DB::table('user_subscribe as us')
                ->select('*')
                ->where('us.status', "Active")
                ->whereNotIn('us.transaction_status', ["Success", "SuccessWithWarning"])
                ->get();
        echo "<pre>"; print_r($last_status); exit;
        //agreement_id
        foreach ($last_status as $value) {
            $transaction_details = PaymentPlan::getTransactionDetail($value->agreement_id);
            //echo "<pre>"; print_r($transaction_details); //exit;
            //$transaction_details = array_reverse($transaction_details->agreement_transaction_list);
            $transaction_details = (array) ($transaction_details->agreement_transaction_list);
            $transaction_details = end($transaction_details);

            if ($transaction_details) {
                $transaction_history = TransactionHistory::where('transaction_id', $transaction_details->transaction_id)
                                ->where('transaction_status', $transaction_details->status)->first();

                if (count($transaction_history) == 0) {

                    $transaction_history = new TransactionHistory;
                    $transaction_history->agreement_id = $agreement_detail->id;
                    $transaction_history->transaction_id = $transaction_details->transaction_id;
                    $transaction_history->transaction_status = $transaction_details->status;
                    //echo "<pre>"; print_r($transaction_history); exit;
                    $transaction_history->save();
                }
            }
        }
        exit;
    }

}
