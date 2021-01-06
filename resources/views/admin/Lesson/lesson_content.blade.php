@extends('layouts.admin')
@section('content')
<?php //print_r($lesson_content); exit;?>
<div class="form-horizontal">
    <div class="form-group">
        <!--<label class="col-lg-2 control-label">Content :</label>-->
        <div class="col-lg-12">
            <p class="form-control-static"><?php echo $lesson_content[0]; ?></p>
        </div>
    </div>
</div>
@endsection
