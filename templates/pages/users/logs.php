<?php require_once 'inc/header.php';
$year = date("Y"); ?>
<div class="page-content">

    <div class="container-fluid">

        <div class="card">
            <div class="card-header d-flex"><h5 class="flex-grow-1">User Logs List</h5></div>
            <div class="card-body">
                <?php
                $selectuser = "SELECT * FROM logs  ORDER BY Time desc";
                $user_list = DB::getInstance()->querySample($selectuser);
                if ($user_list) {
                ?>
                    <table class="table table-bordered" id="datatable">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>User</th>
                                <th>Action Made</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            foreach ($user_list as $user) {
                            ?>
                                <tr>
                                    <td><?php echo $user->Time; ?></td>
                                    <td><?php echo $user->User; ?></td>
                                    <td><?php echo $user->Log; ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->

</div>
<?php require_once 'inc/footer.php' ?>