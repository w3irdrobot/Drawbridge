<?php namespace Searsaw\Drawbridge\Models;

use Magniloquent\Magniloquent\Magniloquent;

class BridgeUser extends Magniloquent {

    /**
     * @var array The relationships this model has to other models
     */
    protected static $relationships = array(
        'roles' => array('belongsToMany', 'Role', 'users_roles', 'user_id', 'role_id')
    );

} 