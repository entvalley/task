<?php

namespace Entvalley\AppBundle\Domain;

use Symfony\Component\Security\Core\SecurityContextInterface;

class UserContext
{
    private $context;

    public function __construct(SecurityContextInterface $context)
    {
        $this->context = $context;
    }

    public function getUser()
    {
        if (null === $token = $this->context->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }
}
