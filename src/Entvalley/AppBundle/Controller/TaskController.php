<?php

namespace Entvalley\AppBundle\Controller;

use Entvalley\AppBundle\Domain\TaskFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Entvalley\AppBundle\Domain\UserContext;
use JMS\SerializerBundle\Serializer\SerializerInterface;
use Entvalley\AppBundle\Domain\Status;
use Entvalley\AppBundle\Component\HttpFoundation\JsonResponse;
use Entvalley\AppBundle\Domain\JsonEncoder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Entvalley\AppBundle\Entity\Factory\TaskFactory;
use Entvalley\AppBundle\Form\TaskType;
use Entvalley\AppBundle\Entity\Task;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class TaskController extends Controller
{
    private $serializer;
    private $userContext;

    public function __construct(Request $request,
        RouterInterface $router,
        $templating,
        SessionInterface $session,
        RegistryInterface $doctrine,
        FormFactoryInterface $formFactory,
        SerializerInterface $serializer,
        UserContext $userContext)
    {
        $this->serializer = $serializer;
        $this->userContext = $userContext;
        parent::__construct($request, $router, $templating, $session, $doctrine, $formFactory);
    }

    /**
     * @param string $filter filter by task status
     * @return \Entvalley\AppBundle\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($filter = null)
    {
        $user = $this->userContext->getUser();

        $em = $this->doctrine->getManager();

        $taskRepository = $em->getRepository('Entvalley\AppBundle\Entity\Task');
        if (!is_null($filter)) {
            $taskFilter = new TaskFilter();
            $taskFilter->thatAre($filter);
            $tasks = $taskRepository->findWithFilterForCompany($taskFilter, $user->getCompany());
        } else {
            $filter = null;
            $tasks = $taskRepository->findNewOrAssignedTo($user);
        }

        if ($this->request->isXmlHttpRequest()) {
            $this->serializer->setGroups(array('summary'));
            return JsonResponse::createWithSerializer($this->serializer, $tasks);
        } else {
            return $this->view(array(
                'tasks' => $tasks
            ));
        }
    }

    public function createAction()
    {
        $user = $this->userContext->getUser();
        $em = $this->doctrine->getManager();

        $taskFactory = new TaskFactory;
        $task = $taskFactory->createFor($user);

        $form = $this->formFactory->create(new TaskType(), $task);

        if ('POST' === $this->request->getMethod()) {
            $form->bind($this->request);

            if ($form->isValid()) {
                $em->persist($task);
                $em->flush();
                $this->session->getFlashBag()->add('success', 'A new task has been saved!');

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
        if ($this->request->isXmlHttpRequest()) {
            $this->serializer->setGroups(array('details', 'summary'));
            return JsonResponse::createWithSerializer($this->serializer, $task);
        } else {
            return $this->view(array(
                'task' => $task
            ));
        }
    }

    public function deleteAction(Task $task)
    {
        $em = $this->doctrine->getManager();

        $em->remove($task);
        $em->flush();

        $this->session->getFlashBag()->add('success', 'The task has been deleted!');

        return $this->redirect($this->url('app_task_list'));
    }
}
