<?php require_once 'inc/header.php'; ?>
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="page-header d-flex">
            <h3>Registered Employees for <?php echo $year ?></h3>
            <div class="pull-right--">
                <a href="index.php?page=<?php echo $crypt->encode("register_employees"); ?>" class=" btn btn-success btn-xs"><i class="fa fa-pencil"></i> Register employees</a>
                
                    <a class="btn btn-primary btn-xs" href="">Print Staff List</a>
               
            </div>
        </div>

        <div class="table-responsive">
        <?php 
            $employee_list =DB::getInstance()->querySample("select * from employees where status=1");
            if($employee_list){
        ?>
        <div class="row">
                    <table class="table table-bordered" id="datatable">
                        <thead>
                            <tr>
                                
                                <th><label for="selectall" id="selectControl" onclick="Check()">Select All</label></th>
                                <th>No.</th>
                                <th>Name</th>
                                <th>Photo</th>
                                <th>LIN</th>
                                <th>Registration Number</th>
                                <th>Phone</th>
                                <?php if ($IS_GENDER_VISIBLE) { ?><th>Gender</th><?php } ?>
                                <th>Stream</th>
                                <?php if ($MODULE == "finance") { ?>
                                    <th>Expected Fees</th>
                                    <th>Fees Paid</th>
                                    <th>%age Paid</th>
                                    <th>Balance</th>
                                <?php }  ?>
                                <th>
                                    Option
                                    <a id="delete_all" style="display: none;" data-toggle='modal' class='btn btn-danger btn-xs' href='#delete-all-modal-form' onclick="whatwaschecked()">delete selected</a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $nn = 1;
                            foreach ($employee_list as $employees) {
                            ?>
                                <tr class="odd gradeX">
                                    <td>
                                        <input type="checkbox" value="<?php echo $employees->employee_id; ?>" name="employee_id[]">
                                    </td>
                                    <td><?php echo $nn++; ?></td>
                                    <td><?php echo $employees->first_name . ' ' . $employees->last_name; ?></td>
                                    <td class="center"> <img class="img-circle" height="40px" width="40px" src="employees/<?php echo $employees->photo; ?>" alt=""></td>
                                    <td><?php echo $employees->gender; ?></td>
                                    <td><?php echo $employees->email; ?></td>
                                    <td><?php echo $employees->phone; ?></td>
                                    <td><?php echo $employees->decription; ?></td>
                                    <td>
                                        <button onClick='showModal("index.php?modal=employees/details<?php echo "&id=$employees->employee_id&reroute=" . $crypt->encode('page=' . $_GET['page'] . "&year=" . $crypt->encode($year) ) ?>","large");return false' class="btn btn-primary btn-xs">details</button>
                                        <button onClick='showModal("index.php?modal=employees/edit<?php echo "&id=$employees->employee_id&reroute=" . $crypt->encode('page=' . $_GET['page'] . "&year=" . $crypt->encode($year) )?>","large");return false' data-toggle="modal" class="btn btn-success btn-xs">Edit</button>
                                        <button onClick='showModal("index.php?modal=employees/delete<?php echo "&id=$employees->employee_id&reroute=" . $crypt->encode('page=' . $_GET['page'] . "&year=" . $crypt->encode($year))?>");return false' data-toggle="modal" class="btn btn-danger btn-xs">delete</button>
                                    </td>
                                </tr>
                            <?php } ?>
                            </div>
                        </tbody>
                    </table>
                <?php } else {
                ?>
                    <div class="alert alert-danger">
                        <strong> NO EMPLOYEES</strong>

                    </div>
                <?php } ?>
                </form>
            
        </div>

    </div>
    <!-- /.container-fluid -->

</div>
<?php require_once 'inc/footer.php' ?>
<script type="text/javascript">
function Check() {
    var checkBoxes = document.getElementsByName('staff_id[]');
    for (i = 0; i < checkBoxes.length; i++) {
        checkBoxes[i].checked = (selectControl.innerHTML == "Select All") ? 'checked' : '';
    }
    selectControl.innerHTML = (selectControl.innerHTML == "Select All") ? "Unselect All" : 'Select All';
}
window.onload = function() {
    var selectControl = document.getElementById("selectControl");
    selectControl.onclick = function() {
        Check();
    };
};
</script>