<?php require_once 'inc/header.php' ?>
<div class="page-content">
    <div class="container-fluid">

        <h3 class="page-header">SEND STUDENT OR CLASS SMS</h3>
        <div class="row card">
            <div class="col-lg-12">
                <?php
                if (isset($_GET['class']) && $_GET['class'] != "") {
                    $class = $_GET['class'];
                    $stream = $_GET['stream'];
                    $year = $_GET['year'];
                    $term=$_GET['term'];
                }
                ?>
                <form role="form" action="" method="post" class="form-inline">
                    <div class="form-group">
                        <label>Year</label>
                        <select name="year" class="form-control" required>
                            <option value="">Choose...</option>
                            <?php
                            for ($x = $INITIAL_YEAR; $x <= (date('Y') + 1); $x++) {
                                echo '<option value="' . $x . '">' . $x . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Term:</label>
                        <select name="term" class="form-control" required>
                            <option value="">Choose...</option>
                            <?php
                            foreach ($TERMS_ARRAY AS $TERM ) {
                                $selected=$TERM==$term?"selected":"";
                                echo "<option value='$TERM' $selected>$TERM</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Select Class:</label>
                        <select name="class" class="form-control" required id="classAssign">
                            <?php echo DB::getInstance()->dropDowns('class', 'Class_Id', 'Class_Name'); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Stream(s):</label>
                        <select style="width:100%;" name="stream[]" id="stream_id" multiple class="select2 form-control" required></select>
                    </div>
                    <button type="submit" class="btn btn-primary" name="search_students" value="search_students">Search students</button>
                </form>
                <?php
                if (Input::exists()) {
                    if ((Input::get("search_students") == "search_students") || (isset($_GET["class"]) && $_GET["class"] != "")) {
                        $year = (Input::get("year") != "") ? Input::get("year") : Input::get("year");
                        if (Input::exists() && Input::get("search_students") == "search_students") {
                            $class = Input::get('class');
                            $stream = implode(',', Input::get('stream'));

                            // var_dump ($stream);
                        }
                        $term=Input::get("term");
                    }
                    if (Input::get("send_sms_btn") == "send_sms_btn") {
                        $message = Input::get("message");

                        $phone_numbers = Input::get('all_phone_numbers');
                        if ($phone_numbers) {
                            $phone_numbers = explode(",", $phone_numbers);
                        }
                        $phone_numbers = implode(",", $phone_numbers);

                        if ($SMS_SERVICE_PROVIDER == "pandora") {
                            $phone_numbers = str_replace("+256", "0", $phone_numbers);
                        }
                        $phone_numbers = trim(str_replace("/", ",", $phone_numbers),',');
                        if ($phone_numbers) {
                            var_dump($phone_numbers);
                            sendMessage($phone_numbers, $message, getConfigValue("sms_from"));
                            echo '<div class="alert alert-success">' . (count(explode(",", $phone_numbers))) . ' Message sent successfully</div>';
                        } else {
                            echo '<div class="alert alert-danger">No phone numbers selected</div>';
                        }
                        Redirect::go_to("");
                    }
                }


                //fetch the student names and display
                if (($class && $stream) != '') {
                    $querystudent = "select s.*,p.Phone from enrollment e,student s LEFT JOIN parent p ON (s.Parent_Id=p.Parent_Id) WHERE e.Student_Id=s.Student_Id AND s.Status=1 AND e.Class_Id='$class' AND e.Stream_Id IN ($stream) AND e.Year='$year' GROUP BY s.Student_Id ORDER BY CONCAT(s.Fname,s.Lname)";
                    $fetchstudents = DB::getInstance()->querySample($querystudent);
                } else {
                    $querystudent = "";
                }
                $no = 1;
                ?>
                <div class="panel panel-info">
                    <?php
                    if ($fetchstudents) {
                    ?>
                        <div class="panel-heading">
                            <h5 class="page-header-" style="text-align: center;">
                                SEND SMS'S TO STUDENT(S) IN <?php
                                                            $stream = explode(',', $stream);

                                                            echo DB::getInstance()->getName('class', $class, 'Class_Name', 'Class_Id') . ' ';

                                                            foreach ($stream as $s) {
                                                                echo DB::getInstance()->getName('stream', $s, 'Stream_Name', 'Stream_Id') . '.';
                                                            }
                                                            ?>
                            </h5>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="content">
                                            <div class="table-responsive">
                                                <div class="pull-right">
                                                    <a class="btn btn-primary btn-xs" href="index.php?page=<?php echo $crypt->encode("student_classlist") . "&download_type=view_parent_contactlist&class_id=$class&year=$year&term=$term&token=" . Token::generate(); ?>">Print Contact-list</a>
                                                    <a class="btn btn-primary btn-xs" href="index.php?page=<?php echo $crypt->encode("student_photo_list") . '&download_type=view_classlist&class_id=' . $class . '&stream=' . $stream . '&token=' . Token::generate(); ?>">Print Classlist</a>
                                                    <a class="btn btn-primary btn-xs" target="_blank" href="index.php?page=<?php echo $crypt->encode("student_ids_list") . '&download_type=view_classlist&class_id=' . $class . '&stream=' . $stream . '&token=' . Token::generate(); ?>">Print IDs</a>
                                                    <a id="sms_all" style="display: none;" data-toggle='modal' class='btn btn-success btn-md' href='#sms-all-modal-form' onclick="whatwaschecked()">SMS Selected</a>
                                                </div>
                                                <!-- <form role="form" action="index.php?page=<?php echo $crypt->encode("send_to_class") . "&class=$class&stream=$stream&year=$year&term=$term"; ?>" method="post" class="form-inline" enctype="multipart/form-data"> -->
                                                <table class="table table-bordered" style="font-size: 13px;">
                                                    <thead>
                                                        <tr>
                                                            <th>
                                                                <label class="btn btn-success" for="selectall" id="selectControl" onclick="Check()">All</label>
                                                            </th>
                                                            <th>Name</th>
                                                            <?php if ($IS_GENDER_VISIBLE) { ?><th>Gender</th><?php } ?>
                                                            <th>Reg No.</th>
                                                            <th>Photo</th>
                                                            <th>Parent Phone No.</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        foreach ($fetchstudents as $studentdata) {
                                                        ?>
                                                            <tr class="gradeA">
                                                                <td>
                                                                    <?php if ($studentdata->Phone) { ?>
                                                                        <input type="checkbox" value="<?php echo $studentdata->Phone; ?>" onclick="hide_or_unhide_sms_all(this)" id="students<?php echo $studentdata->Student_Id; ?>" name="phone_numbers[]">
                                                                    <?php } ?>
                                                                </td>
                                                                <td>
                                                                    <?php echo "$studentdata->Fname $studentdata->Lname" ?>
                                                                </td>
                                                                <?php if ($IS_GENDER_VISIBLE) { ?>
                                                                    <td class="center">
                                                                        <?php echo $studentdata->Gender; ?>
                                                                    </td>
                                                                <?php } ?>
                                                                <td class="center">
                                                                    <?php echo $studentdata->Regno; ?>
                                                                </td>
                                                                <td class="center">
                                                                    <img width="50px" height="60px" id="blah<?php echo $studentdata->Student_Id; ?>" src="students/<?php echo $studentdata->Image; ?>" alt="student image" /><br />
                                                                    <input type="hidden" name="std_id[]" value="<?php echo $studentdata->Student_Id; ?>">
                                                                    <input type="hidden" name="image[]" value="<?php echo $studentdata->Image; ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="phone[]" class="form-control" value="<?php echo $studentdata->Phone; ?>" readonly />
                                                                    <input type="hidden" name="phone_old[]" value="<?php echo $studentdata->Phone; ?>" />
                                                                    <input type="hidden" name="parent_id[]" value="<?php echo $studentdata->Parent_Id; ?>" />
                                                                </td>

                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>

                                                </table>
                                                <!-- <button type="submit" class="btn btn-success pull-right" name="editStudentenphotos" value="editStudentenphotos">Save Changes</button> -->

                                                <!-- </form> -->

                                                <div class="modal fade bs-modal-md" id="sms-all-modal-form" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-md">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Send SMS'S to all selected students</h4>
                                                            </div>
                                                            <form role="form" action="index.php?page=<?php echo $crypt->encode("send_to_class") . "&class=$class&stream=$stream[0]&year=$year&term=$term"; ?>" method="POST">
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-md-12 col-lg-12 col-sm-12">
                                                                            <div class="form-group">
                                                                                <label>Message</label>
                                                                                <textarea class="form-control" cols="15" rows="6" name="message" oninput="countCharacters(this.value)" required></textarea>
                                                                                <input type="hidden" name="all_phone_numbers" id="all_phone_numbers">
                                                                                <span id="character-count"></span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                                                                    <button type="submit" name="send_sms_btn" value="send_sms_btn" class="btn btn-success">SEND</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <!-- /.modal-content -->
                                                    </div>
                                                    <!-- /.modal-dialog -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    <?php
                    } else {
                        echo '<h4 style="color:red"> There Are No Students For  ' . DB::getInstance()->getName('class', $class, 'Class_Name', 'Class_Id') . DB::getInstance()->getName('stream', $stream, 'Stream_Name', 'Stream_Id') . ' ' . $year . '</h4>';
                    }
                    ?>
                </div>
                <?php
                // }
                ?>
            </div>
        </div>

    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->

<?php require_once 'inc/footer.php' ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#classAssign').on('change', function() {
            $('#stream_id').val(null).trigger('change');
            var ClassName = $(this).val();
            if (ClassName && ClassName != "") {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?page=<?php echo $crypt->encode("ajax_data") ?>',
                    data: 'class_name_model=' + ClassName,
                    success: function(html) {
                        $('#stream_id').html(html);
                    }
                });
            } else {
                $('#stream_id').html("");
            }

        });
    });

    function countCharacters(text) {
        var countText = text ? `<strong>${text.length}</strong> character(s) equivalent to <strong>${Math.ceil(text.length/160)}</strong> message(s)` : ``;
        document.getElementById('character-count').innerHTML = countText;
    }

    function Check() {
        var checkBoxes = document.getElementsByName('phone_numbers[]');
        for (i = 0; i < checkBoxes.length; i++) {
            checkBoxes[i].checked = (selectControl.innerHTML == "All") ? 'checked' : '';
        }
        if (selectControl.innerHTML == "All") {
            document.getElementById("sms_all").style.display = 'unset';
        } else {
            document.getElementById("sms_all").style.display = 'none';
        }

        selectControl.innerHTML = (selectControl.innerHTML == "All") ? "Unselect" : 'All';
    }

    function hide_or_unhide_sms_all(element) {
        let number = element;

        if ($(element).checked) {
            document.getElementById("sms_all").style.display = 'unset';
        } else {
            var phone_numbers_array = document.querySelectorAll("input[name='phone_numbers[]']");
            for ($i = 0; $i < phone_numbers_array.length; $i++) {
                if ((phone_numbers_array[$i].checked)) {
                    document.getElementById("sms_all").style.display = 'unset';
                    return;
                }
            }
            document.getElementById("sms_all").style.display = 'none';
        }
    }

    function whatwaschecked() {
        var phone_number_array = document.querySelectorAll("input[name='phone_numbers[]']");
        var hold_parent_phone_numbers = ''
        for ($i = 0; $i < phone_number_array.length; $i++) {
            hold_parent_phone_numbers += (phone_number_array[$i].checked) ? phone_number_array[$i].value + ',' : '';
        }
        hold_parent_phone_numbers = hold_parent_phone_numbers.trim(',');
        document.getElementById('all_phone_numbers').value = hold_parent_phone_numbers;
    }

    window.onload = function() {
        var selectControl = document.getElementById("selectControl");
        selectControl.onclick = function() {
            Check();
        };
    };

    function get_student_id(student_id) {
        document.getElementById("student_id").value = student_id;
    }
</script>