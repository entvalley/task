<?php

namespace Entvalley\AppBundle\Security\Authorization\Voter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Entvalley\AppBundle\Domain\IHaveOwner;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class OwnerVoter implements VoterInterface
{
    public function __construct(ContainerInterface $container, $map)
    {
        $this->container = $container;
        $this->map = $map;
    }

    public function supportsAttribute($attribute)
    {
        echo $this->map->contains((string)$attribute);
        return $attribute === 'VIEW';
    }

    public function supportsClass($class)
    {
        return 'Entvalley\AppBundle\Domain\IHaveOwner';
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        return VoterInterface::ACCESS_ABSTAIN;
        if (!($object instanceof IHaveOwner)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (!$this->supportsAttribute($attributes[0])) {
            var_dump($attributes);
            return VoterInterface::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();
        if (!($user instanceof UserInterface)) {
            return VoterInterface::ACCESS_DENIED;
        }

        echo get_class($object);
        $request = $this->container->get('request');
        return VoterInterface::ACCESS_GRANTED;

        return VoterInterface::ACCESS_ABSTAIN;
    }
}