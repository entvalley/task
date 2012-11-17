<?php

namespace Entvalley\AppBundle\Controller;

use Entvalley\AppBundle\Domain\CompanyContext;
use Entvalley\AppBundle\Domain\UserContext;
use Entvalley\AppBundle\Form\ProjectType;
use Entvalley\AppBundle\Entity\Project;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;

class ProjectController extends Controller
{
    private $companyContext;

    public function __construct(Request $request,
        RouterInterface $router,
        $templating,
        SessionInterface $session,
        RegistryInterface $doctrine,
        FormFactoryInterface $formFactory,
        UserContext $userContext,
        CompanyContext $companyContext)
    {
        $this->companyContext = $companyContext;
        parent::__construct($request, $router, $templating, $session, $doctrine, $formFactory, $userContext);
    }

    public function navigationAction($project = null)
    {
        $em = $this->doctrine->getManager();
        $projectRepository = $em->getRepository('Entvalley\AppBundle\Entity\Project');

        $projects = $projectRepository->findByUser($this->userContext->getUser());

        return $this->view([
                'projects' => $projects,
                'current_project' => $project
            ]);
    }

    public function indexAction()
    {
        $em = $this->doctrine->getManager();
        $projectRepository = $em->getRepository('Entvalley\AppBundle\Entity\Project');

        $projects = $projectRepository->findByUser($this->userContext->getUser());

        return $this->view([
                'projects' => $projects
            ]);
    }

    public function createAction()
    {
        $em = $this->doctrine->getManager();

        $project = new Project();
        $project->setCompany($this->companyContext->getCompany());

        $form = $this->formFactory->create(new ProjectType(), $project);

        if ('POST' === $this->request->getMethod()) {
            $form->bind($this->request);

            if ($form->isValid()) {
                $em->persist($project);
                $em->flush();

                return $this->javascript(
                    $this->renderView('EntvalleyAppBundle:Project:create_success.html.twig', ['project' => $project])
                );
            }
        }

        return $this->view([
            'form'  => $form->createView(),
            'project' => $project,
        ]);
    }
}
