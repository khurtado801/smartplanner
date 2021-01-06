<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    public function getOptionBySubCategory($subcategory){
        $options = DB::table('options')->where('sub_category_id', $subcategory)->lists('title', 'id');
        return $options;
    }
    
    public function getOptionById($id){
        $data = DB::table('options')->where('id', $id)->get();
        return array_shift($data);
    }
}
