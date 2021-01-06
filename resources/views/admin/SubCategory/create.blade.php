@extends('layouts.admin')
@section('content')

    <div class="panel-body">
                
        {!! Form::open(array('url'=>'/admin/sub_categories','class'=>'form-horizontal','method'=>'POST','id'=>'subcategory')) !!}

            <div class="form-group">
                {!! Form::label('category_id', 'Category'); !!}
                {!! Form::select('category_id',  ['' => 'Select Category']+$categories, null, ['class' => 'form-control']) !!}
            </div>
        
            <div class="form-group">
                {!! Form::label('name', 'Name'); !!}
                {!! Form::text('name',null,array('class'=>'form-control')) !!}
            </div>           
            
            <div class="form-group">
                {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
                <a class="btn btn-default" href="{{ url('/admin/sub_categories')}}">Cancel</a> 
            </div>
        {!! Form::close() !!}
    </div>

@endsection
