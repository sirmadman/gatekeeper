<?php
namespace Psecio\Gatekeeper\Handler;

use Psecio\Gatekeeper\Gatekeeper as g;

class Delete extends \Psecio\Gatekeeper\Handler
{
    /**
     * Execute the deletion handling
     *
     * @return boolean Success/failure of delete
     */
    public function execute()
    {
        $args = $this->getArguments();
        $name = $this->getName();

        $model = g::buildModel($name, $args, 'delete');
        return $this->getDb()->delete($model);
	}
}