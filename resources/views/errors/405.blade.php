<!DOCTYPE html>
<html lang="en" class="app">
<head>
  <meta charset="utf-8" />
  <title>405</title>
  <meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        {{Html::style("/resources/assets/css/bootstrap.css")}}
        {{Html::style("/resources/assets/css/animate.css")}}
        {{Html::style("/resources/assets/css/font-awesome.min.css")}}
        {{Html::style("/resources/assets/css/font.css")}}
        {{Html::style("/resources/assets/css/app.css")}}
        {{Html::script("/resources/assets/js/jquery.min.js")}}
        {{Html::script("/resources/assets/js/bootstrap.js")}}
        {{Html::script("/resources/assets/js/app.js")}}
        {{Html::script("/resources/assets/js/slimscroll/jquery.slimscroll.min.js")}}
        {{Html::script("/resources/assets/js/ckeditor/ckeditor.js")}}
        {{Html::script("/resources/assets/js/app.plugin.js")}}
  <!--[if lt IE 9]>
    <script src="js/ie/html5shiv.js"></script>
    <script src="js/ie/respond.min.js"></script>
    <script src="js/ie/excanvas.js"></script>
  <![endif]-->
</head>
<body class="">
    <section id="content">
    <div class="row m-n">
      <div class="col-sm-4 col-sm-offset-4">
        <div class="text-center m-b-lg">
          <h1 class="h text-white animated fadeInDownBig">405</h1>
        </div>
        <div class="list-group m-b-sm bg-white m-b-lg">
          <div href="javascript:void(0);" class="list-group-item">
             Oops, Method Not Allowed!
          </div>
          <a href="{{ url('/admin/dashboard')}}" class="list-group-item">
            <i class="fa fa-chevron-right icon-muted"></i>
            <i class="fa fa-fw fa-home icon-muted"></i>
            Go to Dashboard
          </a>
        </div>
      </div>
    </div>
  </section>
  <!-- footer -->
  <footer id="footer">
    <div class="text-center padder clearfix">
      <p>
        <small>Smart Planner<br>&copy; 2016</small>
      </p>
    </div>
  </footer>
</body>
</html>
