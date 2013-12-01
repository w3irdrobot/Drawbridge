<?php namespace Searsaw\Drawbridge\Models;

use Magniloquent\Magniloquent\Magniloquent;
use Searsaw\Drawbridge\Models\BridgeRole;
use Searsaw\Drawbridge\Models\BridgePermission;

class BridgeUser extends Magniloquent {

    /**
     * The Laravel application instance
     *
     * @var \Illuminate\Foundation\Application
     */
    public static $app;

    /**
     * @var array The relationships this model has to other models
     */
    protected static $relationships = array(
        'roles' => array('belongsToMany', 'Role', 'users_roles', 'user_id', 'role_id')
    );

    /**
     * The constructor of the model. Takes optional array of attributes.
     * Also, it sets validationErrors to be an empty MessageBag instance.
     * Also, sets app attribute to the current app instance
     *
     * @param array $attributes The attributes of the model to set at instantiation
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);

        if (! static::$app)
            static::$app = app();
    }

    /**
     * Add a role to the user. The argument can be the role name, ID, Role object, or an array of the previous
     *
     * @param $role array|\Traversable|string|integer The role to add to the current user
     *
     * @return \Searsaw\Drawbridge\Models\BridgeRole|\InvalidArgumentException
     */
    public function addRole($role)
    {
        if (is_array($role) || $role instanceof \Traversable)
            return $this->addMultipleRoles($role);
        else
            return $this->addSingleRole($role);
    }

    /**
     * Add roles to the user. The argument is an array of names, IDs, or Role objects
     *
     * @param $roles array|\Traversable The role to be added to the user
     *
     * @return \Searsaw\Drawbridge\Models\BridgeRole
     */
    public function addMultipleRoles($roles)
    {
        foreach ($roles as $role)
            $this->addSingleRole($role);
    }

    /**
     * Add a single role. The argument is a string, integer, or instance of BridgeRole
     *
     * @param $role string|integer|\Searsaw\Drawbridge\Models\BridgeRole
     *
     * @throws \InvalidArgumentException
     *
     * @return \Searsaw\Drawbridge\Models\BridgeRole
     */
    public function addSingleRole($role)
    {
        if (is_string($role))
            return $this->addRoleByName($role);
        elseif (is_numeric($role))
            return $this->addRoleById($role);
        elseif ($role instanceof BridgeRole)
            return $this->addRoleByObject($role);
        else
            throw new \InvalidArgumentException('Role must be a name, ID, or Role object.');
    }

    /**
     * Add a single role to the user by name
     *
     * @param $role_name string The name of the role to add
     *
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return \Searsaw\Drawbridge\Models\BridgeRole
     */
    public function addRoleByName($role_name)
    {
        $role = static::$app['db']->connection()
            ->table('roles')->where('name', '=', $role_name)->first();

        if (! $role)
            throw new \RuntimeException('No role with that name found.');

        if (is_array($role))
            return $this->addRoleById($role['id']);
        elseif (is_object($role))
            return $this->addRoleById($role->id);
        else
            throw new \UnexpectedValueException('Value returned not array or instance of BridgeRole.');
    }

    /**
     * Add a single role to the user by ID
     *
     * @param $role_id integer The ID of the role to add
     *
     * @return \Searsaw\Drawbridge\Models\BridgeRole
     */
    public function addRoleById($role_id)
    {
        return $this->roles()->attach($role_id);
    }

    /**
     * Add a single role to the user by using the Role object
     *
     * @param $role_obj \Searsaw\Drawbridge\Models\BridgeRole The Role object to add
     *
     * @return \Searsaw\Drawbridge\Models\BridgeRole
     */
    public function addRoleByObject(BridgeRole $role_obj)
    {
        if (! $role_obj->exists)
            $role_obj->save();

        $role_id = $role_obj->getKey();

        return $this->addRoleById($role_id);
    }

