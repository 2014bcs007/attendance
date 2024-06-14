<div class="row">
        <div class="col-md-4">
            <table class="table table-striped">
                <tbody>
                    <?php
                    $hiddenColumnsArray = array("Student_Id","Password", "Image", "Status", "Class_Id", "Stream_Id", "Time");
                    foreach ((array)$student as $key => $val) {
                        if (!in_array($key, $hiddenColumnsArray)) {
                            echo "<tr><td>", str_replace("_", " ", $key), "</td><td> $val</td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-8">
            <div class="tabs-container pl-3">
                <ul class="nav nav-tabs" role="tablist">
                    <?php if ((in_array("viewMarksheets", $user_permissions) && $MODULE == "academics")||($_SESSION['student_login']=='student')) { ?><li><a class="nav-link active" data-toggle="tab" href="#tab-classes-attended"> Classes Attended</a></li>
                    <?php } ?>
                    <li class="nav-item  <?php echo $MODULE == "finance" ? " active" : ""; ?>"><a class="nav-link  <?php echo $MODULE == "finance" ? " active" : ""; ?>" data-toggle="tab" href="#tab-enrollment"> Enrollment</a></li>
                    <?php if (($MODULE == "finance")||($_SESSION['student_login']=='student')) { ?><li><a class="nav-link" data-toggle="tab" href="#tab-payment-history"> Payment History</a></li>
                        <li><a class="nav-link" data-toggle="tab" href="#tab-bursaries"> Bursaries</a></li><?php } ?>
                </ul>
                <div class="tab-content">
                    <?php if ((in_array("viewMarksheets", $user_permissions) && $MODULE == "academics")||($_SESSION['student_login']=='student')) { ?>
                        <div role="tabpanel" id="tab-classes-attended" class="tab-pane active">
                            <div class="panel-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Year</th>
                                            <th>Term</th>
                                            <th>Class</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $marksQuery = "SELECT e.Student_Id,e.Class_Id,e.Stream_Id,e.Year,e.Term,c.Class_Name,s.Stream_Name,(CASE WHEN c.Year<=e.Year THEN True Else False END) Is_New FROM enrollment e,class c,stream s WHERE e.Student_Id='$id' AND s.Stream_Id=e.Stream_Id AND c.Class_Id=e.Class_Id GROUP BY e.Year,e.Term,e.Class_Id,e.Stream_Id ORDER BY e.Year, e.Term";
                                        $marksTableData = DB::getInstance()->querySample($marksQuery);
                                        foreach ($marksTableData as $list) {
                                            $set_id=$list->Is_New?"units-combined":"combined"; ?>
                                            <tr>
                                                <td><?php echo $list->Year ?></td>
                                                <td><?php echo $list->Term ?></td>
                                                <td><?php echo "$list->Class_Name $list->Stream_Name" ?></td>
                                                <td>
                                                    <form action="index.php?page=<?php echo $crypt->encode("report_pdf") . '&student_id=' . $id . '&class=' . $list->Class_Id . '&stream=' . $list->Stream_Id . '&term=' . $list->Term . '&year=' . $list->Year.'&set_id=' . $set_id; ?>" method="POST">
                                                        <button type="submit" name="generatereportsall" value="generatereportsall" class="btn btn-primary btn-xs">Print reportcard</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php } ?>
                    <div role="tabpanel" id="tab-enrollment" class="tab-pane <?php echo $MODULE == "finance" ? " active" : ""; ?>">
                        <div class="panel-body">
                            <?php
                            if ($enrollments ||((($student->Starting_Balance&&$MODULE == "finance"))||($_SESSION['student_login']=='student'))) { ?>
                                <div class="table-responsive mb-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Year</th>
                                                <th>Class</th>
                                                <th>Term</th>
                                                <th>Enrolled as</th>
                                                <?php if ($MODULE == "finance") {
                                                    $incomeAccounts = DB::getInstance()->querySample("SELECT * FROM account WHERE Category='income'");
                                                ?><th>Fees Structure</th>
                                                    <?php foreach ($incomeAccounts as $account) { ?>
                                                        <th><?php echo $account->Name ?></th>
                                                    <?php } ?>
                                                    <th>Deductions (Bursary)</th>
                                                    <th>Total Expected</th>
                                                    <th>Brought Forward</th>
                                                    <th>Paid</th>
                                                    <th>Carried Forward</th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                            <?php
                                            if($student->Starting_Balance &&$MODULE == "finance"){?>
                                                <tr>
                                                    <td colspan="11">Starting Balance</td>
                                                    <td><?php echo $student->Starting_Balance?></td>
                                                </tr>
                                            <?php }
                                            $brought_forward = $student->Starting_Balance;
                                            $balance = $student->Starting_Balance;
                                            $totalPaid = $balancePaid = array_sum(array_column(json_decode(json_encode($incomeList), true), 'Amount'));
                                            foreach ($enrollments as $enrollment) {
                                                $incomeAccounts = DB::getInstance()->querySample("SELECT e.Amount FROM account a LEFT JOIN enrollment_dues e ON (a.Id=e.Account_Id AND e.Status=1 AND e.Enrollment_Id='$enrollment->Enrollment_Id') WHERE a.Category='income'");
                                                $total_additions = array_sum(array_column(json_decode(json_encode($incomeAccounts), true), 'Amount'));
                                                $total_fees = $enrollment->Has_Bursary?$enrollment->Bursary_Fees+ $total_additions:$enrollment->Fees_Structure + $total_additions;
                                                $paid = $balancePaid >= $total_fees ? $total_fees : $balancePaid;
                                                $balancePaid = $balancePaid - $paid;
                                                $balance += $total_fees - $paid;
                                            ?>
                                                <tr>
                                                    <td><?php echo $enrollment->Year ?></td>
                                                    <td><?php echo "$enrollment->Class_Name $list->Stream_Name" ?></td>
                                                    <td><?php echo $enrollment->Term ?></td>
                                                    <td><?php echo $enrollment->Schooling_Type ?></td>
                                                    <?php if ($MODULE == "finance") { ?>
                                                        <td><?php echo $enrollment->Fees_Structure ?></td>
                                                        <?php foreach ($incomeAccounts as $account) { ?>
                                                            <td><?php echo $account->Amount ?></td>
                                                        <?php }
                                                        ?>
                                                        <td><?php echo $enrollment->Bursary_Fees ?></td>
                                                        <td><?php echo $total_fees ?></td>
                                                        <td><?php echo $brought_forward ?></td>
                                                        <td><?php echo $paid ?></td>
                                                        <td><?php echo $balance ?></td>
                                                    <?php } ?>
                                                </tr>
                                            <?php
                                                $brought_forward = $balance;
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php
                                if (($MODULE == "finance")||($_SESSION['student_login']=='student')) {
                                    if ($balancePaid > $balance) {
                                        echo "<div class='alert alert-success'>Current Cumulative balance for refund: <strong>$balancePaid</strong></div>";
                                    } else {?>
                                        <div class='alert alert-danger'>Current Cumulative balance unpaid: <strong><?php echo $brought_forward ?></strong>
                                            <?php if ($balance>0) {
                                                if(!$_SESSION['student_login']=='student'){?><button type="button" onClick='showModal("index.php?modal=finance/income/edit<?php echo "&category=income&student_id=$id&reroute=" . $_GET['reroute'] ?>","large");return false' class="btn btn-xs btn-primary float-end"><i class="fa fa-plus"></i>  Receive payment</button>
                                            <?php }}?>
                                        </div>
                                    <?php }
                                }
                            } else {
                                echo '<div class="alert alert-danger">No enrollment data available</div>';
                            }
                            ?>
                        </div>
                    </div>
                    <?php if (($MODULE == "finance")||($_SESSION['student_login']=='student')) { ?>
                        <div role="tabpanel" id="tab-payment-history" class="tab-pane">
                            <div class="panel-body">
                                <?php
                                if ($incomeList) {
                                ?>
                                    <h4><?php echo $headingTitle ?></h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered dataTables-example">
                                            <thead>
                                                <tr style="width:100%;">
                                                    <th>Date / Year</th>
                                                    <th>Term</th>
                                                    <th>Account</th>
                                                    <th>Amount</th>
                                                    <th>Mode</th>
                                                    <th>Ref. Number</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $total_income = 0;
                                                foreach ($incomeList as $income) {
                                                    $total_income += $income->Amount;
                                                    $transactions = DB::getInstance()->querySample("SELECT *,a.Name AS Source,(CASE WHEN Target_Table='student' THEN (SELECT CONCAT(Fname,' ',Lname) FROM student WHERE Student_Id=t.Target_Id) WHEN Target_Table='staff' THEN (SELECT CONCAT(Fname,' ',Lname) FROM staff WHERE Staff_Id=t.Target_Id) ELSE t.Target_Id END) AS Payee FROM transaction t,account a WHERE (t.Debit_Account=a.Id OR t.Credit_Account=a.Id) AND a.Status=1 AND t.Status=1 AND t.Transaction_Code='$income->Transaction_Code' GROUP BY t.Transaction_Code ORDER BY t.Id DESC");
                                                    $transaction = $transactions[0];
                                                ?>
                                                    <tr>
                                                        <td><?php echo $income->Date ?></td>
                                                        <td><?php echo $income->Term ?></td>
                                                        <td><?php echo $income->Source ?></td>
                                                        <td><?php echo $income->Amount ?></td>
                                                        <td><?php echo $income->Transaction_Mode ?></td>
                                                        <td><?php echo $income->Reference_Number ?></td>
                                                        <td>
                                                            <div class="d-none">
                                                                <div id="receiptSection<?php echo $income->Transaction_Code ?>">
                                                                    <table class="table table-striped">
                                                                        <tr>
                                                                            <td><label>Date: </label> <?php echo $transaction->Date ?></td>
                                                                            <td>
                                                                                <label>Term</label> <?php echo $transaction->Term ?>
                                                                            </td>
                                                                            <td style="min-width:20%;">
                                                                                <label><?php echo $category == "income" ? 'Paid For' : 'Payee' ?></label> <?php echo $transaction->Payee ?>
                                                                            </td>
                                                                            <td>
                                                                                <label>Payment Mode</label><?php echo $transaction->Transaction_Mode ?>
                                                                            </td>
                                                                            <td><label>Ref No.</label><?php echo $transaction->Reference_Number ?></td>
                                                                        </tr>
                                                                    </table>
                                                                    <h5>Receipt Items</h5>
                                                                    <table class="table">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Account</th>
                                                                                <th style="width:100px">Qty</th>
                                                                                <th>Amount (Unit price)</th>
                                                                                <th style="width:150px">Amount(Total)</th>
                                                                                <th>Desc.</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                            $total = 0;
                                                                            foreach ($transactions as $transaction) {
                                                                                $total += $transaction->Amount;
                                                                            ?>
                                                                                <tr>
                                                                                    <td><?php echo $transaction->Source ?></td>
                                                                                    <td><?php echo $transaction->Quantity ?></td>
                                                                                    <td><?php echo $transaction->Unit_Price ?></td>
                                                                                    <td><?php echo $transaction->Amount ?></td>
                                                                                    <td><?php echo $transaction->Comment ?></td>

                                                                                </tr>
                                                                            <?php } ?>
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr>
                                                                                <td>TOTAL</td>
                                                                                <th></th>
                                                                                <td></td>
                                                                                <td colspan="2"><?php echo $total ?></td>
                                                                            </tr>
                                                                        </tfoot>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        if(!$_SESSION['student_login']=='student'){?>
                                                        <button class="btn btn-xs btn-primary" onclick="PrintSection('receiptSection<?php echo $income->Transaction_Code ?>', '21.0', '29.7')">print receipt</button>
                                                        <?php }?>
                                                        </td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                            <tfoot>
                                                <th>GRAND TOTAL</th>
                                                <th></th>
                                                <th></th>
                                                <th><?php echo ugandan_shillings($total_income) ?></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tfoot>
                                        </table>
                                    </div>
                                <?php
                                } else {
                                    echo "<div class='alert alert-danger'>No payment history found</div>";
                                }
                                ?>
                            </div>
                        </div>
                        <div role="tabpanel" id="tab-bursaries" class="tab-pane">
                            <div class="panel-body">
                                <?php
                                $bursariesQuery = "SELECT *,e.* FROM enrollment e,class c,stream s WHERE e.Class_Id=c.Class_Id AND e.Stream_Id=s.Stream_Id AND e.Student_Id='$id' AND Has_Bursary=1";
                                $list = DB::getInstance()->querySample($bursariesQuery);
                                if ($list) { ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Class</th>
                                                    <th>Term</th>
                                                    <th>Year</th>
                                                    <th>Bursary Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($list as $item) { ?>
                                                    <tr>
                                                        <td><?php echo "$item->Class_Name$item->Stream_Name" ?></td>
                                                        <td><?php echo $item->Term ?></td>
                                                        <td><?php echo $item->Year ?></td>
                                                        <td><?php echo $item->Bursary_Fees ?></td>
                                                    </tr>
                                                <?php }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } else {
                                    echo "<div class='alert alert-danger'>No bursaries history found</div>";
                                }
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>


            </div>
        </div>

    </div>