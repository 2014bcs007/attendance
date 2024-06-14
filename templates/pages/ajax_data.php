<?php
if (Input::get("userAction") == "reconcileTransaction") {
    DB::getInstance()->update("transaction", $_POST['code'], array("Is_Reconciled" => 1), 'Transaction_Code');
}
if (isset($_POST["addSubjectUnits"]) && !empty($_POST["class_id"]) && !empty($_POST["year"]) && isset($_POST["year"])) {
    $class_id = $_POST['class_id'];
    $year = $_POST["year"];
    $addSubjectUnits = $_POST["addSubjectUnits"];
    $class = DB::getInstance()->querySample("SELECT *,(CASE WHEN Year<='$year' THEN True Else False END) Is_New FROM class WHERE Class_Id='$class_id'")[0];
    $subjects_done = $class->Subjects_Done;
    $Name = $class->Class_Name;
    $level = $class->Level_Name;

    if($CURRICULUM_COMBINED||!$class->Is_New){
    $setsQuery = "SELECT * FROM exam_set ORDER BY Set_Name";
    if (DB::getInstance()->checkRows($setsQuery)) {
?>
        <label>Select sets to be done:</label>
        <select name="numb[]" id="exam_sets" style="width:100%" multiple class="select2" onchange="calculateSets();" <?php echo $class->Is_New?'':'required'?>>
            <?php
            $set_list = DB::getInstance()->query($setsQuery);
            foreach ($set_list->results() as $sets) :
                echo '<option value="' . $sets->Set_Id . '">' . $sets->Set_Name . "</option>";
            endforeach;
            ?>
        </select>
        <input type="hidden" name="set_id" id="setsSelected">
        <div id="sets_selected_section"></div>
        <!-- </div> -->
        <div class="form-group" style="width:100%;">
            <div id="result" style="color: red;display: none">Can not register greater than three exam sets per class</div>
        <?php } else { ?>
            <b style="color: red">There are no exam sets registered in the school, <a href="#" class="btn btn-primary btn-xs">Click here</a> to upload them</b>
        <?php }
}

    if ($class->Is_New) {
        $subjects_done = $subjects_done?unserialize($subjects_done):array();
        $queryParam = ($Name == "S1" || $Name == "S2") ? " AND Subject_Id IN (" . implode(",", $subjects_done) . ")" : "";
        $subject_query = "select * from subject where subject.Level_Name='$level' $queryParam";
        $subject_list = DB::getInstance()->querySample($subject_query);

        ?>
        <h5 class="mt-2">Termly Subjects Units</h5>
            <table class="table table-bordered">
                <!-- <caption>Subject Topics / Units done this Term</caption> -->
                <tr>
                    <th style="width:20%">Subject</th>
                    <th>Units</th>
                </tr>
                <tbody>
                    <?php
                    $subject_unit_data = [];

                    // if ($Name == "S1" || $Name == "S2") {
                        foreach ($subject_list as $subjects) {
                            // if(in_array($subjects->Subject_Id, $subjects_done)){
                            // var_dump($subject);
                    ?>
                            <tr>
                                <td>
                                    <?php echo $subjects->Subject_Code . " " . $subjects->Subject_Name; ?>
                                </td>
                                <td>
                                    <?php
                                    $search_papers = 'select * from subject_unit WHERE Subject_Id="' . $subjects->Subject_Id . '"';
                                    $units = DB::getInstance()->querySample($search_papers);
                                    if ($units) {
                                    ?>
                                        <input type='hidden' name='subjects[]' value=' <?php echo $subjects->Subject_Id; ?>' />
                                        <div class="controls">
                                            <select name="SubjectUnits[<?php echo $subjects->Subject_Id ?>]" class="select2" id="SubjectUnits_<?php echo $subjects->Subject_Id ?>" multiple style="width:100%" onchange="sortsubjectunits('<?php echo $subjects->Subject_Id; ?>')">
                                                <?php
                                                foreach ($units as $unit) {
                                                    echo '<option value="' . $unit->Id . '">' . $unit->Title . '</option>';
                                                }
                                                ?>
                                            </select>
                                            <input type="hidden" name="units_id[<?php echo $subjects->Subject_Id ?>]" id="unitsSelected_<?php echo $subjects->Subject_Id ?>">
                                            <input type="hidden" name="is_new" value='is_new' ;>
                                        </div>
                                    <?php
                                    } else {
                                        echo '<div class="alert alert-danger">No Units Registered</div>';
                                    }
                                    ?>
                                </td>
                            </tr>
                    <?php
                            // $subject_unit_data = [$subjects->Subject_Code,SubjectUnits[]]
                            // }
                        }
                    // }
                    ?>
                </tbody>
            </table>
        <?php
    }
}

