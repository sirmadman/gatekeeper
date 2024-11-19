<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Model\Mysql;
use Psecio\Gatekeeper\GroupParentModel;
use Psecio\Gatekeeper\UserCollection;
use Psecio\Gatekeeper\PermissionCollection;
use Psecio\Gatekeeper\GroupCollection;

/**
* Group class
*
* @property string $description
* @property string $id
* @property string $name
* @property string $expire
* @property string $created
* @property string $updated
*/
class GroupModel extends Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected string $tableName = 'groups';

    /**
     * Model properties
     * @var array
     */
    protected array $properties = array(
        'description' => array(
            'description' => 'Group Description',
            'column' => 'description',
            'type' => 'varchar'
        ),
        'id' => array(
            'description' => 'Group ID',
            'column' => 'id',
            'type' => 'integer'
        ),
        'name' => array(
            'description' => 'Group name',
            'column' => 'name',
            'type' => 'varchar'
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
        ),
        'users' => array(
            'description' => 'Users belonging to this group',
            'type' => 'relation',
            'relation' => array(
                'model' => UserCollection::class,
                'method' => 'findByGroupId',
                'local' => 'id'
            )
        ),
        'permissions' => array(
            'description' => 'Permissions belonging to this group',
            'type' => 'relation',
            'relation' => array(
                'model' => PermissionCollection::class,
                'method' => 'findByGroupId',
                'local' => 'id'
            )
        ),
        'children' => array(
            'description' => 'Child Groups',
            'type' => 'relation',
            'relation' => array(
                'model' => GroupCollection::class,
                'method' => 'findChildrenByGroupId',
                'local' => 'id'
            )
        )
    );

    /**
     * Add a user to the group
     *
     * @param integer|UserModel $user Either a user ID or a UserModel instance
     *
     * @return boolean
     */
    public function addUser($user): bool
    {
        if ($this->id === null) {
            return false;
        }
        if ($user instanceof UserModel) {
            $user = $user->id;
        }
        $data = array(
            'group_id' => $this->id,
            'user_id' => $user
        );
        $groupUser = new UserGroupModel($this->getDb(), $data);
        return $this->getDb()->save($groupUser);
    }

    /**
     * Remove a user from a group
     *
     * @param integer|UserModel $user User ID or model instance
     *
     * @return boolean Success/fail of removal
     */
    public function removeUser($user): bool
    {
        if ($this->id === null) {
            return false;
        }
        if ($user instanceof UserModel) {
            $user = $user->id;
        }
        $data = array(
            'group_id' => $this->id,
            'user_id' => $user
        );
        $groupUser = new UserGroupModel($this->getDb(), $data);
        return $this->getDb()->delete($groupUser);
    }

    /**
     * Check to see if the group has a permission
     *
     * @param integer|PermissionModel $permission Either a permission ID or PermissionModel
     *
     * @return boolean Permission found/not found
     */
    public function hasPermission($permission): bool
    {
        if ($this->id === null) {
            return false;
        }
        if ($permission instanceof PermissionModel) {
            $permission = $permission->id;
        }

        $perm = new GroupPermissionModel($this->getDb());
        $perm = $this->getDb()->find($perm, array(
            'permission_id' => $permission,
            'group_id' => $this->id
        ));
        return ($perm->id !== null && $perm->permissionId == $permission) ? true : false;
    }

    /**
     * Add a permission relation for the group
     *
     * @param integer|PermissionModel $permission Either a permission ID or PermissionModel
     *
     * @return boolean Success/fail of removal
     */
    public function addPermission($permission): bool
    {
        if ($this->id === null) {
            return false;
        }
        if ($permission instanceof PermissionModel) {
            $permission = $permission->id;
        }
        $data = array(
            'permission_id' => $permission,
            'group_id' => $this->id
        );
        $groupPerm = new GroupPermissionModel($this->getDb(), $data);
        return $this->getDb()->save($groupPerm);
    }

    /**
     * Remove a permission from a group
     *
     * @param integer|PermissionModel $permission Permission model or ID
     *
     * @return boolean Success/fail of removal
     */
    public function removePermission($permission): bool
    {
        if ($this->id === null) {
            return false;
        }
        if ($permission instanceof PermissionModel) {
            $permission = $permission->id;
        }
        $data = array(
            'permission_id' => $permission,
            'group_id' => $this->id
        );
        $groupPerm = new GroupPermissionModel($this->getDb(), $data);
        return $this->getDb()->delete($groupPerm);
    }

    /**
     * Check if the user is in the current group
     *
     * @param integer $userId User ID
     *
     * @return boolean Found/not found in group
     */
    public function inGroup($userId): bool
    {
        $userGroup = new UserGroupModel($this->getDb());
        $userGroup = $this->getDb()->find($userGroup, array(
            'group_id' => $this->id,
            'user_id' => $userId
        ));
        return ($userGroup->id !== null) ? true : false;
    }

    /**
     * Add the given group or group ID as a child of the current group
     *
     * @param integer|GroupModel $group Group ID or Group model instance
     *
     * @return boolean Result of save operation
     */
    public function addChild($group): bool
    {
        if ($this->id === null) {
            return false;
        }
        if ($group instanceof self) {
            $group = $group->id;
        }
        $childGroup = new GroupParentModel(
            $this->getDb(),
            array('groupId' => $group, 'parentId' => $this->id)
        );
        return $this->getDb()->save($childGroup);
    }

    /**
     * Remove a child group either by ID or Group model instance
     *
     * @param integer|GroupModel $group Group ID or Group model instance
     *
     * @return boolean Result of delete operation
     */
    public function removeChild($group): bool
    {
        if ($this->id === null) {
            return false;
        }
        if ($group instanceof self) {
            $group = $group->id;
        }
        $childGroup = new GroupParentModel($this->getDb());

        $childGroup = $this->getDb()->find(
            $childGroup,
            array('group_id' => $group, 'parent_id' => $this->id)
        );
        return $this->getDb()->delete($childGroup);
    }

    /**
     * Check to see if the group is expired
     *
     * @return boolean Expired/Not expired result
     */
    public function isExpired(): bool
    {
        return ($this->expire !== null && $this->expire <= time());
    }
}
