<?php

namespace Psecio\Gatekeeper;

abstract class Restriction
{
    /**
     * Restriction configuration
     * @var array
     */
    private array $config = array();

    /**
     * Init the object and set the configuration
     *
     * @param array $config Configuration settings
     */
    public function __construct(array $config)
    {
        $this->setConfig($config);
    }

    /**
     * Set the configuration property
     *
     * @param array $config Configuration settings
     *
     * @return void
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * Get the confguration settings
     *
     * @return array Configuration settings
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Evaluate the restriction based on given data
     *
     * @return boolean Pass/fail of restriction
     */
    abstract public function evaluate(): bool;
}
