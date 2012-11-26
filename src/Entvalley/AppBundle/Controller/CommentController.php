<?php

namespace Entvalley\AppBundle\Controller;

use Entvalley\AppBundle\Entity\Comment;
use Entvalley\AppBundle\Domain\JsonEncoder;
use Entvalley\AppBundle\Form\CommentType;

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

    public function editAction(Comment $comment)
    {
        $em = $this->doctrine->getManager();

        $form = $this->formFactory->create(new CommentType(), $comment);

        if ('POST' === $this->request->getMethod()) {
            $form->bind($this->request);
            if ($form->isValid()) {
                $em->flush();
            }
        }

        $result = $this->viewContent(
            [
                'form' => $form->createView(),
                'comment' => $comment,
            ]
        );

        return $this->javascript($this->viewContent(
            [
                'comment' => $comment,
                'result' => JsonEncoder::encode($result)
            ],
            'js.twig'
        ));
    }
}
