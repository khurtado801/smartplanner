<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Auth;
use App\EducationalQuote;
use App\ActivityLog;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class EducationalQuotesController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('admin/EducationalQuotes/index', ['title_for_layout' => 'Educational Quotes']);
    }

    public function getData() {
        return Datatables::of(EducationalQuote::query())->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin/EducationalQuotes/create', ['title_for_layout' => 'Add EducationalQuote']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
                    'quote_line1' => 'required|unique:educational_quotes',
                    'quote_line2' => 'unique:educational_quotes',
                    'author' => 'required',
                    'status' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect('/admin/educationalquotes/create')
                            ->withInput()
                            ->withErrors($validator);
        }
        $educationalquote = new EducationalQuote;
        $educationalquote->quote_line1 = $request->quote_line1;
        $educationalquote->quote_line2 = $request->quote_line2;
        $educationalquote->author = $request->author;
        $educationalquote->status = $request->status;
        $educationalquote->save();

        $msg = 'Educational Quote has been added successfully.';
        $log = ActivityLog::createlog(Auth::Id(), "EducationalQuote", $msg, Auth::Id());
        Session::flash('success_msg', $msg);
        return redirect('/admin/educationalquotes');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $educationalquote = EducationalQuote::find($id);
        if (empty($educationalquote)) {
            Session::flash('error_msg', 'Educational Quote not found.');
            return redirect('/admin/educationalquotes');
        }
        return view('admin/EducationalQuotes/edit', ['title_for_layout' => 'Edit EducationalQuote', 'educationalquote' => $educationalquote]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $validator = Validator::make($request->all(), [
                    'quote_line1' => 'required|unique:educational_quotes,id,'.$id,
                    'quote_line2' => 'unique:educational_quotes,id,'.$id,
                    'author' => 'required',
                    'status' => 'required',
        ]);
        if ($validator->fails()) {

            return redirect('admin/educationalquotes/' . $id . '/edit')
                            ->withInput()
                            ->withErrors($validator);
        } else {

            $educationalquote = EducationalQuote::find($id);
            $educationalquote->quote_line1 = $request->quote_line1;
            $educationalquote->quote_line2 = $request->quote_line2;
            $educationalquote->author = $request->author;
            $educationalquote->status = $request->status;
            $educationalquote->save();

            $msg = 'Educational Quote has been updated successfully.';
            $log = ActivityLog::createlog(Auth::Id(), "EducationalQuote", $msg, Auth::Id());
            Session::flash('success_msg', $msg);
            return redirect('/admin/educationalquotes');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //echo $id;die;
        $educationalquote = EducationalQuote::findOrFail($id);
        $educationalquote->delete();
        $msg = 'Educational Quote has been deleted successfully.';
        Session::flash('success_msg', $msg);
        echo 1;
        //exit;
    }

}
