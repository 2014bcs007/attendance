<?php
$id = $_GET['id'];
// $student = DB::getInstance()->getRow("student", $id, "*", "Student_Id");
?>
<div class="modal-header">
    <h5 class="modal-title">Delete Student(s)</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">

    <div class="form-group">
        <label class="control-label">Specify Reason for deleting:</label><br />
        <label class="radio-inline"> <input type="radio" name="deleting_reason" value="Duplicate" required>Duplicate</label>
        <label class="radio-inline"> <input type="radio" name="deleting_reason" value="Left school" required>Left school</label>
        <label class="radio-inline"> <input type="radio" name="deleting_reason" value="Others" required>Others</label>
        <input type="hidden" name="std_id" value="<?php echo $id; ?>">
    </div>
    <input type="hidden" name="action" value="deleteStudent">

    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="reroute" value="<?php echo $_GET['reroute']; ?>">
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
    <button type="submit" class="btn btn-primary">Continue</button>
</div>