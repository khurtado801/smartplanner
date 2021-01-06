<html>
    <head>
        <title>Evolved Educator</title>
        <base href="/smartplanner/" />
        <link href="https://fonts.googleapis.com/css?family=Dosis:300,400,500,700|Unica+One" rel="stylesheet">
        <link href="public/app/build/css/font-awesome.css" type="text/css" rel="stylesheet" />
        <link href="public/app/build/css/bootstrap.css" type="text/css" rel="stylesheet" />
        <link href="public/app/build/css/style.css" type="text/css" rel="stylesheet" />
        <link href="public/app/build/css/responsive.css" type="text/css" rel="stylesheet" />
        <style type="text/css">
            .result-discription h4 {
                color: #1e1e1d;
                display: table;
                float: left;
                font-size: 17px;
                font-weight: 600;
                margin: 0 0 14px;
                width: 100%;
            }
        </style>
    </head>
    <body>
        <section>
            <div class="container">
                <div class="guid-work">            
                    <div class="col-xs-12 col-sm-6 col-md-12">
                        <div class="editormain-container">
                            <div id="preview_html">
                                <div class="editordesc">                   
                                    <div class="lession-complete">
                                        <div class="editor_description">
                                            <div class="col-md-5"> <h3> Grade : <?php echo $lesson_data->grade_name; ?> </h3> </div>
                                            <div class="col-md-7"> <h3> Subject : <?php echo $lesson_data->subject_name; ?> </h3> </div>
                                            <div class="col-md-5"> <h3> Theme : <?php echo $lesson_data->theme_name; ?> </h3> </div>
                                            <div class="col-md-7"> <h3> Unit Title & Teacher Name : <?php echo $lesson_data->unit_title; ?> </h3> </div>
                                        </div>
                                    </div>
                                <!--</div>-->

                                <?php
                                if (count($pdfdata) > 0) {
                                    if ($pdfdata->temp_content != '') {
                                        ?>
                                        <!--<div class="editordesc">-->                   
                                            <div class="lession-complete">
                                                <div class="editor_description">
                                                    <div class="col-md-12">
                                                        <?php echo $pdfdata->temp_content; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                <?php } else { ?>
                                    <div class="editordesc">
                                        <div class="result-title">
                                            <h2>No Records found.</h2>              
                                        </div>                                        
                                    </div>
                                <?php } ?>
                            </div>
                        </div>                  
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>
