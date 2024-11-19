<?php

namespace Psecio\Gatekeeper\Handler;

use Psecio\Gatekeeper\Handler;
use Psecio\Gatekeeper\Gatekeeper as g;

class Delete extends Handler
{
    /**
    * Execute the deletion handling
    *
    * @return boolean Success/failure of delete
    */
    public function execute(): bool
    {
        $args = $this->getArguments();
        $name = $this->getName();

        $model = g::buildModel($name, $args, 'delete');
        return $this->getDb()->delete($model);
    }
}
