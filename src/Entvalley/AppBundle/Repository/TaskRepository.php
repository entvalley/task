<?php

namespace Entvalley\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Entvalley\AppBundle\Domain\TaskFilter;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Entvalley\AppBundle\Service\Pagination;
use Entvalley\AppBundle\Entity\Company;

class TaskRepository extends EntityRepository
{
    public function findByFilterAndCompany(Pagination $pagination, TaskFilter $filter, Company $company)
    {
        $query = $this->getEntityManager()
            ->createQuery("SELECT t, c, sc
                             FROM EntvalleyAppBundle:Task t
                             JOIN t.project p
                             LEFT JOIN t.comments c
                             LEFT JOIN c.statusChange sc
                            WHERE p.company = :company_id AND p.id = :project_id
                            ORDER BY t.createdAt DESC")
        ;

        $query->setParameter('company_id', $company->getId());
        $query->setParameter('project_id', $filter->getWithinProject()->getId());

        $result = new Paginator($query, true);
        $pagination->setTotal(count($result));
        $query->setFirstResult($pagination->getOffset());
        $query->setMaxResults($pagination->getPerPage());

        $tasks = array();
        foreach ($result as $task) {
            $tasks[] = $task;
        }
        return $tasks;
    }
/*
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
    }*/

}