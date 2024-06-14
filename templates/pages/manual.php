<!DOCTYPE html>
<html>

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>CSMS MANUAL</title>

        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <!--<link href="css/sb-admin.css" rel="stylesheet">-->

        <!-- Morris Charts CSS -->
        <link href="css/plugins/morris.css" rel="stylesheet">

        <!-- Custom Fonts -->
        <link href="assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

        <style type="text/css">


            h2 {
                font-family: 'Yanone Kaffeesatz', sans-serif;
                font-size:30px;
                text-shadow: 0 0px 20px rgba(0, 0, 0, 0.3);
                color:#fff;
            }
            .move_top{
                text-align: center;
                width: 50px;
                height: 50px;
                font-size: 2em;
                background-color: blue;
                border-radius: 50%;
                position: fixed;
                bottom: 0;
                right: 10px;
                color: white;
            }
            @media (max-height: 700px) {
                .sticky {
                    position: relative;
                }
            }
            .monthly {
                width:100%;
                border: 1px solid #F3F3F5;
            }
            ul li,li{
                list-style: none 
            }
            ol li{
                list-style: decimal;
            }

        </style>
        <link rel="stylesheet" href="css/monthly.css">

    </head>
    <body>
        <?php $page = $_GET['page']; ?>
        <h1 id="top" style="text-shadow:2px 2px 4px white;background-color:#3300FF;text-align:center;">CSMS:CUSTOMISABLE SCHOOL MANAGEMENT SYSTEM <br> USER GUIDE</h1>
        <h4> <a href="<?php echo $_SESSION['previous_page']?>"><i class="fa fa-fw fa-backward"></i> Back</a>
            <a href="#middle"><i class="fa fa-fw fa-dashboard"></i> middle</a>
            <a  href="#bottom"><i class="fa fa-fw fa-meh-o"></i> bottom</a> 
        </h4>
        <p style="color:#EEEFEF; text-shadow:3px 4px 5px red;text-align:center;background-color:#009900"> What makes CSMS unique?? </p>
        <div class="row">
            <div class="col-lg-12 " style="text-align:center;">
                CSMS stands for "CUSTOMISABLE SCHOOL MANAGEMENT SYSTEM".<br/>
                This is a software designed with the ability to operate basing on users' settings and interest.It can be changed at any as time deemed necessary .</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 " >
                <h2 style="color:red ;text-shadow:2px 2px 4px #009900; text-align:center;">Frequently asked questions</h2>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <?php
                if ((isset($_SESSION['User_Type']) && ($_SESSION['User_Type'] == "TEACHER" || $_SESSION['User_Type'] == "BURSAR")) && !isset($_SESSION['immergencepassword'])) {
                    
                } else {
                    if ($_SESSION['User_Type'] != "DOS" || !isset($_SESSION['User_Type'])) {
                        ?>
                        <li>
                            <a href="#report_setting" style="color:orange" ><i class="fa fa-fw fa-gear"></i>Report Settings </a>
                            <ol>
                                <li>
                                    <a href="#report_setting">How do I register the school?</a>
                                </li>
                                <li>
                                    <a href="#hm_signiture">How do I upload Head teacher's signature?</a>
                                </li> 

                                <li>
                                    <a href="#exam_sets">How do I register Exam sets  done at our school?</a>
                                </li> 
                                <li>
                                    <a href="#exam_sets">How do I register exam sets to be done in the current term eg BOT, MOT & EOT ?</a>
                                </li>  
                                <li>
                                    <a href="#term_setting">How do I set reporting date for the next term?</a>
                                </li> 

                                <li>
                                    <a href="#promotion_settings">How do I stop other users from promoting students?</a>
                                </li> 
                            </ol>
                        </li>

                        <li>
                            <a href="#user_account" style="color:orange" ><i class="fa fa-fw fa-user"></i>Account </a>
                            <ol >
                                <li>
                                    <a href="#user_account">How do I login ?</a>
                                </li>
                                <li>
                                    <a href="#user_account">How do I give other users rights to access this system (login)?</a>
                                </li>
                                <li>
                                    <a href="#user_account">How do I edit my account?</a>
                                </li>

                            </ol>
                        </li>
                    <?php } ?>
                    <li>
                        <a href="#class" style="color:orange" ><i class="fa fa-fw fa-home"></i>Class </a>
                        <ol >
                            <li>
                                <a href="#class">How do I register classes?</a>
                            </li>
                            <li>
                                <a href="#stream">How do I register streams?</a>
                            </li>

                        </ol>
                    </li>
                    <li>
                        <a href="#subject" style="color:orange" ><i class="fa fa-fw fa-edit"></i>Subjects </a>
                        <ol >
                            <li>
                                <a href="#subject">How do I register Subjects?</a>
                            </li>
                            <li>
                                <a href="#paper">How do I register papers?</a>
                            </li>

                        </ol>
                    </li>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">

                    <li>
                        <a href="#student" style="color:orange" ><i class="fa fa-fw fa-users"></i>Students </a>
                        <ol >
                            <li>
                                <a href="#student">How do I register students?</a>
                            </li>
                            <li>
                                <a href="#excel_students">How can I export already registered students in excel?</a>
                            </li>
                            <li>
                                <a href="#student_photoes">How do I upload students' photoes?</a>
                            </li>
                            <li>
                                <a href="#student">How do I upload students' signitures?</a>
                            </li>
                            <li>
                                <a href="#student_subject">How do I assign optional subjects to students?</a>
                            </li>

                        </ol>
                    </li>

                    <li>
                        <a href="#staff" style="color:orange" ><i class="fa fa-fw fa-book"></i>Staff </a>
                        <ol >
                            <li>
                                <a href="#staff">How do I register a staff?</a>
                            </li>
                            <li>
                                <a href="#teacher_subject">How do I assign subject and class to a teacher ?</a>
                            </li>

                        </ol>
                    </li>   

                    <li>
                        <a href="#grading" style="color:orange" ><i class="fa fa-fw fa-user"></i>Grading </a>
                        <ol >
                            <li>
                                <a href="#sets_grading_scale">How do I set the pass mark (bot/30,mot/20,eot/50)?</a>
                            </li>
                            <li>
                                <a href="#marks_grading">How do I set or change grading ?</a>
                            </li>

                        </ol>
                    </li>

                <?php } ?>
                <?php if ((isset($_SESSION['User_Type']) && ($_SESSION['User_Type'] != "BURSAR")) || isset($_SESSION['immergencepassword']) || !isset($_SESSION['User_Type'])) { ?>
                    <li>
                        <a href="#marks" style="color:orange" ><i class="fa fa-fw fa-user"></i>Marks </a>
                        <ol >
                            <li>
                                <a href="#enter_marks">where do I enter marks from?</a>
                            </li>
                            <li>
                                <!--<a href="#edit_marks">how do i edit marks?</a>-->
                                <a class="active" href="#edit_marks">how do I edit marks </a>
                            </li>
                            <li>
                                <a href="#delete_marks">How can I delete marks?</a>
                            </li>
                            <li>
                                <a href="#convert_marks">How can I convert marks?</a>
                            </li>

                            <li>
                                <a href="#combine_two">How can I combine two sets of marks to make it one set?</a>
                            </li>

                        </ol>
                    </li><?php } ?>
            </div>
            <?php if (($_SESSION['User_Type'] != "TEACHER" && $_SESSION['User_Type'] != "BURSAR") || !isset($_SESSION['User_Type'])) { ?>
                <div class="col-lg-4 col-md-6 col-sm-6">

                    <li>
                        <a href="#marksheets" style="color:orange" ><i class="fa fa-fw fa-table"></i>Reports marksheets and performance </a>
                        <ol >
                            <li>
                                <a href="#marksheets">How do I print the marksheets?</a>
                            </li>
                            <li>
                                <a href="#reports">How do I print students academic reports ?</a>
                            </li>
                            <li>
                                <a href="#performance">How can I view the best performing students ?</a>
                            </li>

                        </ol>
                    </li>

                    <li>
                        <a href="#promotion_settings" style="color:orange" ><i class="fa fa-fw fa-car"></i>Promotion settings </a>
                        <ol >
                            <li>
                                <a href="#View_student_performance">How do I see the performance of students if i want to promote them according to thier performance?</a>
                            </li>
                            <li>
                                <a href="#promote_students">How do I promote students ?</a>
                            </li>
                            <li>
                                <a href="#demote_students">How do I  demote students that i have promoted by a mistake  ?</a>
                            </li>

                        </ol>
                    </li>


                </div>
            <?php } ?>

        </div>
        <h2 style="color:black;text-align:center;background-color:blue;">FEATURES</h2>
        <div class="container">
            <h3 style="color:blue">DASHBOARD</h3>
            <p>This displays the menu of the system and statistics overview or standings of the system.<br/>
                The system overview has a summary of the classes and streams,subjects,students and staff.Details of these can be viewed by clicking on the <b>view details</b> link below each button.</p>
            <h3><b>System Backups</b></h3>
            <p>This allows you to download the system databse and save it.This can be helpful in restoring data in case the system crashes.<br>
                To backup the databse,click on Back up now and the database will appear below the <b>Back up now</b> button.<br/>
                Download the backed up database can be dowloaded by clicking on the Download link and then saved on secure storage medium like an external storage device or preferably online.<br>
                When multiple databases are showing after multiple backups,you can use the <b>DELETE</b> button tou delete and reduce them to one.<br>
                There also is a bar chart which shows the number of students per class<br>
                <b>System logs</b>:These keep track of users who login and operations they make while logged in.
            </p>
            <h2 id="middle"style="color:blue">MENU</h2>
            <p>The Menu on the right side of the Home page has various links performing different actions</p>
            <h3 id="report_setting">Report settings</h3>

            <p>Under report settings,there are also sub linksdetailed below that help in setting the appearance of the report.</p>
            <h4 id="" style="color:blue">Upload principal's signature</h4>
            <p>This feature lets you upload the Principal/Head Teacher's signature that will appear on the student reports.
                The Principal/Head teacher will then not be required to sign every student's report since their signature will automatically be appended to all student reports.<br/>
                Follow the following steps to upload it.<br/>
                <a id="hm_signiture">click on upload principal's signature</a>
                Attach principal's signature
                Click on choose file and select the signature file from where you previously saved it and then click open.
                Click Submit principal's signature<br/>
            </p>
            <h4>Report appearance</h4>
            <p>This defines the general information to be printed on the report i.e School name/address and the corresponding details.Make sure you provide correct information.<br/>
                Click on choose file to upload school badge,select the badge from a file directory and then click open<br/>
                Provide the school information in by filing the corresponding fields<br/>
                Click  submit school information
            </p>
            <h4> <a href="<?php echo $_SESSION['previous_page']?>"><i class="fa fa-fw fa-backward"></i> Back</a>
                <a href="#top"><i class="fa fa-fw fa-dashboard"></i> top</a>
                <a  href="#bottom"><i class="fa fa-fw fa-meh-o"></i> bottom</a> 
            </h4>
            <h4 id="term_setting">Beginning of term</h4> 
            <p>This feature helps in setting time frame for the following term.i.e when the term will begin and when it will end</br/>
                Select class(es).Note that classes with with the same reporting dateS can be set collectively by selecting different classes one at a time.<br/>
                Select the current term,provide the Beginning and End of term respectively and then click Submit<br/>
                The set dates for each class are displayed on the right side of the page.<br/>
            </p>
            <h4 id="exam_sets">Exam sets</h4 >
            <p>
                Under this setting,you can register Exam sets done e.g BOT,MOT,EOT,MOCK etc.Note that the Exam set must not be greater than six characters<br/>
                The registered sets are displayed on the right side of the page,these can be edited by clicking ont the edit icon which gives you a provision to edit and save changes otherwise close to discard changes.
            </p>
            <h4 id="marks_grading">Exam format setting</h4>
            <p>This setting helps you specify the exam sets that will be done by a particular class for a given term.<br/>
                On the entry form,select the class,term and then sets to be done are specified by checking on a check box beside a set you want
                to specify and then click submit.<br/>
                The registered sets with their corresponding classes are shown on the right side of the page.</p>
            <h4 id="sets_grading_scale">Exam set grading scale</h4 >
            <p>This lets you set the percentage each set of exam contributes to make 100% at the end of term.<br/>
                Select term,class and the percentage for each set.Sets previously defined for that class and term are displayed and the percentage set for all must be totaling to 100%<br>
            </p>
            <h3 style="color:blue" id="user_account">ACCOUNT</h3> <br>
            The Account feature adds users by creating Accounts,you can also edit already created accounts.<br>
            <h4>Add Account</h4><br>
            <p>This is where you can add a user by creating user name and password.<br/>
                After clicking on Add Account,on the Account Registration form,you will select a staff member for whom an account is to be created i.e a staff member who has already been registered.<br>
                Provide a user name and then select a user Type.N:B.User_Types have different privileges.<br>
                Provide and confirm password and the click Assign Account Remember all fields must be filled before clicking the Assign account button
                On the same page is a list of users previously added with a Deactivate/Activate
                button beside each user.The button Deactivates and activates users respectively and the user loss access to the system once deactivated.<br> and can regains access when Re-activated.
            </p>
            <h4>Edit Account</h4>
            This feature allows you edit a user account by changing password.You are required to enter the user name ,old password,give and confirm a new password in their respective fields and then click Edit Account.<br>
            <h3 style="color:blue" id="class">CLASS</h3> <br>
            This feature gives you a provision to Register classes and view registred classes,Register streams and view registered streams<br>
            <h4>Register class</h4><br>
            Click on Class,Register class,In the class  name fied provided,enter class in digits which must be in a range of 1-6 and then register class.<br>
            The registered classes are registered classes are then listed below with their respective levels and the maximum subjects done registered for that class.<br>
            Subjects are listed with an edit button that allows you edit already registered subjects.<br>
            Click on edit and a window from where you can add or delete a subject will be displayed.
            <h4>Registered classes</h4><br>
            To view registered classes,click on Class and then click registered classes.The registered classes will be displayed with an edit button from where you can edit subjects already registered for that class.<br>
            <h4 id="stream">Register Stream</h4>
            To register a stream,click on Class, Click on Register stream<br>
            In the Stream name that displays,enter a stream name.<br>
            From the class button,select the class for which the stream is to be assigned.<br>
            Click Register stream to save the stream<br>
            To edit a stream,click on the edit buttom beside the stream you want to edit,enter stream name and select class from the window that displays and then click update to save the changes.<br>
            <h4>Registered Stream</h4><br>
            To view registred streams,click on class,click registred streams.<br>
            A list of registred streams will then display.The streams can also be edited using the edit button.<br>
            Click on the edit button,enter a strem name and select rhe class for which the stream is to be assigned.<br>
            Click update to save the changes.
            <h3 style="color:blue" id="subject">SUBJECT</h3> <br>
            Under the subjet link,you can register subjects,view registred subjects and register subject papers.<br>
            <h4>Register subject</h4>
            To register a subject,click on Suject<br>
            Click register subject.<br>
            Enter subject name and subject code in their respective fields.<br>
            Select the level to which the subject ou are registering lies.<br>
            Click submit to register subject.
            <b>N:B: You can enter more than one subject at a time by clicking the </b>add subject button which adds more subject buttons.<br>
            <h4>Registered subject</h4>
            To view registered  subjects,click on Subject and then click registred subjects.<br>
            A list of registred subjects will then be displayed.<br>
            Click on <b>OLEVEL</b> for O'level subjects and <b>ALEVEL</b> for A'level subjects.<br>
            The show button allows you to specify the number of subjects you want to display at a time and can be selected from the show drop down button.<br>
            <h4 style="color:blue" id="paper">Edit Papers</h4>
            Click Subject<br>
            Click Register papers.<br>
            Select subject from the subject drop down and select paper from the paper drop down button.<br>
            Click submit to register paper.<br>
            The registered papers are listed on the right hand side of the page with their respective papers  
            <br>Registered papers can be deleted by clicking the red icon against the paper nad then confirm the acton by clicking <b>ok</b>
            <h3 id="student"style="color:blue">STUDENTS</h3>
            <h4 id="register_students">Register Students</h4>
            Click Students from the menu<br>
            Click Register students<br>
            On the Student Registration page that displays,Click the <b>upload photo</b> button to upload a student photo.<br>
            You will the br redirected to a file directory from where you can select and open the folder in which you previously saved the student photos.<br>
            Select the the photo you want to upload and click open.<br>
            Enter Student First name and Last name by clicking and typing  in the First name and last name fields respectively.<br>
            Select the student's Gender,date of birth,class and stream from their respective drop down fields<br>
            Click Register student to save the given information.<br>
            Registered students show on the same page with a<b>show</b>from where you can choose the number of row you want displayed at at time.<br>
            A <b>Search</b> button from where you can search a given student.To search for a student,click in the seach field and type the student name you want to search.<br>
            <h4>Edit/Delete student informationt</h4>
            Click Students from the Menu<br>
            Click registered students which will display a page showing registered students<br>
            Click on the choose button and select a year in which you want to edit a student<br>
            Select the class to which a student you want to edit belongs<br>
            Click in the search field on top of the list and type the student name you want to edit which will then appear <br>
            To <b>Edit</b> a student,CLick edit <b>a  blue icon</b> from the option column<br>
            A small window with that student's information will show up,you can then click make the required changes and click <b> save changes/Close</b> to save and discard changes respectively.<br>
            To<b>Delete</b> a student,click the delete(<b>red icon</b>) in the option column<br>
            specify a deleting reason from the small window that displays.The reason is either <b>Duplicate</b>for students appearing more than once,<b>Left school</b> for students who left the school or <b>Others</b> for any other reasons.<br>
            Click close or save changes to discard and save changes respectively.<br>
            <h4 id="excel_students"><b>Import students from excel</b></h4>
            This feature allows you transfer data you already typed in excel to the system.<br>
            To import students onto the system,follow the following steps.
            Click students from the menu<br/>
            Click import students from excel and From the upload page,Click and select class from the class button.<br/>
            Click and select stream from the stream button. Click the <b>choose file</b> button and browse through the file directory to select the excel file you want to upload and then click open.
            Click the <b>register student</b> button to save the uploaded students.

            <h4 id="student_photoes"><b>Upload Student photos</b></h4>
            Follow the following steps to upload student photos.<br>
            Click students from the menu, Click upload student photos.<br/> 
            From the student photos page,click on the class select button and select a class for which you want to upload photos.<br/>
            Click on the stream select button and select a stream for you wnt to upload photos
            Click on the search students button and a form containing students from the class you selected will be dispalyed
            From the upload photo column,click the <b>choose photo</b> and then browse to the folder where you previously saved the student photos<br/>
            click on and open the folder,select the photo for that student whose photo you are uploading and then click open. <br/>
            Repeat the two previous steps for all the students.<br/>          
            Small thumbnails of the uploaded photos will  be showing in the photo's column<br/>
            You can then click the <b>submit student photos</b> button at the botom of the page to save the uploaded photos. <br/>
            <h4>view registered students</h4> 
            Click students from the menu<br/>
            Click view registered students<br/>
            On the Registered students' page,click on the year <b>choose</b> button and select the year to want to show<br/>
            Select the class you want to view from a list of classes <br/>
            Click on the show button and choose by selecting th enumber of students yo want dispalyed at a time.<br/>
            From the search button on the right side of the page,you can type a name of the student you want to search.The name you type automatically shows up<br/>
            Upload students signature<br/>
            <h4 style="color:blue" id="student_subject"><b>Assigning subjects to students</b></h4>
            <h4 style="color:blue"  id="staff"><b>Staff Registration</b></h4>
            <h4 style="color:blue" id="teacher_subject"><b>Assigning subjects to teachers</b></h4>

            <h4 style="color:blue" id="promotion_settings"><b>PROMOTION SETTINGS</b></h4> <br>
            Under promotion settings,you can set pass mark for students,view student performance and promote as well as demote students.Student promotion 
            can be activated and deactivated at any time and this can be done under promotion settings.
            From the Menu,click Promotion setings.Click on any of the links that show below promotion setting depending on what you want to do.
            <h4 style="color:blue" id="View_student_performance">View student performance</h4> <br>
            Click promotion settings and on a table showing general student performance will be dispalyed on the page that shows up.
            The table shows a class and the percentage of students who passed and those who  faileld according to the pass mark entered.
            <h4 style="color:blue" id="promote_students">Promote Students</h4> <br>
            To promote students,Click Promotion settings and then promote students under the Menu.Click and select the year,class and stream for which students you want to promote.Click search students
            <br> Students in that class will be dispalyed with their respective scores.<br>
            Click the <b>Promote students</b> button and you will be required to select a new strem for the promoted students.<br>
            Select the stream and click save changes to promote students.You can click close in case you want to discard the changes.
            <br>Note that students who are below the pass mark have an option of being  promoted on probation.This can be done by clicking 
            the Promote on probation button beside the student you want to promote.
            <h4 style="color:blue" id="demote_students">Demote students</h4> <br>
            To deomote students,Click Promotion settings and then demote students under the Menu.Click and select the year,class and stream for which students you want to deomote.Click search students
            <br> Students in that class will be dispalyed with an action column where you can check/click beside the student(s) you want to demote.<br>
            Click the <b>deomote students</b> button and you will be required to select a new strem for the deomoted students and select the reason for demotion.<br>
            click save changes to demote students.You can click close in case you want to discardd the changes.

            <h3 style="color:blue" id="enter_marks">MARKS</h3>
            From the Menu,click Marks,Click marks entry form.<br>
            Select class,select stream and the subject for which you want to enter marks.Select the current term and click search students.<br/>
            N:B:It is important to note that unless you are an Administrator,a teacher is allowed to enter marks for only the subjects they are assigned to teach in specific classes.<br>
            A list of students in the selected class will then be displayed with marks fields next to each in which you can enter their respective marks.    
            N:B.On the marks entry form,marks and any changes(editing or deleting) are automatically saved as you enter

            <h4 style="color:blue"class="active"id="edit_marks">Editing marks</h4>
            Marks are edited from the same page they are entered.,click <b>Marks</b> from the Menu,Click marks entry form.<br>
            Select class,select stream and the subject for which marks you want to edit.Select the current term and click search students.<br/>
            Click in the marks field you want to edit,use <b>Back space</b> keyboard button to delete the marks and enter a new mark which is automatically saved as you enter.<br>

            <h4 style="color:blue" id="delete_marks">Deleting marks</h4>
            Marks are edited from the same page they are entered.Click <b>Marks</b> from the Menu,Click marks entry form.<br>
            Select class,select stream and the subject for which marks you want to delete.Select the current term and click search students.<br/>
            Click in the marks field and use the keyboard <b>Back space</b> button to delete marks.The changes are automatically saved.<br>
            <h4 id="convert_marks">To be converted</h4>
            This option lets you enter marks that are not converted to the set scale so the system can convert them.<br>
            What you do is check the <b>to be converted</b> option on the marks entry form,provide the mark for which the exam  marked out of in the <b>marked out of</b> field<br>
            You can then enter your marks in the fields provided against each student and the system will automatically
            convert them to the set scale.<br>
            <h4 id="combine_two"><b>Combine 2</b></h4>
            This allows you enter two sets of an exam that will then be combined and converted to the set mark.The marks entered must be in percentage and are entered in the fields next to each student's name on the marks entry form. 

            <h4 style="color:blue" id="marksheets">Reports and Marksheets</h4>
            <h4 id="reports">Student Mark sheets</h4>
            To view student mark sheets,click Reports and mark sheets under the Menu,click student mark sheets.
            You will then be required to select the class,stream,term,year and exam set of your choice.<br>
            Click search students<br>
            The general mark sheet for the selected class will be dispalyed with provisions to generate general mark sheets,general grade mark sheets  and generate reports.<br>
            You can click on their respective buttons to generate any of them.<br>
            <h4>Print Reports</h4>
            To generate individual reports,click the Generate button beside a student for whom you want to generate a report.
            <h4 style="color:blue" id="performance">General Performance</h4>
            To view student's general performance,click Reports and mark sheets fron thhe Menu and then click general performance.
            <br> Select the class,term and year you want to view and click search students.<br>
            You can print the performance form by clicking the print form button.
            <h4> <a href="<?php echo $_SESSION['previous_page']?>"><i class="fa fa-fw fa-backward"></i> Back</a>
                <a href="#middle" id="bottom"><i class="fa fa-fw fa-dashboard"></i> middle</a>
                <a  href="#top"><i class="fa fa-fw fa-meh-o"></i> top</a> 
            </h4>
        </div>

        <a href="#top" class="move_top"><i class="fa fa-arrow-up"></i></a>
        <script src="js/jquery.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="js/bootstrap.min.js"></script>

        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/monthly.js"></script>


        <script type="text/javascript">
            $(window).load(function () {

                $('#mycalendar').monthly({
                    mode: 'event',
                    xmlUrl: 'events.xml'
                });

                $('#mycalendar2').monthly({
                    mode: 'picker',
                    target: '#mytarget',
                    setWidth: '250px',
                    startHidden: true,
                    showTrigger: '#mytarget',
                    stylePast: true,
                    disablePast: true
                });

                switch (window.location.protocol) {
                    case 'http:':
                    case 'https:':
                        // running on a server, should be good.
                        break;
                    case 'file:':
                    //alert('Just a heads-up, events will not work when run locally.');
                }

            });
        </script>


    </body>
</html>