<?php

##################################
###       QUICK ACTIONS        ###
##################################
if (isset($_GET['quick-action'])) {
    $user_id = $_SESSION['school_user_id'];
    $status = "";
    $message = "";
    $data = $_GET;
    switch ($_GET['quick-action']) {
        case 'deleteTransaction':
            DB::getInstance()->update("transaction",$data['code'],array("Status"=>0),"Transaction_Code");
            $message=$_GET['category']." info deleted successfully";
            $status="success";

            break;
        case 'deletePurchase':
            DB::getInstance()->update("purchase",$data['uuid'],array("Status"=>0),"UUID");
            $message=$_GET['category']." info deleted successfully";
            $status="success";

            break;
    }
    if ($message != "") {
        $_SESSION["message"] = array('status' => $status, 'message' => $message);
    }
    Redirect::to('?' . $crypt->decode($_GET['reroute']));
}
