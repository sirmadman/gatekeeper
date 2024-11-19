<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Model\Mysql;

class GroupPermissionModel extends Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected string $tableName = 'group_permission';

    /**
     * Model properties
     * @var array
     */
    protected array $properties = array(
        'groupId' => array(
            'description' => 'Group Id',
            'column' => 'group_id',
            'type' => 'integer'
        ),
        'permissionId' => array(
            'description' => 'Permission ID',
            'column' => 'permission_id',
            'type' => 'integer'
        ),
        'id' => array(
            'description' => 'ID',
            'column' => 'id',
            'type' => 'integer'
        ),
        'created' => array(
            'description' => 'Date Created',
            'column' => 'created',
            'type' => 'datetime'
        ),
        'updated' => array(
            'description' => 'Date Updated',
            'column' => 'updated',
            'type' => 'datetime'
        )
    );
}
