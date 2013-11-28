<?php namespace Searsaw\Drawbridge\Models;

use Magniloquent\Magniloquent\Magniloquent;

class Permission extends Magniloquent {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * @var array The rules used to validate the model
     */
    protected static $rules = array(
        'save'   => array(
            'name' => 'required|min:3',
            'display_name' => 'required|min:3'
        ),
        'create' => array(),
        'update' => array()
    );

    /**
     * @var array The relationships this model has to other models
     */
    protected static $relationships = array(
        'users' => array('belongsToMany', 'User', 'users_permissions', 'permission_id', 'user_id'),
        'roles' => array('belongstoMany', 'Role', 'permissions_roles', 'permission_id', 'user_id')
    );

} 