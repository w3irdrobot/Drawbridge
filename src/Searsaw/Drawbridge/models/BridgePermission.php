<?php namespace Searsaw\Drawbridge\Models;

use Magniloquent\Magniloquent\Magniloquent;

class BridgePermission extends Magniloquent {

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
            'name'         => 'required|min:3',
            'display_name' => 'required|min:3'
        ),
        'create' => array(
            'name' => 'unique:permissions'
        ),
        'update' => array()
    );

    /**
     * @var array The relationships this model has to other models
     */
    protected static $relationships = array(
        'roles' => array('belongstoMany', 'Role', 'roles_permissons', 'permission_id', 'role_id')
    );

} 