@extends('layouts.admin')
@section('content')

    <div class="panel-body">
                
        {!! Form::open(array('url'=>'/admin/options','files'=>true,'class'=>'form-horizontal','method'=>'POST','id'=>'option')) !!}

            <div class="form-group">
                {!! Form::label('sub_category_id', 'Sub Category'); !!}
                {!! Form::select('sub_category_id',  ['' => 'Select Sub Category']+$subcategories, null, ['class' => 'form-control']) !!}
            </div>
        
            <div class="form-group">
                {!! Form::label('title', 'Title'); !!}
                {!! Form::text('title',null,array('class'=>'form-control')) !!}
            </div>
        
            <div class="form-group">
                {!! Form::label('price', 'Price'); !!}
                {!! Form::text('price',null,array('class'=>'form-control')) !!}
            </div>
            
            <div class="form-group">
                {!! Form::label('image', 'Image'); !!}
                {!! Form::file('image',array('class'=>'filestyle','id'=>'image', 'data-icon' => "false", 'data-classButton' => "btn btn-default", 'data-classInput' => "form-control inline input-s errorcontain")) !!}
            </div>
       
            <div class="form-group">
                {!! Form::label('content', 'Content'); !!}
                {!! Form::textarea('content',null,array('class'=>'ckeditor form-control', 'rows' => '6', 'cols' => '30')) !!}
            </div>
        
            <div class="form-group">
                {!! Form::label('status', 'Status'); !!}
                {!! Form::select('status', array('1' => 'Active', '0' => 'InActive'), null, array('class' => 'form-control')) !!}
            </div>
            
            <div class="form-group">
                {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
                <a class="btn btn-default" href="{{ url('/admin/options')}}">Cancel</a> 
            </div>
        {!! Form::close() !!}
    </div>

@endsection