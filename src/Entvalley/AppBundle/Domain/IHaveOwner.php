<?php

namespace Entvalley\AppBundle\Domain;

use Entvalley\AppBundle\Entity\User;

interface IHaveOwner
{
    public function isBelongingTo(User $user);
}
