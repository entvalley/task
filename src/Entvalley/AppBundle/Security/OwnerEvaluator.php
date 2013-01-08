<?php

namespace Entvalley\AppBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Entvalley\AppBundle\Domain\IHaveOwner;

class OwnerEvaluator
{
    public function belongsTo($object, $user)
    {
        if (!($object instanceof IHaveOwner)) {
            return true;
        }

        if (!($user instanceof UserInterface)) {
            return false;
        }

        return $object->isBelongingTo($user);
    }
}