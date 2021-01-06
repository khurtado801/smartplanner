@extends('layouts.admin')
@section('content')


<div class="panel-body">

    {!! Form::model($EmailTemplates,array('route' => array('admin.emailtemplates.update', $EmailTemplates->id),'files'=>true,'class'=>'form-horizontal','method'=>'PUT','id'=>'car')) !!}
    <div class="form-group">
        {!! Form::label('subject', 'Subject'); !!}
        {!! Form::text('subject',null,array('class'=>'form-control')) !!}
    </div> 

    <div class="form-group">
        {!! Form::label('email_content', 'Content'); !!}
        {!! Form::textarea('email_content',null,array('class'=>'ckeditor form-control', 'rows' => '6', 'cols' => '30')) !!}
    </div>

    <div class="form-group">
        {!! Form::label('status', 'Status'); !!}
        {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), null, array('class' => 'form-control')) !!}
    </div> 

    <div class="form-group">
        {!! Form::submit('Update',array('class'=>'btn btn-primary')); !!}
        <a class="btn btn-default" href="{{ url('/admin/emailtemplates')}}">Cancel</a> 
    </div>

    {!! Form::close() !!}
</div>

<script type="text/javascript">

    $("#car").validate({
        ignore: [],
        highlight: function (element) {
            $(element).parent('div').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).parent('div').removeClass('has-error');
        },
        errorElement: 'div',
        errorClass: 'help-block help-block-error',
        //errorElement: 'span',
        rules: {
            subject: {required: true},
            email_content: {required: function () {
                    CKEDITOR.instances.email_content.updateElement();
                }
            },
        },
        messages: {
            subject: {required: "Please enter subject for email template."},
            email_content: {required: "Please enter email content."},
        },
        errorPlacement: function (error, element) {
            if (element.attr('id') == 'email_content') {
                error.insertAfter($('#cke_'+element.attr("id")+''));
            } else {
                error.insertAfter(element);
            }
        },
        success: function (element) {
            $(element).parent('.form-group').removeClass('has-error');
        },
    });
</script>

@endsection
