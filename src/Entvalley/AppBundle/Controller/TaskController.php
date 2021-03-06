<?php

namespace Entvalley\AppBundle\Controller;

use Entvalley\AppBundle\Domain\TaskFilter;
use Entvalley\AppBundle\Entity\Project;
use Entvalley\AppBundle\Component\HttpFoundation\JsonResponse;
use Entvalley\AppBundle\Domain\JsonEncoder;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Entvalley\AppBundle\Entity\Factory\TaskFactory;
use Entvalley\AppBundle\Form\TaskType;
use Entvalley\AppBundle\Entity\Task;
use Entvalley\AppBundle\Service\Pagination;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;
use Entvalley\AppBundle\Service\PaginationRouterUrlGenerator;

class TaskController extends Controller
{
    /**
     * @var \JMS\Serializer\SerializerInterface
     */
    private $serializer;
    /**
     * @var \JMS\Serializer\SerializationContext
     */
    private $serializationContext;
    private $htmlPurifier;

    public function __construct(
        ControllerContainer $container,
        SerializerInterface $serializer,
        SerializationContext $serializationContext,
        $htmlPurifier)
    {
        $this->serializer = $serializer;
        $this->htmlPurifier = $htmlPurifier;
        $this->serializationContext = $serializationContext;
        parent::__construct($container);
    }

    /**
     * @param Project $project
     * @param string $filter filter by task status
     * @return \Entvalley\AppBundle\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Project $project, $filterByType = null)
    {
        $user = $this->container->getUserContext()->getUser();

        $em = $this->container->getDoctrine()->getManager();

        $taskRepository = $em->getRepository('Entvalley\AppBundle\Entity\Task');

        $page = $this->container->getRequest()->query->get('page', 1);
        $pagination = new Pagination(3, new PaginationRouterUrlGenerator($this->container->getRouter(), $this->container->getRequest()));
        $pagination->setCurrentPage($page);

        $taskFilter = new TaskFilter();
        $taskFilter->withinProject($project);
        if (!is_null($filterByType)) {
            $taskFilter->thatAre($filterByType);
        }
        $tasks = $taskRepository->findByFilterAndCompany($pagination, $taskFilter, $user->getCompany());


        if ($this->container->getRequest()->isXmlHttpRequest()) {
            foreach ($tasks as $task) {
                $task->setHtmlPurifier($this->htmlPurifier);
            }

            $this->serializationContext->setGroups(['summary']);

            return [
                'tasks' => $tasks,
                'pagination' => [
                    'last' => $pagination->getLastPage(),
                    'next' => $pagination->getNextUrl(),
                    'previous' => $pagination->getPreviousUrl(),
                    'current' => $pagination->getCurrentPage()
                ]
            ];
        } else {
            return $this->view([
                'tasks' => $tasks,
                'project' => $project
            ]);
        }
    }

    public function createAction()
    {
        $user = $this->container->getUserContext()->getUser();
        $em = $this->container->getDoctrine()->getManager();

        $taskFactory = new TaskFactory;
        $task = $taskFactory->createFor($user);

        $form = $this->container->getFormFactory()->create(new TaskType(), $task);

        if ($this->bindRequestToFormAndValidateIt($form)) {
            $em->persist($task);
            $em->flush();

            return $this->redirect($this->url('app_task_list'));
        }

        return $this->view([
            'form'  => $form->createView(),
            'task' => $task,
        ]);
    }

    public function editAction(Task $task)
    {
        $em = $this->container->getDoctrine()->getManager();

        $form = $this->container->getFormFactory()->create(new TaskType(), $task);

        if ($this->bindRequestToFormAndValidateIt($form)) {
            $em->flush();

            return $this->redirect(
                $this->url(
                    'app_task_view',
                    [
                        'id' => $task->getId(),
                        'project' => $task->getProject()->getId(),
                        'project_name' => $task->getProject()->getCanonicalName()
                    ]
                )
            );
        }

        $result = $this->viewContent(
            [
                'form' => $form->createView(),
                'task' => $task,
            ]
        );

        return $this->javascript($this->viewContent(
            [
                'task' => $task,
                'result' => JsonEncoder::encode($result)
            ],
            'js.twig'
        ));
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function viewAction(Task $task)
    {
        if ($this->container->getRequest()->isXmlHttpRequest()) {
            $task->setHtmlPurifier($this->htmlPurifier);
            $this->serializationContext->setGroups(['details', 'summary']);
            return $task;
        } else {
            return $this->view([
                'task' => $task,
                'project' => $task->getProject()
            ]);
        }
    }

    public function deleteAction(Task $task)
    {
        $em = $this->container->getDoctrine()->getManager();

        $em->remove($task);
        $em->flush();

        return $this->redirect($this->url('app_task_list'));
    }
}
