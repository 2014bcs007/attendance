<?php require_once 'inc/header.php'; ?>
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="page-header d-flex">
            <h3>Attandance Tracker <?php echo $year ?></h3>
            <div class="pull-right--">
                <a href="index.php?page=<?php echo $crypt->encode("register_employees"); ?>" class=" btn btn-success btn-xs"><i class="fa fa-pencil"></i> Attend</a>
                <a class="btn btn-primary btn-xs" href="">Print My attendance Map</a>               
            </div>
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