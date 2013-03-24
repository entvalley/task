<?php

namespace Entvalley\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Entvalley\AppBundle\Domain\IProjectCollaboratorRepository;
use Entvalley\AppBundle\Entity\Project;
use Entvalley\AppBundle\Entity\User;

class ProjectCollaboratorRepository extends EntityRepository implements IProjectCollaboratorRepository
{
    public function findByUserAndProject(User $user, Project $project)
    {
        // NO DQL is allowed. see [#DDC-2032]
        return $this->findOneBy(
                   ['collaborator' => $user, 'project' => $project]
                );
    }
}