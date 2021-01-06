@extends('layouts.admin')
@section('content')

    <div class="panel-body">
                
        {!! Form::open(array('route' => array('admin.option_images.update', $optionImage->id),'files'=>true,'class'=>'form-horizontal','method'=>'PUT','id'=>'optionimage')) !!}

            <div class="form-group">
                {!! Form::label('car_id', 'Car'); !!}
                {!! Form::select('car_id',  ['' => 'Select Car']+$cars, $optionImage->car_id, ['class' => 'form-control']) !!}
            </div>
        
            <div class="form-group">
                {!! Form::label('bodycolor', 'Body Color'); !!}
                {!! Form::select('bodycolor',  ['' => 'Select Bodycolor']+$bodycolors, $optionImage->bodycolor, ['class' => 'form-control']) !!}
            </div>
        
            <div class="form-group">
                {!! Form::label('wheelcolor', 'Wheel Color'); !!}
                {!! Form::select('wheelcolor',  ['' => 'Select Wheelcolor']+$wheelcolors, $optionImage->wheelcolor, ['class' => 'form-control']) !!}
            </div>
            
        <?php /*
        <div class="form-group">
                {!! Form::label('image', 'Image'); !!}
                {!! Form::file('image',array('class'=>'filestyle', 'data-icon' => "false", 'data-classButton' => "btn btn-default", 'data-classInput' => "form-control inline input-s")) !!}
        </div>
        <?php
        if(!empty($optionImage->image)) {
                $oldImage = storage_path().'/sliderImages/'.$optionImage->image;
                if(file_exists($oldImage)) {
            ?>
            <div class="form-group">
                <img src="{{ URL::to('/storage/sliderImages/'.$optionImage->image) }}" width="100px" height="100px" />
            </div>
            <?php 
                }
        } */
        ?>
        
        
        
<div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label>Slider Images</label>
    </div>
<div class="line line-dashed line-lg pull-in"></div>
            <div class="form-group" id="sliderimage">
                {!! Form::label('image', 'Image'); !!}
                <a class="btn btn-sm btn-default add_slider_images" href="javascript:void(0);"><i class="fa fa-plus text"></i></a>
                <?php
                if(!empty($slider)) {
                    $i = 0;
                    foreach ($slider as $slider_img) {
                        ?>
                        <div id="<?php echo $i ?>" class="input-group">
                              <!--  {!! Form::file('image',array('name' => "image[$i]",'class'=>'filestyle slider_image_file', 'data-icon' => "false", 'data-classButton' => "btn btn-default", 'data-classInput' => "form-control inline input-s errorcontain")) !!} -->
                            <div>
                                <img src="{{ URL::to('/storage/sliderImages/'.$slider_img->image) }}" width="334px" height="150px" />
                            
                            <?php
                           // if($i > 0) {
                            ?>
                            <a href="javascript:void(0);" class="delete_slider_image btn btn-sm btn-default fa fa-times" title="Delete" 
                               onclick="delete_record('<?php echo $slider_img->id ?>', '<?php echo $i?>' )">
                            </a>

                            <?php          
                           //}
                           ?>
                            </div>
                        </div>
                        
                        <?php
                    $i++;
                    }
                }
                else {
                ?>
                    <div id="0" class="input-group">
                        {!! Form::file('image',array('name' => "image[0]",'class'=>'filestyle slider_image_file', 'data-icon' => "false", 'data-classButton' => "btn btn-default", 'data-classInput' => "form-control inline input-s errorcontain")) !!}
                    </div>
                <?php
                }
                ?>
            </div>
        
        
        
        
        
        
        
        
        
        

        
            <div class="form-group">
                {!! Form::submit('Save',array('class'=>'btn btn-primary')); !!}
                <a class="btn btn-default" href="{{ url('/admin/option_images')}}">Cancel</a> 
            </div>
        {!! Form::close() !!}
    </div>

@endsection