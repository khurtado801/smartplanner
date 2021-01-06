@extends('layouts.admin')
@section('content')

<div class="panel-body">

    {!! Form::model($page,array('route' => array('admin.language.update', $page->id),'files'=>true,'class'=>'form-horizontal','method'=>'PUT','id'=>'language')) !!}
       <div class="form-group">
            {!! Form::label('label', 'Label'); !!}
            {!! Form::text('label',null,array('class'=>'form-control', 'readonly' => 'true')) !!}
        </div>

        <div class="form-group">
            {!! Form::label('changed_label', 'Changed Label'); !!}
            {!! Form::text('changed_label',null,array('class'=>'form-control')) !!}
        </div>

        <div class="form-group">
            {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
            <a class="btn btn-default" href="{{ url('/admin/language')}}">Cancel</a> 
        </div>
    {!! Form::close() !!}
</div>

@endsection
