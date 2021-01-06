<aside class="bg-light lter b-r aside-md hidden-print hidden-xs" id="nav">
    <section class="vbox">
        <section class="w-f scrollable">
            <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333">
                <nav class="nav-primary hidden-xs">
                    <ul class="nav">
                        <li>
                            <a href="{{ url('/admin/dashboard')}}">
                                <i class="fa fa-file-text icon">
                                    <b class="bg-success"></b>
                                </i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <?php
                        $controller = Request::segment(2);
                        $class = '';
                        if ($controller == 'user') {
                            $class = 'class="active"';
                        }
                        ?>
                        <li <?php echo $class ?>>
                           <a <?php echo $class ?> href="{{ url('/admin/user')}}">  
                                <i class="fa fa-file-text icon">
                                    <b class="bg-success"></b>
                                </i>
                                <span>Users</span>
                            </a>
                        </li>
                        
                        <?php
                        $controller = Request::segment(2);
                        $class = '';
                        if ($controller == 'lessons') {
                            $class = 'class="active"';
                        }
                        ?>
                        <li <?php echo $class ?>>
                           <a <?php echo $class ?> href="{{ url('/admin/lessons')}}">
                                <i class="fa fa-file-text icon">
                                    <b class="bg-success"></b>
                                </i>
                                <span>Lessons</span>
                            </a>
                        </li>

                        <?php
                        $controller = Request::segment(2);
                        $class = '';
                        if ($controller == 'grades') {
                            $class = 'class="active"';
                        }
                        ?>
                        <li <?php echo $class ?>>
                           <a <?php echo $class ?> href="{{ url('/admin/grades')}}">
                                <i class="fa fa-file-text icon">
                                    <b class="bg-success"></b>
                                </i>
                                <span>Grades</span>
                            </a>
                        </li>

                        <?php
                        $class = '';
                        if ($controller == 'subjects') {
                            $class = 'class="active"';
                        }
                        ?>
                        <li <?php echo $class ?>>
                           <a <?php echo $class ?> href="{{ url('/admin/subjects')}}">  
                                <i class="fa fa-file-text icon">
                                    <b class="bg-success"></b>
                                </i>
                                <span>Subjects</span>
                            </a>
                        </li>

                        <?php
                        $class = '';
                        if ($controller == 'themes') {
                            $class = 'class="active"';
                        }
                        ?>
                        <li <?php echo $class ?>>
                           <a <?php echo $class ?> href="{{ url('/admin/themes')}}">  
                                <i class="fa fa-file-text icon">
                                    <b class="bg-success"></b>
                                </i>
                                <span>Themes</span>
                            </a>
                        </li>

                        <?php
                        $class = '';
                        if ($controller == 'keyconcepts') {
                            $class = 'class="active"';
                        }
                        ?>
                        <li <?php echo $class ?>>
                           <a <?php echo $class ?> href="{{ url('/admin/keyconcepts')}}">  
                                <i class="fa fa-file-text icon">
                                    <b class="bg-success"></b>
                                </i>
                                <span>Key Concepts</span>
                            </a>
                        </li>
                        <?php
                        $class = '';
                        if ($controller == 'learningtargetsname') {
                            $class = 'class="active"';
                        }
                        ?>
                        <li <?php echo $class ?>>
                           <a <?php echo $class ?> href="{{ url('/admin/learningtargetsname')}}">  
                                <i class="fa fa-file-text icon">
                                    <b class="bg-success"></b>
                                </i>
                                <span>Learning Name</span>
                            </a>
                        </li>
                        <?php
                        $class = '';
                        if ($controller == 'learningtargets') {
                            $class = 'class="active"';
                        }
                        ?>
                        <li <?php echo $class ?>>
                           <a <?php echo $class ?> href="{{ url('/admin/learningtargets')}}">  
                                <i class="fa fa-file-text icon">
                                    <b class="bg-success"></b>
                                </i>
                                <span>Learning Targets</span>
                            </a>
                        </li>

                        <?php
                        $class = '';
                        if ($controller == 'paymentplans') {
                            $class = 'class="active"';
                        }
                        ?>
                        <li <?php echo $class ?>>
                           <a <?php echo $class ?> href="{{ url('/admin/paymentplan')}}">  
                                <i class="fa fa-file-text icon">
                                    <b class="bg-success"></b>
                                </i>
                                <span>Payment Plans</span>
                            </a>
                        </li>                        
                        
                        <li <?php echo ($controller == 'country' || $controller == 'cms' || $controller == 'educationalquotes' || $controller == 'emailtemplates' ? 'class="active"' : ''); ?>>
                           <a <?php echo ($controller == 'country' || $controller == 'cms' || $controller == 'educationalquotes' || $controller == 'emailtemplates' ? 'class="active"' : ''); ?> href="javascript:void(0);">
                               <i class="fa fa-tasks icon">
                                   <b class="bg-success"></b>
                               </i>
                               <span class="pull-right">
                                   <i class="fa fa-angle-down text"></i>
                                   <i class="fa fa-angle-up text-active"></i>
                               </span>
                               <span>Configurations</span>
                           </a>
                           <ul class="nav lt">
                               <li <?php echo ($controller == 'country' ? 'class="active"' : ''); ?>>
                                   <a href="{{ url('/admin/country')}}">                                                        
                                       <i class="fa fa-angle-right"></i>
                                       <span>Country</span>
                                   </a>
                               </li>
                               <li <?php echo ($controller == 'cms' ? 'class="active"' : ''); ?>>
                                   <a href="{{ url('/admin/cms')}}">                                                        
                                       <i class="fa fa-angle-right"></i>
                                       <span>CMS Pages</span>
                                   </a>
                               </li>
                               <li <?php echo ($controller == 'educationalquotes' ? 'class="active"' : ''); ?>>
                                   <a href="{{ url('/admin/educationalquotes')}}">                                                        
                                       <i class="fa fa-angle-right"></i>
                                       <span>Educational Quotes</span>
                                   </a>
                               </li>
                               <li <?php echo ($controller == 'emailtemplates' ? 'class="active"' : ''); ?>>
                                   <a href="{{ url('/admin/emailtemplates')}}">                                                        
                                       <i class="fa fa-angle-right"></i>
                                       <span>Email Templates</span>
                                   </a>
                               </li>
                           </ul>
                       </li>
                        
                        <?php
                        $controller = Request::segment(2);

                        $route = Route::current();
                        $actionName = $route->getActionName();
                        $method = explode("@", $actionName);
                        $methodUser = $method[1];

                        $class = '';
                        if ($controller == 'grades') {
                            $class = 'class="active"';
                        }
                        ?>
                    </ul>
                </nav>
            </div>
        </section>
        <footer class="footer lt hidden-xs b-t b-light">
            <a href="#nav" data-toggle="class:nav-xs" class="pull-right btn btn-sm btn-light btn-icon">
                <i class="fa fa-angle-left text"></i>
                <i class="fa fa-angle-right text-active"></i>
            </a>
        </footer>
    </section>
</aside>
