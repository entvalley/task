<?php

namespace Entvalley\UserBundle\Doctrine;

use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use Entvalley\AppBundle\Entity\User;
use Entvalley\AppBundle\Entity\Company;

class UserManager extends BaseUserManager
{
    public function createCompanyForUser(User $user)
    {
        $company = new Company();
        $company->setName($user->getUsername());
        $company->setOwner($user);
        $user->setCompany($company);

        $this->objectManager->persist($company);
        $this->objectManager->flush();
    }
}
