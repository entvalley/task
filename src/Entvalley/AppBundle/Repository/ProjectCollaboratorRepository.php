<?php

namespace Entvalley\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Entvalley\AppBundle\Domain\IProjectCollaboratorRepository;
use Entvalley\AppBundle\Entity\Project;
use Entvalley\AppBundle\Entity\User;

class ProjectCollaboratorRepository extends EntityRepository implements IProjectCollaboratorRepository
{
    public function findByUserAndProject(User $user, Project $project)
    {
        $query = $this->getEntityManager()
            ->createQuery("SELECT pc
                             FROM EntvalleyAppBundle:ProjectCollaborator pc
                            WHERE pc.collaborator = :user AND pc.project = :project")
        ;

        $query->setParameter('user', $user);
        $query->setParameter('project', $project);

        return $query->getOneOrNullResult();
    }
}