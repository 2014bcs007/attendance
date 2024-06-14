<?php
$crypt = new Encryption();

// All available permissions list to be used through out the system

$SIDEMENU = array(
    array(
        "title" => 'Dashboard',
        'icon' => 'mdi mdi-home',
        'url' => '?page=' . $crypt->encode('dashboard')
    ),
    array(
        "title" => 'Settings',
        'icon' => 'mdi mdi-cog-outline',
        'permissions' => array('managesettings'),
        'submenu' => array(
            array('url' => "?tab=general&page=" . $crypt->encode('configurations'), 'title' => 'General Settings', 'permission' => 'managesettings'),
            array('url' => "?tab=sms&page=" . $crypt->encode('configurations'), 'title' => 'SMS Settings', 'permission' => 'managesettings'),
            
        )
    ),
    array(
        "title" => 'User Management',
        'icon' => 'mdi mdi-account-lock-open',
        'permissions' => array('addUserRole','viewUsers','viewLogs'),
        'submenu' => array(
            array('url' => "?page=" . $crypt->encode('user-roles'), 'title' => 'User Groups', 'permission' => 'addUserRole'),
            array('url' => "?page=" . $crypt->encode('users'), 'title' => 'User List', 'permission' => 'viewUsers'),
            array('url' => "?page=" . $crypt->encode('logs'), 'title' => 'User Logs', 'permission' => 'viewLogs'),
            array('url' => "?page=" . $crypt->encode('profile'), 'title' => 'My Account'),
        )
    ),
    array(
        "title" => 'Attendance',
        'icon' => 'mdi mdi-account-multiple',
        'permissions' => array('addemployee', 'viewemployees'),
        'submenu' => array(
            array('url' => '?page=' . $crypt->encode('add_attendance'), 'title' => 'Add Attendance', 'permission' => 'addattendance'),
            array('url' => '?page=' . $crypt->encode('view_attendance'), 'title' => 'View Attendance', 'permission' => 'viewattendance'),
        )
    ),
    array(
        "title" => 'Staff Management',
        'icon' => 'mdi mdi-account-multiple',
        'permissions' => array('viewStaff', 'managesettings'),
        'submenu' => array(
            array('url' => '?page=' . $crypt->encode('register_employee'), 'title' => 'Register Employee', 'permission' => 'addemployee'),
            array('url' => '?page=' . $crypt->encode('registered_employees'), 'title' => 'View Employees', 'permission' => 'viewemployees'),
            
        )
    ),
     array(
        "title" => 'Message Center',
        'icon' => 'mdi mdi-android-messages',
        'permissions' => array('sendSMS'),
        'submenu' => array(
            array('url' => '?page=' . $crypt->encode('send_bulk_sms'), 'title' => 'Send Bulk SMS'),
            array('url' => '?page=' . $crypt->encode('send_to_class'), 'title' => 'Send Class SMSs'),
        )
    ),

);
$TOPMENU = array(
    array(
        "title" => 'Settings',
        'icon' => 'mdi mdi-cog-outline',
        'permissions' => array('manageSettings', 'manageClasses', 'manageStreams'),
        'submenu' => array(
            array('url' => "?tab=general&page=" . $crypt->encode('configurations'), 'title' => 'General Settings', 'permission' => 'managesettings'),
        )
    ),
    array(
        "title" => 'Staff Management',
        'icon' => 'mdi mdi-account-multiple',
        'permissions' => array('viewStaff', 'managesettings'),
        'submenu' => array(
            array('url' => "?page=" . $crypt->encode('staff_registration'), 'title' => 'Staff List', 'permission' => 'viewStaff'),
            
        )
    ),
    array(
        "title" => 'User Management',
        'icon' => 'mdi mdi-account-lock-open',
        'permissions' => '',
        'submenu' => array(
            array('url' => "?page=" . $crypt->encode('user-roles'), 'title' => 'User Groups', 'permission' => 'addUserRole'),
            array('url' => "?page=" . $crypt->encode('users'), 'title' => 'User List', 'permission' => 'viewUsers'),
            array('url' => "?page=" . $crypt->encode('logs'), 'title' => 'User Logs', 'permission' => 'viewLogs'),
            array('url' => "?page=" . $crypt->encode('profile'), 'title' => 'My Account'),
        )
    ),
);

$TOPMENU = json_decode(json_encode($TOPMENU), FALSE);
$SIDEMENU = json_decode(json_encode($SIDEMENU), FALSE);
