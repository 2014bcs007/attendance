<?php
$current_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
if (!isset($_SESSION['immergencepassword']) && !isset($_SESSION['password'])) {
    Redirect::to('index.php?page=' . $crypt->encode("logout") . '&next=' . $crypt->encode($current_url));
}
$MODULE = $_SESSION['module'];
$MODULE = $MODULE ? $MODULE : "attendance";
$system_user_id = $user_id = $_SESSION['user_id'];
$permissions = $_SESSION['permissions'];
if ($_SESSION['employee_id']) {
    $userInfo = DB::getInstance()->getRow("users", $user_id, "*", "user_id");
}
?>
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg">

<?php require_once 'inc/head_items.php' ?>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">
        <!-- THE TOP BAR -->
        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="?page=<?php echo $crypt->encode("dashboard") ?>" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="<?php echo $APP_LOGO_DARK ?>" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="<?php echo $APP_LOGO_DARK ?>" alt="" height="17">
                                </span>
                            </a>

                            <a href="?page=<?php echo $crypt->encode("dashboard") ?>" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="<?php echo $APP_LOGO ?>" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="<?php echo $APP_LOGO ?>" alt="" height="17">
                                </span>
                            </a>
                        </div>

                        <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>

                        <!-- App Search-->
                        <form class="app-search d-none d-md-block">
                            <div class="position-relative">
                                <input type="text" class="form-control" placeholder="Search..." autocomplete="off" id="search-options" value="">
                                <span class="mdi mdi-magnify search-widget-icon"></span>
                                <span class="mdi mdi-close-circle search-widget-icon search-widget-icon-close d-none" id="search-close-options"></span>
                            </div>
                        </form>
                    </div>

                    <div class="d-flex align-items-center">

                        <div class="dropdown d-md-none topbar-head-dropdown header-item">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="page-header-search-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-search fs-22"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                                <form class="p-3">
                                    <div class="form-group m-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                            <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php
                        foreach ($TOPMENU as $index => $menu) {
                            if ((!$menu->module || $menu->module == $MODULE) && (!$menu->permissions || array_intersect($menu->permissions, $permissions))) {

                        ?>
                                <?php if (!($_SESSION['employee_id'] == 'employee')) { ?>
                                    <div class=" mx-2 <?php if ($menu->submenu) { ?>dropdown topbar-head-dropdown<?php } ?> ">
                                        <a <?php if (!$menu->submenu) { ?>href="<?php echo $menu->url ?>" <?php } ?> title="<?php echo $menu->title; ?>" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle p-1" data-toggle="dropdown">
                                            <?php if ($menu->icon) { ?><i class="<?php echo $menu->icon ?> fs-22"></i> <?php } else {
                                                                                                                        echo $menu->title;
                                                                                                                    } ?>
                                        </a>
                                        <?php if ($menu->submenu) { ?>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <?php
                                                foreach ($menu->submenu as $i => $submenu) {
                                                    if ((!$submenu->module || $submenu->module == $MODULE) && (!$submenu->permission || in_array($submenu->permission, $permissions))) { ?>
                                                        <!-- item-->
                                                        <a href="<?php echo $submenu->url ?>" class="dropdown-item notify-item language py-2">
                                                            <span class="align-middle"><?php echo $submenu->title ?></span>
                                                        </a>
                                                <?php }
                                                } ?>
                                            </div>
                                    <?php }
                                    } ?>
                                    </div>
                            <?php }
                        } ?>
                            <?php
                            if (isset($_POST['set_module'])) {
                                $module = $_POST['set_module'];
                                $_SESSION['module'] = $module;
                                Redirect::to("?page=" . $crypt->encode("dashboard"));
                            }
                            ?>
                            <form action="" method="POST">
                                <?php foreach ($MODULES as $i => $module) {
                                    if (in_array($module, $permissions)) { ?>
                                        <button name="set_module" value="<?php echo $module ?>" class="btn btn-sm btn<?php echo $MODULE == $module ? '' : '-outline' ?>-info ms-2"><?php echo $module ?></button>
                                <?php }
                                }
                                ?>
                            </form>

                            <div class="ms-1 header-item d-none d-sm-flex">
                                <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                                    <i class='bx bx-moon fs-22'></i>
                                </button>
                            </div>
                            <div class="dropdown ms-sm-3 header-item">
                                <button type="button" class="btn" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="d-flex align-items-center">
                                        <img class="rounded-circle header-profile-user" src="assets/images/users/avatar-1.jpg" alt="Header Avatar">
                                        <span class="text-start ms-xl-2">
                                            <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"><?php echo $_SESSION['fname'] . ' ' . $_SESSION['lname'] ?></span>
                                            <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text"><?php echo strtoupper($_SESSION['User_Type']) ?></span>
                                        </span>
                                    </span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <?php if (!($_SESSION['student_login'] == 'student')) { ?>
                                        <a class="dropdown-item" href="<?php echo "?page=" . $crypt->encode('profile') ?>"><i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span class="align-middle">Profile</span></a>
                                    <?php } ?>
                                    <a class="dropdown-item" href="?page=<?php echo $crypt->encode('logout') ?>"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- ========== App SIDE  Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="?page=<?php echo $crypt->encode("dashboard") ?>" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="<?php echo $APP_LOGO_DARK ?>" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="<?php echo $APP_LOGO_DARK ?>" alt="" height="17">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="?page=<?php echo $crypt->encode("dashboard") ?>" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="<?php echo $APP_LOGO ?>" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="<?php echo $APP_LOGO ?>" alt="" height="17">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">

                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <?php

                        $openNav = '';      //Need to get the parent menu item based on the current url
                        // $id = array_search('?page='.$_GET['page'], array_column($SIDEMENU, 'url'));
                        // echo("ID: ".$id);

                        foreach ($SIDEMENU as $index => $menu) {
                            // var_dump($menu->submenu);die();
                            if ((!$menu->module || $menu->module == $MODULE) && (!$menu->permissions || array_intersect($menu->permissions, $permissions))) {
                                $isActiveUrl = strpos($current_url, $menu->url) !== false ? 'active' : '';

                        ?>
                                <li class="nav-item">
                                    <a <?php if (!$menu->submenu) { ?>href="<?php echo $menu->url ?>" <?php } else { ?> href="#menu-<?php echo $index ?>" data-toggle="collapse" role="button" aria-expanded="<?php echo $openNav == $menu->title ? 'true' : 'false' ?>" aria-controls="menu-<?php echo $index ?>" <?php } ?> class="nav-link menu-link collapsed <?php echo $isActiveUrl ?>">
                                        <i class="<?php echo $menu->icon ?>"></i> <span data-key="t-<?php echo $index ?>"><?php echo $menu->title ?></span>
                                    </a>
                                    <?php
                                    if ($menu->submenu) { ?>
                                        <div class="collapse menu-dropdown <?php echo $openNav == $menu->title ? 'show' : '' ?>" id="menu-<?php echo $index ?>">
                                            <ul class="nav nav-sm flex-column">
                                                <?php
                                                foreach ($menu->submenu as $i => $submenu) {
                                                    if ((!$submenu->module || $submenu->module == $MODULE) && (!$submenu->permission || in_array($submenu->permission, $permissions))) {
                                                        $isActiveUrl = strpos($current_url, $submenu->url) !== false ? 'current-menu-item active' : ''; ?>
                                                        <li class="nav-item">
                                                            <a <?php if (!$submenu->submenu) { ?>href="<?php echo $submenu->url ?>" <?php } else { ?> data-toggle="collapse" href="#submenu-<?php echo "-$i-$index" ?>" <?php } ?> class="nav-link <?php echo $isActiveUrl ?>"><?php echo $submenu->title ?></a>
                                                            <?php if ($submenu->submenu) { ?>
                                                                <div class="collapse menu-dropdown" id="submenu-<?php echo "-$i-$index" ?>">
                                                                    <ul class="nav nav-sm flex-column">
                                                                        <?php
                                                                        foreach ($submenu->submenu as $i => $sub) {
                                                                            if ((!$sub->module || $sub->module == $MODULE) && (!$sub->permission || in_array($sub->permission, $permissions))) {
                                                                                $isActiveUrl = strpos($current_url, $sub->url) !== false ? 'current-menu-item active' : ''; ?>
                                                                                <li class="nav-item">
                                                                                    <a href="<?php echo $sub->url ?>" class="nav-link"> <?php echo $sub->title ?> </a>
                                                                                </li>
                                                                        <?php }
                                                                        } ?>
                                                                    </ul>
                                                                </div>
                                                            <?php } ?>
                                                        </li>
                                                <?php }
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    <?php } ?>
                                </li>
                        <?php }
                        }
                        ?>
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">