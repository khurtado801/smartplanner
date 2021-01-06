<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Session;
use Auth;
use App\Category;
use App\ActivityLog;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        return view('admin/Category/index', ['title_for_layout' => 'Categories']);
    }
    
    public function getData() {
        return Datatables::of(Category::query())->make(true);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        return view('admin/Category/create', ['title_for_layout' => 'Add Category']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
                'name'  => 'required|max:255|unique:categories',
                'status'=> 'required',               
            ]);
        if ($validator->fails()) {
            return redirect('/admin/categories/create')
                ->withInput()
                ->withErrors($validator);
        }        
        $category = new Category;
        $category->name = $request->name;
        $category->status = $request->status;       
        $category->save();
        
        $msg = 'Category Added Successfully.';
        $log = ActivityLog::createlog(Auth::Id(),"Category",$msg,Auth::Id());
        Session::flash('success_msg', $msg);
        return redirect('/admin/categories');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
            $category = Category::find($id);
            if(empty($category)) {
                    Session::flash('error_msg', 'Category not found.');
                    return redirect('/admin/categories');
            }
            return view('admin/Category/show', ['title_for_layout' => 'Category', 'category' => $category]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
            $category = Category::find($id);
            if(empty($category)) {
                Session::flash('error_msg', 'Category not found.');
                return redirect('/admin/categories');
            }
            return view('admin/Category/edit', ['title_for_layout' => 'Edit Category', 'category' => $category]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
                'name' => 'required|max:255|unique:categories',
                'status'=> 'required',
            ]);
        if ($validator->fails()) {
            return redirect('admin/categories/' . $id . '/edit')
                ->withInput()
                ->withErrors($validator);
        }
        else {
            $category = Category::find($id);
            $category->name = $request->name;
            $category->status = $request->status;            
            $category->save();

            $msg = 'Category Updated Successfully.';
            $log = ActivityLog::createlog(Auth::Id(),"Category",$msg,Auth::Id());
        
            Session::flash('success_msg', $msg);
            return redirect('/admin/categories');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */    
    public function destroy($id)  {
        $category = Category::findOrFail($id);
        $category->delete();
        echo 1;exit;
    }
    
}
