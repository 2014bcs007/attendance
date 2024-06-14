<?php
$id = $_GET["id"];
if ($id) {
    $roles = DB::getInstance()->getRow("user_roles", $id, "*", "Role_Id");
}
?>
<form action="" method="POST" name="roleForm">
    <div class="modal-header">
        <h5 class="modal-title"><?php echo ($id) ? "Edit" : "New"; ?> user role</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="form-group">
            <label>Role Name</label>
            <input type="hidden" name="id" value="<?php echo $id ?>">
            <input type="text" class="form-control" name="role_name" value="<?php echo $roles->Role_Name ?>" required>
        </div>
        <div class="form-group">
            <label>Permissions</label>
            <div class="row">
                <?php
                $modules = DB::getInstance()->querySample("SELECT DISTINCT(Section) Section FROM permissions");
                foreach ($modules as $module) {
                    $permissions = DB::getInstance()->querySample("SELECT *,p.Id FROM permissions p LEFT JOIN user_permission up ON(p.Id=up.Permission_Id AND up.Role_Id='$id') WHERE p.Section='$module->Section' GROUP BY p.Id");
                    echo '<div class="col-sm-4 col-md-2"><h6 class="text-primary">' . $module->Section . '</h6>';
                    foreach ($permissions as $permission) {
                        $checked = $permission->Role_Id ? "checked" : "";
                        echo '<div><label><input type="checkbox" name="permissions[]" ' . $checked . ' value="' . $permission->Id . '"> ' . $permission->Name . '</label></div>';
                    }
                    echo '</div>';
                }
                // foreach ($PERMISSIONS_LIST AS $key => $value) {
                //     echo '<div class="col-sm-4 col-md-2"><h6 class="text-primary">' . $key . '</h6>';
                //     foreach ($value AS $perm_id => $perm_value) {
                //         $checked = (in_array($perm_id, $permissions)) ? "checked" : "";
                //         echo '<div><label><input type="checkbox" name="permissions[]" ' . $checked . ' value="' . $perm_id . '"> ' . $perm_value . '</label></div>';
                //     }
                //     echo '</div>';
                // }
                ?>
            </div>
        </div>
        <input type="hidden" name="action" value="editUserRole">
        <input type="hidden" name="reroute" value="<?php echo $_GET['reroute']; ?>">
    </div>
    <div class="modal-footer">
        <a onclick="javascript:checkAll('roleForm', true);" class="btn btn-default"><i class="fa fa-check-square-o"></i> Check All</a>
        <a onclick="javascript:checkAll('roleForm', false);" class="btn btn-default"><i class="fa fa-square-o"></i> Uncheck All</a>
        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>