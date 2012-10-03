<?php

namespace Entvalley\AppBundle\Domain;

class CompanyContext
{
    private $userContext;

    public function __construct(UserContext $userContext = null)
    {
        $this->userContext = $userContext;
    }

    public function getCompany()
    {
        $user = $this->userContext->getUser();
        return $user ? $user->getCompany() : null;
    }
}