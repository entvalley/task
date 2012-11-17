<?php

namespace Entvalley\AppBundle\Controller;

use Entvalley\AppBundle\Entity\Comment;

class CommentController extends Controller
{
    public function deleteAction(Comment $comment)
    {
        $em = $this->doctrine->getManager();

       // $em->remove($comment);
        $em->flush();

        return $this->javascript($this->viewContent(
            [
                'comment' => $comment
            ],
            'js.twig'
        ));
    }
}
