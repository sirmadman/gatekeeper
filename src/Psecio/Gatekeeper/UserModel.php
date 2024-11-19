<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Model\Mysql;
use Psecio\Gatekeeper\UserGroupModel;
use Psecio\Gatekeeper\PermissionModel;
use Psecio\Gatekeeper\GroupModel;
use Psecio\Gatekeeper\UserGroupCollection;
use Psecio\Gatekeeper\UserPermissionCollection;
use Psecio\Gatekeeper\UserModel;
use Psecio\Gatekeeper\ThrottleModel;
use Psecio\Gatekeeper\SecurityQuestionCollection;
use Psecio\Gatekeeper\AuthTokenCollection;
use Psecio\Gatekeeper\Exception\PasswordResetInvalid;
use Psecio\Gatekeeper\Exception\PasswordResetTimeout;
use InvalidArgumentException;
use DateTime;

/**
 * User class
 *
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $firstName
 * @property string $lastName
 * @property string $phone
 * @property string $mobile
 * @property string $created
 * @property string $updated
 * @property string $status
 * @property string $id
 * @property string $resetCode
 * @property string $resetCodeTimeout
 * @property string $lastLogin
 */
class UserModel extends Mysql
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * Database table name
     * @var string
     */
    protected string $tableName = 'users';

    /**
     * Model properties
     * @var array
     */
    protected array $properties = array(
        'username' => array(
            'description' => 'Username',
            'column' => 'username',
            'type' => 'varchar'
        ),
        'password' => array(
            'description' => 'Password',
            'column' => 'password',
            'type' => 'varchar'
        ),
        'email' => array(
            'description' => 'Email Address',
            'column' => 'email',
            'type' => 'varchar'
        ),
        'firstName' => array(
            'description' => 'First Name',
            'column' => 'first_name',
            'type' => 'varchar'
        ),
        'lastName' => array(
            'description' => 'Last Name',
            'column' => 'last_name',
            'type' => 'varchar'
        ),
        'phone' => array(
            'description' => 'Telephone',
            'column' => 'phone',
            'type' => 'varchar'
        ),
        'mobile' => array(
            'description' => 'Mobile phone',
            'column' => 'mobile',
            'type' => 'varchar'
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
        'status' => array(
            'description' => 'Status',
            'column' => 'status',
            'type' => 'varchar'
        ),
        'id' => array(
            'description' => 'User ID',
            'column' => 'id',
            'type' => 'integer'
        ),
        'resetCode' => array(
            'description' => 'Password Reset Code',
            'column' => 'password_reset_code',
            'type' => 'varchar'
        ),
        'resetCodeTimeout' => array(
            'description' => 'Password Reset Code Timeout',
            'column' => 'password_reset_code_timeout',
            'type' => 'datetime'
        ),
        'lastLogin' => array(
            'description' => 'Date and Time of Last Login',
            'column' => 'last_login',
            'type' => 'datetime'
        ),
        'groups' => array(
            'description' => 'Groups the User Belongs to',
            'type' => 'relation',
            'relation' => array(
                'model' => UserGroupCollection::class,
                'method' => 'findByUserId',
                'local' => 'id'
            )
        ),
        'permissions' => array(
            'description' => 'Permissions the user has',
            'type' => 'relation',
            'relation' => array(
                'model' => UserPermissionCollection::class,
                'method' => 'findByUserId',
                'local' => 'id'
            )
        ),
        'loginAttempts' => array(
            'description' => 'Number of login attempts by user',
            'type' => 'relation',
            'relation' => array(
                'model' => UserModel::class,
                'method' => 'findAttemptsByUser',
                'local' => 'id',
                'return' => 'value'
            )
        ),
        'throttle' => array(
            'description' => 'Full throttle information for a user',
            'type' => 'relation',
            'relation' => array(
                'model' => ThrottleModel::class,
                'method' => 'findByUserId',
                'local' => 'id'
            )
        ),
        'securityQuestions' => array(
            'description' => 'Security questions for the user',
            'type' => 'relation',
            'relation' => array(
                'model' => SecurityQuestionCollection::class,
                'method' => 'findByUserId',
                'local' => 'id'
            )
        ),
        'authTokens' => array(
            'description' => 'Current auth (remember me) tokens for the user',
            'type' => 'relation',
            'relation' => array(
                'model' => AuthTokenCollection::class,
                'method' => 'findTokensByUserId',
                'local' => 'id'
            )
        )
    );

    /**
     * Check to see if the password needs to be rehashed
     *
     * @param string $value Password string
     *
     * @return string Updated string value
     */
    public function prePassword(string $value): string
    {
        if (password_needs_rehash($value, PASSWORD_DEFAULT) === true) {
            $value = password_hash($value, PASSWORD_DEFAULT);
        }
        return $value;
    }

    /**
     * Find the user by username
     *     If found, user is automatically loaded into model instance
     *
     * @param string $username Username
     *
     * @return object Either a collection or model instance
     */
    public function findByUsername(string $username): object|bool
    {
        return $this->getDb()->find(
            $this,
            array('username' => $username)
        );
    }

    /**
     * Find a user by their given ID
     *
     * @param integer $userId User ID
     *
     * @return object Either a collection or model instance
     */
    public function findByUserId(int $userId): object|bool
    {
        return $this->getDb()->find(
            $this,
            array('id' => $userId)
        );
    }

    /**
     * Attach a permission to a user account
     *
     * @param integer|PermissionModel $perm Permission ID or model instance
     * @param integer $expire Expiration time of the permission relationship
     *
     * @return boolean
     */
    public function addPermission(int|PermissionModel $perm, ?int $expire = null): bool
    {
        if ($perm instanceof PermissionModel) {
            $perm = $perm->id;
        }
        $data = [
            'user_id' => $this->id,
            'permission_id' => $perm
        ];
        if ($expire !== null && is_int($expire)) {
            $data['expire'] = $expire;
        }
        $perm = new UserPermissionModel($this->getDb(), $data);
        return $this->getDb()->save($perm);
    }

    /**
     * Revoke a user permission
     *
     * @param integer|PermissionModel $perm Permission ID or model instance
     *
     * @return boolean Success/fail of delete
     */
    public function revokePermission(int|PermissionModel $perm): bool
    {
        if ($perm instanceof PermissionModel) {
            $perm = $perm->id;
        }
        $perm = new UserPermissionModel($this->getDb(), array(
            'user_id' => $this->id,
            'permission_id' => $perm
        ));
        return $this->getDb()->delete($perm);
    }

    /**
     * Add a group to the user
     *
     * @param integer|GroupModel $group Add the user to a group
     *
     * @return boolean Success/fail of add
     */
    public function addGroup(int|GroupModel $group, $expire = null): bool
    {
        if ($group instanceof GroupModel) {
            $group = $group->id;
        }
        $data = [
            'group_id' => $group,
            'user_id' => $this->id
        ];
        if ($expire !== null && is_int($expire)) {
            $data['expire'] = $expire;
        }
        $group = new UserGroupModel($this->getDb(), $data);
        return $this->getDb()->save($group);
    }

    /**
     * Revoke access to a group for a user
     *
     * @param integer|GroupModel $group ID or model of group to remove
     *
     * @return boolean
     */
    public function revokeGroup(int|GroupModel $group): bool
    {
        if ($group instanceof GroupModel) {
            $group = $group->id;
        }
        $group = new UserGroupModel($this->getDb(), array(
            'group_id' => $group,
            'user_id' => $this->id
        ));
        return $this->getDb()->delete($group);
    }

    /**
     * Activate the user (status)
     *
     * @return boolean Success/fail of activation
     */
    public function activate(): bool
    {
        // Verify we have a user
        if ($this->id === null) {
            return false;
        }
        $this->status = self::STATUS_ACTIVE;
        return $this->getDb()->save($this);
    }

    /**
     * Deactivate the user
     *
     * @return boolean Success/fail of deactivation
     */
    public function deactivate(): bool
    {
        // Verify we have a user
        if ($this->id === null) {
            return false;
        }
        $this->status = self::STATUS_INACTIVE;
        return $this->getDb()->save($this);
    }

    /**
     * Generate and return the code for a password reset
     *     Also updates the user record
     *
     * @param integer $length Length of returned string
     *
     * @return bool|string Genrated code
     */
    public function getResetPasswordCode(int $length = 80): bool|string
    {
        // Verify we have a user
        if ($this->id === null) {
            return false;
        }
        // Generate a random-ish code and save it to the user record
        $code = substr(bin2hex(openssl_random_pseudo_bytes($length)), 0, $length);
        $this->resetCode = $code;
        $this->resetCodeTimeout = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->getDb()->save($this);

        return $code;
    }

    /**
     * Check the given code against he value in the database
     *
     * @param string $resetCode Reset code to verify
     *
     * @return boolean Pass/fail of verification
     */
    public function checkResetPasswordCode(string $resetCode): bool
    {
        // Verify we have a user
        if ($this->id === null) {
            return false;
        }
        if ($this->resetCode === null) {
            throw new PasswordResetInvalid('No reset code defined for user ' . $this->username);
        }

        // Verify the timeout
        $timeout = new DateTime($this->resetCodeTimeout);
        if ($timeout <= new DateTime()) {
            $this->clearPasswordResetCode();
            throw new PasswordResetTimeout('Reset code has timeed out!');
        }

        // We made it this far, compare the hashes
        $result = (hash_equals($this->resetCode, $resetCode));
        if ($result === true) {
            $this->clearPasswordResetCode();
        }
        return $result;
    }

    /**
     * Clear all data from the passsword reset code handling
     *
     * @return boolean [type] [description]
     */
    public function clearPasswordResetCode(): bool
    {
        // Verify we have a user
        if ($this->id === null) {
            return false;
        }
        $this->resetCode = null;
        $this->resetCodeTimeout = null;
        return $this->getDb()->save($this);
    }

    /**
     * Check to see if the user is in the group
     *
     * @param integer|string $groupId Group ID or name
     *
     * @return boolean Found/not found in the group
     */
    public function inGroup(int|string $groupId): bool
    {
        $find = ['user_id' => $this->id];
        if (!is_numeric($groupId)) {
            $g = Gatekeeper::findGroupByName($groupId);
            $groupId = $g->id;
        }
        $find['group_id'] = $groupId;

        $userGroup = new UserGroupModel($this->getDb());
        $userGroup = $this->getDb()->find($userGroup, $find);
        if ($userGroup->id === null) {
            return false;
        }
        return ($userGroup->id !== null && $userGroup->groupId == $groupId) ? true : false;
    }

    /**
     * Check to see if a user has a permission
     *
     * @param integer|string $permId Permission ID or name
     *
     * @return boolean Found/not found in user permission set
     */
    public function hasPermission(int|string $permId): bool
    {
        $find = ['user_id' => $this->id];
        if (!is_numeric($permId)) {
            $p = Gatekeeper::findPermissionByName($permId);
            $permId = $p->id;
        }
        $find['permission_id'] = $permId;

        $perm = new UserPermissionModel($this->getDb());
        $perm = $this->getDb()->find($perm, $find);
        return ($perm->id !== null && $perm->id === $permId) ? true : false;
    }

    /**
     * Check to see if a user is banned
     *
     * @return boolean User is/is not banned
     */
    public function isBanned(): bool
    {
        $throttle = new ThrottleModel($this->getDb());
        $throttle = $this->getDb()->find($throttle, array('user_id' => $this->id));

        return ($throttle->status === ThrottleModel::STATUS_BLOCKED) ? true : false;
    }

    /**
     * Find the number of login attempts for a user
     *
     * @param integer $userId User ID [optional]
     *
     * @return integer Number of login attempts
     */
    public function findAttemptsByUser(?int $userId = null): int
    {
        $userId = $userId ?? $this->id;

        $throttle = new ThrottleModel($this->getDb());
        $throttle = $this->getDb()->find($throttle, array('user_id' => $userId));

        return ($throttle->attempts === null) ? 0 : $throttle->attempts;
    }

    /**
     * Grant permissions and groups (multiple) at the same time
     *
     * @param array $config Configuration settings (permissions & groups)
     * @param integer $expire Expiration time for the settings
     *
     * @return bool Success/fail of grant
     */
    public function grant(array $config, ?int $expire = null): bool
    {
        $return = true;
        if (isset($config['permissions'])) {
            $result = $this->grantPermissions($config['permissions'], $expire);
            if ($result === false && $return === true) {
                $return = false;
            }
        }
        if (isset($config['groups'])) {
            $result = $this->grantGroups($config['groups'], $expire);
            if ($result === false && $return === true) {
                $return = false;
            }
        }
        return $return;
    }

    /**
     * Handle granting of multiple permissions
     *
     * @param array $permissions Set of permissions (either IDs or objects)
     * @param integer $expire EXpiration (unix timestamp) for the permissions
     *
     * @return boolean Success/fail of all saves
     */
    public function grantPermissions(array $permissions, ?int $expire = null): bool
    {
        $return = true;
        foreach ($permissions as $permission) {
            $permission = ($permission instanceof PermissionModel) ? $permission->id : $permission;
            $data = [
                'userId' => $this->id,
                'permissionId' => $permission
            ];
            if ($expire !== null && is_int($expire)) {
                $data['expire'] = $expire;
            }
            $userPerm = new UserPermissionModel($this->getDb(), $data);
            $result = $this->getDb()->save($userPerm);
            if ($result === false && $return === true) {
                $return = false;
            }
        }
        return $return;
    }

    /**
     * Handle granting of multiple groups
     *
     * @param array $groups Set of groups (either IDs or objects)
     * @param integer $expire EXpiration (unix timestamp) for the permissions
     *
     * @return boolean Success/fail of all saves
     */
    public function grantGroups(array $groups, ?int $expire = null): bool
    {
        $return = true;
        foreach ($groups as $group) {
            $group = ($group instanceof GroupModel) ? $group->id : $group;
            $data = [
                'userId' => $this->id,
                'groupId' => $group
            ];
            if ($expire !== null && is_int($expire)) {
                $data['expire'] = $expire;
            }
            $userGroup = new UserGroupModel($this->getDb(), $data);
            $result = $this->getDb()->save($userGroup);
            if ($result === false && $return === true) {
                $return = false;
            }
        }
        return $return;
    }

    /**
     * Add a new security question to the current user
     *
     * @param array $data Security question data
     *
     * @return boolean Result of save operation
     */
    public function addSecurityQuestion(array $data): bool
    {
        if (!isset($data['question']) || !isset($data['answer'])) {
            throw new InvalidArgumentException('Invalid question/answer data provided.');
        }

        // Ensure that the answer isn't the same as the user's password
        if (password_verify($data['answer'], $this->password ?? '') === true) {
            throw new InvalidArgumentException('Security question answer cannot be the same as password.');
        }

        $question = new SecurityQuestionModel($this->getDb(), array(
            'question' => $data['question'],
            'answer' => $data['answer'],
            'userId' => $this->id
        ));
        return $this->getDb()->save($question);
    }

    /**
     * Update the last login time for the current user
     *
     * @param integer $time Unix timestamp [optional]
     *
     * @return boolean Success/fail of update
     */
    public function updateLastLogin(?int $time = null): bool
    {
        $time = ($time !== null) ? $time : time();
        $this->lastLogin = date('Y-m-d H:i:s', $time);
        return $this->getDb()->save($this);
    }
}