    /**
     * Checks to see if a user has a certain role
     *
     * @param $role string|integer|\Searsaw\Drawbridge\Models\BridgeRole The role to check
     *
     * @return bool
     */
    public function hasRole($role)
    {
        $roles = $this->roles;
        foreach ($roles as $role_obj)
            if ($this->checkRole($role, $role_obj))
                return true;

        return false;
    }

    /**
     * Checks to see if a given role is equal to a role the user already has
     *
     * @param $check string|integer|\Searsaw\Drawbridge\Models\BridgeRole The role to check
     * @param $has   \Searsaw\Drawbridge\Models\BridgeRole The role the user has to check against
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function checkRole($check, BridgeRole $has)
    {
        if (is_string($check))
            return $this->checkRoleByName($check, $has);
        elseif (is_numeric($check))
            return $this->checkRoleById($check, $has);
        elseif ($check instanceof BridgeRole)
            return $this->checkRoleByObject($check, $has);
        else
            throw new \InvalidArgumentException('Role to check must be a name, ID, or Role object.');
    }

    /**
     * Check to see if the string provided is the same as the name
     * of the BridgeRole object passed in
     *
     * @param string     $check The name to check
     * @param BridgeRole $has   The object to check against
     *
     * @return bool
     */
    public function checkRoleByName($check, BridgeRole $has)
    {
        if ($check == $has->name)
            return true;
        else
            return false;
    }

    /**
     * Check to see if the number provided is the same as the ID
     * of the BridgeRole object passed in
     *
     * @param string     $check The ID to check
     * @param BridgeRole $has   The object to check against
     *
     * @return bool
     */
    public function checkRoleById($check, BridgeRole $has)
    {
        if ($check == $has->id)
            return true;
        else
            return false;
    }

    /**
     * Check to see if the Role provided is the same as the
     * BridgeRole object passed in
     *
     * @param BridgeRole $check The object to check
     * @param BridgeRole $has   The object to check against
     *
     * @return bool
     */
    public function checkRoleByObject(BridgeRole $check, BridgeRole $has)
    {
        return $this->checkRoleById($check->id, $has);
    }

    /**
     * Check to see if the user has the given permission.  Permission
     * can be an ID, name, or Permission object
     *
     * @param $permission integer|string|\Searsaw\Drawbridge\Models\BridgePermission The permission to check for
     *
     * @return bool
     */
    public function hasPermission($permission)
    {
        $perm_id = $this->getPermissionId($permission);
        $roles = $this->getRolesWithPermission($perm_id);

        foreach ($roles as $role)
            if ($this->hasRole($role))
                return true;

        return false;
    }

    /**
     * Get the ID of the passed in permission.  Permission
     * can be an ID, name, or Permission object
     *
     * @param $permission integer|string|\Searsaw\Drawbridge\Models\BridgePermission The permission whose ID to get
     *
     * @return integer
     * @throws \InvalidArgumentException
     */
    public function getPermissionId($permission)
    {
        if (is_numeric($permission))
            return $permission;
        elseif (is_string($permission))
            return $this->getPermissionIdFromName($permission);
        elseif ($permission instanceof BridgePermission)
            return $permission->id;
        else
            throw new \InvalidArgumentException('Permission to check must be a name, ID, or Permission object.');
    }

    /**
     * Get the ID of a permission with the passed in name
     *
     * @param $perm_name string The name of the permission whose ID to get
     *
     * @return integer
     */
    public function getPermissionIdFromName($perm_name)
    {
        $permission = static::$app['db']->connection()
            ->table('permissions')->where('name', '=', $perm_name)->first();

        return $permission->id;
    }

    /**
     * Get the roles who have the given permission
     *
     * @param $perm_id integer The ID of the permission
     *
     * @return integer
     */
    public function getRolesWithPermission($perm_id)
    {
        $roles = static::$app['db']->connection()
            ->table('roles_permissions')->where('permission_id', '=', $perm_id)->lists('role_id');

        return $roles;
    }

}