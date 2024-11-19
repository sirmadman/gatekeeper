<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Model\Mysql;
use Psecio\Gatekeeper\GroupCollection;
use Psecio\Gatekeeper\UserCollection;
use Psecio\Gatekeeper\PermissionCollection;

/**
 * Permission class
 *
 * @property string $name
 * @property string $description
 * @property string $id
 * @property string $created
 * @property string $updated
 */
class PermissionModel extends Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected string $tableName = 'permissions';

    /**
     * Model properties
     * @var array
     */
    protected array $properties = array(
        'name' => array(
            'description' => 'Group Name',
            'column' => 'name',
            'type' => 'varchar'
        ),
        'description' => array(
            'description' => 'Description',
            'column' => 'description',
            'type' => 'text'
        ),
        'id' => array(
            'description' => 'Group ID',
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
        ),
        'expire' => array(
            'description' => 'Expiration Date',
            'column' => 'expire',
            'type' => 'datetime'
        ),
        'groups' => array(
            'description' => 'Groups the permission belongs to',
            'type' => 'relation',
            'relation' => array(
                'model' => GroupCollection::class,
                'method' => 'findGroupsByPermissionId',
                'local' => 'id'
            )
        ),
        'users' => array(
            'description' => 'Users that have the permission',
            'type' => 'relation',
            'relation' => array(
                'model' => UserCollection::class,
                'method' => 'findUsersByPermissionId',
                'local' => 'id'
            )
        ),
        'children' => array(
            'description' => 'Child Permissions',
            'type' => 'relation',
            'relation' => array(
                'model' => PermissionCollection::class,
                'method' => 'findChildrenByPermissionId',
                'local' => 'id'
            )
        )
    );

    /**
     * Add a permission as a child of the current instance
     *
     * @param integer|PermissionModel $permission Either permission ID or model instance
     *
     * @return boolean Result of save operation
     */
    public function addChild($permission): bool
    {
        if ($this->id === null) {
            return false;
        }
        if ($permission instanceof PermissionModel) {
            $permission = $permission->id;
        }
        $childPermission = new PermissionParentModel(
            $this->getDb(),
            array('permission_id' => $permission, 'parent_id' => $this->id)
        );
        return $this->getDb()->save($childPermission);
    }

    /**
     * Remove a permission as a child of this instance
     *
     * @param integer|PermissionModel $permission Either permission ID or model instance
     *
     * @return boolean Resultk of delete operation
     */
    public function removeChild($permission): bool
    {
        if ($this->id === null) {
            return false;
        }
        if ($permission instanceof PermissionModel) {
            $permission = $permission->id;
        }
        $childPermission = new PermissionParentModel($this->getDb());

        $childPermission = $this->getDb()->find(
            $childPermission,
            array('permission_id' => $permission, 'parent_id' => $this->id)
        );
        return $this->getDb()->delete($childPermission);
    }

    /**
     * Test if the permission is expired
     *
     * @return boolean Expired/not expired
     */
    public function isExpired(): bool
    {
        return ($this->expire !== null && $this->expire <= time());
    }
}
