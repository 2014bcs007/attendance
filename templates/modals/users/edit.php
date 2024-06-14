<?php
$id = $_GET['id'];
$user = DB::getInstance()->getRow("user", $id, "*", "User_Id");
$roles=DB::getInstance()->querySample("SELECT * FROM user_roles ORDER BY Role_Name");
?>
<div class="modal-header">
    <h5 class="modal-title"><?php echo !$id ? "New User" : "Edit $user->Fname $user->Lname"; ?></h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="form-group">
        <label>Staff</label>
        <select name="staff_id" class="form-control" required>
            <option value="">Select...</option>
            <?php
            $staffList = DB::getInstance()->querySample("SELECT * FROM staff WHERE Staff_Status=1");
            foreach ($staffList as $staff) {
                $selected=$user->Staff_Id==$staff->Staff_Id?' selected':'';
                echo "<option value='$staff->Staff_Id' $selected >($staff->Code) $staff->Fname $staff->Lname</option>";
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <label>Username:</label>
        <input type="text" name="username" class="form-control" value="<?php echo $user->Username?>" required>
    </div>
    <div class="form-group">
        <label>User Group:</label>
        <select class="form-control" name="role_id" required>
            <option value="">SELECT...</option>
            <?php
                foreach ($roles as $role) {
                    $selected=$user->Role_Id==$role->Role_Id?' selected':'';
                    echo "<option value='$role->Role_Id' $selected >$role->Role_Name</option>";
                }
            ?>
        </select>
    </div>
    <div class="form-group">
        <label>Password:</label>
        <input type="password" name="pass" class="form-control" <?php echo $id?'':'required'?>>
    </div>


    <div class="form-group">
        <label>Confirm Password:</label>
        <input type="password" name="confirmpass" class="form-control" <?php echo $id?'':'required'?>>
    </div>

    <input type="hidden" name="action" value="editUser">

    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="reroute" value="<?php echo $_GET['reroute']; ?>">
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
    <button type="submit" class="btn btn-primary">Save</button>
</div>