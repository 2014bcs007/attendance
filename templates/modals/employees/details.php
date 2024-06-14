<?php
$id = $_GET['id'];
$student = DB::getInstance()->querySample("SELECT s.*,p.Phone AS Parent_Phone_Number FROM student s LEFT JOIN parent p ON (p.Parent_Id=s.Parent_Id) WHERE s.Student_Id='$id' LIMIT 1")[0];
$enrollments = DB::getInstance()->querySample("SELECT c.*,fs.*,e.*,fs.Amount AS Fees_Structure FROM enrollment e LEFT JOIN fees_structure fs ON (fs.Class_Id=e.Class_Id AND fs.Year=e.Year AND fs.Term=e.Term AND fs.Category=e.Schooling_Type),class c WHERE c.Class_Id=e.Class_Id AND e.Student_Id='$id' AND e.Enrollment_Status=1 GROUP BY e.Enrollment_Id ORDER BY e.Year, e.Term");
$incomeQuery = "SELECT *,SUM(t.Amount) AS Amount,COUNT(t.Id) AS Entries,a.Name AS Source FROM transaction t,account a WHERE  t.Target_table='student' AND t.Target_Id='$id' AND (t.Debit_Account=a.Id OR t.Credit_Account=a.Id) AND a.Status=1 AND t.Status=1 $incomeCondition GROUP BY t.Transaction_Code ORDER BY t.Id DESC";
$incomeList = DB::getInstance()->querySample($incomeQuery);
?>
<div class="modal-header">
    <h5 class="modal-title"><?php echo $student->Image ? "<img src='students/$student->Image' class='img-circle' height='40'> " : "" ?><?php echo "$student->Fname $student->Lname"; ?></h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
<?php require 'inc/singlestudentdetails.php' ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
</div>