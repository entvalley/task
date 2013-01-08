<?php

namespace Entvalley\AppBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Security\Authorization\Expression\Expression;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\DependencyInjection\Container;
use Entvalley\AppBundle\Entity\Project;

class AclListener
{
    private $securityContext;

    public function __construct(SecurityContextInterface $securityContext, $aclProvider)
    {
        $this->securityContext = $securityContext;

        $this->aclProvider = $aclProvider;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        /*
         * if no token found, we should rely on the firewall rules
         */
        if ($this->securityContext->getToken() == null) {
            return $entity;
        }

        $user = $this->securityContext->getToken()->getUser();

        if ($entity instanceof UserInterface && $user->isUser($entity)) {
            return $entity;
        }

        if (!$this->securityContext->isGranted(new Expression('belongsTo(object, user) or invitedTo(object, user)'), $args->getEntity())) {
            throw new AccessDeniedException("insufficient privileges to view the object of class: " . get_class($entity));
        }

        return $entity;
    }

    public function preUpdate(LifecycleEventArgs $args)
    {

    }

    public function preRemove(LifecycleEventArgs $args)
    {

    }

    public function preLoad(LifecycleEventArgs $args)
    {echo 2;
        return;
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof Project) {
            $user = $this->container->get('context.user')->getUser();
            $callback = function ($projectStatsService) use ($user, $entityManager, $entity) {
                $entityManager->getRepository(get_class($entity))->populateWithStats($user, $projectStatsService);
            };
            $entity->getProjectStatsService()->setLoadCallback($callback);
        }
    }
}
