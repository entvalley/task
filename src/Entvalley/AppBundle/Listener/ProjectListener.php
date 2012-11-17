<?php

namespace Entvalley\AppBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\Container;
use Entvalley\AppBundle\Entity\Project;

class ProjectListener
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
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
