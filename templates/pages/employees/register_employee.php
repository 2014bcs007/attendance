<?php require_once 'inc/header.php'; ?>
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h4>Employee Entry Form</h4>
                    </div>
                    <div class="card-body">
                        <form role="form" action="" method="POST" enctype="multipart/form-data">
                            <span id="show"></span>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>employee Photo:</label>
                                        <div id="file_div" class="file_div">
                                            <img style="width:10%;" id="blah" alt="" align="left" />
                                            <p style="color:blue">Upload a clear and professional photo .</p>
                                            <input type='file' class="form-control" id="i_file" name="employee_photo" accept="image/*" onchange="readURL(this);" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>First Name:</label>
                                        <input type="text" name="first_name" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label>Last Name:</label>
                                        <input type="text" name="last_name" class="form-control" required>
                                    </div>

                                        <div class="form-group">
                                            <label>Gender:</label>
                                            <select id="employee_gender" name="employee_gender" class="form-control" required>
                                                <option value="">Select..</option>
                                                <option value="Female">Female</option>
                                                <option value="Male">Male</option>
                                            </select>
                                        </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input class="form-control" type="phone" name="phone_number" value="<?php echo $employee->phone_number ?>">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input class="form-control" type="text" name="email" value="<?php echo $employee->email ?>">
                                </div>


                                </div>
                                <!-- Other contact Info -->
                                
                                <div class="col-md-6">
                            <!-- Submit the employee username and password -->
                                <div class="form-group">
                                    <label for="usernameInput" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" >
                                </div>
                                <div class="form-group">
                                    <label for="newpasswordInput" class="form-label">New Password*</label>
                                    <input type="password" class="form-control" id="newpasswordInput" name="new_password" >
                                </div>
                                <div class="form-group">
                                    <label for="confirmpasswordInput" class="form-label">Confirm Password*</label>
                                    <input type="password" class="form-control" id="confirmpasswordInput" name="password_confirm" >
                                </div>
                            <!-- end of employee lin and pawword section -->
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control" rows="9" cols="40" type="text" name="description" ><?php echo $employee->description ?>
                                </textarea>
                                </div>
                                </div>
                            </div>
                            <input type="hidden" name="reroute" value="<?php echo $crypt->encode('page=' . $_GET['page'])?>"/>
                            <button type="submit" class="btn btn-primary" name="action" value="addemployee" style="width: 100%;">Register employee</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container-fluid -->

</div>
<?php require_once 'inc/footer.php' ?>

