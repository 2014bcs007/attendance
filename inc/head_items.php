<head>

    <meta charset="utf-8" />
    <title><?php echo $TITLE . $APP_NAME ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $DEVELOPER['name'] . ' - ' . $APP_NAME . ' - ' . $DEVELOPER['app_description'] ?>" />

    <link rel="canonical" href="<?php echo $DEVELOPER['url'] ?>" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo $DEVELOPER['name'] . ' - ' . $APP_NAME . ' - ' . $DEVELOPER['app_description'] ?>" />
    <meta property="og:description" content="<?php echo $DEVELOPER['name'] . ' - ' . $APP_NAME . ' - ' . $DEVELOPER['app_description'] ?>" />
    <meta property="og:url" content="https://famitechsolutions.com/" />
    <meta property="og:site_name" content="<?php echo $DEVELOPER['name'] . ' - ' . $APP_NAME ?>" />
    <meta property="og:image" content="<?php echo $SYSTEM_URL . '/' . $APP_FAVICON ?>" />
    <meta property="og:image:width" content="2056" />
    <meta property="og:image:height" content="755" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:label1" content="Est. reading time">
    <meta name="twitter:data1" content="2 minutes">

    <style>
        .select2-dropdown {
            z-index: 1060 !important;
        }

        .navbar-menu .navbar-nav .nav-link {
            padding: 7px 1.5rem 0 1.5rem !important;
        }
    </style>

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

    <link rel="stylesheet" href="assets/libs/select2/select2.min.css">

    <link rel="stylesheet" href="assets/libs/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/libs/datatables.net/datatables.min.css" />

</head>