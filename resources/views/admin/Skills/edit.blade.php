@extends('layouts.admin')
@section('content')

<div class="panel-body">

    {!! Form::model($page,array('route' => array('admin.skills.update', $page->id),'files'=>true,'class'=>'form-horizontal','method'=>'PUT','id'=>'skill')) !!}

    <div class="form-group">
        {!! Form::label('skill', 'Skills'); !!}
        {!! Form::text('skill',null,array('class'=>'form-control')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('status', 'Status'); !!}
        {!! Form::select('status', array('Active' => 'Active', 'InActive' => 'InActive'), null, array('class' => 'form-control')) !!}
    </div> 


    <div class="form-group">
        {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
        <a class="btn btn-default" href="{{ url('/admin/skills')}}">Cancel</a> 
    </div>

    {!! Form::close() !!}
</div>

@endsection