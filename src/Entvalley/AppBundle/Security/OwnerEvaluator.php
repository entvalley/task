<?php

namespace Entvalley\AppBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Entvalley\AppBundle\Domain\IHaveOwner;

class OwnerEvaluator
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function belongsTo($object, $user)
    {
        if (!($object instanceof IHaveOwner)) {
            return false;
        }

        if (!($user instanceof UserInterface)) {
            return false;
        }

        return $object->isBelongingTo($user);
    }
}