if (isset($_POST["returnStaffExpectedPayment"]) && $_POST['staff_id'] != "") {
    $staff_id = Input::get('staff_id');
    $station_id = Input::get("station_id");
    $payment_date = Input::get("payment_date");
    $payment_type = Input::get("payment_type");

    $total_salaries_paid = DB::getInstance()->calculateSum("SELECT Amount_Paid FROM staff_payments WHERE Staff_Id='$staff_id'", "Amount_Paid");
    $total_salary_expected = 0;
    $start_date = DB::getInstance()->displayTableColumnValue("SELECT Date_From FROM salary_structure WHERE Staff_Id='$staff_id' ORDER BY Date_From ASC LIMIT 1", "Date_From");
    $end_date = $payment_date;
    $begin = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = new DateInterval('P1M');
    $daterange = new DatePeriod($begin, $interval, $end);
    foreach ($daterange as $date) {
        $actual_date = $date->format("Y-m-d");
        $current_month_and_year = $date->format("Y-m");
        $monthly_salary = DB::getInstance()->displayTableColumnValue("SELECT Structure_Amount FROM salary_structure WHERE Staff_Id='$staff_id' AND Date_From<='$current_month_and_year' ORDER BY Date_From DESC LIMIT 1", "Structure_Amount");
        $monthly_salary = ($monthly_salary != "") ? $monthly_salary : 0;
        $total_salary_expected += $monthly_salary;
    }
    if ($payment_type == "Advance") {
        $date_diff = calculateDateDifference($actual_date, $payment_date, "days");
        $date_diff = ($date_diff == 31) ? 30 : $date_diff;
        $monthly_salary = DB::getInstance()->displayTableColumnValue("SELECT Structure_Amount FROM salary_structure WHERE Staff_Id='$staff_id' AND Date_From<='$payment_date' ORDER BY Date_From DESC LIMIT 1", "Structure_Amount");
        $total_salary_expected += round(($monthly_salary / 30) * $date_diff);
    }
    $total_salaries_not_paid = $total_salary_expected - $total_salaries_paid;
    $total_salaries_not_paid = ($total_salaries_not_paid > 0) ? $total_salaries_not_paid : 0;
    echo $total_salary_expected . '#' . $total_salaries_paid . '#' . $total_salaries_not_paid;
}
if (isset($_POST["updateStudentAssessment"]) && Input::get("student_id") != "") {
    $student_id = Input::get('student_id');
    $class_id = Input::get('class_id');
    $stream_id = Input::get('stream_id');
    $term = Input::get('term');
    $year = Input::get('year');
    $comment = Input::get('comment');
    $result = "";
    //ALTER TABLE `descipline_comment` ADD `Assessment_Comment` TEXT NOT NULL ;
    $assessmentQuery = "SELECT * FROM descipline_comment WHERE Student_Id='$student_id' AND Class_Id='$class_id' AND Stream_Id='$stream_id' AND Term='$term' AND Year='$year'";
    if (DB::getInstance()->checkRows($assessmentQuery)) {
        DB::getInstance()->query("UPDATE descipline_comment SET Assessment_Comment='$comment' WHERE Student_Id='$student_id' AND Class_Id='$class_id' AND Stream_Id='$stream_id' AND Term='$term' AND Year='$year'");
        $result = "updated";
    } else {
        DB::getInstance()->insert("descipline_comment", array(
            "Student_Id" => $student_id,
            "Class_Id" => $class_id,
            "Stream_Id" => $stream_id,
            "Term" => $term,
            "Year" => $year,
            "Assessment_Comment" => $comment
        ));
        $result = "registered";
    }
    $assessment = DB::getInstance()->DisplayTableColumnValue($assessmentQuery, "Assessment_Comment");
    echo $assessment . "##Assessment successfully " . $result;
}
if (isset($_POST['submitStudentSubjectComment'])) {
    $submission_type = Input::get("submission_type");
    $comment = Input::get('comment');
    $student_id = Input::get('student_id');
    $class_id = Input::get('class_id');
    $stream = Input::get('stream_id');
    $level = Input::get('level_name');
    $term = Input::get('term');
    $year = Input::get('year');
    $subject = Input::get('subject_id');
    $column = Input::get('column');
    $paper = Input::get('paper_id');
    $unit_id = Input::get('set_id');
    if ($GENERIC_SKILLS_TYPE == "unit" && $column == "Generic_Skills") {
        if ($submission_type == "insert") {
            $search_comment = "SELECT * FROM marksv2 WHERE Student_Id='$student_id' AND Subject_Id='$subject' AND Year='$year' AND Term='$term' AND Class_Id='$class_id' AND Stream_Id='$stream' AND Marks_Type='$unit_id'";
            if (DB::getInstance()->checkRows($search_comment)) {
                $dataInserted = DB::getInstance()->query("UPDATE marksv2 SET $column='$comment' WHERE Student_Id='$student_id' AND Subject_Id='$subject' AND Year='$year' AND Term='$term' AND Class_Id='$class_id' AND Stream_Id='$stream' AND Marks_Type='$unit_id'");
            } else {
                $dataInserted = DB::getInstance()->insert('marksv2', array(
                    'Student_Id' => $student_id,
                    'Subject_Id' => $subject,
                    'Year' => $year,
                    'Term' => $term,
                    'Class_Id' => $class_id,
                    'Stream_Id' => $stream,
                    "Marks_Type" => $unit_id,
                    $column => $comment
                ));
            }
        } else {
            $dataInserted = DB::getInstance()->query("UPDATE marksv2 SET $column='$comment' WHERE Student_Id='$student_id' AND Subject_Id='$subject' AND Year='$year' AND Term='$term' AND Class_Id='$class_id' AND Stream_Id='$stream' AND Marks_Type='$unit_id'");
        }
    } else {
        if ($submission_type == "insert") {
            $search_comment = "SELECT * FROM student_subject_comment WHERE Student_Id='$student_id' AND Subject_Id='$subject' AND Year='$year' AND Term='$term' AND Class_Id='$class_id' AND Stream_Id='$stream'";
            if (DB::getInstance()->checkRows($search_comment)) {
                $dataInserted = DB::getInstance()->query("UPDATE student_subject_comment SET $column='$comment' WHERE Student_Id='$student_id' AND Subject_Id='$subject' AND Year='$year' AND Term='$term' AND Class_Id='$class_id' AND Stream_Id='$stream'");
            } else {
                $dataInserted = DB::getInstance()->insert('student_subject_comment', array(
                    'Student_Id' => $student_id,
                    'Subject_Id' => $subject,
                    'Year' => $year,
                    'Term' => $term,
                    'Class_Id' => $class_id,
                    'Stream_Id' => $stream,
                    $column => $comment
                ));
            }
        } else {
            $dataInserted = DB::getInstance()->query("UPDATE student_subject_comment SET $column='$comment' WHERE Student_Id='$student_id' AND Subject_Id='$subject' AND Year='$year' AND Term='$term' AND Class_Id='$class_id' AND Stream_Id='$stream'");
        }
    }
}
if (isset($_POST["submitStudentMarks"]) && Input::exists() && Input::get("submitStudentMarks") == "submitStudentMarks") {
    $submission_type = Input::get("submission_type");
    $marks = Input::get('marks_entered');
    $student_id = Input::get('student_id');
    $exam_set = Input::get('set_id');
    $class_id = Input::get('class_id');
    $stream = Input::get('stream_id');
    $level = Input::get('level_name');
    $term = Input::get('term');
    $year = Input::get('year');
    $subject = Input::get('subject_id');
    $paper = Input::get('paper_id');
    $marks_type=Input::get("marks_type");
    $dataInserted = 0;
    $class = DB::getInstance()->querySample("SELECT *,(CASE WHEN Year<='$year' THEN True Else False END) Is_New FROM class WHERE Class_Id='$class_id'")[0];
    if ($marks_type=='new') {
        if ($submission_type == "insert") {
            $search_marks = "SELECT * FROM marksv2 WHERE Student_Id='$student_id' AND Subject_Id='$subject' AND Marks_Type='$exam_set' AND Year='$year' AND Term='$term' AND Class_Id='$class_id' AND Stream_Id='$stream' AND Status=1";
            if (DB::getInstance()->checkRows($search_marks)) {
                $action = "Updated to " . $marks;
                $dataInserted = DB::getInstance()->query("UPDATE marksv2 SET Marks='$marks' WHERE Student_Id='$student_id' AND Subject_Id='$subject' AND Marks_Type='$exam_set' AND Year='$year' AND Term='$term' AND Class_Id='$class_id' AND Stream_Id='$stream'");
            } else {
                $action = "Registered " . $marks;
                $dataInserted = DB::getInstance()->insert('marksv2', array(
                    'Student_Id' => $student_id,
                    'Subject_Id' => $subject,
                    'Marks_Type' => $exam_set,
                    'Year' => $year,
                    'Term' => $term,
                    'Class_Id' => $class_id,
                    'Stream_Id' => $stream,
                    'Marks' => $marks
                ));
            }
        } else {
            $action = "marks deleted";
            $dataInserted = DB::getInstance()->query("DELETE FROM marksv2 WHERE Student_Id='$student_id' AND Subject_Id='$subject' AND Marks_Type='$exam_set' AND Year='$year' AND Term='$term' AND Class_Id='$class_id' AND Stream_Id='$stream'");
        }
    } else
    if ($level == "OLEVEL") {
        if ($submission_type == "insert") {
            $search_marks = "SELECT * FROM marks WHERE Student_Id='$student_id' AND Subject_Id='$subject' AND Marks_Type='$exam_set' AND Year='$year' AND Term='$term' AND Class_Id='$class_id' AND Stream_Id='$stream' AND Status=1";
            if (DB::getInstance()->checkRows($search_marks)) {
                $action = "Updated to " . $marks;
                $dataInserted = DB::getInstance()->query("UPDATE marks SET Marks='$marks' WHERE Student_Id='$student_id' AND Subject_Id='$subject' AND Marks_Type='$exam_set' AND Year='$year' AND Term='$term' AND Class_Id='$class_id' AND Stream_Id='$stream'");
            } else {
                $action = "Registered " . $marks;
                $dataInserted = DB::getInstance()->insert('marks', array(
                    'Student_Id' => $student_id,
                    'Subject_Id' => $subject,
                    'Marks_Type' => $exam_set,
                    'Year' => $year,
                    'Term' => $term,
                    'Class_Id' => $class_id,
                    'Stream_Id' => $stream,
                    'Marks' => $marks
                ));
            }
        } else {
            $action = "marks deleted";
            $dataInserted = DB::getInstance()->query("DELETE FROM marks WHERE Student_Id='$student_id' AND Subject_Id='$subject' AND Marks_Type='$exam_set' AND Year='$year' AND Term='$term' AND Class_Id='$class_id' AND Stream_Id='$stream'");
        }
    } else if ($level == "ALEVEL") {
        if ($submission_type == "insert") {
            $search_marks = "SELECT * FROM amarks WHERE Student_Id='$student_id' AND Subject_Id='$subject' AND Paper_Id='$paper' AND Marks_Type='$exam_set' AND Year='$year' AND Term='$term' AND Class_Id='$class_id' AND Stream_Id='$stream' AND Status=1";
            if (DB::getInstance()->checkRows($search_marks)) {
                $action = "Updated to " . $marks;
                $dataInserted = DB::getInstance()->query("UPDATE amarks SET Marks='$marks' WHERE Student_Id='$student_id' AND Subject_Id='$subject' AND Paper_Id='$paper' AND Marks_Type='$exam_set' AND Year='$year' AND Term='$term' AND Class_Id='$class_id' AND Stream_Id='$stream'");
            } else {
                $action = "Registered " . $marks;
                $dataInserted = DB::getInstance()->insert('amarks', array(
                    'Student_Id' => $student_id,
                    'Subject_Id' => $subject,
                    'Paper_Id' => $paper,
                    'Marks_Type' => $exam_set,
                    'Year' => $year,
                    'Term' => $term,
                    'Class_Id' => $class_id,
                    'Stream_Id' => $stream,
                    'Marks' => $marks
                ));
            }
        } else {
            $action = "Marks deleted";
            $dataInserted = DB::getInstance()->query("DELETE FROM amarks WHERE Student_Id='$student_id' AND Subject_Id='$subject' AND Paper_Id='$paper' AND Marks_Type='$exam_set' AND Year='$year' AND Term='$term' AND Class_Id='$class_id' AND Stream_Id='$stream'");
        }
    }
    if ($dataInserted) {
        echo $action;
    } else {
        echo 'Error';
    }
}
//check whether the term has some students
if (isset($_POST['class_namepickterm'])) {
    $year = date("Y");
    $ClassID = $_POST['class_namepickterm'];
        ?>
        <div class="form-group">
            <label>Term:</label>
            <select name="term" class="form-control" required>
                <option value="">select Term..</option>
                <?php
                foreach ($terms_array as $term) {
                    echo "<option value='$term'>$term</option>";
                }
                ?>

            </select>
        </div>
        <?php
    }
    if (isset($_POST['term_name']) && isset($_POST['class_id']) && $_POST["display_type"] == "termly_sets") {
        $term = Input::get("term_name");
        $year = Input::get("year");
        $class = Input::get("class_id");
        $query_sets = "select Sets FROM exam_setting WHERE Class_Id='$class' AND Term='$term' AND Year='$year'";
        if (DB::getInstance()->checkRows($query_sets)) {
            $sets = explode(',', DB::getInstance()->displayTableColumnValue($query_sets, 'Sets'));
            for ($i = 0; $i < sizeof($sets); $i++) {
        ?>
                <div class="row">
                    <div class="col-lg-4">
                        <h5><?php echo DB::getInstance()->getName("exam_set", $sets[$i], "Set_Name", "Set_Id") ?>:</h5>
                    </div>
                    <div class="col-lg-4">
                        <div class="control-group form-group" style="width:100%;">
                            <div class="controls">
                                <input type="number" class="form-control" name="marks[]" min='0' max='100' required>
                                <input type="hidden" value='<?php echo $sets[$i] ?>' name='markstype[]'>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
            ?>
            <button type="submit" class="btn btn-primary">SUBMIT</button>
        <?php
        } else {
            redirect('No Sets found in your select', "index.php?page=" . $crypt->encode("exam_setting"));
        }
    }

    //Getting the streams in the specific class
    if (isset($_POST['class_name'])) {
        $ClassID = $_POST['class_name'];
        $select_streams = "select * from stream,class WHERE class.Class_Id=stream.Class_Id and class.Class_Id='$ClassID'";
        if (!DB::getInstance()->checkRows($select_streams)) {
            echo '<h4 style="color:red">No streams in that class</h4>';
        } else {
        ?>
            <div class="form-group">
                <label>Stream:</label>
                <select name="stream" class="form-control" required>
                    <?php
                    $query2 = "select * from stream,class WHERE class.Class_Id=stream.Class_Id and class.Class_Id='$ClassID' AND Stream_Name!=''";
                    if (!DB::getInstance()->checkRows($query2)) {
                        $query2 = "select * from stream,class WHERE class.Class_Id=stream.Class_Id and class.Class_Id='$ClassID' AND Stream_Name=''";
                    } else {
                        echo '<option value="">Select ....</option>';
                    }
                    $stream_list = DB::getInstance()->query($query2);
                    foreach ($stream_list->results() as $streams) {
                        $stream_name = ($streams->Stream_Name != "") ? $streams->Stream_Name : "No stream";
                        echo '<option value="' . $streams->Stream_Id . '">' . $stream_name . '</option>';
                    }
                    ?>
                </select>
            </div>
        <?php
        }
    }
    if (isset($_POST['className']) && $_POST['yearName'] != "" && $_POST['termName'] != "" && $_POST['display_mode'] == "add_combined_option") {
        $class = DB::getInstance()->querySample("SELECT *,(CASE WHEN Year<='".$_POST['yearName']."' THEN True Else False END) Is_New FROM class WHERE Class_Id='".$_POST['className']."'")[0];
        $query_sets = "select Sets FROM exam_setting WHERE Class_Id=" . $_POST['className'] . " AND Term='" . $_POST['termName'] . "' AND Year='" . $_POST['yearName'] . "'";
        ?>
        <label>Exam Set:</label>
        <select name="set_name" class="form-control">
            <?php if($class->Is_New){echo '<option value="units-combined">Units Combined</option>';}?>
            <option value="combined">Combined</option>
            <?php
            if (DB::getInstance()->checkRows($query_sets)) {
                $sets = explode(',', DB::getInstance()->displayTableColumnValue($query_sets, 'Sets'));
                for ($i = 0; $i < count($sets); $i++) {
            ?>
                    <option value="<?php echo $sets[$i] ?>"><?php echo DB::getInstance()->getName("exam_set", $sets[$i], "Set_Name", "Set_Id"); ?></option>
            <?php
                }
            }
            ?>

        </select>
        <?php
    }
    if (isset($_POST['class_name_model'])) {
        $ClassID = $_POST['class_name_model'];
        $select_streams = "select * from stream,class WHERE class.Class_Id=stream.Class_Id and class.Class_Id='$ClassID'";
        if (!DB::getInstance()->checkRows($select_streams)) {
            echo '<h4 style="color:red">No streams in that class</h4>';
        } else {
            $query2 = "select * from stream,class WHERE class.Class_Id=stream.Class_Id and class.Class_Id='$ClassID' AND Stream_Name!=''";
            if (!DB::getInstance()->checkRows($query2)) {
                $query2 = "select * from stream,class WHERE class.Class_Id=stream.Class_Id and class.Class_Id='$ClassID' AND Stream_Name=''";
            }
            $stream_list = DB::getInstance()->query($query2);
            foreach ($stream_list->results() as $streams) {
                $stream_name = ($streams->Stream_Name != "") ? $streams->Stream_Name : "No stream";
                echo '<option value="' . $streams->Stream_Id . '">' . $stream_name . '</option>';
            }
        }
    }
    if (isset($_POST["Class_Names"]) && !empty($_POST["Class_Names"])) {
        $ClassID = $_POST["Class_Names"];
        $query = "select * from stream,class WHERE class.Class_Id=stream.Class_Id and class.Class_Id='$ClassID'";
        if (DB::getInstance()->checkRows($query)) {
        ?>
            <div class="form-group">
                <label>Stream:</label>
                <select name="stream" class="form-control" required id="streamAssign">
                    <option value="">Select ....</option>
                    <?php
                    $query2 = "select * from stream,class WHERE class.Class_Id=stream.Class_Id and class.Class_Id='$ClassID' AND Stream_Name!=''";
                    if (!DB::getInstance()->checkRows($query2)) {
                        $query2 = "select * from stream,class WHERE class.Class_Id=stream.Class_Id and class.Class_Id='$ClassID' AND Stream_Name=''";
                    }
                    $streams = DB::getInstance()->query($query2);
                    foreach ($streams->results() as $streams) {
                        $stream_name = ($streams->Stream_Name != "") ? $streams->Stream_Name : "No stream";
                        echo '<option value="' . $streams->Stream_Id . '">' . $stream_name . '</option>';
                    }
                    ?>
                </select>
            </div>
            <?php
        } else {
            echo '<h5 style="color:red">Please select class first</h5>';
        }
        $Name = DB::getInstance()->displayTableColumnValue("select * from class Where Class_Id='$ClassID'", 'Class_Name');
        $subjects_done = DB::getInstance()->getName("class", $ClassID, "Subjects_Done", "Class_Id");
        if ($Name == "S1" || $Name == "S2") {
            $subjects_done = $subjects_done?unserialize($subjects_done):array();
            $subject_query = "select distinct(subject.Subject_Name),subject.Subject_Id,subject.Subject_Code from subject,class where subject.Level_Name=class.Level_Name and class.Class_Id='$ClassID'";
            if (DB::getInstance()->checkRows($subject_query)) {
            ?>
                <div class="form-group">
                    <label>Subject:</label>
                    <select name="sub" class="form-control" required>
                        <option value="">Select..</option>
                        <?php
                        $subject_list = DB::getInstance()->query($subject_query);
                        foreach ($subject_list->results() as $subjects) {
                            $hidden = (in_array($subjects->Subject_Id, $subjects_done)) ? "" : "hidden";
                            echo "<option value='" . $subjects->Subject_Id . "' " . $hidden . ">" . $subjects->Subject_Code . " " . $subjects->Subject_Name . "</option>";
                        }
                        ?>
                    </select>
                </div>
            <?php
            }
        } else if ($Name == "S3" || $Name == "S4") {
            $subject_query = "select distinct(subject.Subject_Name),subject.Subject_Id,subject.Subject_Code from subject,class where subject.Level_Name=class.Level_Name and class.Class_Id='$ClassID'";
            if (DB::getInstance()->checkRows($subject_query)) {
            ?>
                <div class="form-group">
                    <label>Subject:</label>
                    <select name="sub" class="form-control" required>
                        <option value="">Select..</option>
                        <?php
                        $subject_list = DB::getInstance()->query($subject_query);
                        foreach ($subject_list->results() as $subjects) {
                            echo "<option value='" . $subjects->Subject_Id . "'>" . $subjects->Subject_Code . " " . $subjects->Subject_Name . "</option>";
                        }
                        ?>
                    </select>
                </div>
            <?php
            }
        } else if ($Name == "S5" || $Name == "S6") {
            $subject_query = "select distinct(subject.Subject_Name),subject.Subject_Id,subject.Subject_Code from subject,class,paper
where subject.Level_Name=class.Level_Name and subject.Subject_Id=paper.Subject_Id and class.Class_Id='$ClassID'";
            if (DB::getInstance()->checkRows($subject_query)) {
            ?>
                <div class="form-group">
                    <label>Subject :</label>
                    <select name="sub" class="form-control" required id="sub" onchange="returnsubjectPapers(this.value);">
                        <option value="">Select..</option>
                        <?php
                        $subject_list = DB::getInstance()->query($subject_query);
                        foreach ($subject_list->results() as $subjects) {
                            echo "<option value='" . $subjects->Subject_Id . "'>" . $subjects->Subject_Code . " " . $subjects->Subject_Name . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><br />Paper:</label>
                    <select name="paper" class="form-control" required id="paper" required>
                        <option value="">Select..</option>
                    </select>
                </div>
            <?php
            }
        }
    }
    if (isset($_POST["display_class_subjects"]) && Input::get("class_id") != "") {
        $ClassID = $_POST["class_id"];
        $Name = DB::getInstance()->displayTableColumnValue("select * from class Where Class_Id='$ClassID'", 'Class_Name');
        $subjects_done = DB::getInstance()->getName("class", $ClassID, "Subjects_Done", "Class_Id");
        if ($Name == "S1" || $Name == "S2") {
            $subjects_done = $subjects_done?unserialize($subjects_done):array();
            $subject_query = "select distinct(subject.Subject_Name),subject.Subject_Id,subject.Subject_Code from subject,class where subject.Level_Name=class.Level_Name and class.Class_Id='$ClassID'";
            if (DB::getInstance()->checkRows($subject_query)) {
            ?>
                <div class="form-group">
                    <label>Subject:</label>
                    <select name="sub" class="form-control" required>
                        <option value="">Select..</option>
                        <?php
                        $subject_list = DB::getInstance()->query($subject_query);
                        foreach ($subject_list->results() as $subjects) {
                            $hidden = (in_array($subjects->Subject_Id, $subjects_done)) ? "" : "hidden";
                            echo "<option value='" . $subjects->Subject_Id . "' " . $hidden . ">" . $subjects->Subject_Code . " " . $subjects->Subject_Name . "</option>";
                        }
                        ?>
                    </select>
                </div>
            <?php
            }
        } else if ($Name == "S3" || $Name == "S4") {
            $subject_query = "select distinct(subject.Subject_Name),subject.Subject_Id,subject.Subject_Code from subject,class where subject.Level_Name=class.Level_Name and class.Class_Id='$ClassID'";
            if (DB::getInstance()->checkRows($subject_query)) {
            ?>
                <div class="form-group">
                    <label>Subject:</label>
                    <select name="sub" class="form-control" required>
                        <option value="">Select..</option>
                        <?php
                        $subject_list = DB::getInstance()->query($subject_query);
                        foreach ($subject_list->results() as $subjects) {
                            echo "<option value='" . $subjects->Subject_Id . "'>" . $subjects->Subject_Code . " " . $subjects->Subject_Name . "</option>";
                        }
                        ?>
                    </select>
                </div>
            <?php
            }
        } else if ($Name == "S5" || $Name == "S6") {
            $subject_query = "select distinct(subject.Subject_Name),subject.Subject_Id,subject.Subject_Code from subject,class,paper
where subject.Level_Name=class.Level_Name and subject.Subject_Id=paper.Subject_Id and class.Class_Id='$ClassID'";
            if (DB::getInstance()->checkRows($subject_query)) {
            ?>
                <div class="form-group">
                    <label>Subject :</label>
                    <select name="sub" class="form-control" required id="sub" onchange="returnsubjectPapers(this.value);">
                        <option value="">Select..</option>
                        <?php
                        $subject_list = DB::getInstance()->query($subject_query);
                        foreach ($subject_list->results() as $subjects) {
                            echo "<option value='" . $subjects->Subject_Id . "'>" . $subjects->Subject_Code . " " . $subjects->Subject_Name . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><br />Paper:</label>
                    <select name="paper" class="form-control" required id="paper" required>
                        <option value="">Select..</option>
                    </select>
                </div>
            <?php
            }
        }
    }
    if (isset($_POST["subjectName"]) && !empty($_POST["subjectName"])) {
        $subjectID = $_POST["subjectName"];
        // $trid = mysql_query("select * from paper WHERE Subject_Id='$subjectID'");
        echo '<option value="">Select Paper</option>';
        $paper_list = DB::getInstance()->query("select * from paper WHERE Subject_Id='$subjectID'");
        foreach ($paper_list->results() as $papers) {
            echo "<option value='" . $papers->Paper_Id . "'>" . $papers->Paper . "</option>";
        }
    }
    if (Input::exists() && Input::get("displaySourceNames") == "displaySourceNames") {
        echo '<option value="">Select....</option>';
        $source_type = Input::get("source_type");
        $sourcesList = DB::getInstance()->querySample("SELECT * FROM source_items WHERE Item_Type='$source_type'");
        foreach ($sourcesList as $list) {
            echo '<option value="' . $list->Item_Id . '">' . $list->Item_Name . '</option>';
        }
    }

    if (isset($_POST["addStudentCombination"]) && !empty($_POST["student_id"]) && isset($_POST["year"]) && !empty($_POST["year"])) {
        $student_id = $_POST["student_id"];
        $year = $_POST["year"];
        if (DB::getInstance()->checkRows("SELECT * FROM student_subject_assignment WHERE Student_Id='$student_id' AND Year='$year'")) {
            ?>
            <input type="hidden" value="<?php echo $student_id ?>" name="student_id">
            <div class="">
                <div class="col-md-12">
                    <lable>
                        <center>Select principal subjects.(only unique subjects should be selected)</center>
                    </lable>
                </div>

                <div class="col-md-4">
                    <select name="principal_subjects[]" class="form-control" required>
                        <option value="">Choose...</option>
                        <?php
                        $subjectListLoop = DB::getInstance()->querySample("SELECT subject.Subject_Name FROM subject,student_subject_assignment WHERE student_subject_assignment.Subject_Id=subject.Subject_Id AND subject.Subject_Code NOT LIKE 'S%' AND student_subject_assignment.Student_Id='$student_id' AND student_subject_assignment.Year='$year'");
                        foreach ($subjectListLoop as $subjects) {
                        ?>
                            <option value="<?php echo $subjects->Subject_Name; ?>"><?php echo $subjects->Subject_Name; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="principal_subjects[]" class="form-control" required>
                        <option value="">Choose...</option>
                        <?php
                        $subjectListLoop = DB::getInstance()->querySample("SELECT subject.Subject_Name FROM subject,student_subject_assignment WHERE student_subject_assignment.Subject_Id=subject.Subject_Id AND subject.Subject_Code NOT LIKE 'S%' AND student_subject_assignment.Student_Id='$student_id' AND student_subject_assignment.Year='$year'");
                        foreach ($subjectListLoop as $subjects) {
                        ?>
                            <option value="<?php echo $subjects->Subject_Name; ?>"><?php echo $subjects->Subject_Name; ?></option>
                        <?php } ?>
                    </select>

                </div>
                <div class="col-md-4">
                    <select name="principal_subjects[]" class="form-control" required>
                        <option value="">Choose...</option>
                        <?php
                        $subjectListLoop = DB::getInstance()->querySample("SELECT subject.Subject_Name FROM subject,student_subject_assignment WHERE student_subject_assignment.Subject_Id=subject.Subject_Id AND subject.Subject_Code NOT LIKE 'S%' AND student_subject_assignment.Student_Id='$student_id' AND student_subject_assignment.Year='$year'");
                        foreach ($subjectListLoop as $subjects) {
                        ?>
                            <option value="<?php echo $subjects->Subject_Name; ?>"><?php echo $subjects->Subject_Name; ?></option>
                        <?php } ?>
                    </select>

                </div>
            </div>
            <div class="form-group">
                <lable>Select subsidiary</lable>
                <select name="subsidiary_subject" class="form-control" required>
                    <?php
                    $subjectListLoop = DB::getInstance()->querySample("SELECT subject.Subject_Name,subject.Subject_Code FROM subject,student_subject_assignment WHERE student_subject_assignment.Subject_Id=subject.Subject_Id AND subject.Subject_Code LIKE 'S%' AND student_subject_assignment.Student_Id='$student_id' AND student_subject_assignment.Year='$year'");
                    foreach ($subjectListLoop as $subjects) {
                        if ($subjects->Subject_Code != "S101") {
                    ?>
                            <option value="<?php echo $subjects->Subject_Name; ?>"><?php echo $subjects->Subject_Name; ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>
    <?php
        }
    }
    ?>