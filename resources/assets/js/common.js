$(document).ready(function () {
    $('#user').validate({
        rules: {
            firstname: "required",
            lastname: "required",
            terms_conditions: "required",
            phone_number: "required",
            usertype: "required",
            password: {
                required: true,
                minlength: 6
            },
            confirmpassword: {
                required: true,
                minlength: 6,
                equalTo: "#password"
            },
            email: {
                required: true,
                email: true
            },
            country: "required"
        },
        messages: {
            firstname: "Please enter your firstname.",
            phone_number: "Please enter your phone number.",
            lastname: "Please enter your lastname.",
            password: {
                required: "Please provide password.",
                minlength: "Your password must be at least 6 characters long."
            },
            confirmpassword: {
                required: "Please provide confirm password",
                minlength: "Your password must be at least 6 characters long.",
                equalTo: "Password and confirm password must be same."
            },
            email: "Please enter a valid email address.",
            terms_conditions: "Please accept our policy.",
            usertype: "Please select user type.",
            country: "Please select country."
        }

    });

    $('#category').validate({
        rules: {
            name: "required",
            status: "required",
        },
        messages: {
            name: "Please enter Category Name.",
            status: "Category status is required.",
        }
    });

    $('#subcategory').validate({
        rules: {
            "name": {required: true, },
            "category_id": {required: true, },
        },
        errorPlacement: function (error, element)
        {
            return true;
        }
    });

    $('#skills').validate({
        rules: {
            skill: "required",
            status: "required",
        },
        messages: {
            skill: "Please enter skills.",
            status: "Skill status is required.",
        }
    });

    $('#language').validate({
        rules: {
            changed_label: "required",
            label: "required",
        },
        messages: {
            changed_label: "Please enter new label.",
            label: "Page enter label.",
        }
    });

    $('#cms').validate({
        rules: {
            title: "required",
            content: "required",
        },
        messages: {
            title: "Please enter page title.",
            content: "Page enter page content.",
        }
    });

    $('#jobs').validate({
        rules: {
            "job_category": {required: true, },
            "job_title": {required: true, },
            "job_subtitle": {required: true, },
            "job_stage": {required: true, },
            "job_cost_max": {required: true, },
            "job_cost_min": {required: true, },
            "job_stattime": {required: true, },
            "job_endtime": {required: true, },
            "job_availble_for": {required: true, },
            "job_visible_duration": {required: true, },
            "job_keywords": {required: true, },
            "terms_conditions": {required: true, },
            "job_skills[]": {required: true, },
            "status": {required: true, },
        },
        messages: {
            job_category: "Please select job category.",
            job_title: "Please enter job title.",
            job_subtitle: "Please enter job subtitle.",
            job_stage: "Please select job stage.",
            job_cost_max: "Please enter maximum job cost.",
            job_cost_min: "Please enter minimum job cost.",
            job_stattime: "Please select job start time.",
            job_endtime: "Please select job end time.",
            usertype: "Please select job available for user type.",
            job_visible_duration: "Please select job visible up to date.",
            job_keywords: "Please enter job search keywords.",
            job_skills: "Please select job skills",
            terms_conditions: "This is required field",
            status: "Skill status is required.",
        }
    });

    $('#option').validate({
        rules: {
            "title": {required: true, },
            "sub_category_id": {required: true, },
            "image": {required: false, extension: "jpeg|jpg|png|gif"},
            "price": {required: true, },
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "image") {
                $(".errorcontain").addClass('error');
            }
            return true;
        }
    });


    $(document).ready(function () {
        /* date picker */
        $(".datepicker").datepicker({
            minDate: new Date(),
            changeMonth: true,
            changeYear: true,
        });
    });

    /* select2 */
    $(".select2").select2();

    /*append field*/
    init_multifield(5, '.input_fields_wrap', '.add_field_button', 'documents[]');
    init_multifield(1, '.input_fields_wrap2', '.add_field_button2', 'profile_image[]');/* used for admin */
    init_multifield(1, '.input_fields_wrap3', '.add_field_button3', 'videos[]');
    init_multifield(1, '.input_fields_wrap4', '.add_field_button4', 'sva_document[]');
    init_multifield(5, '.input_fields_wrap5', '.add_field_button5', 'portfolio_images[]');
    init_multifield(3, '.input_fields_wrap6', '.add_field_button6', 'company_verification_documents[]');

    function init_multifield(max, wrap, butt, fname_p) {
        var max_fields = max; //maximum input boxes allowed
        var wrapper = $(wrap); //Fields wrapper
        var add_button = $(butt); //Add button class
        var fname = fname_p;

        var x = 0; //initlal text box count
        $(add_button).click(function (e) { //on add input button click
            e.preventDefault();
            if (x < max_fields) { //max input box allowed
                x++; //text box increment
                var cstring = '$(wrapper).append(\'<div class="form-group files"><div class="col-md-6"><input class="form-control" type="file" name=' + fname + '></div><div class="col-md-6"><div class="remove_field btn btn-primary">Delete</div></div></div>\');' //add input box
                eval(cstring);
            }
        });

        $(wrapper).on("click", ".remove_field", function (e) { //user click on remove text
            e.preventDefault();
            $(this).closest('.files').remove();
            x--;
        })
    }
   
    //var counter = 1;
    var counter = $(".gradelist").length;
    $("#theme").on('click', '.add_options', function () {
         
   
        var grades = $('#grade_id').html();
        var subjects = $('#subject_id').html();
        //console.log(grades);
        var html = '<div class="row"><div class="col-md-5">' +
                '<label class="control-label"> Grade </label>' +
                '<select id="grade_id" class="form-control gradelist" name="grade_id['+counter+'][]" multiple="multiple">'+ grades +'</select></div>' +
                '<div class="col-md-6">' +
                '<label class="control-label"> Subject </label>' +
                '<select id="subject_id" class="form-control subjectlist" name="subject_id[]">'+ subjects +'</select></div>' +
                '<div class="col-md-1"><div class="form-group no-margin"><label class="control-label">&nbsp;</label><a class="form-control btn btn-outline btn-circle dark btn-sm red delete_options" style="line-height: 1.8;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;" href="javascript:;"><i class="fa fa-trash-o"> &nbsp;Delete</i> </a></div>' +
                '</div></div>';
//        var html = '<div class="row"><div class="col-md-10">' +
//                '<div class="col-md-8"><div class="form-group">' +
//                '<label class="control-label">Answer ' + counter + '</label>' +
//                '<input type="text" class="form-control" id="values" value="" name="question_answer[]"><input type="hidden" class="form-control chk_box_value" id="answer" value="0" name="question_value[]"></div>' +
//                '</div>' +
//                '<div class="col-md-1" style="margin: -2px 0px 0px 5px;"><div class="form-group"><label class="control-label">&nbsp;</label><a class="form-control btn btn-outline btn-circle dark btn-sm red delete_options" style="line-height: 1.8;font-family:"Helvetica Neue",Helvetica,Arial,sans-serif;" href="javascript:;"><i class="fa fa-trash-o"> &nbsp;Delete</i> </a></div>' +
//                '</div></div>' + '<div class="col-md-2"><div class="form-group"><input name="correct_answer[]" type="checkbox" value="0" style="margin: 25px 0 0 -46px;" class="form-control chk_box_val_options"></div></div></div>';
        $(".extra-attribute:last").append(html);
        counter++;

        $('.gradelist').SumoSelect({
            selectAll: true,
            triggerChangeCombined: false,
            placeholder: '--- Select Grade ---'
        });
    });

    $(document).on('click', '.delete_options', function () {
        $(this).parent().parent().parent().remove();
    });
});


