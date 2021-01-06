@extends('layouts.admin')
@section('content')

<div class="form-horizontal">
    <div class="form-group">
        <label class="col-lg-2 control-label">Id :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $EmailTemplates->id; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Title :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $EmailTemplates->subject; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Slug :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $EmailTemplates->slug; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Content :</label>
        <div class="col-lg-10">
            <div class="form-control-static"><?php echo $EmailTemplates->content; ?></div>
        </div>
    </div> 
     
    
</div>
@endsection