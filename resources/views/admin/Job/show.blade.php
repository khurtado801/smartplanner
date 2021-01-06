@extends('layouts.admin')
@section('content')

<div class="form-horizontal">

    <div class="form-group">
        <label class="col-lg-2 control-label">Job Id :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $jobdeatils->id; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Category :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $jobcategory; ?></p>
        </div>
    </div>
    
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Title :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $jobdeatils->job_title; ?></p>
        </div>
    </div>
    
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Sub Title :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $jobdeatils->job_subtitle; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Key Words :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $jobdeatils->job_keywords; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Stage :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $jobdeatils->job_stage; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Skills :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $allSkills?></p>
        </div>
    </div> 

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Description :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $jobdeatils->job_description; ?></p>
        </div>
    </div> 

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Comments :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $jobdeatils->job_comments; ?></p>
        </div>
    </div> 

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Price Range:</label>
        <div class="col-lg-10">
            <div class="form-control-static"><?php echo $jobdeatils->job_cost_min; ?> To <?php echo $jobdeatils->job_cost_max; ?> CHF</div>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Duration:</label>
        <div class="col-lg-10">
            <div class="form-control-static"><?php echo $jobdeatils->job_stattime; ?> To <?php echo $jobdeatils->job_endtime; ?></div>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Available For:</label>
        <div class="col-lg-10">
            <div class="form-control-static"><?php echo $jobdeatils->job_availble_for; ?></div>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Visible duration:</label>
        <div class="col-lg-10">
            <div class="form-control-static"><?php echo $jobdeatils->job_visible_duration; ?></div>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Documents :</label>
        <div class="col-lg-10">
            <div class="form-control-static">
                 <?php               
            if($jobDocArray == "N/A")
            {
                echo "No Document Uploaded"; 
            } 
            else
            {
                $docCount =1;
                foreach ($jobDocArray as $key => $value)
                {
                    ?>
                    <a target="_blank" href="<?php echo URL::to('storage/jobDocuments').'/'.$value; ?>">
                        <?php echo $docCount; ?>. Document
                    </a>
                    &nbsp;
                    <?php
                    $docCount++;
                }                   

                ?>
                <strong>[ <?php echo $docCount - 1; ?> Uploaded Document(s) ]</strong>
                <?php
            }
            ?>

            </div>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Status :</label>
        <div class="col-lg-10">
            <div class="form-control-static"><?php echo $jobdeatils->status; ?></div>
        </div>
    </div>
    
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Created At :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $jobdeatils->created_at; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Updated Date:</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $jobdeatils->updated_at; ?></p>
        </div>
    </div>
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Posted By :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $username; ?></p>
        </div>
    </div>
</div>
@endsection