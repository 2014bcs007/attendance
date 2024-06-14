<?php require_once 'inc/header.php';
$year = date("Y");

$id=isset($_GET['id'])?$_GET['id']:$system_user_id;

if(!$_SESSION['student_login']){
    $user = DB::getInstance()->getRow("user", $user_id, "*", "User_Id");
    }else{
        $user = DB::getInstance()->getRow("student", $user_id, "*", "Student_Id");
    }

?>
<div class="page-content">

    <div class="container-fluid">
        <?php if($user){?>
        <div class="row">
            <div class="col-xxl-3 d-none">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="text-center">
                            <div class="profile-user position-relative d-inline-block mx-auto  mb-4">
                                <img src="assets/images/users/avatar-1.jpg" class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">
                                <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                    <input id="profile-img-file-input" type="file" class="profile-img-file-input">
                                    <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                        <span class="avatar-title rounded-circle bg-light text-body">
                                            <i class="ri-camera-fill"></i>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <h5 class="fs-16 mb-1">Anna Adame</h5>
                            <p class="text-muted mb-0">Lead Designer / Developer</p>
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->
            <div class="col-xxl-9">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                            <!-- <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#personalDetails" role="tab">
                                    <i class="fa fa-home"></i> Personal Details
                                </a>
                            </li> -->
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#changePassword" role="tab">
                                    <i class="mdi mdi-account-lock"></i> Change Password
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content">
                            <div class="tab-pane active" id="changePassword" role="tabpanel">
                                <form action="" method="POST">
                                    <div class="row g-2">
                                        <?php if ($user->Student_Id==$user_id){?>
                                        <div class="col-lg-4">
                                            <div>
                                                <label for="oldpasswordInput" class="form-label">Old Password*</label>
                                                <input type="password" class="form-control" id="oldpasswordInput" name="old_password" required>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <?php }?>
                                        <div class="col-lg-4">
                                            <div>
                                                <label for="newpasswordInput" class="form-label">New Password*</label>
                                                <input type="password" class="form-control" id="newpasswordInput" name="new_password" required>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-lg-4">
                                            <div>
                                                <label for="confirmpasswordInput" class="form-label">Confirm Password*</label>
                                                <input type="password" class="form-control" id="confirmpasswordInput" name="password_confirm" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="text-end">
                                                <button type="submit" class="btn btn-success">Change Password</button>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <input type="hidden" name="id" value="<?php echo $id?>">
                                        <input type="hidden" name="action" value="resetPassword">
                                        <input type="hidden" name="reroute" value="<?php echo $crypt->encode("id=$user->User_Id&page=".$_GET['page'])?>">
                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                            <!--end tab-pane-->
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
        <?php }else{echo '<div class="alert alert-danger">User account not found</div>';}?>

    </div>
    <!-- container-fluid -->

</div>
<?php require_once 'inc/footer.php' ?>