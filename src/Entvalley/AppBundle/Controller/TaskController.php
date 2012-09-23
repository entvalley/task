<?php

namespace Entvalley\AppBundle\Controller;

use Mzz\MzzBundle\Controller\Controller;
use Entvalley\AppBundle\Component\HttpFoundation\JsonResponse;
use Entvalley\AppBundle\Domain\JsonEncoder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Entvalley\AppBundle\Entity\Factory\TaskFactory;
use Entvalley\AppBundle\Form\TaskType;
use Entvalley\AppBundle\Entity\Task;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class TaskController extends Controller
{
    public function indexAction()
    {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $taskRepository = $em->getRepository('Entvalley\AppBundle\Entity\Task');

        $tasks = $taskRepository->findNewOrAssignedTo($user);

        if ($this->getRequest()->isXmlHttpRequest()) {
            $serializer = $this->get('serializer');
            $serializer->setGroups(array('summary'));
            return JsonResponse::createWithSerializer($serializer, $tasks);
        } else {
            return $this->view(array(
                'tasks' => $tasks
            ));
        }
    }

    public function createAction()
    {
        $user = $this->getUser();


        $em = $this->getDoctrine()->getManager();

        $taskFactory = new TaskFactory;
        $task = $taskFactory->createFor($user);

        $form = $this->createForm(new TaskType(), $task);

        $request = $this->getRequest();

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $em->persist($task);
                $em->flush();
                $this->get('session')->setFlash('success', 'A new task has been saved!');

                return $this->redirect($this->url('app_task_list'));
            }
        }

        return $this->view(array(
            'form'  => $form->createView(),
            'task' => $task,
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function viewAction(Task $task)
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $serializer = $this->get('serializer');
            $serializer->setGroups(array('details', 'summary'));
            return JsonResponse::createWithSerializer($serializer, $task);
        } else {
            return $this->view(array(
                'task' => $task
            ));
        }
    }

    public function deleteAction(Task $task)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($task);
        $em->flush();

        $this->get('session')->setFlash('success', 'The task has been deleted!');

        return $this->redirect($this->url('app_task_list'));
    }
}
