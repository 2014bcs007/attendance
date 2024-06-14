<?php
$id = $_GET['id'];
$student = DB::getInstance()->querySample("SELECT s.*,p.Phone FROM student s LEFT JOIN parent p ON s.Parent_Id=p.Parent_Id WHERE s.Student_Id='$id' LIMIT 1")[0];

?>
<div class="modal-header">
    <h5 class="modal-title">Edit <?php echo "$student->Fname $student->Lname"; ?></h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">

    <div class="row">
        <div class="col-md-6">
            <label>Student Photo:</label>
            <input type="hidden" name="image" value="<?php echo $student->Image; ?>">
            <input type="hidden" name="append" value="<?php echo $student->Fname . '_' . $student->Lname . '_' . str_replace("/", "", $student->Regno) ?>">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="file" name="edit_photo" class="form-control" accept="image/*">
        </div>
        <div class="col-md-6">
            <label>Regno:</label>
            <input type="text" name="regno" class="form-control" value="<?php echo $student->Regno; ?>">
        </div>
        <div class="col-md-6">
            <label>First Name:</label>
            <input type="text" name="fname" class="form-control" value="<?php echo $student->Fname; ?>" required>
        </div>
        <div class="col-md-6">
            <label>Last Name:</label>
            <input type="text" name="lname" class="form-control" value="<?php echo $student->Lname; ?>" required>
        </div>
        <div class="col-md-6">
            <label>LIN</label>
            <input class="form-control" type="text" name="LIN" value="<?php echo $student->LIN ?>">
        </div>
        <?php if ($IS_GENDER_VISIBLE) { ?>
            <div class="col-md-6">
                <label>Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="">Select..</option>
                    <option value="Female" <?php echo ($student->Gender == "Female") ? " selected" : ""; ?>>Female</option>
                    <option value="Male" <?php echo ($student->Gender == "Male") ? " selected" : ""; ?>>Male</option>
                </select>
            </div>
        <?php } else {
            echo '<input type="hidden" name="gender" value="' . $student->Gender . '">';
        } ?>
        <div class="col-md-6">
            <label>DOB</label>
            <input type="date" max="<?php echo date("Y-m-d") ?>" class="form-control" name="dob" value="<?php echo $student->DOB; ?>">
        </div>
        <div class="col-md-6">
            <label>Dormitory</label>
            <input class="form-control" type="text" name="dormitory" value="<?php echo $student->Dormitory ?>">
        </div>
        <div class="col-md-6">
            <label>Bank/Payment Code</label>
            <input class="form-control" type="text" name="bank_number" value="<?php echo $student->Bank_Number ?>">
        </div>
        <div class="col-md-6">
            <label>Starting Balance</label>
            <input class="form-control" type="text" name="starting_balance" value="<?php echo $student->Starting_Balance ?>">
        </div>
        <div class="col-md-6">
            <label>Parent Phone No.</label>
            <input type="text" name="phone" class="form-control" value="<?php echo $student->Phone; ?>" />
            <input type="hidden" name="phone_old" value="<?php echo $student->Phone; ?>" />
            <input type="hidden" name="parent_id" value="<?php echo $student->Parent_Id; ?>" />
        </div>
        <div class="col-md-6">
            <label>Address</label>
            <input class="form-control" type="text" name="address" value="<?php echo $student->Address ?>">
        </div>
        <div class="col-md-6">
            <?php
            $student->PLE_Aggregates = $student->PLE_Aggregates ? $student->PLE_Aggregates : null;
            $student->UCE_Aggregates = $student->UCE_Aggregates ? $student->UCE_Aggregates : null;?>
            <label> PLE Aggregates</label>
            <input type="number" class="form-control" min="4" max="36" name="ple_results" value="<?php echo $student->PLE_Aggregates?>">
        </div>
        <div class="col-md-6">
            <label>UCE Aggregates (A'Level)</label>
            <input type="number" class="form-control" min="4" max="72" name="uce_results" value="<?php echo $student->UCE_Aggregates?>">
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="pass" class="form-control" <?php echo $id?'':'required'?>>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" name="confirmpass" class="form-control" <?php echo $id?'':'required'?>>
            </div>
        </div>

    </div>

    <input type="hidden" name="action" value="editStudent">

    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="reroute" value="<?php echo $_GET['reroute']; ?>">
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
    <button type="submit" class="btn btn-primary">Save</button>
</div>