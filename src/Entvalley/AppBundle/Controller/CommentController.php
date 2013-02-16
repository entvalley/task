<?php

namespace Entvalley\AppBundle\Controller;

use Entvalley\AppBundle\Entity\Comment;
use Entvalley\AppBundle\Domain\JsonEncoder;
use Entvalley\AppBundle\Form\CommentType;


class CommentController extends Controller
{
    private $htmlPurifier;

    public function __construct(ControllerContainer $container, $htmlPurifier)
    {
        $this->htmlPurifier = $htmlPurifier;
        parent::__construct($container);
    }

    public function deleteAction(Comment $comment)
    {
        $em = $this->container->getDoctrine()->getManager();

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
        $em = $this->container->getDoctrine()->getManager();

        $form = $this->container->getFormFactory()->create(new CommentType(), $comment);
        $comment->setHtmlPurifier($this->htmlPurifier);

        if ($this->bindRequestToFormAndValidateIt($form)) {
            $em->flush();
            $comment->purifyHtmlTags();
            return $this->javascript(
                $this->renderView(
                    'EntvalleyAppBundle:Comment:edit_success.html.twig',
                    array(
                        'comment' => $comment,
                        'comment_text' => JsonEncoder::encode($comment->getText()),
                        'comment_safe_text' => JsonEncoder::encode($comment->getSafeText()),
                    )
                )
            );
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
