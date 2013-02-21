<?php

namespace Entvalley\AppBundle\Tests\Entity;

use Entvalley\AppBundle\Entity\Comment;
use Entvalley\AppBundle\Entity\StatusChange;

class CommentTest extends \PHPUnit_Framework_TestCase
{
    public function testStatusChangeCanBeAttachedToComment()
    {
        $comment = new Comment();
        $statusChange = $this->getMock('Entvalley\AppBundle\Entity\StatusChange');

        $comment->setStatusChange($statusChange);

        $this->assertEquals($statusChange, $comment->getStatusChange());
        $this->assertTrue($comment->hasStatusChange(), "must be true because the comment has status change");
    }
}