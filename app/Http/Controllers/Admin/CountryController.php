<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Auth;
use App\Country;
use App\ActivityLog;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class CountryController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        return view('admin/Country/index', ['title_for_layout' => 'Country']);
    }

    public function getData() {
        return Datatables::of(Country::query())->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin/Country/create', ['title_for_layout' => 'Add Country']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
                    'name' => 'required|max:255|unique:country',
                    'status' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect('/admin/country/create')
                            ->withInput()
                            ->withErrors($validator);
        }
        $country = new Country;
        $country->name = $request->name;
        $country->status = $request->status;
        $country->save();

        $msg = 'Country has been added successfully.';
        $log = ActivityLog::createlog(Auth::Id(), "Country", $msg, Auth::Id());
        Session::flash('success_msg', $msg);
        return redirect('/admin/country');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $country = Country::find($id);
        if (empty($country)) {
            Session::flash('error_msg', 'Country not found.');
            return redirect('/admin/country');
        }
        return view('admin/Country/edit', ['title_for_layout' => 'Edit Country', 'country' => $country]);
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
                    'name' => 'required|max:255|unique:country,id,'.$id,
                    'status' => 'required',
        ]);
        if ($validator->fails()) {

            return redirect('admin/country/' . $id . '/edit')
                            ->withInput()
                            ->withErrors($validator);
        } else {

            $country = Country::find($id);
            $country->name = $request->name;
            $country->status = $request->status;
            $country->save();
            
            $msg = 'Country has been updated successfully.';
            $log = ActivityLog::createlog(Auth::Id(), "Country", $msg, Auth::Id());
            Session::flash('success_msg', $msg);
            return redirect('/admin/country');
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
        $country = Country::findOrFail($id);
        $country->delete();
        $msg = 'Country has been deleted successfully.';
        Session::flash('success_msg', $msg);
        echo 1;
        //exit;
    }

}
