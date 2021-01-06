@extends('layouts.admin')
@section('content')

<div class="panel-body">                
{!! Form::open(array('url'=>'/admin/cms','files'=>true,'class'=>'form-horizontal','method'=>'POST','id'=>'cms')) !!}

    <div class="form-group">
        {!! Form::label('title', 'Page Title'); !!}
        {!! Form::text('title',null,array('class'=>'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('content', 'Page Description'); !!}
        {!! Form::textarea('description',null,array('class'=>'ckeditor form-control', 'rows' => '6', 'cols' => '30')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('status', 'Status'); !!}
        {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), null, array('class' => 'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
        <a class="btn btn-default" href="{{ url('/admin/cms')}}">Cancel</a> 
    </div>

    {!! Form::close() !!}
</div>

@endsection
