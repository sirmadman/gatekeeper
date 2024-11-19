<?php

namespace Psecio\Gatekeeper;

use Psecio\Gatekeeper\DataSource;

abstract class Handler
{
    /**
     * Method name called for handler type
     * @var string
     */
    protected string $name;

    /**
     * Arguments called to pass into handler
     * @var array
     */
    protected array $arguments = array();

    /**
     * Data source instance
     * @var \Psecio\Gatekeeper\DataSource
     */
    protected Datasource $datasource;

    /**
     * Init the object and set up the name, arguments and data source
     *
     * @param string $name Method name called
     * @param array $arguments Arguments to pass to handler
     * @param \Psecio\Gatekeeper\DataSource $datasource Data source instance
     */
    public function __construct(string $name, array $arguments, DataSource $datasource)
    {
        $this->setArguments($arguments);
        $this->setName($name);
        $this->setDb($datasource);
    }

    /**
     * Set the current arguments
     *
     * @param array $arguments Method arguments
     *
     * @return void
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * Get the current set of arguments
     *
     * @return array Arguemnt data set
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Set method name called for handler
     *
     * @param string $name Method name called
     *
     * @return void
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * Get the method name called
     *
     * @return string Method name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the current data source
     *
     * @param \Psecio\Gatekeeper\DataSource $datasource data source instance (DB)
     *
     * @return void
     */
    public function setDb(DataSource $datasource): void
    {
        $this->datasource = $datasource;
    }

    /**
     * Get the current data source instance
     *
     * @return \Psecio\Gatekeeper\DataSource instance
     */
    public function getDb(): DataSource
    {
        return $this->datasource;
    }

    /**
     * Execute the handler logic
     *
     * @return mixed
     */
    abstract public function execute();
}
