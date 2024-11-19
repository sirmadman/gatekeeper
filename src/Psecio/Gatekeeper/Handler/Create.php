<?php

namespace Psecio\Gatekeeper\Handler;

use Psecio\Gatekeeper\Handler;
use Psecio\Gatekeeper\Exception\ModelNotFoundException;

class Create extends Handler
{
    /**
    * Execute the object/record creation handling
    *
    * @throws \Psecio\Gatekeeper\Exception\ModelNotFoundException If model type is not found
    *
    * @return mixed Either model object instance or false on failure
    */
    public function execute(): object|bool
    {
        $args = $this->getArguments();
        $name = $this->getName();

        $model = '\\Psecio\\Gatekeeper\\' . str_replace('create', '', $name) . 'Model';
        if (class_exists($model) === true) {
            $instance = new $model($this->getDb(), $args[0]);
            $instance = $this->getDb()->save($instance);
            return $instance;
        } else {
            throw new ModelNotFoundException(
                'Model type ' . $model . ' could not be found'
            );
        }
        return false;
    }
}
