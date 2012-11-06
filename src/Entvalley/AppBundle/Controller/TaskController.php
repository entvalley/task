<?php

namespace Entvalley\AppBundle\Controller;

use Entvalley\AppBundle\Domain\TaskFilter;
use Entvalley\AppBundle\Entity\Project;
use Entvalley\AppBundle\Service\ContentNegotiator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Entvalley\AppBundle\Domain\UserContext;
use JMS\SerializerBundle\Serializer\SerializerInterface;
use Entvalley\AppBundle\Component\HttpFoundation\JsonResponse;
use Entvalley\AppBundle\Domain\JsonEncoder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Entvalley\AppBundle\Entity\Factory\TaskFactory;
use Entvalley\AppBundle\Form\TaskType;
use Entvalley\AppBundle\Entity\Task;

class TaskController extends Controller
{
    private $serializer;
    private $userContext;
    private $negotiator;
    private $htmlPurifier;

    public function __construct(Request $request,
        RouterInterface $router,
        $templating,
        SessionInterface $session,
        RegistryInterface $doctrine,
        FormFactoryInterface $formFactory,
        SerializerInterface $serializer,
        UserContext $userContext,
        ContentNegotiator $negotiator,
        $htmlPurifier)
    {
        $this->serializer = $serializer;
        $this->userContext = $userContext;
        $this->negotiator = $negotiator;
        $this->htmlPurifier = $htmlPurifier;
        parent::__construct($request, $router, $templating, $session, $doctrine, $formFactory);
    }

    /**
     * @param Project $project
     * @param string $filter filter by task status
     * @return \Entvalley\AppBundle\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Project $project, $filterByType = null)
    {
        $user = $this->userContext->getUser();

        $em = $this->doctrine->getManager();

        $taskRepository = $em->getRepository('Entvalley\AppBundle\Entity\Task');

        if (!$project->belongsToCompany($user->getCompany())) {
            throw new NotFoundHttpException();
        }

        $taskFilter = new TaskFilter();
        $taskFilter->withinProject($project);
        if (!is_null($filterByType)) {
            $taskFilter->thatAre($filterByType);
            $tasks = $taskRepository->findWithFilterByCompany($taskFilter, $user->getCompany());
        } else {
            $tasks = $taskRepository->findWithFilterNewOrAssignedTo($taskFilter, $user);
        }


        if ($this->request->isXmlHttpRequest()) {
            $this->serializer->setGroups(['summary']);
            return JsonResponse::createWithSerializer($this->serializer, array_map(function ($task) {
                        $task->setPurifier($this->htmlPurifier);
                        return $task;
                    }, $tasks));
        } else {
            return $this->view([
                'tasks' => $tasks,
                'project' => $project
            ]);
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

        return $this->view([
            'form'  => $form->createView(),
            'task' => $task,
        ]);
    }

    public function editAction(Task $task)
    {
        //var_dump($this->negotiator->getPreferredType($this->request->headers->get('accept')));

        $em = $this->doctrine->getManager();

        $form = $this->formFactory->create(new TaskType(), $task);

        if ('POST' === $this->request->getMethod()) {
            $form->bind($this->request);
            if ($form->isValid()) {
                $em->flush();
                $this->session->getFlashBag()->add('success', 'A new task has been saved!');

                return $this->redirect($this->url('app_task_view', [
                            'id' => $task->getId(),
                            'project' => $task->getProject()->getId(),
                            'project_name' => $task->getProject()->getCanonicalName()
                        ]));
            }
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
        if ($this->request->isXmlHttpRequest()) {
            $this->serializer->setGroups(['details', 'summary']);
            $task->setPurifier($this->htmlPurifier);
            return JsonResponse::createWithSerializer($this->serializer, $task);
        } else {
            return $this->view([
                'task' => $task,
                'project' => $task->getProject()
            ]);
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
