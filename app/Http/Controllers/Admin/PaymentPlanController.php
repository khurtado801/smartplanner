<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Auth;
use App\PaymentPlan;
use App\UserSubscribe;
use App\ActivityLog;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class PaymentPlanController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {
        return view('admin/PaymentPlan/index', ['title_for_layout' => 'Payment Plans']);
    }

    public function getData() {
        return Datatables::of(PaymentPlan::query())->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin/PaymentPlan/create', ['title_for_layout' => 'Add PaymentPlan']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
                    'name' => 'required|max:255|unique:payment_plan',
                    'duration' => 'required',
                    //'frequency' => 'required',
                    'price' => 'required',
                    'status' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect('/admin/paymentplan/create')
                            ->withInput()
                            ->withErrors($validator);
        }

        $paymentplan = new PaymentPlan;
        $paymentplan->name = $request->name;
        $paymentplan->frequency = $request->frequency;
        $paymentplan->price = $request->price;
        $paymentplan->status = $request->status;
        $paymentplan->save();

        $msg = 'PaymentPlan has been added successfully.';
        $log = ActivityLog::createlog(Auth::Id(), "PaymentPlan", $msg, Auth::Id());
        Session::flash('success_msg', $msg);
        return redirect('/admin/paymentplan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $paymentplan = PaymentPlan::find($id);
        if (empty($paymentplan)) {
            Session::flash('error_msg', 'PaymentPlan not found.');
            return redirect('/admin/paymentplan');
        }
        return view('admin/PaymentPlan/show', ['title_for_layout' => 'PaymentPlan', 'paymentplan' => $paymentplan]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $paymentplan = PaymentPlan::find($id);
        if (empty($paymentplan)) {
            Session::flash('error_msg', 'PaymentPlan not found.');
            return redirect('/admin/paymentplan');
        }
        return view('admin/PaymentPlan/edit', ['title_for_layout' => 'Edit PaymentPlan', 'paymentplan' => $paymentplan]);
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
                    'name' => 'required|max:255|unique:payment_plan,id,' . $id,
                    'duration' => 'required',
                    //'frequency' => 'frequency',
                    'price' => 'required',
                    'status' => 'required'
        ]);
        if ($validator->fails()) {

            return redirect('admin/paymentplan/' . $id . '/edit')
                            ->withInput()
                            ->withErrors($validator);
        } else {

            $paymentplan = PaymentPlan::find($id);
            $paymentplan->name = $request->name;
            $paymentplan->frequency = $request->frequency;
            $paymentplan->price = $request->price;
            $paymentplan->status = $request->status;
            //echo "<pre>"; print_r($paymentplan); exit;
            $paymentplan->save();

            $msg = 'PaymentPlan has been updated successfully.';
            $log = ActivityLog::createlog(Auth::Id(), "PaymentPlan", $msg, Auth::Id());
            Session::flash('success_msg', $msg);
            return redirect('/admin/paymentplan');
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
        $paymentplan = PaymentPlan::findOrFail($id);
        $paymentplan->delete();
        $msg = 'PaymentPlan has been deleted successfully.';
        Session::flash('success_msg', $msg);
        echo 1;
        //exit;
    }

    public function getExpiredSubscriptions() {
        $expired_data = UserSubscribe::getDataRenewal();
        //echo "<pre>"; print_r($expired_data); //exit;
        if (count($expired_data) > 0) {
            //foreach ($expired_data as $key => $value) {
            //$value->status = 'Inactive';
            //$value->updated_at = date('Y-m-d');
            //print_r($value);
            //$value->save();
            //}
            //exit;
            $msg = 'Subscription plan has been expired.';
            $log = ActivityLog::createlog(Auth::Id(), "Subscription", $msg, Auth::Id());
            Session::flash('success_msg', $msg);
            return redirect('/admin/paymentplan');
        } else {
            Session::flash('success_msg', "0000");
        }
    }

}
