<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Model\Mysql;

/**
 * UserGroup class
 *
 * @property string $groupId
 * @property string $userId
 * @property string $id
 * @property string $expire
 * @property string $created
 * @property string $updated
 */
class UserGroupModel extends Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected string $tableName = 'user_group';

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
        'userId' => array(
            'description' => 'User ID',
            'column' => 'user_id',
            'type' => 'integer'
        ),
        'id' => array(
            'description' => 'ID',
            'column' => 'id',
            'type' => 'integer'
        ),
        'expire' => array(
            'description' => 'Expiration Date',
            'column' => 'expire',
            'type' => 'datetime'
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
