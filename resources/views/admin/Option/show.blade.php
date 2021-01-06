@extends('layouts.admin')
@section('content')

<div class="form-horizontal">
    <div class="form-group">
        <label class="col-lg-2 control-label">Id :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $option->id; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Title :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $option->title; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Sub Category :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><a href="{{ url('admin/sub_categories/'.$option->sub_category_id)}}"><?php echo $option->subcategory_name; ?></a></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Image :</label>
        <div class="col-lg-10">
            <p class="form-control-static">
                <img src="{{ URL::to('/storage/optionImages/'.$option->image) }}" width="100px" height="100px" />
            </p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Price :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $option->price; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Content :</label>
        <div class="col-lg-10">
            <div class="form-control-static"><?php echo $option->content; ?></div>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Created :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo display_date_ymd($option->created_at); ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Updated :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo display_date_ymd($option->updated_at); ?></p>
        </div>
    </div>
</div>
@endsection