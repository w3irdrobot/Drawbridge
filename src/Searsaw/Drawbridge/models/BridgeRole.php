<?php namespace Searsaw\Drawbridge\Models;

use Magniloquent\Magniloquent\Magniloquent;
use Searsaw\Drawbridge\Models\BridgePermission;

class BridgeRole extends Magniloquent {

    /**
     * The Laravel application instance
     *
     * @var \Illuminate\Foundation\Application
     */
    public static $app;

    /**
     * @var array The rules used to validate the model
     */
    protected static $rules = array(
        'save'   => array(
            'name'         => 'required|min:3',
            'display_name' => 'required|min:3'
        ),
        'create' => array(
            'name' => 'unique:roles'
        ),
        'update' => array()
    );

    /**
     * @var array The relationships this model has to other models
     */
    protected static $relationships = array(
        'users'       => array('belongsToMany', 'User', 'users_roles', 'role_id', 'user_id'),
        'permissions' => array('belongsToMany', 'Permission', 'roles_permissions', 'role_id', 'permission_id')
    );

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

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
     * Add a permission to the role. The argument can be the permission name,
     * ID, Permission object, or an array of the previous
     *
     * @param $permission array|\Traversable|string|integer The permission to add to the current role
     *
     * @return \Searsaw\Drawbridge\Models\BridgePermission|\InvalidArgumentException
     */
    public function addPermission($permission)
    {
        if (is_array($permission) || $permission instanceof \Traversable)
            return $this->addMultiplePermissions($permission);
        else
            return $this->addSinglePermission($permission);
    }

    /**
     * Add permissions to the role. The argument is an array of names, IDs, or Permission objects
     *
     * @param $permissions array|\Traversable The permission to be added to the role
     *
     * @return \Searsaw\Drawbridge\Models\BridgePermission
     */
    public function addMultiplePermissions($permissions)
    {
        foreach ($permissions as $permission)
            $this->addSinglePermission($permission);
    }

    /**
     * Add a single permission. The argument is a string, integer, or instance of BridgePermission
     *
     * @param $permission string|integer|\Searsaw\Drawbridge\Models\BridgePermission
     *
     * @throws \InvalidArgumentException
     *
     * @return \Searsaw\Drawbridge\Models\BridgePermission
     */
    public function addSinglePermission($permission)
    {
        if (is_string($permission))
            return $this->addPermissionByName($permission);
        elseif (is_numeric($permission))
            return $this->addPermissionById($permission);
        elseif ($permission instanceof BridgePermission)
            return $this->addPermissionByObject($permission);
        else
            throw new \InvalidArgumentException('Permission must be a name, ID, or Permission object.');
    }

    /**
     * Add a single permission to the role by name
     *
     * @param $permission_name string The name of the permission to add
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return \Searsaw\Drawbridge\Models\BridgePermission
     */
    public function addPermissionByName($permission_name)
    {
        $permission = static::$app['db']->connection()
            ->table('permissions')->where('name', '=', $permission_name)->first();

        if (! $permission)
            return new \RuntimeException('No permission with that name found.');

        if (is_array($permission))
            return $this->addPermissionById($permission['id']);
        elseif (is_object($permission))
            return $this->addPermissionById($permission->id);
        else
            throw new \UnexpectedValueException('Value returned not array or instance of BridgePermission.');
    }

    /**
     * Add a single permission to the role by ID
     *
     * @param $permission_id integer The ID of the permission to add
     *
     * @return \Searsaw\Drawbridge\Models\BridgePermission
     */
    public function addPermissionById($permission_id)
    {
        return $this->permissions()->attach($permission_id);
    }

    /**
     * Add a single permission to the role by using the Permission object
     *
     * @param $permission_obj \Searsaw\Drawbridge\Models\BridgePermission The Permission object to add
     *
     * @return \Searsaw\Drawbridge\Models\BridgePermission
     */
    public function addPermissionByObject(BridgePermission $permission_obj)
    {
        $permission_id = $permission_obj->getKey();

        return $this->addPermissionById($permission_id);
    }

} 