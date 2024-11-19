<?php

namespace Psecio\Gatekeeper\DataSource;

use Psecio\Gatekeeper\DataSource;
use Modler\Model;
use Modler\Collection;

class Stub extends DataSource
{
    /**
     * Save the given model
     *
     * @param \Modler\Model $model Model instance
     *
     * @return boolean Success/fail of action
     */
    public function save(Model $model): bool
    {
    }

    /**
     * Create a new record with model given
     *
     * @param \Modler\Model $model Model instance
     *
     * @return boolean Success/fail of action
     */
    public function create(Model $model): bool
    {
    }

    /**
     * Update the record for the given model
     *
     * @param \Modler\Model $model Model instance
     *
     * @return boolean Success/fail of action
     */
    public function update(Model $model): bool
    {
    }

    /**
     * Delete the record defined by the model data
     *
     * @param \Modler\Model $model Model instance
     *
     * @return boolean Success/fail of action
     */
    public function delete(Model $model): bool
    {
    }

    /**
     * Find and populate a model based on the model type and where criteria
     *
     * @param \Modler\Model $model Model instance
     * @param array $where "Where" data to locate record
     *
     * @return object Either a collection or model instance
     */
    public function find(Model $model, array $where = array()): object|bool
    {
    }

    /**
     * Return the number of entities in DB per condition or in general
     *
     * @param \Modler\Model $model Model instance
     * @param array $where
     *
     * @return bool Success/fail of action
     * @internal param array $where "Where" data to locate record
     */
    public function count(Model $model, array $where = array()): bool
    {
    }

    /**
     * Return the last error from action taken on the datasource
     *
     * @return string Error string
     */
    public function getLastError(): string
    {
    }

    /**
     * Fetch the data from the source
     *
     * @return array
     */
    public function fetch(): array
    {
    }
}
