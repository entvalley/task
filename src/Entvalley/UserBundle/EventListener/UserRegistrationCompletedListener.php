<?php

namespace Entvalley\UserBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Entvalley\AppBundle\Entity\Company;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserRegistrationCompletedListener implements EventSubscriberInterface
{
    private $objectManager;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted',
        );
    }

    public function onRegistrationCompleted(UserEvent $event)
    {
        $user = $event->getUser();
        $company = new Company();
        $company->setName($user->getUsername());
        $company->setOwner($user);
        $user->setCompany($company);

        $this->objectManager->persist($company);
        $this->objectManager->flush();
    }
}
