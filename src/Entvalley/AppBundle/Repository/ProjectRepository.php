<?php

namespace Entvalley\AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Entvalley\AppBundle\Entity\Company;

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

}