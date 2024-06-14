<?php require_once 'inc/header.php';
$year = date("Y"); ?>
<div class="page-content">

    <div class="container-fluid">

        <div class="card">
            <div class="card-header d-flex">
                <h5 class="flex-grow-1">User Accounts List</h5>
                <div>
                    <?php if (in_array('addUser', $user_permissions)) { ?>
                        <a onClick='showModal("index.php?modal=staff/edit<?php echo "&reroute=" . $crypt->encode('page=' . $_GET['page']) ?>");return false' data-toggle="modal" class="btn btn-xs btn-primary"><i class="fa fa-plus"></i> Register Staff</a>
                    <?php
                    }
                    if (in_array('addStaff', $user_permissions)) {
                    ?>
                        <a onClick='showModal("index.php?modal=users/edit<?php echo "&reroute=" . $crypt->encode('page=' . $_GET['page']) ?>");return false' data-toggle="modal" class="btn btn-xs btn-primary"><i class="fa fa-plus"></i> Register User</a>
                    <?php } ?>
                </div>
            </div>
            <div class="card-body">
                <?php
                $selectuser = "SELECT *,u.Status FROM staff,user u LEFT JOIN user_roles ur ON (u.Role_Id=ur.Role_Id) WHERE u.Staff_Id=staff.Staff_Id AND staff.Staff_Status=1  ORDER BY u.User_Id desc";
                $user_list = DB::getInstance()->querySample($selectuser);
                if ($user_list) {
                ?>
                    <table class="table table-bordered" id="datatable<?php echo $no; ?>">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>User Group</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            foreach ($user_list as $user) {
                            ?>
                                <tr>
                                    <td><?php echo "$user->Fname $user->Lname"; ?></td>
                                    <td><?php echo $user->Username; ?></td>
                                    <td><?php echo $user->Role_Name; ?></td>
                                    <td class="d-flex">
                                        <?php
                                        if (in_array('editUser', $user_permissions)) { ?>
                                        <a onClick='showModal("index.php?modal=users/edit<?php echo "&id=$user->User_Id&reroute=" . $crypt->encode('page=' . $_GET['page']) ?>");return false' data-toggle="modal" class="btn btn-xs btn-primary m-2 mt-0">Edit</a>
                                        <?php }
                                        if (in_array('deleteUser', $user_permissions)) { ?>
                                            <form role="form" action="" method="POST">
                                                <input name="action" type="hidden" value="toggleUserStatus" />
                                                <input name="id" type="hidden" value="<?php echo $user->User_Id ?>" />
                                                <input name="status" type="hidden" value="<?php echo $user->Status ? 0 : 1 ?>" />
                                                <input name="reroute" type="hidden" value="<?php echo $crypt->encode('page=' . $_GET['page']) ?>" />
                                                <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-xs <?php echo $user->Status == 1 ? 'btn-danger' : 'btn-success'; ?>"><?php echo $user->Status == 1 ? 'Deactivate' : 'Activate'; ?></button>
                                            </form>
                                        <?php } ?>
                                        <a href="<?php echo "?id=$user->User_Id&page=" . $crypt->encode('profile') ?>" class="btn btn-primary btn-xs m-2 mt-0">profile</a>
                                    </td>
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