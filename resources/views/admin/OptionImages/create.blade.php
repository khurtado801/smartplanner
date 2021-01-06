@extends('layouts.admin')
@section('content')

<div class="panel-body">                
    {!! Form::open(array('url'=>'/admin/option_images','files'=>true,'class'=>'form-horizontal','method'=>'POST','id'=>'option')) !!}

    <div class="form-group" id="sliderimage">
    {!! Form::label('image', 'Add Multiple Images'); !!}
        <a class="btn btn-sm btn-default add_slider_images" href="javascript:void(0);"><i class="fa fa-plus text"></i></a>
        <div id="0" class="input-group">
            {!! Form::file('image',array('name' => "image[0]",'class'=>'filestyle slider_image_file', 'data-icon' => "false", 'data-classButton' => "btn btn-default", 'data-classInput' => "form-control inline input-s errorcontain")) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
        <a class="btn btn-default" href="{{ url('/admin/option_images')}}">Cancel</a> 
    </div>
    {!! Form::close() !!}
</div>

@endsection
