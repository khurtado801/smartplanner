@extends('layouts.admin')
@section('content')

<div class="form-horizontal">
    <div class="form-group">
        <label class="col-lg-2 control-label">Id :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $user->id; ?></p>
        </div>
    </div>
     <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Profile Image :</label>
        <div class="col-lg-10">
            <p class="form-control-static">
            <?php
                $profileImage  = $user->profile_image;
                $defaultPath   = URL::to('/storage/adminProfileImages/').'/'.'avatar.jpg';
                if($profileImage && $profileImage !="")
                {
                    $imgPath = URL::to('/storage/adminProfileImages/').'/'.$profileImage;

                    if (file_exists($imgPath)) 
                    {
                        $imgPath = $imgPath;
                    }
                    else
                    {
                       
                        $imgPath = $defaultPath;
                    } 
                }
                else
                {
                    $imgPath = $defaultPath; 
                } 
            ?>
            <img class="m-r-sm" alt="Not Found" src="<?php echo $imgPath; ?>">
            </p>
        </div>
    </div>   
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Full Name :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $user->firstname." ".$user->lastname; ?></p>
        </div>
    </div>
    
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Gender :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $user->email; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Phone Number :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $user->phone_number; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Email :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->gender; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Birth Date :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->birth_date; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Qualifications :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->qualifications; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">School / Gratuation :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->school_gratuation; ?></p>
        </div>
    </div>
    
    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Country :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->countryid; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">State :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->stateid; ?></p>
        </div>
    </div>   

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">City :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->stateid; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Location :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->location; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Zip Code :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->zipcode; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Company Address :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->company_address; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Commercial Register Number :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->commercial_register_number; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Company Verification Document(s) :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->company_verification_documents; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Job Title :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->job_title; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Hourly Rate :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->hourly_rate; ?> &nbsp;CHF</p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Website :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->website; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">SVA Number :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->sva_number; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">SVA Document(s):</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->sva_document; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">VAT Number :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->vat_number; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Portfolio :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->portfolio_images; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Video :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->videos; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">VAT Number :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->vat_number; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Invoice Address :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->invoice_adress; ?></p>
        </div>
    </div>   

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Deievery Adress :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->delivery_adress; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">User Status :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $user->status; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Registered On :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $user->created_at; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
        <label class="col-lg-2 control-label">Account Created On :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->created_at; ?></p>
        </div>
    </div>

    <div class="line line-dashed line-lg pull-in"></div>
    <div class="form-group">
    <label class="col-lg-2 control-label">Profile Updated On :</label>
        <div class="col-lg-10">
            <p class="form-control-static"><?php echo $userProfile->updated_at; ?></p>
        </div>
    </div>
</div>
@endsection
