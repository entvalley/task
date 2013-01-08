<?php

namespace Entvalley\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProjectInvitationRepository extends EntityRepository
{
    public function findByEmails($project, array $emails)
    {
        if (count($emails) === 0) {
            return array();
        }

        $qb = $this->createQueryBuilder('pi');
        $expr = $qb->expr()->andX(
            $qb->expr()->in('pi.inviteeEmail', ':invitee_emails'),
            $qb->expr()->eq('pi.project', ':project')
        );

        $qb->where($expr);
        $query = $qb->getQuery();
        $query->setParameter('invitee_emails', $emails);
        $query->setParameter('project', $project);
        return $query->getResult();
    }

    public function findByHash($company, $publicHash)
    {
        $query = $this->getEntityManager()
            ->createQuery("SELECT pi
                             FROM EntvalleyAppBundle:ProjectInvitation pi
                             JOIN pi.project p
                            WHERE p.company = :company_id AND pi.publicHash = :publicHash")
        ;

        $query->setParameter('company_id', $company->getId());
        $query->setParameter('publicHash', $publicHash);

        return $query->getOneOrNullResult();
    }
}