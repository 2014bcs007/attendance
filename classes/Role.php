<?php
class Role
{
    protected $permissions;

    protected function __construct()
    {
        $this->permissions = array();
    }

    // return a role object with associated permissions
    public static function getRolePerms($role_id)
    {
        $sql = "SELECT * FROM user_permission up
                JOIN permissions p ON up.permission_id = p.permission_id
                WHERE up.user_role_id = '$role_id'";

        $data = DB::getInstance()->querySample($sql);
        return array_column(json_decode(json_encode($data), true), 'code');
    }

    // check if a permission is set
    public function hasPerm($permission)
    {
        return isset($this->permissions[$permission]);
    }
    public static function registerPermissions()
    {
        global $PERMISSIONS_LIST;
        foreach ($PERMISSIONS_LIST as $module => $perms) {
            if ($perms) {
                foreach ($perms as $key => $value) {
                    if ($key && $value) {
                        if (!DB::getInstance()->checkRows("SELECT code FROM permissions WHERE code='$key'")) {
                            DB::getInstance()->insert("permissions", array("Section" => $module, "Code" => $key, "Name" => $value));
                        }
                    }
                }
            }
        }
    }
    public static function registerUserPermissions()
    {
        $roles = DB::getInstance()->querySample("SELECT * FROM user_roles");
        foreach ($roles as $role) {
            if (!DB::getInstance()->checkRows("SELECT * FROM user_permission WHERE user_role_id='$role->user_role_id'")) {
                $permissions = unserialize($role->permissions);
                //$data=DB::getInstance()->querySample("SELECT * FROM permission WHERE code IN (".implode(",",$permissions).")");
                foreach ($permissions as $perm_name) {
                    $perm = DB::getInstance()->getRow("permissions", $perm_name, "*", "code");
                    if ($perm->Id) {
                        DB::getInstance()->insert("user_permission", array("user_role_id" => $role->user_role_id, "permission_id" => $perm->user_role_id));
                    }
                }
            }
        }
    }
}
