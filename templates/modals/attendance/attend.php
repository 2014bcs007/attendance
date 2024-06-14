<?php
$year = date('Y-m-d');
$id = $_GET['id'];
$fetch_data = DB::getInstance()->querySample("select * from attendance where attendance_date='$year' order by updated_at desc")[0];
?>
<div class="modal-header">
    <h5 class="modal-title">
        <?php echo $fetch_data->first_name . " " . $fetch_data->last_name; ?>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">

    <?php
    if (!$fetch_data->clockin_time) {
    ?>
        <div class="form-group">
            <input type="checkbox" id="employeecheckin" name="employeecheckin" value="employeecheckin"><label>CheckIn</label>
            <!-- <button type="submit" class="btn btn-success" id="employeecheckin" name="action" value="employeecheckin">Check In</button> -->
        </div>
        <div class="col-md-12">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Upload Clock In time Proof:</label>
                    <input type="hidden" name="clockin_image" value="<?php echo $fetch_data->image_proof; ?>">
                    <input type="hidden" name="append" value="<?php echo $fetch_data->first_name . '_' . $fetch_data->last_name . '_' . str_replace("/", "", $student->Regno) ?>">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <input type="file" name="clockin_image" class="form-control" accept="image/*">
                    <input id="mydata" type="hidden" name="mydata" value=""/>
                </div>
            </div>

            <div class="col-sm-6">
                <div id="results">Your captured image will appear here...</div>
                <div id="my_camera"></div>
                <input type=button value="Take Snapshot" onClick="take_snapshot()">
            </div>
        <?php } else { ?>
            <div class="form-group">
                <label>You checked In Today <?php echo $fetch_data->attendance_date; ?></label>
                <label>At: <?php echo $fetch_data->clockin_time; ?></label>
            </div>

            <div class="form-group">
                <label>Already Uploaded Clock In Proof:</label>
            </div>
            <div class="form-group">
                <input type="checkbox" id="employeecheckout" name="employeecheckout" value="employeecheckout"><label>CheckOut</label>
            </div>
            <div class="form-group">
                <label>Upload Clock Out Proof:</label>
                <input type="hidden" name="clockout_image" value="<?php echo $fetch_data->image_proof; ?>">
                <input type="hidden" name="append" value="<?php echo $fetch_data->first_name . '_' . $fetch_data->last_name . '_' . str_replace("/", "", $student->Regno) ?>">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="file" name="clockout_image" class="form-control" accept="image/*">
            </div>
            <?php }
        if ($fetch_data->clockout_time !== null) {
            if (!$fetch_data->clockout_time) {
            ?>
                <div class="form-group">

                    <input type="checkbox" id="employeecheckout" name="employeecheckout" value="employeecheckout"><label>CheckOut</label>
                    <!-- <button type="submit" class="btn btn-warning" id="employeecheckout" name="action" value="employeecheckout">Check Out</button> -->
                </div>
                <div class="form-group">
                    <label>Upload Clock Out Proof:</label>
                    <input type="hidden" name="clockout_image" value="<?php echo $fetch_data->image_proof; ?>">
                    <input type="hidden" name="append" value="<?php echo $fetch_data->first_name . '_' . $fetch_data->last_name . '_' . str_replace("/", "", $student->Regno) ?>">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <input type="file" name="clockout_image" class="form-control" accept="image/*">
                </div>
            <?php } else { ?>
                <div class="form-group">
                    <label>Check out time is: <?php echo $fetch_data->clockout_time; ?></label>
                </div>
            <?php } ?>
            <div class="form-group">
                <label>Already existing Clock Out Proof:</label>
            </div>
        <?php } ?>
        <input type="hidden" name="action" value="employeecheckinorout">

        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="reroute" value="<?php echo $_GET['reroute']; ?>">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-bs-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
            <button type="submit" class="btn btn-primary" value="employeecheckinorout">Save</button>
        </div>

        <!-- Code to handle taking the snapshot and displaying it locally -->
        <script type="text/javascript">
            Webcam.set({
                width: 450,
                height: 360,
                image_format: 'jpeg',
                jpeg_quality: 90
            });
            Webcam.attach('#my_camera');

            function take_snapshot() {
                // take snapshot and get image data
                Webcam.snap(function(data_uri) {
                    // display results in page

                    Webcam.snap(function(data_uri) {
                        document.getElementById('results').innerHTML = '<img name="clockin_image" src="' + data_uri + '"/>';
                        var raw_image_data = data_uri.replace(/^data\:image\/\w+\;base64\,/, '');
		
		                document.getElementById('mydata').value = raw_image_data;
                    });

                    var username = 'jhuckaby';
                    var image_fmt = 'jpeg';
                    var url = 'myscript.php?username=' + username + '&format=' + image_fmt;

                    Webcam.upload(data_uri, url, function(code, text) {
                        document.getElementById('results').innerHTML =
                            '<h2>Here is your image:</h2>' +
                            '<img src="' + text + '"/>';
                    });

                });
            }
        </script>