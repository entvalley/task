<?php

namespace Entvalley\AppBundle\Controller;

use Entvalley\AppBundle\Domain\CompanyContext;
use Entvalley\AppBundle\Form\ProjectType;
use Entvalley\AppBundle\Entity\Project;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends Controller
{
    private $companyContext;

    public function __construct(
        ControllerContainer $container,
        CompanyContext $companyContext)
    {
        $this->companyContext = $companyContext;
        parent::__construct($container);
    }

    public function navigationAction($project = null)
    {
        $em = $this->container->getDoctrine()->getManager();
        $projectRepository = $em->getRepository('Entvalley\AppBundle\Entity\Project');

        $projects = $projectRepository->findByUser($this->container->getUserContext()->getUser());

        return $this->view([
                'projects' => $projects,
                'current_project' => $project
            ]);
    }

    public function indexAction()
    {
        $em = $this->container->getDoctrine()->getManager();
        $projectRepository = $em->getRepository('Entvalley\AppBundle\Entity\Project');

        $projects = $projectRepository->findByUser($this->container->getUserContext()->getUser());

        return $this->view([
                'projects' => $projects
            ]);
    }

    public function createAction()
    {
        $em = $this->container->getDoctrine()->getManager();

        $project = new Project();
        $project->setCompany($this->companyContext->getCompany());

        $form = $this->container->getFormFactory()->create(new ProjectType(), $project);

        if ($this->isValidForm($form)) {
            $em->persist($project);
            $em->flush();

            return $this->javascript(
                $this->renderView('EntvalleyAppBundle:Project:create_success.html.twig', ['project' => $project])
            );
        }

        return $this->view([
            'form'  => $form->createView(),
            'project' => $project,
        ]);
    }
}
