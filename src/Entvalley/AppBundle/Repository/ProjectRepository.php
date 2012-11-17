<?php

namespace Entvalley\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Entvalley\AppBundle\Service\ProjectStatsService;
use Doctrine\ORM\Query;
use Entvalley\AppBundle\Entity\User;
use Entvalley\AppBundle\Domain\Status;
use Entvalley\AppBundle\Entity\Company;
use Entvalley\AppBundle\Entity\Project;

class ProjectRepository extends EntityRepository
{
    public function findByNameAndCompany($name, Company $company)
    {
        $query = $this->getEntityManager()
            ->createQuery("SELECT p
                             FROM EntvalleyAppBundle:Project p
                            WHERE p.canonicalName = :name AND p.company = :company_id")
        ;

        $query->setParameter('name', $name);
        $query->setParameter('company_id', $company->getId());
        return $query->getOneOrNullResult();
    }

    public function findByUser(User $user)
    {
        $result = $this->_getQueryWithCounts($user);

        $projects = [];

        foreach ($result as $project) {
            $projectStatsService = $project[0]->getProjectStatsService();
            $projectStatsService->setInprogressNumber($project['in_progress']);
            $projectStatsService->setUnresolvedNumber($project['unresolved_total']);
            $projectStatsService->setTotalNumber($project['total']);
            $projects[] = $project[0];
        }

        return $projects;
    }

    public function populateWithStats(User $user, ProjectStatsService $projectStatsService)
    {
        $result = $this->_getQueryWithCounts($user, false);

        if ($result) {
            $projectStatsService->setInprogressNumber($result[0]['in_progress']);
            $projectStatsService->setUnresolvedNumber($result[0]['unresolved_total']);
            $projectStatsService->setTotalNumber($result[0]['total']);
        }
    }

    private function _getQueryWithCounts($user, $needEntity = true)
    {
        $company = $user->getCompany();
        $query = $this->getEntityManager()
            ->createQuery(
            "SELECT " . ($needEntity ? 'p, ' : '') . "(
                                  SELECT COUNT(t0.id)
                                    FROM EntvalleyAppBundle:Task t0
                                    WHERE t0.project = p.id AND (t0.status = :reopened_status OR t0.status = :accepted_status)
                                          AND t0.assignedTo = :assigned_to) AS in_progress,
                                 (SELECT COUNT(t1.id)
                                    FROM EntvalleyAppBundle:Task t1
                                   WHERE t1.project = p.id AND t1.status = :unresolved_status) AS unresolved_total,
                                 (SELECT COUNT(t2.id)
                                    FROM EntvalleyAppBundle:Task t2
                                   WHERE t2.project = p.id) AS total
                             FROM EntvalleyAppBundle:Project p
                            WHERE p.company = :company_id"
        );

        $query->setParameter('company_id', $company->getId());
        $query->setParameter('reopened_status', Status::REOPENED);
        $query->setParameter('accepted_status', Status::ACCEPTED);
        $query->setParameter('unresolved_status', Status::UNASSIGNED);
        $query->setParameter('assigned_to', $user->getId());
        $result = $query->getResult($needEntity ? Query::HYDRATE_OBJECT : Query::HYDRATE_ARRAY);
        return $result;
    }
}