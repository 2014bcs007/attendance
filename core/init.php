<?php
error_reporting(0);
$GLOBALS['config'] = array(
    'mysql' => array(
        'host' => '127.0.0.1',
       'username' => 'debian-sys-maint',
       'password' => 'KGiuOzosLhzq4VbY',
       'db' => 'attendance_management',
    ),
    'remember' => array(
        'cookie_name' => 'hash',
        'cookie_expiry' => 604800,
    ),
    'session' => array(
        'session_name' => 'user',
        'token_name' => 'token',
    ),
);
spl_autoload_register(function ($class) {
    if (file_exists('classes/' . $class . '.php')) {
        require_once 'classes/' . $class . '.php';
        return true;
    }
    return false;
});
$crypt = new Encryption();
//Hold array of Bulk SMS Service Providers
$SMS_SERVICE_PROVIDERS_LIST = array("AfricasTalking", "egoSMS", "pandora");
require_once 'functions.php';
require_once 'utils.php'; //Load utils content such as sidemenu info

$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$SYSTEM_URL = $_SERVER['REQUEST_SCHEME'] . "://";
$url_array = explode("/", $url);

for ($i = 0; $i < count($url_array) - 1; $i++) {
    $SYSTEM_URL .= $url_array[$i] . "/";
}
$year=date("Y");
$company_data = DB::getInstance()->querySample("select * FROM attendance_settings where status=1 LIMIT 1")[0];
$company_attendance_settings_id = $company_data->attendance_settings_id;
$company_name = $company_data->company_name;
$company_logo = $company_data->logo;
$company_slogan = $company_data->company_slogan;
$company_address = $company_data->address;
$company_site = $company_data->site;
$company_phone = $company_data->phone;
$company_year = $company_data->year;
$workday_start_time = $company_data->workday_start_time;
$workday_end_time = $company_data->workday_end_time;
$overtime_threshold = $company_data->overtime_threshold;
$holiday_time = $company_data->Gender_Offered;
$holday_name = $company_data->holday_name;
$water_mark = $company_data->water_mark;

$GLOBAL_KEYS="'modules'";
$ADDITIONAL_SETTINGS = DB::getInstance()->querySample("select name,value FROM config WHERE name IN ($GLOBAL_KEYS)");

// Grab all settings at once, loop through them at once
foreach($ADDITIONAL_SETTINGS AS $SETTING){
    $name = strtoupper("{$SETTING->name}");                            //create the variable from the key and capitalize it
    $$name = "$SETTING->value";                                        //Get the key value into the created KEY
    if (in_array($name,array("MINIMUM_ALLOWED_NEW_CURRICULUM_MARKS"))){
        $$name = floatval("$SETTING->value");
    }
}

$MODULES = $MODULES?unserialize($MODULES):array();

define('COMPANY_ADDRESS', $company_address);
define('COMPANY_DESCRIPTION', $company_slogan);
define('COMPANY_PHONE', $company_phone);
define('COMPANY_LOGO', $company_logo);


$INITIAL_YEAR = $company_year ? explode('-', $company_year)[0] : 2024;

$APP_NAME = "famitech attendance trucking";
// $APP_LOGO="assets/images/logo-light.png";
$APP_LOGO = "assets/images/logo.png";
$APP_LOGO_DARK = "assets/images/logo.png";
$APP_FAVICON = "assets/images/logo-favicon.jpeg";
$DEVELOPER = array('name' => 'FamITech', 'url' => 'https://famitechsolutions.com/', "app_description" => "For all your company solutions");

$ALL_MODULES_LIST = array("attendance" => "Attendance", "leave" => "Leave");
$MODULES = $MODULES ? array_combine($MODULES, $MODULES) : $ALL_MODULES_LIST;
$MODULE = $_SESSION['module'];
$MODULE = $MODULE ? $MODULE : "attendance";

$WELCOME_NOTE="Welcome In the Attendance System";

// All available permissions list to be used through out the system
$PERMISSIONS_LIST = array(
    "Settings" => array("managesettings" => "Manage Settings", "viewLogs" => "View Logs", "addUserRole" => "Add user roles"),
    "Users" => array("addUser" => "Add User", "viewUsers" => "View Users", "editUser" => "Edit User", "deleteUser" => "Delete User"),
    "Employees" => array("addemployee" => "Add Employee", "viewemployees" => "View Employees","addattendance"=>"Add Attendance","viewattendance"=>"View Attendance"),
    
);
