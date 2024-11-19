<?php

namespace Psecio\Gatekeeper\Collection;

use Modler\Collection;

class Mysql extends Collection
{
    /**
     * Current database object
     * @var object
     */
    private object $db;

    /**
     * Last database error
     * @var string
     */
    private string $lastError = '';

    /**
     * Current database table name
     * @var string
     */
    protected string $tableName;

    /**
     * Init the collection and set up the database instance
     *
     * @param object $db Database instance
     */
    public function __construct(object $db)
    {
        $this->setDb($db);
    }

    /**
     * Set the current DB object instance
     *
     * @param object $db Database object
     */
    public function setDb(object $db)
    {
        $this->db = $db;
    }

    /**
     * Get the current database object instance
     *
     * @return object Database instance
     */
    public function getDb(): object
    {
        return $this->db;
    }

    /**
     * Get the current model's table name
     *
     * @return string Table name
     */
    public function getTableName(): string
    {
        $dbConfig = $this->db->config;
        return (isset($dbConfig['prefix']))
            ? $dbConfig['prefix'] . '_' . $this->tableName : $this->tableName;
    }

    public function getPrefix(): string
    {
        $dbConfig = $this->db->config;
        return (isset($dbConfig['prefix'])) ? $dbConfig['prefix'] . '_' : '';
    }

    /**
     * Get the last error from the database requests
     *
     * @return string Error message
     */
    public function getLastError(): string
    {
        return $this->lastError;
    }
}
