<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Auth;
use App\User;
use App\Country;
use App\States;
use App\Cities;
use App\ActivityLog;
use App\PaymentPlan;
use App\UserSubscribe;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\UserProfiles;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('admin/User/index', ['title_for_layout' => 'List Users']);
    }

    public function getData() {

        //return Datatables::of(User::query()->where("usertype", '!=', "Super Admin"))->make(true);
        return Datatables::of(User::query())->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $countries = Country::where('status', 'Active')->lists('name', 'id')->all();

        $usertype = [
            //'Admin' => 'Admin',
            'Super Admin' => 'Admin',
            'Teacher' => 'Teacher'
        ];

        return view('admin/User/create', ['title_for_layout' => 'Add User',
            'usertype' => $usertype, 'countries' => $countries]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [

                    'firstname' => 'required|max:100',
                    'lastname' => 'required|max:100',
                    'email' => 'required|max:100|unique:users',
                    'password' => 'required|min:6|same:confirmpassword',
                    'confirmpassword' => 'required|min:6|same:password',
                    'country' => 'required',
                    'phone_number' => 'required',
                    'user_type' => 'required',
                    'status' => 'required'
        ]);

        if ($validator->fails()) {

            return redirect('/admin/user/create')
                            ->withInput()
                            ->withErrors($validator);
        }
        //print_r($request->usertype); exit;
        $user = new User;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->country = $request->country;
        $user->phone_number = $request->phone_number;
        $user->usertype = $request->usertype;
        $user->status = $request->status;
        //$user->email_verify_code = rand();
        $user->email_verified = 'Yes';
        //echo '<pre>'; print_r($user); exit;
        $user->save();

        if ($user->save()) {

            $lastUserinsertedId = $user->id;

            $userProfile = new UserProfiles;
            $userProfile->user_id = $lastUserinsertedId;
            $userProfile->save();

            $msg = "User registered successfully.";
            $log = ActivityLog::createlog(Auth::Id(), "Admin User", $msg, Auth::Id());

            Session::flash('success_msg', $msg);
            return redirect('/admin/user');
        } else {
            Session::flash('error_msg', 'Problem in registration please continue again.');
            return redirect('/admin/user');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {

        $user = User::find($id);
        $isAdmin = false;
        if (!empty($user)) {
            $countries = Country::where('status', 'Active')->lists('name', 'id')->all();
            $usertype = [
                //'Admin' => 'Admin',
                'Super Admin' => 'Admin',
                'Teacher' => 'Teacher'
            ];
            $userId = $user->id;
            $userType = $user->usertype;
            if ($userType == "Super Admin" || $userType == "Admin") {
                $isAdmin = true;
            }
        } else {
            Session::flash('error_msg', 'User not found.');
            return redirect('/admin/user');
        }
        return view('admin/User/edit', ['title_for_layout' => 'Edit Details', 'user' => $user, 'isAdmin' => $isAdmin,
            'usertype' => $usertype, 'countries' => $countries]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $user = User::find($id);
        $error = false;
        //echo $user->profile_image; exit;
        if ($user->usertype == "Super Admin" || $user->usertype == "Admin") {
            $validator = Validator::make($request->all(), [
                        'firstname' => 'required|max:100',
                        'lastname' => 'required|max:100',
                        'email' => 'required|max:100',
                        'country' => 'required',
                        'phone_number' => 'required|min:10|max:15',
                        'usertype' => 'required',
                        'status' => 'required'
            ]);

            if ($validator->fails()) {
                return redirect('admin/user/' . $id . '/edit')
                                ->withInput()
                                ->withErrors($validator);
            } else {
                $user->firstname = $request->firstname;
                $user->lastname = $request->lastname;
                $user->email = $request->email;
                $user->country = $request->country;
                $user->phone_number = $request->phone_number;
                $user->usertype = $request->usertype;
                $user->status = $request->status;

                $files = Input::file('profile_image');
                if ($files && !empty($files)) {
                    foreach ($files as $file) {
                        $oldPicture = $user->profile_image;
                        $rules = array('file' => 'mimes:jpg,jpeg,png,gif|max:2000');
                        $validator = Validator::make(array('file' => $file), $rules);
                        if ($validator->passes()) {
                            $destinationPath = storage_path() . '/adminProfileImages/';
                            $timestamp = time();
                            $filename = $timestamp . '_' . trim($file->getClientOriginalName());
                            $upload_success = $file->move($destinationPath, $filename);
                            if ($upload_success) {
                                $unlink_success = File::delete($destinationPath . $oldPicture);
                            }
                        } else {
                            $error = true;
                            $filename = $oldPicture;
                            return Redirect::to('/admin/user/' . $id . '/edit')->withInput()->withErrors($validator);
                        }
                    }

                    $user->profile_image = $filename;
                }

                if ($request->update_password && $request->update_password == true) {
                    $validator = Validator::make($request->all(), [

                                'password' => 'required|min:6|same:confirmpassword',
                                'confirmpassword' => 'required|min:6|same:password',
                    ]);

                    if ($validator->fails()) {
                        $error = true;
                        return redirect('admin/user/' . $id . '/edit')
                                        ->withInput()
                                        ->withErrors($validator);
                    } else {
                        $user->password = bcrypt($request->password);
                    }
                }

                if ($error == false) {
                    $user->save();
                    $msg = "Admin details updated successfully.";
                    $log = ActivityLog::createlog(Auth::Id(), "Admin User", $msg, Auth::Id());

                    Session::flash('success_msg', $msg);
                    //return redirect('/admin/user/' . $id . '/edit');
                    return redirect('/admin/user');
                }
            }
        } else {
            $validator = Validator::make($request->all(), [
                        'firstname' => 'required|max:100',
                        'lastname' => 'required|max:100',
                        'email' => 'required|max:100',
                        'country' => 'required',
                        'phone_number' => 'required|min:10|max:15',
                        'usertype' => 'required',
                        'status' => 'required'
            ]);

            if ($validator->fails()) {
                return redirect('admin/user/' . $id . '/edit')
                                ->withInput()
                                ->withErrors($validator);
            } else {
                $user->firstname = $request->firstname;
                $user->lastname = $request->lastname;
                $user->email = $request->email;
                $user->country = $request->country;
                $user->phone_number = $request->phone_number;
                $user->usertype = $request->usertype;
                $user->status = $request->status;
                $user->save();

                $msg = "User updated successfully.";
                $log = ActivityLog::createlog(Auth::Id(), "Admin User", $msg, Auth::Id());

                Session::flash('success_msg', $msg);
                return redirect('/admin/user');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $user = User::findOrFail($id);
        $user->delete();
        echo 1;
        exit;
    }

    /**
     * Display a listing of the plans.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewPlans($id) {
        return view('admin/User/myplans', ['title_for_layout' => 'My Plans', 'uid' => $id]);
    }

    public function getMyPlans($id) {
        return Datatables::of(UserSubscribe::query()
                                ->join('payment_plan', 'user_subscribe.plan_id', '=', 'payment_plan.id')
                                ->where("user_id", '=', $id))->make(true);
    }

    public function changeAgreementStatus(Request $request) {
        
        $data = $request->all();
        PaymentPlan::RecurringProfileStatus($data);     
        exit;
    }

    public function resultapi($status, $message, $result = array()) {
        $finalArray['STATUS'] = $status;
        $finalArray['MESSAGE'] = $message;
        $finalArray['DATA'] = $result;
        echo json_encode($finalArray);
        exit;
    }

}
