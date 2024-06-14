<?php require_once 'inc/header.php' ?>
<div class="page-content">
    <div class="container-fluid">

        <!-- Page content here -->
        <div class="row">
            <div class="col-md-12">
                <div class="border-0 align-items-center d-flex">
                    <h4 class="flex-grow-1">System Configurations</h4>
                    <div>
                        <a href="index.php?page=<?php echo $crypt->encode("dashboard") ?>" class="btn-sm btn-primary"> Home</a>
                        <a href="index.php?page=<?php echo $crypt->encode("beginning_of_term_setting") ?>" class="btn-sm btn-primary">Next term Begins</a>
                        <a href="index.php?page=<?php echo $crypt->encode("marksheets") ?>" class="btn-sm btn-primary">View Report Format</a>
                    </div>
                </div>
                <div class="card">
                    <?php
                    $tab = isset($_GET['tab']) && $_GET['tab'] != '' ? $_GET['tab'] : 'report-settings';
                    $report_settings_tab_active = $tab == 'report-settings' ? ' active in' : '';
                    $general_tab_active = $tab == 'general' ? ' active in' : '';
                    $sms_tab_active = $tab == 'sms' ? ' active in' : '';
                    $system_configurations_tab_active = $tab == 'system' ? ' active in' : '';
                    $bank_accounts_tab_active = $tab == "bank-accounts" ? ' active in' : '';

                    if (isset($_GET['error'])) {
                        $error = $_GET['error'];
                        echo "<h6 style='color:red;'><center>" . $error . "</center></h6>";
                    }
                    if (isset($_GET['message'])) {
                        $message = $_GET['message'];
                        echo "<h6 style='color:blue;'><center>" . $message . "</center></h6>";
                    }
                    if (Input::exists() && Input::get("other_settings_btn") == "other_settings_btn") {
                        //$SCHOOL_ID;
                        $subject_positioning = Input::get("subject_positioning");
                        $student_positioning = Input::get("student_positioning");
                        $gender = serialize(Input::get("gender"));
                        $schooling_type = serialize(Input::get("schooling_type"));
                        $motto_position = serialize(Input::get("motto_position"));
                        $marks_rounded_to = Input::get("marks_rounded_to");
                        $marks_rounded_to_old = Input::get("marks_rounded_to_old");
                        $education_levels = serialize(Input::get("education_levels"));
                        $display_house_master_comment = Input::get("display_house_master_comment");
                        $display_bursar_comment = Input::get("display_bursar_comment");

                        $class_name = Input::get("class_name");
                        $promoted_on = Input::get("promoted_on");
                        $promotion_setting = serialize(array('class' => $class_name, 'setting' => $promoted_on));

                        if ($SCHOOL_ID) {
                            $executeQuery = DB::getInstance()->update("report", $SCHOOL_ID, array(
                                'Subject_Positioning' => $subject_positioning,
                                'Student_Positioning' => $student_positioning,
                                'Schooling_Type' => $schooling_type,
                                'Motto_Position' => $motto_position,
                                'Marks_Rounded_To' => $marks_rounded_to,
                                'Gender_Offered' => $gender,
                                'Education_Levels' => $education_levels,
                                'Promotion_Setting' => $promotion_setting,
                                'Display_House_Master_Comment' => $display_house_master_comment,
                                'Display_Bursar_Comment' => $display_bursar_comment
                            ), 'Report_Id');
                        } else {
                            $executeQuery = DB::getInstance()->insert("report", array(
                                'Subject_Positioning' => $subject_positioning,
                                'Student_Positioning' => $student_positioning,
                                'Schooling_Type' => $schooling_type,
                                'Motto_Position' => $motto_position,
                                'Marks_Rounded_To' => $marks_rounded_to,
                                'Gender_Offered' => $gender,
                                'Education_Levels' => $education_levels,
                                'Promotion_Setting' => $promotion_setting,
                                'Display_House_Master_Comment' => $display_house_master_comment,
                                'Display_Bursar_Comment' => $display_bursar_comment
                            ));
                        }
                        $settings = Input::get("settings");
                        foreach ($settings as $setting => $value) {
                            DB::getInstance()->updateSetting("$setting", $value);
                        }
                        //if ($marks_rounded_to != $marks_rounded_to_old) {
                            // error_reporting(E_ALL);
                            //Drop all views and recreate them
                            DB::getInstance()->query("DROP VIEW IF EXISTS `alevel_grading_view`, `alevel_grading_viewe`, `alevel_subject_total`, `a_single_set_grading_view`, `a_single_set_grading_viewe`, `a_single_set_subject_total`, `olevel_grading_view`, `olevel_grading_viwe`, `passmarkview`, `single_set_grading_view`, `single_set_grading_viewe`, `single_set_student_total`, `single_set_subject_total`, `student_total`, `subject_total`,`new_curriculum_subject_total`,`new_curriculum_student_total`,`subject_positioning_view`,`single_set_subject_positioning_view`,`single_set_student_positioning_view`,`student_positioning_view`,`combined_curriculum_student_total`,`combined_curriculum_subject_total`;");
                            $createViewQuery = "CREATE VIEW passmarkview AS (SELECT Class_Id, Year,Term, COUNT(Marks_Type) AS Marks_Number,Marks FROM passmark WHERE Status=1 GROUP BY Class_Id, Year, Term);


                                    create view single_set_subject_total as(select marks.Student_Id,marks.Subject_Id,marks.Marks_Type,marks.Class_Id,marks.Stream_Id,marks.Term,marks.Year,passmark.Marks, 
                                    TRIM(FORMAT((marks.Marks/passmark.Marks)*100,$marks_rounded_to))+0 AS Subject_Total,TRIM(FORMAT((marks.Marks/passmark.Marks)*100,$marks_rounded_to))+0 AS Subject_Average 
                                    from marks,passmark where marks.Status=1 AND marks.Marks_Type=passmark.Marks_Type AND marks.Class_Id=passmark.Class_Id AND marks.Year=passmark.Year AND marks.Term=passmark.Term AND passmark.Status=1 group by marks.Subject_Id,marks.Marks_Type,marks.Student_Id,marks.Class_Id,marks.Stream_Id,marks.Term,marks.Year);
                                    
                                    create view subject_total as(select marks.Student_Id,marks.Subject_Id,marks.Class_Id,class.Year AS New_Curriculum_Year,marks.Stream_Id,marks.Term,marks.Year,passmarkview.Marks_Number,passmarkview.Marks, 
                                    CASE WHEN (passmarkview.Marks=100 OR (marks.Year>=class.Year AND passmarkview.Marks=80)) THEN TRIM(FORMAT(Sum(marks.Marks)/passmarkview.Marks_Number,$marks_rounded_to))+0
                                    ELSE TRIM(FORMAT(Sum(marks.Marks),$marks_rounded_to))+0 END as Subject_Total 
                                    from class,marks,passmarkview where class.Class_Id=marks.Class_Id AND marks.Status=1 AND marks.Class_Id=passmarkview.Class_Id AND marks.Year=passmarkview.Year AND marks.Term=passmarkview.Term group by marks.Subject_Id,marks.Student_Id,marks.Class_Id,marks.Stream_Id,marks.Term,marks.Year);
                                    
                                    create view alevel_subject_total as(select amarks.Student_Id,amarks.Subject_Id,Paper_Id,amarks.Class_Id,amarks.Stream_Id,amarks.Term,amarks.Year,passmarkview.Marks_Number,passmarkview.Marks, 
                                    CASE WHEN passmarkview.Marks=100 THEN TRIM(FORMAT(Sum(amarks.Marks)/passmarkview.Marks_Number,$marks_rounded_to))+0
                                    ELSE Sum(amarks.Marks) END as Subject_Total 
                                    from amarks,passmarkview where amarks.Status=1 AND amarks.Class_Id=passmarkview.Class_Id AND amarks.Year=passmarkview.Year AND amarks.Term=passmarkview.Term group by amarks.Subject_Id,amarks.Paper_Id, amarks.Student_Id,amarks.Class_Id,amarks.Stream_Id,amarks.Term,amarks.Year);
                                    
                                    create view student_total as(select Student_Id,Class_Id,Stream_Id,Term,Year,Sum(Subject_Total) as Totals, ROUND(SUM(Subject_Total)/((SELECT COUNT(*) FROM subject WHERE Level_Name='OLEVEL' AND Type='Compulsory')+(SELECT COUNT(*) FROM student_subject_assignment ssa WHERE ssa.Student_Id=st.Student_Id AND ssa.Year=st.Year)),$marks_rounded_to) AS Average from subject_total st group by Student_Id,Class_Id,Stream_Id,Term,Year);
                                    
                                    create view a_single_set_subject_total as(select amarks.Student_Id,amarks.Subject_Id,amarks.Paper_Id,amarks.Marks_Type,amarks.Class_Id,amarks.Stream_Id,amarks.Term,amarks.Year,passmark.Marks, 
                                    TRIM(FORMAT((amarks.Marks/passmark.Marks)*100,$marks_rounded_to))+0 AS Subject_Total 
                                    from amarks,passmark where amarks.Status=1 AND amarks.Marks_Type=passmark.Marks_Type AND amarks.Class_Id=passmark.Class_Id AND amarks.Year=passmark.Year AND amarks.Term=passmark.Term AND passmark.Status=1 group by amarks.Subject_Id,amarks.Paper_Id,amarks.Marks_Type,amarks.Student_Id,amarks.Class_Id,amarks.Stream_Id,amarks.Term,amarks.Year);
                                    
                                    create view single_set_student_total as(select Student_Id,Class_Id,Stream_Id,Term,Year,Marks_Type,Sum(Subject_Total) as Totals,ROUND(SUM(Subject_Total)/((SELECT COUNT(*) FROM subject WHERE Level_Name='OLEVEL' AND Type='Compulsory')+(SELECT COUNT(*) FROM student_subject_assignment ssa WHERE ssa.Student_Id=st.Student_Id AND ssa.Year=st.Year)),$marks_rounded_to) AS Average from single_set_subject_total st group by Student_Id,Class_Id,Stream_Id,Term,Year,Marks_Type);
                                    
                                    create view alevel_grading_view as(select alevel_subject_total.Paper_Id, alevel_subject_total.Student_Id,alevel_subject_total.Subject_Id,alevel_subject_total.Class_Id,alevel_subject_total.Stream_Id,alevel_subject_total.Term,alevel_subject_total.Year,ROUND(alevel_subject_total.Subject_Total,$marks_rounded_to) AS Marks, grading.Grade from alevel_subject_total,grading WHERE alevel_subject_total.Class_Id=grading.Class_Id AND alevel_subject_total.Year=grading.Year AND alevel_subject_total.Subject_Total>=grading.Initial_Marks AND alevel_subject_total.Subject_Total<=grading.Final_Marks group by alevel_subject_total.Paper_Id,alevel_subject_total.Subject_Id,alevel_subject_total.Student_Id,alevel_subject_total.Class_Id,alevel_subject_total.Stream_Id,alevel_subject_total.Term,alevel_subject_total.Year);
                                    
                                    create view single_set_grading_view as(select single_set_subject_total.Student_Id,single_set_subject_total.Subject_Id,single_set_subject_total.Class_Id,single_set_subject_total.Stream_Id,single_set_subject_total.Marks_Type,single_set_subject_total.Term,single_set_subject_total.Year,ROUND(single_set_subject_total.Subject_Total,$marks_rounded_to) AS Marks, grading.Grade AS Grade
                                    from single_set_subject_total,grading WHERE single_set_subject_total.Subject_Total>=grading.Initial_Marks AND single_set_subject_total.Subject_Total<=grading.Final_Marks AND single_set_subject_total.Class_Id=grading.Class_Id AND single_set_subject_total.Year=grading.Year group by single_set_subject_total.Subject_Id,single_set_subject_total.Student_Id,single_set_subject_total.Marks_Type,single_set_subject_total.Class_Id,single_set_subject_total.Stream_Id,single_set_subject_total.Term,single_set_subject_total.Year);
                                    
                                    create view a_single_set_grading_view as(select a_single_set_subject_total.Student_Id,a_single_set_subject_total.Paper_Id,a_single_set_subject_total.Subject_Id,a_single_set_subject_total.Class_Id,a_single_set_subject_total.Stream_Id,a_single_set_subject_total.Marks_Type,a_single_set_subject_total.Term,a_single_set_subject_total.Year,ROUND(a_single_set_subject_total.Subject_Total,$marks_rounded_to) AS Marks, grading.Grade AS Grade
                                    from a_single_set_subject_total,grading WHERE a_single_set_subject_total.Subject_Total>=grading.Initial_Marks AND a_single_set_subject_total.Subject_Total<=grading.Final_Marks AND a_single_set_subject_total.Class_Id=grading.Class_Id AND a_single_set_subject_total.Year=grading.Year group by a_single_set_subject_total.Subject_Id,a_single_set_subject_total.Paper_Id,a_single_set_subject_total.Student_Id,a_single_set_subject_total.Marks_Type,a_single_set_subject_total.Class_Id,a_single_set_subject_total.Stream_Id,a_single_set_subject_total.Term,a_single_set_subject_total.Year);
                                    
                                                                        
                                    
                                    
                                    create view olevel_grading_view as(select subject_total.Student_Id,subject_total.Subject_Id,subject_total.Class_Id,subject_total.Stream_Id,subject_total.Term,subject_total.Year,ROUND(subject_total.Subject_Total,$marks_rounded_to) AS Marks, grading.Grade from subject_total,grading WHERE subject_total.Class_Id=grading.Class_Id AND subject_total.Year=grading.Year AND ROUND(subject_total.Subject_Total,0)>=grading.Initial_Marks AND ROUND(subject_total.Subject_Total,0)<=grading.Final_Marks group by subject_total.Subject_Id,subject_total.Student_Id,subject_total.Class_Id,subject_total.Stream_Id,subject_total.Term,subject_total.Year);
                                    
                                    create view alevel_grading_viewe as(select alevel_subject_total.Paper_Id, alevel_subject_total.Student_Id,alevel_subject_total.Subject_Id,alevel_subject_total.Class_Id,alevel_subject_total.Stream_Id,alevel_subject_total.Term,alevel_subject_total.Year,ROUND(alevel_subject_total.Subject_Total,$marks_rounded_to) AS Marks, grading.Grade from alevel_subject_total,grading WHERE alevel_subject_total.Class_Id=grading.Class_Id AND alevel_subject_total.Year=grading.Year AND ROUND(alevel_subject_total.Subject_Total)>=grading.Initial_Marks AND ROUND(alevel_subject_total.Subject_Total)<=grading.Final_Marks group by alevel_subject_total.Paper_Id,alevel_subject_total.Subject_Id,alevel_subject_total.Student_Id,alevel_subject_total.Class_Id,alevel_subject_total.Stream_Id,alevel_subject_total.Term,alevel_subject_total.Year);
                                    
                                    create view single_set_grading_viewe as(select single_set_subject_total.Student_Id,single_set_subject_total.Subject_Id,single_set_subject_total.Class_Id,single_set_subject_total.Stream_Id,single_set_subject_total.Marks_Type,single_set_subject_total.Term,single_set_subject_total.Year,ROUND(single_set_subject_total.Subject_Total,$marks_rounded_to) AS Marks, grading.Grade AS Grade
                                    from single_set_subject_total,grading WHERE ROUND(single_set_subject_total.Subject_Total)>=grading.Initial_Marks AND ROUND(single_set_subject_total.Subject_Total)<=grading.Final_Marks AND single_set_subject_total.Class_Id=grading.Class_Id AND single_set_subject_total.Year=grading.Year group by single_set_subject_total.Subject_Id,single_set_subject_total.Student_Id,single_set_subject_total.Marks_Type,single_set_subject_total.Class_Id,single_set_subject_total.Stream_Id,single_set_subject_total.Term,single_set_subject_total.Year);
                                    
                                    create view a_single_set_grading_viewe as(select a_single_set_subject_total.Student_Id,a_single_set_subject_total.Paper_Id,a_single_set_subject_total.Subject_Id,a_single_set_subject_total.Class_Id,a_single_set_subject_total.Stream_Id,a_single_set_subject_total.Marks_Type,a_single_set_subject_total.Term,a_single_set_subject_total.Year,ROUND(a_single_set_subject_total.Subject_Total,$marks_rounded_to) AS Marks, grading.Grade AS Grade
                                    from a_single_set_subject_total,grading WHERE ROUND(a_single_set_subject_total.Subject_Total)>=grading.Initial_Marks AND ROUND(a_single_set_subject_total.Subject_Total)<=grading.Final_Marks AND a_single_set_subject_total.Class_Id=grading.Class_Id AND a_single_set_subject_total.Year=grading.Year group by a_single_set_subject_total.Subject_Id,a_single_set_subject_total.Paper_Id,a_single_set_subject_total.Student_Id,a_single_set_subject_total.Marks_Type,a_single_set_subject_total.Class_Id,a_single_set_subject_total.Stream_Id,a_single_set_subject_total.Term,a_single_set_subject_total.Year);
                                    
                                    create view student_positioning_view as(select sc.*,       (select count(*) + 1        from student_total sc2        where ROUND(sc2.Totals) > ROUND(sc.Totals) and sc2.Class_Id = sc.Class_Id and sc2.Term=sc.Term and sc2.Year=sc.Year       ) as Class_Position, (select count(*) + 1        from student_total sc2        where ROUND(sc2.Totals) > ROUND(sc.Totals) and sc2.Class_Id = sc.Class_Id and sc2.Term=sc.Term and sc2.Year=sc.Year and sc2.Stream_Id=sc.Stream_Id       ) as Stream_Position from student_total sc order by Class_Id, Totals);

                                    create view single_set_student_positioning_view as (select sc.*,       (select count(*) + 1        from single_set_student_total sc2        where ROUND(sc2.Totals) > ROUND(sc.Totals) and sc2.Class_Id = sc.Class_Id and sc2.Term=sc.Term and sc2.Year=sc.Year and sc2.Marks_Type=sc.Marks_Type       ) as Class_Position, (select count(*) + 1        from single_set_student_total sc2        where ROUND(sc2.Totals) > ROUND(sc.Totals) and sc2.Class_Id = sc.Class_Id and sc2.Term=sc.Term and sc2.Year=sc.Year and sc2.Marks_Type=sc.Marks_Type and sc2.Stream_Id=sc.Stream_Id       ) as Stream_Position from single_set_student_total sc order by Class_Id, Totals);


                                    create view subject_positioning_view as (select sc.*, (select count(*) + 1 from subject_total sc2 where ROUND(sc2.Subject_Total) > ROUND(sc.Subject_Total) and sc2.Class_Id = sc.Class_Id and sc2.Term=sc.Term and sc2.Year=sc.Year and sc2.Stream_Id=sc.Stream_Id and sc2.Subject_Id=sc.Subject_Id) as Position from subject_total sc order by Class_Id, Subject_Total);

                                    create view single_set_subject_positioning_view as (select sc.*, (select count(*) + 1 from single_set_subject_total sc2 where ROUND(sc2.Subject_Total) > ROUND(sc.Subject_Total) and sc2.Class_Id = sc.Class_Id and sc2.Term=sc.Term and sc2.Year=sc.Year and sc2.Marks_Type=sc.Marks_Type and sc2.Stream_Id=sc.Stream_Id and sc2.Subject_Id=sc.Subject_Id) as Position from single_set_subject_total sc order by Class_Id, Subject_Total);

                                    CREATE VIEW new_curriculum_subject_total AS (select marks.Student_Id,marks.Subject_Id,marks.Class_Id,marks.Stream_Id,marks.Term,marks.Year,(length(`Units`) - length(replace(`Units`, ',', '')) + 1) Total_Units,TRIM(ROUND(Sum(marks.Marks),$ACTIVITIES_DECIMAL_PLACES)) AS Subject_Total, TRIM(ROUND(Sum(marks.Marks)/(length(`Units`) - length(replace(`Units`, ',', '')) + 1),$ACTIVITIES_DECIMAL_PLACES))+0 as Subject_Average from marksv2 marks,subject_termly_setting sts where marks.Status=1 AND marks.Class_Id=sts.Class_Id AND marks.Year=sts.Year AND marks.Term=sts.Term AND sts.Subject_Id=marks.Subject_Id group by marks.Subject_Id,marks.Student_Id,marks.Class_Id,marks.Stream_Id,marks.Term,marks.Year);

                                    create view new_curriculum_student_total as(select Student_Id,Class_Id,Stream_Id,Term,Year,TRIM(ROUND(Sum(Subject_Average),$ACTIVITIES_DECIMAL_PLACES)) as Totals, ROUND(SUM(Subject_Total)/((SELECT COUNT(*) FROM subject WHERE Level_Name='OLEVEL' AND Type='Compulsory')+(SELECT COUNT(*) FROM student_subject_assignment ssa WHERE ssa.Student_Id=st.Student_Id AND ssa.Year=st.Year)),$ACTIVITIES_DECIMAL_PLACES) AS Average from new_curriculum_subject_total st group by Student_Id,Class_Id,Stream_Id,Term,Year);
                                    CREATE VIEW combined_curriculum_subject_total AS ( SELECT e.Student_Id,sub.Subject_Id,sub.Subject_Name,e.Class_Id,e.Stream_Id,e.Term,e.Year,oct.New_Curriculum_Year,oct.Marks_Number,nct.Total_Units, ROUND((COALESCE(nct.Subject_Average,0)*20/3),$marks_rounded_to) AS Over_Twenty,oct.Subject_Total AS Over_Eighty,ROUND(((COALESCE(nct.Subject_Average,0)*20/3)+COALESCE(oct.Subject_Total,0)),$marks_rounded_to) AS Subject_Total FROM (class c,enrollment e, subject sub) LEFT OUTER JOIN new_curriculum_subject_total nct ON (e.Student_Id=nct.Student_Id AND nct.Year=e.Year AND e.Class_Id=nct.Class_Id AND e.Term=nct.Term AND nct.Subject_Id=sub.Subject_Id) LEFT OUTER JOIN subject_total oct ON (e.Student_Id=oct.Student_Id AND oct.Year=e.Year AND e.Class_Id=oct.Class_Id AND e.Term=oct.Term AND oct.Subject_Id=sub.Subject_Id) WHERE e.Class_Id=c.Class_Id AND e.Year>=c.Year AND COALESCE(oct.Student_Id,nct.Student_Id) IS NOT NULL GROUP BY sub.Subject_Id,e.Student_Id,e.Class_Id,e.Stream_Id,e.Term,e.Year);
CREATE VIEW combined_curriculum_student_total AS (SELECT Student_Id,Class_Id,Stream_Id,Term,Year,ROUND(Sum(Subject_Total),$marks_rounded_to) AS Totals, ROUND(SUM(Subject_Total)/((SELECT COUNT(*) FROM subject WHERE Level_Name='OLEVEL' AND Type='Compulsory')+(SELECT COUNT(*) FROM student_subject_assignment ssa WHERE ssa.Student_Id=st.Student_Id AND ssa.Year=st.Year)),$marks_rounded_to) AS Average from combined_curriculum_subject_total st group by Student_Id,Class_Id,Stream_Id,Term,Year);
                                    ";
                            DB::getInstance()->query($createViewQuery);
                        //}
                        if ($executeQuery) {
                            echo '<div class="alert alert-success">Settings has been successfull updated</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error while updating settings</div>';
                        }
                        Redirect::go_to("");
                    }
                    if (Input::exists() && Input::get("submit_settings_btn") == "submit_settings_btn") {
                        $school = strtoupper(Input::get('school'));
                        $motto = strtoupper(Input::get('motto'));

                        $address = strtoupper(Input::get('address'));
                        $site = strtolower(Input::get('site'));
                        $phone = $phone1 = Input::get('phone1');
                        $logo_name = $_FILES["logo"]["name"];
                        $logo = Input::get("old_logo");
                        $old_watermark = Input::get("old_watermark");
                        $watermark_name = $_FILES['watermark']['name'];
                        $hmsignature_name = $_FILES['hmsignature']['name'];
                        if (Input::get('phone2') != "") {
                            $phone .= '/' . Input::get('phone2');
                        }
                        $target_file = 'logo/' . basename($logo_name);
                        if ($logo_name != "") {
                            if (file_exists($target_file)) {
                                //unlink($logo_name);
                            }
                            move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file);
                            $logo = $logo_name;
                        }
                        $watermark = $old_watermark;
                        if ($watermark_name != '') {
                            $watermark_tmp = $_FILES["watermark"]["tmp_name"];
                            $watermark = "watermark" . '.' . end(explode(".", $watermark_name));
                            unlink("logo/" . $old_watermark);
                            move_uploaded_file($watermark_tmp, 'logo/' . basename($watermark));
                        }
                        // Upload HM signature
                        if ($hmsignature_name != '') {
                            $hmsignature_tmp = $_FILES["hmsignature"]["tmp_name"];
                            $photo = ($_FILES["hmsignature"]["name"]);
                            $target_file = 'Hmsignature/' . basename($_FILES["hmsignature"]["name"]);
                            move_uploaded_file($_FILES["hmsignature"]["tmp_name"], $target_file);
                            if (DB::getInstance()->checkRows("select * from headteacher_signature")) {
                                $query = "UPDATE headteacher_signature SET Signature_Image='$photo'";
                                $executeQuery = DB::getInstance()->query($query);
                            } else {
                                $query = "insert into headteacher_signature(Signature_Image)values('$photo')";
                                $executeQuery = DB::getInstance()->query($query);
                            }
                        }

                        $year = date("Y");
                        if ($SCHOOL_ID) {
                            $executeQuery = DB::getInstance()->update("report", $SCHOOL_ID, array(
                                'Logo' => $logo,
                                'Water_Mark' => $watermark,
                                'School_Name' => $school,
                                'School_Motto' => $motto,
                                'Address' => $address,
                                'Site' => $site,
                                'Phone' => $phone,
                                'Year' => $year
                            ), 'Report_Id');
                        } else {
                            $executeQuery = DB::getInstance()->insert("report", array(
                                'Logo' => $logo,
                                'Water_Mark' => $watermark,
                                'School_Name' => $school,
                                'School_Motto' => $motto,
                                'Address' => $address,
                                'Site' => $site,
                                'Phone' => $phone,
                                'Year' => $year
                            ));
                        }
                        if ($executeQuery) {
                            echo '<div class="alert alert-success">Settings has been successfull updated</div>';
                        } else {
                            echo '<div class="alert alert-danger">Error while updating settings</div>';
                        }
                        Redirect::go_to("");
                    }
                    if (Input::exists() && Input::get("submit_sms_settings") == 'submit_sms_settings') {
                        $settings = Input::get("settings");
                        foreach ($settings as $setting => $value) {
                            DB::getInstance()->updateSetting("$setting", $value);
                        }
                        Redirect::to("");
                    }
                    $school_data = $data = DB::getInstance()->querySample("select * FROM report LIMIT 1")[0];
                    $HM_Signature = DB::getInstance()->displayTableColumnValue("select Signature_Image from headteacher_signature", "Signature_Image");
                    ?>
                    <div class="card-body">
                        <ul class="nav nav-pills arrow-navtabs nav-success bg-light mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $general_tab_active ?>" data-toggle="tab" href="#tab-general" role="tab">
                                    <span class="d-block d-sm-none"><i class="mdi mdi-wrench"></i></span>
                                    <span class="d-none d-sm-block"> General</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $report_settings_tab_active ?>" data-toggle="tab" href="#tab-report-settings" role="tab">
                                    <span class="d-block d-sm-none"><i class="mdi mdi-home-variant"></i></span>
                                    <span class="d-none d-sm-block">Report Appearance</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $sms_tab_active ?>" data-toggle="tab" href="#tab-sms" role="tab">
                                    <span class="d-block d-sm-none"><i class="mdi mdi-message"></i></span>
                                    <span class="d-none d-sm-block"> Bulk SMS</span>
                                </a>
                            </li>
                            <?php if ($MODULE == "finance") { ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $bank_accounts_tab_active ?>" data-toggle="tab" href="#tab-bank-accounts" role="tab">
                                        <span class="d-block d-sm-none"><i class="mdi mdi-home"></i></span>
                                        <span class="d-none d-sm-block"> Bank Accounts</span>
                                    </a>
                                </li>
                            <?php }
                            if (in_array("isDeveloper", $user_permissions)) { ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $system_configurations_tab_active ?>" data-toggle="tab" href="#tab-system-configurations" role="tab">
                                        <span class="d-block d-sm-none"><i class="fa fa-gear"></i></span>
                                        <span class="d-none d-sm-block"> System</span>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content text-muted">
                            <div class="tab-pane <?php echo $general_tab_active ?>" id="tab-general">
                                <form action="" method="POST">
                                    <div class="form-group row">
                                    <div class="col-md-4">
                                            <label>Curriculum Combined <small class="text-danger">[new curriculum]</small></label>
                                            <select class="form-control" name="settings[curriculum_combined]">
                                                <option value="0">Off</option>
                                                <option value="1" <?php echo $CURRICULUM_COMBINED ? 'selected' : '' ?>>On</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Enable supplementary set <small class="text-danger">[new curriculum]</small></label>
                                            <select class="form-control" name="settings[enable_supplementary_set]">
                                                <option value="0">Off</option>
                                                <option value="1" <?php echo $ENABLE_SUPPLEMENTARY_SET ? 'selected' : '' ?>>On</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Supplementary set title</label>
                                            <input class="form-control" name="settings[supplementary_set_title]" value="<?php echo $SUPPLEMENTARY_SET_TITLE?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Supplementary set passmark</label>
                                            <input class="form-control" type="number" min="0" name="settings[supplementary_set_passmark]" value="<?php echo $SUPPLEMENTARY_SET_PASSMARK?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Max subject units</label>
                                            <input class="form-control" type="number" min="0" name="settings[maximum_subject_units]" value="<?php echo $MAXIMUM_SUBJECT_UNITS?>" max="10">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Comments on marks entry</label>
                                            <select class="form-control" name="settings[display_comments_on_marks_entry]">
                                                <option value="0">Off</option>
                                                <option value="1" <?php echo $DISPLAY_COMMENTS_ON_MARKS_ENTRY ? 'selected' : '' ?>>On</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Minimum Allowed Marks <small class="text-danger">[new curriculum]</small></label>
                                            <input type="number" step="any" class="form-control" min="0" value="<?php echo getConfigValue("minimum_allowed_new_curriculum_marks") ?>" name="settings[minimum_allowed_new_curriculum_marks]" />
                                        </div>
                                        <div class="col-md-4">
                                            <label>System Start Date</label>
                                            <input type="date" class="form-control" value="<?php echo getConfigValue("start_date") ?>" name="settings[start_date]" />
                                        </div>
                                        <div class="col-md-4">
                                            <label>Base Currency</label>
                                            <input class="form-control" value="<?php echo getConfigValue("base_currency") ?>" name="settings[base_currency]" />
                                        </div>
                                    </div>
                                    <input type="hidden" name="reroute" value="<?php echo $crypt->encode("tab=general&page=" . $_GET['page']) ?>">
                                    <input type="hidden" name="action" value="saveSystemConfigurations" />
                                    <button type="" class="btn btn-primary btn-xs">Save</button>
                                </form>
                            </div>
                            <div class="tab-pane <?php echo $report_settings_tab_active ?>" id="tab-report-settings" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-5 border border-primary p-2">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4>School Details (Report Header)</h4>
                                            </div>
                                            <div class="panel-body">
                                                <form role="form" action="" method="post" style="margin-left:2%;" enctype="multipart/form-data">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label>Attach school badge:</label>
                                                            <div id="logo_div">
                                                                <input type='file' id="logo_file" name="logo" class="form-control" accept="image/gif, image/jpeg, image/png" onchange="readURL(this);" <?php echo ($data->Logo != "") ? "" : "required"; ?> />
                                                                <img style="width:80px;" id="logo_image" src="logo/<?php echo ($data->Logo != "") ? $data->Logo : "mubs.jpg"; ?>" alt="" /><br />
                                                                Upload a clear logo .
                                                            </div>
                                                            <input type="hidden" name="old_logo" value="<?php echo $data->Logo ?>" />
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Attach Watermark:</label>
                                                            <div id="watermark_div">
                                                                <input type='file' id="watermark_file" name="watermark" class="form-control" accept="image/jpeg, image/png" onchange="readURL(this);" />
                                                                <img style="width:80px;" id="watermark_image" src="logo/<?php echo ($data->Water_Mark != "") ? $data->Water_Mark : "mubs.jpg" ?>" alt="" /><br />
                                                                Please Upload a pale image.
                                                            </div>
                                                            <input type="hidden" name="old_watermark" value="<?php echo $data->Water_Mark ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Attach HM Signature:
                                                        </label>
                                                        <div id="hmsignature_div">
                                                            <input type='file' id="hmsignature_file" name="hmsignature" accept="image/gif, image/jpeg, image/png" onchange="readURL(this);" />
                                                        </div>
                                                        <img style="width:10%;" id="hmsignature_image" src="Hmsignature/<?php echo $HM_Signature ?>" alt="" align="left" /><br /><br />
                                                        <p style="color:blue">Upload a clear and professional signature .</p>

                                                    </div>
                                                    <div class="form-group">
                                                        <label>School Name:</label>
                                                        <input type="text" class="form-control" name="school" value="<?php echo $data->School_Name; ?>" required placeholder="Enter school name">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>School Motto:</label>
                                                        <input type="text" class="form-control" name="motto" value="<?php echo $data->School_Motto; ?>" required placeholder="Enter school motto">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Address:</label>
                                                        <input type="text" class="form-control" name="address" value="<?php echo $data->Address; ?>" placeholder="hint: P.O BOX 88,BUSHENYI(UGANDA)" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Email:</label>
                                                        <input type="email" class="form-control" value="<?php echo $data->Site; ?>" name="site" placeholder="hint: yourname@example.com">
                                                    </div>
                                                    <?php
                                                    $phone1 = "";
                                                    $phone2 = "";
                                                    $phones = $data->Phone;
                                                    if ($phones != "") {
                                                        $phones_array = explode("/", $phones);
                                                        $phone1 = $phones_array[0];
                                                        $phone2 = (count($phones_array) > 1) ? $phones_array[1] : "";
                                                    }
                                                    ?>
                                                    <div class="form-group">
                                                        <label>First Phone No:</label>
                                                        <input class="form-control" type="text" name="phone1" value="<?php echo $phone1 ?>" pattern="(\+256|0)[1-9][0-9]{8}" placeholder="hint/0757243251/077243251" required />
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Second Phone No:</label>
                                                        <input class="form-control" type="text" name="phone2" value="<?php echo $phone2 ?>" pattern="(\+256|0)[1-9][0-9]{8}" placeholder="hint/0757243251/077243251" />
                                                    </div>

                                                    <div class="form-group">
                                                        <button type="submit" name="submit_settings_btn" value="submit_settings_btn" class="btn btn-primary" onclick="">submit school information</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-5 border border-primary p-2">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4>Other Settings</h4>
                                            </div>
                                            <div class="panel-body">
                                                <form action="" method="POST">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <label>Reportcard Title <small class="text-danger">(Keys: <?php echo implode(',',$REPORTCARD_TITLE_KEYS)?>)</small></label>
                                                            <input type="text" name="settings[reportcard_title]" class="form-control" value="<?php echo $REPORTCARD_TITLE?>">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>School Head Title </label>
                                                            <input type="text" name="settings[school_head_title]" class="form-control" value="<?php echo $SCHOOL_HEAD_TITLE?>">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Use headed paper</label>
                                                            <select class="form-control" name="settings[use_headed_paper]">
                                                                <option value="0">Off</option>
                                                                <option value="1" <?php echo $USE_HEADED_PAPER ? 'selected' : '' ?>>On</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Class Positioning</label>
                                                            <select class="form-control" name="settings[class_positioning]">
                                                                <option value="0">Off</option>
                                                                <option value="1" <?php echo $CLASS_POSITIONING ? 'selected' : '' ?>>On</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Stream Positioning</label>
                                                            <select class="form-control" name="settings[stream_positioning]">
                                                                <option value="0">Off</option>
                                                                <option value="1" <?php echo $STREAM_POSITIONING ? 'selected' : '' ?>>On</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label>Use detailed report card <small>[New curriculum]</small></label>
                                                            <select class="form-control" name="settings[use_detailed_reportcard]">
                                                                <option value="0">Off</option>
                                                                <option value="1" <?php echo $USE_DETAILED_REPORTCARD ? 'selected' : '' ?>>On</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label>Link Generic skills to</label>
                                                            <select class="form-control" name="settings[generic_skills_type]">
                                                                <option value="subject">Subject</option>
                                                                <option value="unit" <?php echo $GENERIC_SKILLS_TYPE == "unit" ? 'selected' : '' ?>>Unit</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Classteacher comments based on</label>
                                                            <select class="form-control" name="settings[classteacher_comment_type]">
                                                                <option value="total-range" <?php echo $CLASSTEACHER_COMMENT_TYPE == "total-range" ? 'selected' : '' ?>>Total (/100)</option>
                                                                <option value="identifier-range">Out of 3</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Headteacher comments based on</label>
                                                            <select class="form-control" name="settings[headteacher_comment_type]">
                                                                <option value="total-range" <?php echo $HEADTEACHER_COMMENT_TYPE == "total-range" ? 'selected' : '' ?>>Total (/100)</option>
                                                                <option value="identifier-range">Out of 3</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Subject comments based on</label>
                                                            <select class="form-control" name="settings[subject_comment_type]">
                                                                <option value="identifier-range">Identifier Range</option>
                                                                <option value="total-range" <?php echo $SUBJECT_COMMENT_TYPE == "total-range" ? 'selected' : '' ?>>Total (/100)</option>
                                                                <option value="manual" <?php echo $SUBJECT_COMMENT_TYPE == "manual" ? 'selected' : '' ?>>Manual</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Show subject comments</label>
                                                            <select class="form-control" name="settings[display_subject_comments]">
                                                                <option value="1">On</option>
                                                                <option value="0" <?php echo !$DISPLAY_SUBJECT_COMMENTS ? 'selected' : '' ?>>Off</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Descriptor grade based on</label>
                                                            <select class="form-control" name="settings[descriptor_grade_type]">
                                                                <option value="20">Out of 20 (converted to 3)</option>
                                                                <option value="80" <?php echo $DESCRIPTOR_GRADE_TYPE == "80" ? 'selected' : '' ?>>Out of 80 (converted to 3)</option>
                                                                <option value="100" <?php echo $DESCRIPTOR_GRADE_TYPE == "100" ? 'selected' : '' ?>>Out of 100 (converted to 3)</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>/20 column display</label>
                                                            <select class="form-control" name="settings[display_out_of_twenty_column]">
                                                                <option value="1">On</option>
                                                                <option value="0" <?php echo !$DISPLAY_OUT_OF_TWENTY_COLUMN ? 'selected' : '' ?>>Off</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label>Subject Annual Average column <small class="text-danger">(New curriculum)</small></label>
                                                            <select class="form-control" name="settings[display_subject_annual_average]">
                                                                <option value="1">On</option>
                                                                <option value="0" <?php echo !$DISPLAY_SUBJECT_ANNUAL_AVERAGE ? 'selected' : '' ?>>Off</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Show new curriculum grade key</label>
                                                            <select class="form-control" name="settings[display_new_curriculum_grade_key]">
                                                                <option value="1">On</option>
                                                                <option value="0" <?php echo !$DISPLAY_NEW_CURRICULUM_GRADE_KEY ? 'selected' : '' ?>>Off</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label>Fees Balance on Reportcards</label>
                                                            <select class="form-control" name="settings[display_fees_balance_on_reportcard]">
                                                                <option value="0">Off</option>
                                                                <option value="1" <?php echo $DISPLAY_FEES_BALANCE_ON_REPORTCARD ? 'selected' : '' ?>>On</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <hr />
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label>Subject Positioning</label><br />
                                                            <label><input type="radio" name="subject_positioning" <?php echo ($data->Subject_Positioning == 0) ? 'checked' : '' ?> value="0" required> Off</label>
                                                            &nbsp;&nbsp;&nbsp;<label><input type="radio" name="subject_positioning" <?php echo ($data->Subject_Positioning == 1) ? 'checked' : '' ?> value="1" required> On</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Student Position based on</label><br />
                                                            <label><input type="radio" name="student_positioning" <?php echo ($data->Student_Positioning == 'Totals') ? 'checked' : '' ?> value="Totals" required> Totals</label>
                                                            &nbsp;&nbsp;&nbsp;<label><input type="radio" name="student_positioning" <?php echo ($data->Student_Positioning == 'Aggregates') ? 'checked' : '' ?> value="Aggregates" required> Aggregates</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Mode of School (Male or Female)</label>
                                                            <select class="select2" name="gender[]" data-placeholder="Select all applicable" multiple style="width:100%" required>
                                                                <option value="Male" <?php echo ($data->Gender_Offered && in_array("Male", unserialize($data->Gender_Offered))) ? 'selected' : '' ?>>Male</option>
                                                                <option value="Female" <?php echo ($data->Gender_Offered && in_array("Female", unserialize($data->Gender_Offered))) ? 'selected' : '' ?>>Female</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Schooling type (Day or Boaders)</label>
                                                            <select class="select2" name="schooling_type[]" data-placeholder="Select all applicable" multiple style="width:100%" required>
                                                                <option value="Day Scholar" <?php echo ($data->Schooling_Type && in_array("Day Scholar", unserialize($data->Schooling_Type))) ? 'selected' : '' ?>>Day Scholar</option>
                                                                <option value="Boarder" <?php echo ($data->Schooling_Type && in_array("Boarder", unserialize($data->Schooling_Type))) ? 'selected' : '' ?>>Boarder</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Motto Position on report</label>
                                                            <select class="select2" name="motto_position[]" data-placeholder="Select all applicable" multiple style="width:100%" required>
                                                                <option value="Top" <?php echo ($data->Motto_Position && in_array("Top", unserialize($data->Motto_Position))) ? 'selected' : '' ?>>Top</option>
                                                                <option value="Bottom" <?php echo ($data->Motto_Position && in_array("Bottom", unserialize($data->Motto_Position))) ? 'selected' : '' ?>>Bottom</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>General Rounding Off <small class="text-danger">(Decimal Places)</small></label>
                                                            <input type="hidden" name="marks_rounded_to_old" value="<?php echo $data->Marks_Rounded_To ?>">
                                                            <input type="number" class="form-control" name="marks_rounded_to" min="0" max="3" value="<?php echo $data->Marks_Rounded_To ?>" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Activities Decimal Places <small class="text-danger">(New curriculum)</small></label>
                                                            <input type="number" class="form-control" name="settings[activities_decimal_places]" min="0" max="3" value="<?php echo $ACTIVITIES_DECIMAL_PLACES ?>" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Education Levels</label>
                                                            <select class="select2" name="education_levels[]" id="levels_id" data-placeholder="Select all applicable" multiple style="width:100%" required onchange="filterClasses(this)">
                                                                <option value="OLEVEL" <?php echo ($data->Education_Levels && in_array("OLEVEL", unserialize($data->Education_Levels))) ? 'selected' : '' ?>>O'LEVEL</option>
                                                                <option value="ALEVEL" <?php echo ($data->Education_Levels && in_array("ALEVEL", unserialize($data->Education_Levels))) ? 'selected' : '' ?>>A'LEVEL</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Display House Master's comment</label><br />
                                                            <label><input type="radio" name="display_house_master_comment" <?php echo ($data->Display_House_Master_Comment == 1) ? 'checked' : '' ?> value="1" required> Yes</label>
                                                            &nbsp;&nbsp;&nbsp;<label><input type="radio" name="display_house_master_comment" <?php echo ($data->Display_House_Master_Comment == 0) ? 'checked' : '' ?> value="0" required> No</label>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label>Display Bursar's comment</label><br />
                                                            <label><input type="radio" name="display_bursar_comment" <?php echo ($data->Display_Bursar_Comment == 1) ? 'checked' : '' ?> value="1" required> Yes</label>
                                                            &nbsp;&nbsp;&nbsp;<label><input type="radio" name="display_bursar_comment" <?php echo ($data->Display_Bursar_Comment == 0) ? 'checked' : '' ?> value="0" required> No</label>
                                                        </div>
                                                    </div>
                                                    <h4>Promotion configurations</h4>
                                                    <table class="table table-bordered">
                                                        <tr>
                                                            <th>Class</th>
                                                            <th>Based on</th>
                                                        </tr>
                                                        <tbody id="level_settings_id">
                                                            <?php
                                                            if ($data->Promotion_Setting) {
                                                                $promotion_setting = unserialize($data->Promotion_Setting);
                                                                for ($i = 0; $i < count($promotion_setting['class']); $i++) {
                                                            ?>
                                                                    <tr>
                                                                        <td><input class="form-control" readonly name="class_name[]" value="<?php echo $promotion_setting['class'][$i] ?>"></td>
                                                                        <td>
                                                                            <?php
                                                                            if ($promotion_setting['class'][$i] == 'S5') {
                                                                                echo '<input class="form-control" name="promoted_on[]" readonly value="' . $promotion_setting['setting'][$i] . '">';
                                                                            } else {
                                                                            ?>
                                                                                <select class="form-control" name="promoted_on[]">
                                                                                    <option value="Average" <?php echo ($promotion_setting['setting'][$i] == "Average") ? ' selected' : '' ?>>Average</option>
                                                                                    <option value="Aggregates" <?php echo ($promotion_setting['setting'][$i] == "Aggregates") ? ' selected' : '' ?>>Aggregates</option>
                                                                                </select>
                                                                            <?php } ?>
                                                                        </td>
                                                                    </tr>
                                                            <?php }
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                    <button class="btn btn-primary" name="other_settings_btn" value="other_settings_btn">Save Changes</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane <?php echo $sms_tab_active ?>" id="tab-sms" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <h4>Bulk SMS Settings</h4>
                                            </div>
                                            <div class="panel-body">
                                                <form role="form" action="" method="post">
                                                    <div class="row form-group">
                                                        <div class="col-md-6">
                                                            <label for="sms_provider">SMS Provider</label>
                                                            <select class="form-control" id="sms_provider" name="settings[sms_provider]">
                                                                <?php
                                                                foreach ($SMS_SERVICE_PROVIDERS_LIST as $provider) {
                                                                    $selected = $provider == $SMS_SERVICE_PROVIDER ? ' selected' : '';
                                                                    echo "<option value='$provider' $selected>$provider</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label>Username</label>
                                                            <input class="form-control" type="text" name="settings[sms_username]" value="<?php echo getConfigValue("sms_username") ?>">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="sms_password">Password <small style="color:red">Only for EGO SMS and Pandora SMS</small></label>
                                                            <input class="form-control" id="sms_password" type="password" name="settings[sms_password]" value="<?php echo getConfigValue("sms_password") ?>">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="sms_api_id">API Key <small style="color:red">Only for AfricasTalking</small></label>
                                                            <input class="form-control" id="sms_api_id" type="text" name="settings[sms_api_key]" value="<?php echo getConfigValue("sms_api_key") ?>">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="sms_from">From Name/Number</label>
                                                            <input class="form-control" id="sms_from" type="text" name="settings[sms_from]" value="<?php echo getConfigValue("sms_from") ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit" name="submit_sms_settings" value="submit_sms_settings" class="btn btn-primary">Save</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if ($MODULE == "finance") { ?>
                                <div class="tab-pane <?php echo $bank_accounts_tab_active ?>" id="tab-bank-accounts">
                                    <div class="p-2">
                                        <a onClick='showModal("index.php?modal=settings/edit-bank-account<?php echo "&reroute=" . $crypt->encode('tab=bank-accounts&page=' . $_GET['page']) ?>");return false' class="btn btn-xs btn-primary">new account number</a>
                                    </div>
                                    <?php
                                    $accounts = DB::getInstance()->querySample("SELECT * FROM bank_account ORDER BY `Bank`,`Name`");
                                    if ($accounts) { ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered" id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Account</th>
                                                        <th>Account Number</th>
                                                        <th>Bank Name</th>
                                                        <th>Total Transactions</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($accounts as $account) { ?>
                                                        <tr>
                                                            <td><?php echo $account->Name ?></td>
                                                            <td><?php echo $account->Account_Number ?></td>
                                                            <td><?php echo $account->Bank ?></td>
                                                            <td></td>
                                                            <td>
                                                                <a onClick='showModal("index.php?modal=settings/edit-bank-account<?php echo "&id=$account->Id&reroute=" . $crypt->encode('tab=bank-accounts&page=' . $_GET['page']) ?>");return false' class="btn btn-xs btn-primary">edit</a>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php } else {
                                        echo '<div class="alert alert-danger">No bank accounts registered</div>';
                                    } ?>
                                </div>
                            <?php }
                            if (in_array("isDeveloper", $user_permissions)) { ?>
                                <div class="tab-pane <?php echo $system_configurations_tab_active ?>" id="tab-system-configurations">
                                    <form action="" method="POST">
                                        <div class="form-group">
                                            <label>System Expiry Date</label>
                                            <input type="date" class="form-control" value="<?php echo $crypt->decode(getConfigValue("expiry_date")) ?>" name="settings[expiry_date]" />
                                        </div>
                                        <label>Subscribed Modules</label>
                                        <div class="form-group">
                                            <select class="form-control select2" name="modules[]" multiple>
                                                <?php foreach ($ALL_MODULES_LIST as $i => $module) {
                                                    $selected = in_array($i, $MODULES) ? ' selected' : '';
                                                    echo "<option value='$i' $selected>$module</option>";
                                                } ?>
                                            </select>
                                        </div>
                                        <br />
                                        <input type="hidden" name="reroute" value="<?php echo $crypt->encode("tab=system&page=" . $_GET['page']) ?>">
                                        <input type="hidden" name="action" value="saveSystemConfigurations" />
                                        <button type="" class="btn btn-primary btn-xs">Save</button>
                                    </form>
                                </div>
                            <?php } ?>
                        </div>
                    </div><!-- end card-body -->
                </div>
            </div>
        </div>

    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->

<?php require_once 'inc/footer.php' ?>

<script type="text/javascript">
    function readURL(input) {
        var name = input.name;
        var fsize = $('#' + input.id)[0].files[0].size;
        if (fsize > 200000 && name !== 'watermark') {
            alert('The image size is very big\nPlease select another image');
            //document.getElementById('#i_file').value='';
            $('#' + name + '_div').html('<input type="file" class="form-control" id="' + input.id + '" name="' + name + '" accept="image/jpeg, image/png" onchange="readURL(this);"/><img style="width:60px;"id="' + name + '_image" src="logo/mubs.jpg" alt=""/><br/><p style="color:blue">Upload a clear file.</p>');
        } else {
            if (input.files && input.files[0]) {

                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#' + name + '_image')
                        .attr('src', e.target.result)
                        .width(50)
                        .height(50);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
    }

    function filterClasses(levelsElement) {
        var olevel_classes = new Array('S1', 'S2', 'S3'),
            olevelData = "",
            aLevelData = "";
        aLevelData += '<tr><td><input class="form-control" readonly name="class_name[]" value="S5"></td><td><input class="form-control" readonly name="promoted_on[]" value="Points"></td></tr>';
        for (var i = 0; i < olevel_classes.length; i++) {
            olevelData += '<tr><td><input class="form-control" readonly name="class_name[]" value="' + olevel_classes[i] + '"></td><td><select class="form-control" name="promoted_on[]"><option value="Average">Average</option><option value="Aggregates">Aggregates</option></select></td></tr>';
        }
        var selectedValues = new Array();
        for (var i = 0; i < levelsElement.options.length; i++) {
            if (levelsElement.getElementsByTagName('option')[i].selected) {
                selectedValues.push(levelsElement.options[i].value);
            }
            //levelsElement.getElementsByTagName('option')[i].selected = true;
            //use var.includes(item)
        }
        olevelData = (selectedValues.includes('OLEVEL')) ? olevelData : '';
        aLevelData = (selectedValues.includes('ALEVEL')) ? aLevelData : '';
        $("#level_settings_id").html(olevelData + aLevelData);
    }
</script>
