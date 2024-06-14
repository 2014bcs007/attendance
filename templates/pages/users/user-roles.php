<?php require_once 'inc/header.php'; ?>
<div class="page-content">

    <div class="container-fluid">

        <div class="card">
            <div class="card-header d-flex">
                <h5 class="flex-grow-1">User Roles</h5>
                <div>
                    <a onClick='showModal("index.php?modal=users/edit-role<?php echo "&reroute=" . $crypt->encode('page=' . $_GET['page']) ?>","large");return false' data-toggle="modal" class="btn btn-xs btn-primary"><i class="fa fa-plus"></i> New Role</a>
                </div>
            </div>
            <div class="card-body">
                <?php
                $query = "SELECT r.Role_Id,r.Role_Name,(SELECT COUNT(u.Role_Id) FROM user u WHERE u.Role_Id=r.Role_Id AND u.Status=1 GROUP BY u.Role_Id) total_users,COUNT(up.Role_Id)total_permissions FROM user_roles r LEFT JOIN user_permission up ON (up.Role_Id=r.Role_Id) WHERE r.Status=1 GROUP BY r.Role_Id ORDER BY r.Role_Name;";
                $roles_list = DB::getInstance()->querySample($query);
                if ($roles_list) {
                ?>
                    <table class="table table-bordered" id="datatable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Total Users</th>
                                <th>Total Permissions</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            foreach ($roles_list as $roles) {
                            ?>
                                <tr>
                                    <td><?php echo $roles->Role_Name; ?></td>
                                    <td><?php echo $roles->total_users;
                                        echo $userInfo->Role_Id&&$roles->Role_Id == $userInfo->Role_Id ? ' <b class="text-success">Me inclussive</b>' : ''; ?></td>
                                    <td><?php echo $roles->total_permissions ?></td>
                                    <td class="d-flex">
                                        <a onClick='showModal("index.php?modal=users/edit-role<?php echo "&id=$roles->Role_Id&reroute=" . $crypt->encode('page=' . $_GET['page']) ?>","large");return false' data-toggle="modal" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i> Edit</a>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                <?php } else {
                    echo '<div class="alert alert-danger">No user roles available</div>';
                } ?>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->

</div>
<?php require_once 'inc/footer.php' ?>