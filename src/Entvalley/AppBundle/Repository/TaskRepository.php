<?php

namespace Entvalley\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Entvalley\AppBundle\Entity\User;

class TaskRepository extends EntityRepository
{
    public function findNewOrAssignedTo(User $user)
    {

        $query = $this->getEntityManager()
            ->createQuery("SELECT t
                             FROM EntvalleyAppBundle:Task t
                            WHERE (t.assignedTo IS NULL OR t.assignedTo = :user)
                                  AND t.company = :company_id
                            ORDER BY t.createdAt DESC")
        ;

        $query->setParameter('user', $user->getId());
        $query->setParameter('company_id', $user->getCompany()->getId());
        return $query->getResult();
    }
}