<?php

namespace Entvalley\AppBundle\Tests\Domain;


use Entvalley\AppBundle\Entity\ProjectInvitation;
use Entvalley\AppBundle\Domain\ProjectInvitationList;

class ProjectInvitationListTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldRemoveDuplicates()
    {
        $duplicateEmail = 'duplicated@example.com';
        $firstInvite = new ProjectInvitation();
        $firstInvite->setInviteeEmail($duplicateEmail);
        $secondInvite = new ProjectInvitation();
        $secondInvite->setInviteeEmail($duplicateEmail);
        $thirdInvite = new ProjectInvitation();
        $thirdInvite->setInviteeEmail('unique@example.com');

        $list = new ProjectInvitationList();
        $list->setInvitations([$firstInvite, $secondInvite, $thirdInvite]);

        $this->assertCount(2, $list->getInvitations());
        $this->assertEquals([$firstInvite, $thirdInvite], $list->getInvitations());
    }

    public function testShouldSkipEmpty()
    {
        $list = new ProjectInvitationList();
        $list->setInvitations([null, new \stdClass()]);

        $this->assertCount(0, $list->getInvitations());
    }
}