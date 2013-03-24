<?php

namespace Entvalley\AppBundle\Security;

use Entvalley\AppBundle\Domain\IHaveProject;
use Entvalley\AppBundle\Domain\ProjectCollaboratorService;
use Entvalley\AppBundle\Entity\Project;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class IsCollaboratorEvaluator
{
    private $projectCollaboratorService;

    public function __construct(ProjectCollaboratorService $projectCollaboratorService)
    {
        $this->projectCollaboratorService = $projectCollaboratorService;
    }

    public function isCollaborator($user, $object)
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

        return $this->projectCollaboratorService->isCollaborator($user, $project);
    }
}