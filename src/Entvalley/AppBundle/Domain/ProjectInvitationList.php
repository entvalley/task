<?php

namespace Entvalley\AppBundle\Domain;

use Entvalley\AppBundle\Entity\ProjectInvitation;

class ProjectInvitationList
{
    private $invitations = array();

    /**
     * Adds invitations to the list. Duplicated emails and empty invitiation are skipped
     *
     * @param array $invitations
     */
    public function setInvitations(array $invitations)
    {
        foreach ($invitations as $newInvitation) {
            if (!($newInvitation instanceof ProjectInvitation)) {
                continue;
            }
            foreach ($this->invitations as $invitation) {
                if ($invitation->getInviteeEmail() === $newInvitation->getInviteeEmail()) {
                    continue 2;
                }
            }
            $this->invitations[] = $newInvitation;
        }
    }

    public function getInvitations()
    {
        return $this->invitations;
    }
}
