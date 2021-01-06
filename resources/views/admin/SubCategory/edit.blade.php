@extends('layouts.admin')
@section('content')
    <div class="panel-body">
        {!! Form::model($subcategory,array('route' => array('admin.sub_categories.update', $subcategory->id),'class'=>'form-horizontal','method'=>'PUT','id'=>'subcategory')) !!}
            <div class="form-group">
                {!! Form::label('category_id', 'Category'); !!}
                {!! Form::select('category_id',  ['' => 'Select Category']+$categories, null, ['class' => 'form-control']) !!}
            </div>
        
            <div class="form-group">
                {!! Form::label('name', 'Sub Category'); !!}
                {!! Form::text('name',null,array('class'=>'form-control')) !!}
            </div>

            <div class="form-group">
                {!! Form::label('status', 'Status'); !!}
                {!! Form::select('status', array('Active' => 'Active', '0' => 'Inactive'), null, array('class' => 'form-control')) !!}
            </div> 

            <div class="form-group">
                {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
                <a class="btn btn-default" href="{{ url('/admin/sub_categories')}}">Cancel</a> 
            </div>
        {!! Form::close() !!}
    </div>
@endsection