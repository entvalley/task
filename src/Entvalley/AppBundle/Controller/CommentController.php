<?php

namespace Entvalley\AppBundle\Controller;

use Entvalley\AppBundle\Entity\Comment;

class CommentController extends Controller
{
    public function deleteAction(Comment $comment)
    {
        $em = $this->doctrine->getManager();

        $deletedId = $comment->getId();

        $em->remove($comment);
        $em->flush();

        return $this->javascript($this->viewContent(
            [
                'commentId' => $deletedId
            ],
            'js.twig'
        ));
    }
}
