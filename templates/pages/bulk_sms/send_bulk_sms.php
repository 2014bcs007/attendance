<?php require_once 'inc/header.php' ?>
<div class="page-content">
    <div class="container-fluid">

        <!-- Page content here -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Send Bulk SMS</h4>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <?php
                        if (Input::exists() && Input::get("send_sms_btn") == "send_sms_btn") {
                            $message = Input::get("message");
                            $phone_numbers = Input::get("phone_numbers");
                            $to_all_parents = Input::get("to_all_parents");
                            if ($phone_numbers) {
                                $phone_numbers = explode(",", $phone_numbers);
                            }
                            $phone_numbers = implode(",", $phone_numbers);
                            if ($to_all_parents) {
                                $parentPhones = DB::getInstance()->querySample("SELECT Phone FROM parent WHERE Phone IS NOT NULL AND Phone!='' GROUP BY Phone");
                                $phone_numbers .= ',' . implode(',', array_column(json_decode(json_encode($parentPhones), true), 'Phone'));
                            }
                            $phone_numbers = trim($phone_numbers, ",");
                            if ($SMS_SERVICE_PROVIDER == "pandora") {
                                $phone_numbers = str_replace("+256", "0", $phone_numbers);
                            }
                            $phone_numbers = str_replace("/", ",", $phone_numbers);
                            if ($phone_numbers) {
                                sendMessage($phone_numbers, $message, getConfigValue("sms_from"));
                                echo '<div class="alert alert-success">Message sent successfully</div>';
                            } else {
                                echo '<div class="alert alert-danger">No phone numbers selected</div>';
                            }
                            Redirect::go_to("");
                        }
                        ?>
                        <form role="form" action="" method="post">
                            <div class="form-group">
                                <label>Message</label>
                                <textarea class="form-control" name="message" oninput="countCharacters(this.value)" required></textarea>
                                <span id="character-count"></span>
                            </div>
                            <div class="form-group">
                                <label>Send to</label>
                                <labal><input type="checkbox" name="to_all_parents" /> All parents</labal>
                            </div>
                            <div class="form-group">
                                <label>Custom numbers <small class="text-danger">[comma seperated, All numbers MUST be of format +256....]</small></label>
                                <textarea class="form-control" name="phone_numbers"></textarea>
                            </div>

                            <div class="form-group">
                                <button type="submit" name="send_sms_btn" value="send_sms_btn" class="btn btn-primary">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--end col-->
        </div>

    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->

<?php require_once 'inc/footer.php' ?>
<script type="text/javascript">
    function countCharacters(text) {
        var countText = text ? `<strong>${text.length}</strong> character(s) equivalent to <strong>${Math.ceil(text.length/160)}</strong> message(s)` : ``;
        document.getElementById('character-count').innerHTML = countText;
    }
</script>