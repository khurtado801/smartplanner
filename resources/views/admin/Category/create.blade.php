@extends('layouts.admin')
@section('content')

<div class="panel-body">
    {!! Form::open(array('url'=>'/admin/categories','class'=>'form-horizontal','method'=>'POST','id'=>'category')) !!}

    <div class="form-group">
        {!! Form::label('name', 'Category Name'); !!}
        {!! Form::text('name',null,array('class'=>'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('status', 'Status'); !!}
        {!! Form::select('status', array('Active' => 'Active', 'InActive' => 'InActive'), null, array('class' => 'form-control')) !!}
    </div>
    
    <div class="form-group">
        {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
        <a class="btn btn-default" href="{{ url('/admin/categories')}}">Cancel</a> 
    </div>
    {!! Form::close() !!}
</div>

@endsection