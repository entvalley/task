<?php

namespace Entvalley\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Entvalley\AppBundle\Domain\TaskFilter;
use Entvalley\AppBundle\Entity\User;

class TaskRepository extends EntityRepository
{
    public function findWithFilterNewOrAssignedTo(TaskFilter $filter, User $user)
    {
        $query = $this->getEntityManager()
            ->createQuery("SELECT t
                             FROM EntvalleyAppBundle:Task t
                             JOIN t.project p
                            WHERE (t.assignedTo IS NULL OR t.assignedTo = :user)
                                  AND p.company = :company_id AND p.id = :project_id
                            ORDER BY t.createdAt DESC")
        ;

        $query->setParameter('user', $user->getId());
        $query->setParameter('company_id', $user->getCompany()->getId());
        $query->setParameter('project_id', $filter->getWithinProject()->getId());
        return $query->getResult();
    }

    public function findWithFilterByCompany(TaskFilter $filter, $company)
    {
        $statuses = $filter->getStatuses();
        $qb = $this->createQueryBuilder('t');

        $companyExpr = $qb->expr()->eq('p.company', ':company_id');

        if (empty($statuses)) {
           $qb->where($companyExpr);
        } else {
            $qb->where($qb->expr()->andX(
                    $companyExpr,
                    $qb->expr()->in('t.status', $statuses)
                ));
        }

        $qb->orderBy('t.createdAt', 'DESC');
        $qb->join('t.project', 'p');
        $query = $qb->getQuery();
        $query->setParameter('company_id', $company->getId());
        return $query->getResult();
    }

}