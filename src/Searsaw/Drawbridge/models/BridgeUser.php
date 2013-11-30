<?php namespace Searsaw\Drawbridge\Models;

use Magniloquent\Magniloquent\Magniloquent;
use Searsaw\Drawbridge\Models\BridgeRole;

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

}