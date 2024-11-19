<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Model\Mysql;

/**
 * PermissionParent class
 *
 * @property string $id
 * @property string $permissionId
 * @property string $parentId
 * @property string $created
 * @property string $updated
 */
class PermissionParentModel extends Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected string $tableName = 'permission_parent';

    /**
     * Model properties
     * @var array
     */
    protected array $properties = array(
        'id' => array(
            'description' => 'Record ID',
            'column' => 'id',
            'type' => 'integer'
        ),
        'permissionId' => array(
            'description' => 'Permission ID',
            'column' => 'permission_id',
            'type' => 'integer'
        ),
        'parentId' => array(
            'description' => 'Parent ID',
            'column' => 'parent_id',
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
