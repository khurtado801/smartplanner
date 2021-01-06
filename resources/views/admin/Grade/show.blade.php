@extends('layouts.admin')
@section('content')

<div class="form-horizontal">
    <div class="form-group">
        <label class="col-lg-2 control-label">Id :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $grade->id; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Name :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $grade->name; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Status :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $grade->status; ?></p>
        </div>
    </div>
   
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Created :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $grade->created_at; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Updated :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $grade->updated_at; ?></p>
        </div>
    </div>
</div>
@endsection
