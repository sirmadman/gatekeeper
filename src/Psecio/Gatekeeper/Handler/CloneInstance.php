<?php

namespace Psecio\Gatekeeper\Handler;

use Psecio\Gatekeeper\Gatekeeper;
use Psecio\Gatekeeper\UserModel;
use Psecio\Gatekeeper\Handler;

class CloneInstance extends Handler
{
    /**
    * Execute the object/record clone handling
    *
    * @return boolean Success/fail of user cloning
    */
    public function execute(): bool
    {
        $args = $this->getArguments();
        $name = $this->getName();
        $method = ucwords($name);

        if (method_exists($this, $method) === true) {
            return $this->$method($args[0], $args[1]);
        }
        return false;
    }

    /**
    * Clone user
    *
    * @param mixed $user
    * @param array $data
    * @return bool
    */
    public function cloneUser(string $user, array $data): bool
    {
        $ds = Gatekeeper::getDatasource();
        $newUser = new UserModel($ds, $data);
        $result = $newUser->save();

        if ($result == false) {
            return false;
        }

        // Get the user's groups and add
        foreach ($user->groups as $group) {
            $newUser->addGroup($group);
        }

        // Get the user's permissions and add
        foreach ($user->permissions as $permission) {
            $newUser->addPermission($permission);
        }

        return true;
    }
}
