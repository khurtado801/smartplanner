@extends('layouts.admin')
@section('content')
<?php //print_r($subject); ?>
<div class="form-horizontal">
    <div class="form-group">
        <label class="col-lg-2 control-label">Id :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $subject->id; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Name :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $subject->name; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Grade :</label>
        <div class="col-lg-10">
            <p class="form-control-static">
                <a href="{{ url('admin/subjects/'.$subject->grade_id)}}">
                    <?php echo $subject->grade_name; ?>
                </a>
            </p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Created :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $subject->created_at; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Updated :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $subject->updated_at; ?></p>
        </div>
    </div>
</div>
@endsection
