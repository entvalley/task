<?php
namespace Entvalley\AppBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Entvalley\AppBundle\Domain\IHaveOwner;
use Doctrine\ORM\Mapping as ORM;

class User extends BaseUser implements IHaveOwner
{
    protected $id;
    private $company;

    public function __construct()
    {
        parent::__construct();
    }

    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    public function isBelongingTo(User $user)
    {
        // since users are owners of all objects, they don't have owners and belong to anyone
        return true;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    public function getCompanyId()
    {
        $company = $this->getCompany();
        return $company ? $company->getId() : null;
    }
}
