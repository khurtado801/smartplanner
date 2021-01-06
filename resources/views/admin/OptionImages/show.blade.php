@extends('layouts.admin')
@section('content')

<div class="form-horizontal">
    <div class="form-group">
        <label class="col-lg-2 control-label">Id :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $bannerImage->id; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Title :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $bannerImage->title; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Image:</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $bannerImage->image; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Status:</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $bannerImage->status; ?></p>
        </div>
    </div>

    <?php
    if(!empty($sliders)) {
        echo '<div class="line line-dashed line-lg pull-in"></div>';
        echo '<div class="row">';
        foreach ($sliders as $slider) {
            $Image = storage_path().'/sliderImages/'.$slider->image;
            if(file_exists($Image)) {
                ?>
                <div class="col-lg-4">
                    <section class="panel panel-default">
                        <header class="panel-heading">Slider Image</header>
                        <div class="panel-body text-center">              
                            <div class="sparkline inline">
                                <img src="{{ URL::to('/storage/sliderImages/'.$slider->image) }}" width="334px" height="150px" />
                            </div>
                        </div>
                    </section>
                </div>
                <?php
            }
        }
        echo '</div>';
    }
    ?>    
    
    <div class="line line-dashed line-lg pull-in"></div>

    <div class="form-group">
        <label class="col-lg-2 control-label">Created :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $bannerImage->created_at; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Updated :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $bannerImage->updated_at; ?></p>
        </div>
    </div>
</div>
@endsection