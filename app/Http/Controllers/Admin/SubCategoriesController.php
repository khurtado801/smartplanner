<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Auth;
use App\SubCategory;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class SubCategoriesController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            return view('admin/SubCategory/index', ['title_for_layout' => 'Sub Categories']);
    }
    
    /**
     * Fetch data tobe used in datatable
    */
    public function getData() {
        return Datatables::of(SubCategory::query())->make(true);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = DB::table('categories')->lists('name', 'id');
        return view('admin/SubCategory/create', ['title_for_layout' => 'Add Sub Category', 'categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',                
            'category_id'=> 'required|integer',
        ]);
        if ($validator->fails()) {
            return redirect('/admin/sub_categories/create')
                ->withInput()
                ->withErrors($validator);
        }
        
        $subcategory = new SubCategory;
        $subcategory->name = $request->name;
        $subcategory->category_id = $request->category_id;

        $subcategory->content = $request->content;
        $subcategory->save();
        
        Session::flash('success_msg', 'Sub Category has been added successfully.');
        return redirect('/admin/sub_categories');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $subcategory = DB::table('sub_categories')
                ->select('sub_categories.*', 'categories.name as category_name')   
                ->leftJoin('categories', 'sub_categories.category_id', '=', 'categories.id')
                ->where('sub_categories.id', '=', $id)
                ->get();

            if(empty($subcategory)) {
                    Session::flash('error_msg', 'Sub Category not found.');
                    return redirect('/admin/sub_categories');
            }
            return view('admin/SubCategory/show', ['title_for_layout' => 'Sub Category', 'subcategory' => array_shift($subcategory)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
            $sub_category = SubCategory::find($id);
            $categories = DB::table('categories')->lists('name', 'id');
            if(empty($sub_category)) {
                Session::flash('error_msg', 'Sub Category not found.');
                return redirect('/admin/sub_categories');
            }
            return view('admin/SubCategory/edit', ['title_for_layout' => 'Edit Sub Category',
                'categories' => $categories, 'subcategory' => $sub_category]);
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
                        'name'          => 'required|max:255',
                        'status'        => 'required',
                        'category_id'   => 'required',
                    ]);
            if ($validator->fails()) {
                    return redirect('admin/sub_categories/' . $id . '/edit')
                        ->withInput()
                        ->withErrors($validator);
            }
            else {
                    $subcategory = SubCategory::find($id);
                    $subcategory->name          = $request->name;
                    $subcategory->status        = $request->status;                   
                    $subcategory->category_id   = $request->category_id;                   
                    $subcategory->save();

                    Session::flash('success_msg', 'Sub Category has been updated successfully.');
                    return redirect('/admin/sub_categories');
            }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sub_category = SubCategory::findOrFail($id);
        $sub_category->delete();
        echo 1;exit;
    }
}
