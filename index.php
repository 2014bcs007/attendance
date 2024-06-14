<?php

ob_start();
// error_reporting(E_ALL);
date_default_timezone_set('Africa/Kampala');
$date_today = date("Y-m-d");
session_start();
include 'core/init.php';
$title = "CSMS | ";

//Reset the MySQL global variable <sql_mode>
DB::getInstance()->query("SET SESSION sql_mode=''");

$crypt = new Encryption();
// Capture permission session variables for the logged in user
$user_permissions = (isset($_SESSION['permissions'])) ? $_SESSION['permissions'] : array();

$encoded_page = isset($_GET['page']) ? $_GET['page'] : ('login');
$page = $crypt->decode($encoded_page);
//$page = $encoded_page;

if (isset($_GET['modal'])) {
    require('templates/modals/' . $_GET['modal'] . '.php');
    //    die();
}
include 'controllers/actions.php';
include 'controllers/quick-actions.php';
//$page = $encoded_page;
$TEMPLATES_DIRECTORY="templates/pages/";
if (!isset($_GET['modal'])) {
switch ($page) {
    default:
        $page = "login";
        include $TEMPLATES_DIRECTORY.'users/login.php';
        break;

    case 'dashboard':
        if (file_exists($TEMPLATES_DIRECTORY.'' . $page . '.php'))
            include $TEMPLATES_DIRECTORY.'' . $page . '.php';
        break;
    case 'admin_dash':
        if (file_exists($TEMPLATES_DIRECTORY.'settings/' . $page . '.php'))
            include $TEMPLATES_DIRECTORY.'settings/' . $page . '.php';
        break;
    case 'accounts':
        if (file_exists($TEMPLATES_DIRECTORY.'settings/' . $page . '.php'))
            include $TEMPLATES_DIRECTORY.'settings/' . $page . '.php';
        break;

    case 'graph':
        if (file_exists($TEMPLATES_DIRECTORY.'graph/' . $page . '.php'))
            include $TEMPLATES_DIRECTORY.'graph/' . $page . '.php';
        break;

    case 'logs':
        isAuthorized("viewLogs");
        if (file_exists($TEMPLATES_DIRECTORY.'users/' . $page . '.php'))
            include $TEMPLATES_DIRECTORY.'users/' . $page . '.php';
        break;

    case 'user-roles':
        isAuthorized("addUserRole");
        if (file_exists($TEMPLATES_DIRECTORY.'users/' . $page . '.php'))
            include $TEMPLATES_DIRECTORY.'users/' . $page . '.php';
        break;
    case 'users':
        if (file_exists($TEMPLATES_DIRECTORY.'users/' . $page . '.php'))
            include $TEMPLATES_DIRECTORY.'users/' . $page . '.php';
        break;

    case 'profile':
        if (file_exists($TEMPLATES_DIRECTORY.'users/' . $page . '.php'))
            include $TEMPLATES_DIRECTORY.'users/' . $page . '.php';
        break;

    //Settings
    case 'configurations':
        isAuthorized("managesettings");
        if (file_exists($TEMPLATES_DIRECTORY.'settings/' . $page . '.php'))
            include $TEMPLATES_DIRECTORY.'settings/' . $page . '.php';
        break;

        /*     * Employees**************************** */
        case 'register_employee':
            isAuthorized("addemployee");
            if (file_exists($TEMPLATES_DIRECTORY.'employees/' . $page . '.php'))
                include $TEMPLATES_DIRECTORY.'employees/' . $page . '.php';
            break;
    
        case 'registered_employees':
            isAuthorized("viewemployees");
            if (file_exists($TEMPLATES_DIRECTORY.'employees/' . $page . '.php'))
                include $TEMPLATES_DIRECTORY.'employees/' . $page . '.php';
            break;

            /*     * Attendance of Employees**************************** */
            case 'add_attendance':
                isAuthorized("addattendance");
                if (file_exists($TEMPLATES_DIRECTORY.'attendance/' . $page . '.php'))
                    include $TEMPLATES_DIRECTORY.'attendance/' . $page . '.php';
                break;
        
            case 'view_attendance':
                    isAuthorized("viewattendance");
                    if (file_exists($TEMPLATES_DIRECTORY.'attendance/' . $page . '.php'))
                        include $TEMPLATES_DIRECTORY.'attendance/' . $page . '.php';
                    break;
        
            case 'saveimage':
                if (file_exists($TEMPLATES_DIRECTORY.'attendance/' . $page . '.php'))
                    include $TEMPLATES_DIRECTORY.'attendance/' . $page . '.php';
                break;
}
}
ob_flush();
?>