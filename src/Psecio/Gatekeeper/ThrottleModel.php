<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\Model\Mysql;
use DateTime;

/**
* Throttle class
*
* @property string $id
* @property string $userid
* @property string $attempts
* @property string $status
* @property string $lastAttempt
* @property string $statusChange
* @property string $created
* @property string $updated
*/
class ThrottleModel extends Mysql
{
    /**
     * Database table name
     * @var string
     */
    protected string $tableName = 'throttle';

    /**
     * Status constants
     */
    public const STATUS_ALLOWED = 'allowed';
    public const STATUS_BLOCKED = 'blocked';

    /**
     * Default timeout time
     * @var string
     */
    protected string $timeout = '-1 minute';

    /**
     * Default number of attempts before blocking
     * @var integer
     */
    protected int $allowedAttemts = 5;

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
        'userId' => array(
            'description' => 'User ID',
            'column' => 'user_id',
            'type' => 'integer'
        ),
        'attempts' => array(
            'description' => 'Number of Attempts',
            'column' => 'attempts',
            'type' => 'integer'
        ),
        'status' => array(
            'description' => 'Throttle status',
            'column' => 'status',
            'type' => 'string'
        ),
        'lastAttempt' => array(
            'description' => 'Last Attempt',
            'column' => 'last_attempt',
            'type' => 'datetime'
        ),
        'statusChange' => array(
            'description' => 'Date of Last Status Change',
            'column' => 'status_change',
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

    /**
     * Find the throttle information for the given user ID
     *
     * @param integer $userId User ID
     *
     * @return object Either a collection or model instance
     */
    public function findByUserId($userId): object
    {
        return $this->getDb()->find(
            $this,
            array('user_id' => $userId)
        );
    }

    /**
     * Update the number of attempts for the current record
     *
     * @return boolean Success/fail of save operation
     */
    public function updateAttempts(): bool
    {
        $this->lastAttempt = date('Y-m-d H:i:s');
        $this->attempts = $this->attempts + 1;
        return $this->getDb()->save($this);
    }

    /**
     * Mark a user as allowed (status change)
     *
     * @return boolean Success/fail of save operation
     */
    public function allow(): bool
    {
        $this->statusChange = date('Y-m-d H:i:s');
        $this->attempts = 0;
        $this->status = self::STATUS_ALLOWED;
        return $this->getDb()->save($this);
    }

    // See how long it was since the last change (to blocked)
    /**
     * Check the timeout to see if it has passed
     *
     * @param string $timeout Alternative timeout string (ex: "-1 minute")
     *
     * @return boolean True if user is reendabled, false if still disabled
     */
    public function checkTimeout(?string $timeout = null): bool
    {
        $timeout = $timeout ?? $this->timeout;

        $lastChange = new DateTime($this->statusChange);
        $timeout = new DateTime($timeout);

        if ($lastChange <= $timeout) {
            $this->allow();
            return true;
        }
        return false;
    }

    /**
     * Check the number of attempts to see if it meets the threshold
     *
     * @return boolean False if they were blocked, true otherwise
     */
    public function checkAttempts(): bool
    {
        if ($this->attempts >= $this->allowedAttemts) {
            $this->status = self::STATUS_BLOCKED;
            return $this->getDb()->save($this);
        }
        return true;
    }
}
