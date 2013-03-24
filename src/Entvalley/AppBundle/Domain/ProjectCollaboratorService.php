<?php

namespace Entvalley\AppBundle\Domain;

use Entvalley\AppBundle\Entity\Project;
use Entvalley\AppBundle\Entity\User;

class ProjectCollaboratorService
{
    private $projectCollaboratorRepository;

    public function __construct(IProjectCollaboratorRepository $projectCollaboratorRepository)
    {
        $this->projectCollaboratorRepository = $projectCollaboratorRepository;
    }

    public function isCollaborator(User $user, Project $project)
    {
        $collaborator = $this->projectCollaboratorRepository->findByUserAndProject($user, $project);
        return !empty($collaborator);
    }
}