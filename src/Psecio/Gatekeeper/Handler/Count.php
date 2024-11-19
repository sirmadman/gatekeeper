<?php

namespace Psecio\Gatekeeper\Handler;

use Psecio\Gatekeeper\Handler;
use Psecio\Gatekeeper\Exception\ModelNotFoundException;

class Count extends Handler
{
    /**
    * Execute the object/record count handling
    *
    * @throws \Psecio\Gatekeeper\Exception\ModelNotFoundException If model type is not found
    *
    * @return int Count of entities
    */
    public function execute(): int
    {
        $args = $this->getArguments();
        $name = $this->getName();

        $model = '\\Psecio\\Gatekeeper\\' . str_replace(
            'count',
            '',
            $name
        ) . 'Model';
        if (class_exists($model) === true) {
            $instance = new $model($this->getDb());

            $count = (!$args) ? $this->getDb()->count($instance) : $this->getDb()->count(
                $instance,
                $args[0]
            );
            return intval($count['count']);
        } else {
            throw new ModelNotFoundException(
                'Model type ' . $model . ' could not be found'
            );
        }
    }
}
