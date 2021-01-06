<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Session;
use Auth;
use App\OptionImages;
use App\Car;
use App\Option;
use App\CarOption;
use App\Banner;
use Illuminate\Support\Facades\Input;
use File;
use Illuminate\Support\Facades\Validator;
use Yajra\Datatables\Datatables;

class OptionImagesController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }
    
    public function index() {
        return view('admin/OptionImages/index', ['title_for_layout' => 'Car Images']);
    }

    public function create(Request $request) 
    { 
        return view('admin/OptionImages/create', ['title_for_layout' => 'Add Banner Images']);
    }

    public function getData() {
         return Datatables::of(Banner::query())->make(true);
    }

    public function store(Request $request) {
        
            $validator = Validator::make($request->all(), [
                    'car_id' => 'required|integer',
            ]);
                        
            //Save slider image 
            if($request->hasFile('image')) {
                $file = Input::file('image');
                if(!empty($file)) {
                    foreach ($file as $file_obj) {
                        $slider_image = new Slider;
                        $slider_image->option_image_id = $optionImage->id;
                        $timestamp = time().  uniqid();
                        $name = $timestamp. '-' .trim($file_obj->getClientOriginalName());
                        $file_obj->move(storage_path().'/sliderImages/', $name);
                        $slider_image->image = $name;
                        $slider_image->save();
                    }
                }
            }       
        
            Session::flash('success_msg', 'Banner Image has been added successfully.');
            return redirect('/admin/option_images');
    }

    public function show($id) {

            $bannerImage = Banner::find($id);            
            
            return view('admin/OptionImages/show', ['title_for_layout' => 'Banner Image', 
                'bannerImage' => $bannerImage = Banner::find($id)]);
    }


    public function edit($id)
    {
            $optionImage = OptionImages::find($id);
            if(empty($optionImage)) {
                Session::flash('error_msg', 'Car Image not found.');
                return redirect('/admin/option_images');
            }
            $body_colors =  DB::table('options')
                ->select('options.id', 'options.title')   
                ->leftJoin('sub_categories', 'options.sub_category_id', '=', 'sub_categories.id')
                ->where('sub_categories.id', '=', 1)
                ->lists('options.title', 'options.id');
        
            $wheel_colors =  DB::table('options')
                    ->select('options.id', 'options.title')   
                    ->leftJoin('sub_categories', 'options.sub_category_id', '=', 'sub_categories.id')
                    ->where('sub_categories.id', '=', 2)
                    ->lists('options.title', 'options.id');
        
            $cars =  DB::table('cars')->lists('cars.title', 'cars.id');
            $slider = DB::table('sliders')->where('option_image_id', $id)->get();
            return view('admin/OptionImages/edit', ['title_for_layout' => 'Edit Option',
                'bodycolors' => $body_colors, 'wheelcolors' => $wheel_colors,
                'cars' => $cars, 'optionImage' => $optionImage, 'slider' => $slider]);
    }

    public function update(Request $request, $id)
    {
            $validator = Validator::make($request->all(), [
                'car_id' => 'required|integer',
               
            ]);
            if ($validator->fails()) {
                    return redirect('admin/option_images/' . $id . '/edit')
                        ->withInput()
                        ->withErrors($validator);
            }
            $option_image = OptionImages::find($id);
            

            $option_image->car_id = $request->car_id;
            $option_image->bodycolor = $request->bodycolor;
            $option_image->wheelcolor = $request->wheelcolor;
         
            $option_image->save();
            
                $file = Input::file('image');
               
                if(!empty($file)) {
                          $file = array_filter($file);                  
                    foreach ($file as $file_obj) {
                            $slider_image = new Slider;
                            $slider_image->option_image_id = $id;
                            $timestamp = time().  uniqid();
                            $name = $timestamp. '-' .trim($file_obj->getClientOriginalName());
                            $file_obj->move(storage_path().'/sliderImages/', $name);
                            $slider_image->image = $name;
                            $slider_image->save();
                    }
                }

            Session::flash('success_msg', 'Car Image has been updated successfully.');
            return redirect('/admin/option_images');
    }


    public function destroy($id)
    {
        $option_image = OptionImages::findOrFail($id);
        
        if(!empty($option_image->image)) {
            $oldImage = storage_path().'/sliderImages/'.$option_image->image;
            if(file_exists($oldImage)) {
                File::delete($oldImage);
            }
        }
        $option_image->delete();
        echo 1;exit;
    }
}
