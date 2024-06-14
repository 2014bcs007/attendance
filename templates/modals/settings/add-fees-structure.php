<?php
$classes = DB::getInstance()->querySample("SELECT * FROM class ORDER BY Class_Name");
?>
<div class="modal-header">
    <h5 class="modal-title"><?php echo !$id ? "New fees structure" : "Edit fees structure"; ?></h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="form-group">
        <label>Class(es):</label>
        <select name="class[]" class="form-control select2" multiple required>
            <?php
            foreach ($classes as $class) {
                echo "<option value='$class->Class_Id'>$class->Class_Name</option>";
            }
            ?>
        </select>
        <label>Year(s):</label>
        <select name="year[]" class="form-control select2" multiple required>
            <?php
            for ($i = $INITIAL_YEAR; $i <= $MAX_YEAR; $i++) {
                $selected = ($i == $year) ? " selected" : "";
                echo '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
            }
            ?>
        </select>
        <label>Term(s):</label>
        <select name="term[]" class="form-control select2" multiple required>
            <?php
            foreach ($terms_array as $term) {
                echo "<option value='$term'>$term</option>";
            }
            ?>
        </select>
        <br />
        <?php
        foreach ($SCHOOLING_TYPE as $type) {
            echo "<label>$type fees</label>";
            echo "<input type='number' name='amount[$type]' class='form-control' min='0' step='any'/>";
        }
        ?>
    </div>

    <input type="hidden" name="action" value="addFeesStructure">

    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="hidden" name="reroute" value="<?php echo $_GET['reroute']; ?>">
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
    <button type="submit" class="btn btn-primary">Save</button>
</div>
<script>
    $('.select2').select2({'width':'100%'});
</script>