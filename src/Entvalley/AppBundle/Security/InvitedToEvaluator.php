<?php

namespace Entvalley\AppBundle\Security;

use Entvalley\AppBundle\Domain\IHaveProject;
use Entvalley\AppBundle\Entity\Project;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class InvitedToEvaluator
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function invitedTo($object, $user)
    {
        if (!($user instanceof UserInterface)) {
            return false;
        }

        if ($object instanceof Project) {
            $project = $object;
        } elseif ($object instanceof IHaveProject) {
            $project = $object->getProject();
        } else {
            return false;
        }

        return true;

    }
}