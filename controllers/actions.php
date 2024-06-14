<?php

##################################
###           ACTIONS          ###
##################################
if (isset($_POST['action'])) {

    $looged_in_user_id = $_SESSION['user_id'];
    $logged_in_employee_id = $_SESSION['employee_id'];
    $status = "";
    $message = "";
    $data = $_POST;
    switch ($_POST['action']) {
        case 'employeecheckinorout':
            //pick fields 

            $employee_id = Input::get("id");
            $attendance_date = date("Y-m-d H:i:s") . "<br>";
            $clockin_time = date("H:i:s", strtotime(date("Y-m-d H:i:s")));
            $clockout_time = date("H:i:s", strtotime(date("Y-m-d H:i:s")));
            $attendate_status = "Present";
            //save fields
            $employeecheckin = Input::get("employeecheckin");
            $employeecheckout = Input::get("employeecheckout");
            
            $clockout_image = ($_FILES['clockout_image']['name']);
            $clockin_image = ($_FILES['clockin_image']['name']);

            // var_dump($clockin_image);
            // var_dump($clockout_image); die();

            //Uploading the Employees clockin attendance image
            if ($clockin_image != "") {
                $target_dir = "employees/attendance";
                $target_file = $target_dir . basename($_FILES["clockin_image"]["name"]);
                move_uploaded_file($_FILES["clockin_image"]["tmp_name"], $target_file);
            }
            //Uploading the Employees clockout attendance image
            if ($clockout_image != "") {
                $target_dir = "employees/attendance";
                $target_file = $target_dir . basename($_FILES["clockout_image"]["name"]);
                move_uploaded_file($_FILES["clockout_image"]["tmp_name"], $target_file);
            }
            // Ftech the attendance object if it exists
            $attendance_object=DB::getInstance()->querySample("select attendance_id,attendance_date,employee_id from attendance where employee_id='$employee_id' AND attendance_date='$attendance_date'");
                
            // Check which checkin was checked
            if ($employeecheckin == "employeecheckin") {
                if (!$attendance_object) {
                    $submit_check_in = DB::getInstance()->insert('attendance', array(
                        "employee_id" => $employee_id,
                        "attendance_date" => $attendance_date,
                        "clockin_time" => $clockin_time,
                        "attendate_status" => $attendate_status,
                    ));
                    if($submit_check_in && $clockin_image != ""){
                        // fetch attendance id for submission
                        $attendance_ids_object = DB::getInstance()->querySample("select * from attendance where employee_id= '$employee_id' order by attendance_id desc ")[0];
                        $submit_check_proofs = DB::getInstance()->insert('attendance_proof', array(
                            "attendance_id" => $attendance_ids_object->attendance_id,
                            "image_proof" => $clockin_image,
                            "image_proof_category" => "clockin"
                        ));
                    }
                    $message = "Checkin successfully";
                    $status = "success";
                } else {
                    $message = "Checkout not successful";
                    $status = "danger";
                }
            } 
            if ($employeecheckout == "employeecheckout") {

                //save fields
                $id = DB::getInstance()->querySample("select * from attendance where employee_id='$employee_id' order by updated_at desc")[0];
                // var_dump($clockout_time);die();
                $update_clockout_time = DB::getInstance()->update(
                    'attendance',
                    $id->attendance_id,
                    array("clockout_time" => $clockout_time),
                    'attendance_id'
                );

                if($update_clockout_time && $clockout_image != ""){
                    // fetch attendance id for submission
                    $attendance_ids_object = DB::getInstance()->querySample("select * from attendance where employee_id= '$employee_id' order by attendance_id desc ")[0];
                    $submit_check_proofs = DB::getInstance()->insert('attendance_proof', array(
                        "attendance_id" => $attendance_ids_object->attendance_id,
                        "image_proof" => $clockout_image,
                        "image_proof_category" => "clockout"
                    ));
                }

                if ($update_clockout_time) {

                    $message = "Checkout was successful";
                    $status = "success";
                } else {
                    $message = "Checkout not successfull";
                    $status = "danger";
                }
            }
            break;
        case 'saveSystemConfigurations':
            $settings = $data['settings'];
            if ($data['modules']) {
                $settings['modules'] = $data['modules'];
            }
            foreach ($settings as $setting => $value) {
                if ($setting == "expiry_date") { //Grab the date and encode it
                    $value = $date = $crypt->encode($value);
                } else if ($setting == "modules") {
                    $value = serialize($value);
                }
                if ($setting == "supplementary_set_passmark") {
                    if ($SUPPLEMENTARY_SET_PASSMARK != $value) {
                        $query = "UPDATE student_subject_comment SET Supplementary_Set= ROUND(Supplementary_Set*$value/$SUPPLEMENTARY_SET_PASSMARK,$MARKS_ROUNDED_TO) WHERE Supplementary_Set IS NOT NULL";
                        DB::getInstance()->query($query);
                    }
                }
                DB::getInstance()->updateSetting("$setting", $value);
            }
            $message = "Configurations saved successfully";
            $status = "success";
            break;

        case 'editAccount':
            $id = $data['id'];
            $code = $data['code'];
            $array = array(
                "Code" => $data['code'],
                "Name" => $data['name'],
                "Category" => $data['category'],
                "Account_Type" => $data['category'] == "" ? $data['equity'] : ""
            );
            $condition = $id ? " AND Id!='$id'" : "";
            if (!DB::getInstance()->checkRows("SELECT Id FROM account WHERE Code='$code' $condition")) {
                if ($data['id']) {
                    DB::getInstance()->update("account", $data['id'], $array, "Id");
                } else {
                    DB::getInstance()->insert("account", $array);
                }
                $message = $data['category'] . " updated successfully";
                $status = "success";
            } else {
                $message = $data['code'] . " already registered";
                $status = "danger";
            }
            break;

        case "editUserRole":
            $role_name = Input::get("role_name");
            $role_id = Input::get("id");
            $permissions = Input::get("permissions");
            $registeredPermissions = array();

            if ($role_id) {
                DB::getInstance()->update("user_roles", $role_id, array("Role_Name" => $role_name), "Role_Id");
                $role_data = DB::getInstance()->querySample("SELECT * FROM user_permission WHERE Role_Id='$role_id'");
                $registeredPermissions = array_column(json_decode(json_encode($role_data), true), 'Permission_Id');
                $arrayToDelete = array_diff($registeredPermissions, $permissions);
                $permissions = array_diff($permissions, $registeredPermissions);
                if ($arrayToDelete) {
                    DB::getInstance()->query("DELETE FROM user_permission WHERE Permission_Id IN (" . implode(',', $arrayToDelete) . ")");
                }
            } else {
                $role_id = DB::getInstance()->insert("user_roles", array("Role_Name" => $role_name));
            }
            foreach ($permissions as $permission) {
                DB::getInstance()->insert("user_permission", array("Role_Id" => $role_id, "Permission_Id" => $permission));
            }

            if ($role_name == $_SESSION['system_user_role']) {
                $role_data = DB::getInstance()->querySample("SELECT p.code FROM permissions p,user_permission up WHERE p.Id=up.Permission_Id AND up.Role_Id='$role_id'");
                $permissions = array_column(json_decode(json_encode($role_data), true), 'Code');
                $_SESSION['permissions'] = $permissions;
            }
            $message = 'Role updated successfully';
            $status = "success";
            break;

        case 'editUser':
            $status = "danger";
            $staff_id = Input::get("staff_id");
            $username = Input::get('username');
            $role_id = Input::get('role_id');
            $password = Input::get('pass');
            $pass = md5($password);
            $id = $data['id'];
            $confirmpass = md5(Input::get('confirmpass'));
            if ($id && (!$pass || $pass == $confirmpass) || ($pass == $confirmpass)) {
                $array = array('Staff_Id' => $staff_id, 'Username' => $username, 'Role_Id' => $role_id);
                if ($password && $pass == $confirmpass) {
                    $array['Password'] = $pass;
                }
                if ($id) {
                    $queryDup = DB::getInstance()->checkRows("select * from user where Username='$username' AND User_Id!='$id'");
                    if ($queryDup) {
                        $message = "Username $username already taken by another user";
                    } else {
                        $id = DB::getInstance()->update("user", $id, $array, "User_Id");
                        if ($id) {
                            $message = "Credentials for $username have been saved successfully";
                            $status = "success";
                        } else {
                            $message = 'There is an error';
                        }
                    }
                } else {
                    $queryDup = DB::getInstance()->checkRows("select * from user where Username='$username'");
                    if ($queryDup) {
                        $message = "Username $username already taken by another user";
                    } else {
                        $id = DB::getInstance()->insert("user", $array);
                        if ($id) {
                            $message = "The user credentials have been set to username=  " . $username . "  password= " . Input::get('pass');
                            $status = "success";
                        } else {
                            $message = 'There is an error';
                        }
                    }
                }
            } else {
                $message = 'password combination do not match';
            }
            break;
        case 'resetPassword':
            $id = $data['id'];
            $oldpass = md5(Input::get('old_password'));
            $newpass = md5(Input::get('new_password'));
            $confirmpass = md5(Input::get('password_confirm'));
            $status = "danger";
            if ($newpass == $confirmpass) {
                $query1 = !$oldpass ? true : DB::getInstance()->checkRows("select * from user where  Password='$oldpass' AND User_Id='$id'");
                if ($query1) {
                    $query = DB::getInstance()->query("update user set Password='$newpass' where Password='$oldpass' AND User_Id='$id'");
                    if ($query) {
                        $message = "The user password has been changed to " . $data['new_password'];
                        $status = "success";
                    } else {
                        $message = "Update not made, please contact system admin";
                    }
                } else {
                    $message = "wrong old credentials";
                }
            } else {
                $message = "password combination do not match";
            }
            break;
        case 'toggleUserStatus':
            DB::getInstance()->update('user', $data['id'], array('Status' => $data['status']), 'User_Id');
            $message = "successful";
            $status = "success";
            break;

        case 'addemployee':
            //pick the fields

            $first_name = strtoupper(Input::get('first_name'));
            $last_name = strtoupper(Input::get('last_name'));
            $employee_gender = Input::get('employee_gender');
            $phone_number = Input::get('phone_number');
            $email = Input::get('email');
            $employee_photo = ($_FILES['employee_photo']['name']);
            $description = Input::get('description');

            $password = md5(Input::get('new_password'));
            $confirmpassword = md5(Input::get('password_confirm'));
            if ($password == $confirmpassword) {
                $fpassword = $password;
            }
            $username = Input::get('username');
            //Uploading the Employees image
            if ($employee_photo != "") {
                $target_dir = "employees/";
                $target_file = $target_dir . basename($_FILES["employee_photo"]["name"]);
                move_uploaded_file($_FILES["employee_photo"]["tmp_name"], $target_file);
            }
            //Inserting Employees data in the database
            $employee_id = DB::getInstance()->insert('employees', array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'gender' => $employee_gender,
                'photo' => $employee_photo,
                'email' => $email,
                'phone' => $phone_number,
                'decription' => $description,
            ));

            if ($employee_id) {
                // echo $employee_id.' '.$username.' '.$user_type.' '.$fpassword.' '.$user_role_id;
                $user_type = "Employee";
                $role_name = "Employee";
                // echo "select user_role_id from user_roles where role_name='Employee'";
                $user_role_id = DB::getInstance()->querySample("select user_role_id from user_roles where role_name='$role_name'");
                $submit_employee = DB::getInstance()->insert('users', array(
                    'employee_id' => $employee_id,
                    'username' => $username,
                    'user_type' => $user_type,
                    'password' => $fpassword,
                    'user_role_id' => $user_role_id->user_role_id,
                ));
                $message = "Employee Registered successfully";
                $status = "success";
            } else {
                $message = "Employee not registered";
                $status = "danger";
            }
            break;
        case 'editemployee':
            $files = $_FILES;
            //pick the fields
            $student_id = $id = Input::get("id");
            $fname = strtoupper(Input::get('fname'));
            $lname = strtoupper(Input::get('lname'));
            $gender = Input::get('gender');
            $regno = Input::get('regno');
            $class_id = Input::get('class');
            $stream_id = Input::get('stream');
            $ple_results = Input::get("ple_results");
            $uce_results = Input::get("uce_results");
            $dob = Input::get("dob");
            $append = Input::get('append');
            $retrieved_image = Input::get("image");
            $dob = ($dob != "") ? $dob : NULL;
            $dormitory = Input::get("dormitory");
            $bank_number = Input::get('bank_number');
            $parent_id = Input::get("parent_id");
            $phone = Input::get("phone");
            $address = Input::get("address");
            $phone_old = Input::get("phone_old");
            $LIN = Input::get("LIN");
            $starting_balance = Input::get("starting_balance");
            $password = Input::get('pass');
            $pass = md5($password);
            $confirmpass = md5(Input::get('confirmpass'));

            $year = (Input::get('year') != '') ? Input::get('year') : date("Y");
            //add all the student update items to the array

            $array = array("Fname" => $fname, "Lname" => $lname, "Regno" => $regno, "Bank_Number" => $bank_number, 'Starting_Balance' => $starting_balance ? $starting_balance : 0, "Gender" => $gender, "Dormitory" => $dormitory, "DOB" => $dob, "PLE_Aggregates" => $ple_results, "UCE_Aggregates" => $uce_results, "Address" => $address, "LIN" => $LIN);
            //add the updated password to the student update array
            if ($password && $pass == $confirmpass) {
                $array['Password'] = $pass;
            }

            if ($phone && $phone != $phone_old) { //Need to only save the parent info when the new phone is diff from old
                $phone = (substr($phone, 0, 1) == "0") ? "+256" . substr($phone, 1) : $phone;
                if ($parent_id[$x]) {
                    //Need to update the parent table if there is no other student attached to the old number
                    $countOtherStudents = DB::getInstance()->querySample("SELECT COUNT(Student_Id) FROM student WHERE Parent_Id='" . $parent_id[$x] . "' AND Student_Id!='" . $student_id[$x] . "'");
                    if (!$countOtherStudents) {
                        //Need to update the parent table phone number if there is no other student for same parent
                        DB::getInstance()->update("parent", $parent_id[$x], array("Phone" => $phone), "Parent_Id");
                        $pId = $parent_id[$x];
                    }
                }
                if (!$pId) {
                    //Pick the parent with the same phone number
                    $pId = DB::getInstance()->querySample("SELECT Parent_Id FROM parent WHERE Phone='$phone'")[0]->Parent_Id;
                    if (!$pId) {
                        //If not exists, then save the parent first
                        $pId = DB::getInstance()->insert("parent", array("Phone" => $phone));
                    }
                }
                if ($pId) {
                    //Add the Parent_Id Column to the students array to be saved
                    $array['Parent_Id'] = $pId;
                }
            } else if (!$phone) {
                $array['Parent_Id'] = null;
            }

            $edit_photo = ($_FILES['edit_photo']['name']);
            if ($edit_photo != "") {
                $append = $fname . '_' . $lname . '_' . str_replace("/", "", $regno);
                $photo_name = explode(".", $edit_photo);
                $destination = strtoupper($append) . '.' . end($photo_name);

                $target_dir = "students/";
                $target_file = $target_dir . basename($destination);
                unlink("students/" . $retrieved_image);

                move_uploaded_file($_FILES["edit_photo"]["tmp_name"], $target_file);
                $array['Image'] = $destination;
            }
            if ($class_id != "") {
                DB::getInstance()->query("UPDATE marks SET Class_Id='$class_id', Stream_Id='$stream_id' WHERE Student_Id='$student_id' AND Year='$year'");
                //for alevel student
                DB::getInstance()->query("UPDATE amarks SET Class_Id='$class_id', Stream_Id='$stream_id' WHERE Student_Id='$student_id' AND Year='$year'");
            }
            DB::getInstance()->update("student", $student_id, $array, "Student_Id");
            $message = "Student Updated successfully";
            $status = "success";
            break;
        case 'deleteStaff':
            $id = Input::get('staff_id');
            if ($id) {
                DB::getInstance()->query("UPDATE staff SET Staff_Status=0 where Staff_Id=$id");
            } else {
                $message = "Staff couldn't be deleted";
                $status = "danger";
            }
            break;
        case 'deleteemployee':
            $ids = trim(Input::get('std_id'), ",");
            $delete_reason = Input::get('deleting_reason');
            if ($ids) {
                if ($delete_reason == "Duplicate") {
                    DB::getInstance()->query("DELETE FROM student WHERE Student_Id IN ($ids)");
                } else {
                    DB::getInstance()->query("UPDATE student SET Status=0 WHERE Student_Id IN ($ids)");
                }
            } else {
                $message = "Student(s) deleted successfully";
                $status = "danger";
            }
            break;

        case 'editStaff':
            $id = Input::get("id");
            $fname = strtoupper(Input::get('fname'));
            $lname = strtoupper(Input::get('lname'));
            $staff_type = Input::get("staff_type");
            $salary_structure = Input::get("salary_structure");
            $date_from = Input::get("date_from");
            $initial = strtoupper(substr($fname, 0, 1) . substr($lname, 0, 1));
            $array = array(
                "Fname" => $fname,
                "Lname" => $lname,
                "Staff_Type" => $staff_type,
                "Staff_Initial" => $initial
            );
            if (!$id) {
                $query_number = (int)DB::getInstance()->displayTableColumnValue("select Staff_Id from staff order by Staff_Id desc Limit 1", "Staff_Id");
                $number = $query_number + 1;

                $array['Code'] = date("Y") . "/" . $initial . "/" . str_pad($number, 3, '0', STR_PAD_LEFT);
            }
            if ($id) {
                DB::getInstance()->update("staff", $id, $array, "Staff_Id");
            } else {
                $id = DB::getInstance()->insert("staff", $array);
            }

            $message = "Staff updated successfully";
            $status = "success";
            break;
    }
    if ($message != "") {
        $_SESSION["message"] = array('status' => $status, 'message' => $message);
    }
    Redirect::to('?' . $crypt->decode($_POST['reroute']));
}
