
@extends('layouts.admin')
@section('content')
<div class="panel-body">
    {!! Form::open(array('url'=>'/admin/job','class'=>'form-horizontal','method'=>'POST','id'=>'job','files'=>true)) !!}    

    <div class="form-group">
        <div class="col-md-12">
            {!! Form::label('job_category', 'Job Category'); !!}
            {!! Form::select('job_category',  ['' => ' -- Select Category -- ']+$categories, null, ['class' => 'form-control select2']) !!}    
        </div>       
    </div>

    <div class="form-group">        
        <div class="col-md-12">            
            {!! Form::label('job_title', 'Job Title'); !!}
            {!! Form::text('job_title',null,array('class'=>'form-control')) !!}

        </div>
    </div>

    <div class="form-group">
        <div class="col-md-12">            
            {!! Form::label('job_subtitle', 'Job Sub Title'); !!}
            {!! Form::text('job_subtitle',null,array('class'=>'form-control')) !!}
        </div>
    </div>   

    <div class="form-group">        
        <div class="col-md-12">            
            {!! Form::label('job_keywords', 'Job Search Key Words'); !!}
            {!! Form::text('job_keywords',null,array('class'=>'form-control')) !!}
        </div>
    </div>        

    <div class="form-group">
        <div class="col-md-6">
            <div class="form-group">
                <div class="col-md-6">
                    {!! Form::label('job_cost_min', 'Price Cost (Min)'); !!}
                    {!! Form::text('job_cost_min',null,array('class'=>'form-control')) !!}
                </div>
                <div class="col-md-6">
                    {!! Form::label('job_cost_max', 'Price Cost (Max)'); !!}
                    {!! Form::text('job_cost_max',null,array('class'=>'form-control')) !!}
                </div>
            </div>
        </div>   

        <div class="col-md-6">
            <div class="form-group">
                <div class="col-md-6">
                    {!! Form::label('job_stattime', 'Job Start Time'); !!}
                    {!! Form::text('job_stattime',null,array('class'=>'form-control datepicker')) !!}
                </div>
                <div class="col-md-6">
                    {!! Form::label('job_endtime', 'Job End Time'); !!}
                    {!! Form::text('job_endtime',null,array('class'=>'form-control datepicker')) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-6">
            {!! Form::label('job_skills', 'Skills required for the Job'); !!}
            {!! Form::select('job_skills[]', $skills, null,array('multiple', 'class' => 'form-control select2')) !!}  
        </div>
        <div class="col-md-6">
            {!! Form::label('job_stage', 'Job Stage'); !!}
            {!! Form::select('job_stage',  ['' => ' -- Select Job Stage --']+$jobstage, null, ['class' => 'form-control']) !!}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-6">
            {!! Form::label('job_visible_duration', 'Job visible up to'); !!}
            {!! Form::text('job_visible_duration',null,array('class'=>'form-control datepicker')) !!}
        </div>
        <div class="col-md-6">
            {!! Form::label('job_availble_for', 'Job for The User type '); !!}
            {!! Form::select('job_availble_for',  ['' => ' -- Select User Type --']+$usertype, null, ['class' => 'form-control']) !!}
        </div>        
    </div>   

   <!--  <div class="form-group">       
        <div class="col-md-6 file">
            {!! Form::label('documents', 'Upload Document(s)'); !!}
            <div class="form-control">
                {!! Form::file('documents[]', array('multiple'=>true)) !!}
                {!! Form::label('filetype', 'Allowed File Type : txt, pdf, doc, docx, xls, xlsx, odt and size upto 5MB Only'); !!}
            </div>
        </div>    
        <div class="col-md-6 file">
            {!! Form::label('job_images', 'Upload Image(s)'); !!}
            <div class="form-control">
                {!! Form::file('job_images[]', array('multiple'=>true,)) !!}            
                {!! Form::label('job_images', 'Allowed Extensions : jpg, jpeg, png, gif and size upto 1MB Only'); !!}
            </div>
        </div>      
    </div> -->
    
    <div class="form-group">
        <div class="col-md-12">
            {!! Form::label('job_description', 'Job Description'); !!}
            {!! Form::textarea('job_description',null,array('class'=>'ckeditor form-control', 'rows' => '6', 'cols' => '30')) !!}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-12">
            {!! Form::label('job_comments', 'Job Comments'); !!}
            {!! Form::textarea('job_comments',null,array('class'=>'ckeditor form-control', 'rows' => '6', 'cols' => '30')) !!}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-12">
            {!! Form::label('status', 'Status'); !!}
            {!! Form::select('status', array('Active' => 'Active', 'InActive' => 'InActive'), null, array('class' => 'form-control')) !!}
        </div>
    </div>

    <div class="form-group">
        <div class="input_fields_wrap col-md-12">
            <button class="add_field_button btn btn-primary">Add Document(s)</button>        
        </div>       
    </div>   

    <div class="form-group">
        <div class="col-md-12">
            {!! Form::checkbox('terms_conditions',1,array('class'=>'form-control')) !!}
            {!! Form::label('terms_conditions', 'Accept general business terms and conditions'); !!}            
        </div>
    </div>   

    <div class="form-group ">
        <div class="col-md-12">
            {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
            <a class="btn btn-default" href="{{ url('/admin/job')}}">Cancel</a>
        </div>
    </div>   

{!! Form::close() !!}
</div>
@endsection