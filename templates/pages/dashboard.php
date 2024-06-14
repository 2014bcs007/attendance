<?php require_once 'inc/header.php' ?>
<div class="page-content">
    <div class="container-fluid">
        <?php
        $year = date('Y-m-d');
        $looged_in_user_id = $_SESSION['user_id'];
        $logged_in_employee_id = $_SESSION['employee_id'];
        $fetchdata = DB::getInstance()->querySample("select * from attendance where attendance_date='$year' order by updated_at desc")[0];
        ?>

        <!-- Page content here -->
        <div class="row">

            <div class="col-xl-3 col-md-6">
                <!-- card -->
                <div class="card card-animate">
                    <div class="card-body">
                        <form role="form" action="" method="POST" enctype="multipart/form-data">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">

                                </div>
                            </div>
                            <?php if (!$fetchdata->attendance_id) { ?>
                                <div class="form-group">
                                    <a onClick='showModal("index.php?modal=attendance/attend<?php echo "&id=" . $logged_in_employee_id . "&reroute=" . $crypt->encode('page=' . $_GET['page']) ?>");return false' class="btn btn-primary btn-xs">Attend</a>
                                    <input type="hidden" name="reroute" value="<?php echo $crypt->encode('page=' . $_GET['page']) ?>" />
                                </div>
                            <?php } else { ?>

                                <div class="form-group">
                                    <label>You checked In Today <?php echo $fetch_data->attendance_date; ?></label>
                                    <label>At: <?php echo $fetchdata->clockin_time; ?></label>
                                </div>
                                <?php if ($fetchdata->clockout_time) { ?>
                                    <div class="form-group">
                                        <label>Check out time is: <?php echo $fetchdata->clockout_time; ?></label>
                                    </div>
                                <?php } else {
                                ?>
                                    <div class="form-group">
                                        <a onClick='showModal("index.php?modal=attendance/attend<?php echo "&id=" . $logged_in_employee_id . "&reroute=" . $crypt->encode('page=' . $_GET['page']) ?>");return false' class="btn btn-warning btn-xs">Checkout</a>

                                    </div>
                            <?php
                                }
                            } ?>

                            <div class="d-flex align-items-end justify-content-between mt-4">
                                <div>
                                    <h4 class="fs-22 fw-semibold ff-secondary mb-4"><span class="counter-value" data-target="<?php echo $subs; ?>">
                                            <?php
                                            echo $subs; ?>
                                        </span></h4>
                                    <a href="?page=<?php echo $crypt->encode("subject_registration") ?>&%20mode=view" class="text-decoration-underline">View my attendence</a>
                                </div>
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-soft-info rounded fs-3">
                                        <i class="mdi mdi-book text-info"></i>
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div><!-- end card body -->
                </div><!-- end card -->
            </div><!-- end col -->
        </div>
    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->

<?php require_once 'inc/footer.php' ?>
