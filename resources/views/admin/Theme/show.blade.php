@extends('layouts.admin')
@section('content')

<div class="form-horizontal">
    <div class="form-group">
        <label class="col-lg-2 control-label">Id :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $subcategory->id; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Name :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $subcategory->name; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Title :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $subcategory->title; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Category :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><a href="{{ url('admin/categories/'.$subcategory->category_id)}}"><?php echo $subcategory->category_name; ?></a></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Content :</label>
        <div class="col-lg-10">
            <div class="form-control-static"><?php echo $subcategory->content; ?></div>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Created :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo display_date_ymd($subcategory->created_at); ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Updated :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo display_date_ymd($subcategory->updated_at); ?></p>
        </div>
    </div>
</div>
@endsection
