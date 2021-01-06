@extends('layouts.admin')
@section('content')

<div class="form-horizontal">
    <div class="form-group">
        <label class="col-lg-2 control-label">Id :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $skill->id; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Skill :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $skill->skill; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Status :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $skill->status; ?></p>
        </div>
    </div>   
</div>
@endsection