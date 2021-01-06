<!DOCTYPE html>
<html lang="en" class="app">
    <head>
        <meta charset="UTF-8">
        <?php $controller_name = Request::segment(2); ?>
        <title>Smart Planner Admin</title>
        <meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" /> 

        {{Html::style("/resources/assets/css/bootstrap.css")}}
        {{Html::style("/resources/assets/css/animate.css")}}
        {{Html::style("/resources/assets/css/font-awesome.min.css")}}
        {{Html::style("/resources/assets/css/font.css")}}
        {{Html::style("/resources/assets//js/datatables/datatables.css")}}
        {{Html::style("/resources/assets/css/app.css")}}     
        {{Html::style("/resources/assets/css/extra.css")}}
        {{Html::style("/resources/assets/css/sumoselect.css")}}
        {{Html::script("/resources/assets/js/jquery.min.js")}}
        {{Html::script("/resources/assets/js/jquery.validate.js")}}
        {{Html::script("/resources/assets/js/datatables/jquery.dataTables.min.js")}}
        {{Html::script("/resources/assets/js/additional-methods.min.js")}}

        <!--[if lt IE 9]>
        {{Html::script("/resources/assets/js/ie/html5shiv.js")}}
        {{Html::script("/resources/assets/js/ie/respond.min.js")}}
        {{Html::script("/resources/assets/js/ie/excanvas.js")}}
        <![endif]-->

        @yield('styles')
        <script>
            /* data tables */
            var oTable;
            function  delete_record(id, slider = null) {
                var controller = '<?php echo $controller_name ?>';
                if (slider != null) {
                    controller = "sliders";
                }
                var token = '<?php echo csrf_token() ?>';
                var pageurl = '<?php echo url('/admin') ?>/' + controller + '/' + id;
                var confirm_flag = confirm("Are you sure you want to delete # " + id + "?");
                if (confirm_flag === true) {
                    $.ajax({
                        url: pageurl,
                        method: 'DELETE',
                        data: {'_token': token},
                        success: function (result) {
                            if (slider != null) {
                                jQuery("#" + slider).fadeOut('slow', function () {
                                    jQuery("#" + slider).remove();
                                });
                            } else {
                                if (result == 1) {
                                    $("#row_" + id).fadeOut('slow', function () {
                                        oTable.ajax.reload();
                                    });
                                }
                            }
                        }
                    });
                }
            }
        </script>
    </head>
    <body class="">
        <?php $date_format = "Y-m-d"; ?>
        <section class="vbox">
            @include('layouts.header')
            <section>
                <section class="hbox stretch">
                    @include('layouts.navigation')
                    <section id="content">
                        <section class="vbox">
                            <section class="top-margin scrollable padder">
                                <?php
                                list(, $action_name) = explode('@', Route::getCurrentRoute()->getActionName());
                                ?>
                                <div class="m-b-md">
                                    <h3 class="m-b-none">
                                        <?php
                                        echo $title_for_layout;
                                        $controller = Request::segment(2);
                                        $action = Request::segment(3);
                                        if ($action != 'create' && $controller != 'dashboard' && $action != 'viewPlans') {
                                            echo '<a class="add-new-btn-listing btn btn-default pull-right" href="' . url('/admin/' . $controller_name . '/create') . '">Add New</a> ';
                                        }
//                                $edit_action = Request::segment(4);
//                                if( $edit_action == 'edit' ) {
//                                    echo '<a class="add-new-btn-listing btn btn-default pull-right" href="'. url('/admin/'.$controller_name.'/create').'">Add New</a> ';
//                                }
//                                
                                        ?>
                                    </h3>
                                </div>
                                @include('layouts.notifications')
                                <section class="panel panel-default">
                                    <?php
                                    $content_container = "panel-body";
                                    if ($action_name == 'index' || $action_name == 'viewPlans') {
                                        $content_container = "table-responsive";
                                        ?>
                                        <!--                                <header class="panel-heading">
                                        <?php echo $title_for_layout; ?>
                                                                                <a class="btn btn-xs btn-dark pull-right" 
                                                                                href="{{ url('/admin/'.$controller_name.'/create') }}"><i class="fa fa-plus"></i> Add New</a>
                                        
                                        <?php if ($controller_name == "language") { ?>
                                                                                                        <a class="btn btn-refresh btn-xs btn-dark pull-right" 
                                                                                                        href="{{ url('/admin/'.$controller_name.'/refresh') }}"><i class="fa fa-refresh" aria-hidden="true"></i> Refresh</a>
                                        <?php } ?>
                                                                        </header>-->
                                    <?php } ?>
                                    <div class="<?php echo $content_container ?>">
                                        @include('errors.common_errors')
                                        @yield('content')
                                    </div> 
                                </section>
                                <a href="#" class="hide nav-off-screen-block"
                                   data-toggle="class:nav-off-screen, open" 
                                   data-target="#nav,html"></a>
                            </section>
                        </section>
                    </section>
                </section>
            </section>
        </section>    

        {{Html::script("/resources/assets/js/bootstrap.js")}}
        {{Html::script("/resources/assets/js/app.js")}}
        {{Html::script("/resources/assets/js/general.js")}}
        {{Html::script("/resources/assets/js/slimscroll/jquery.slimscroll.min.js")}}
        {{Html::script("/resources/assets/js/ckeditor/ckeditor.js")}}
        {{Html::script("/resources/assets/js/app.plugin.js")}}
        {{Html::script("/resources/assets/js/file-input/bootstrap-filestyle.min.js")}}
        {{Html::script("/resources/assets/js/common.js")}}

        {{Html::style("/resources/assets/js/datepicker/jquery-datepicker-ui.css")}}
        {{Html::script("/resources/assets/js/datepicker/jquery-datepicker-ui.js")}}

        {{Html::style("/resources/assets/js/select2/select2.css")}}
        {{Html::script("/resources/assets/js/select2/select2.min.js")}}

        {{Html::script("/resources/assets/js/jquery.sumoselect.js")}}

        @yield('scripts')

    </body>
</html>
