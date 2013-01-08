<?php

namespace Entvalley\AppBundle\Tests\Service\Mock;

class ProjectInvitationRepositoryMock
{
    private $presentInvitations;

    public function __construct(array $presentInvitations)
    {
        $this->presentInvitations = $presentInvitations;
    }

    public function findByEmails($project, array $emails)
    {
        return $this->presentInvitations;
    }
}
