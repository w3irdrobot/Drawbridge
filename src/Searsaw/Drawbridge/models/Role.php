<?php namespace Searsaw\Drawbridge\Models;

use Magniloquent\Magniloquent\Magniloquent;

class Role extends Magniloquent {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

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
        'permissions' => array('belongstoMany', 'Permission', 'permissions_roles', 'role_id', 'permission_id')
    );

} 