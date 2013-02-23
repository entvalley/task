<?php

namespace Entvalley\AppBundle\Controller;

use Entvalley\AppBundle\Domain\CompanyContext;
use Entvalley\AppBundle\Entity\Company;
use Entvalley\AppBundle\Form\ProjectType;
use Entvalley\AppBundle\Entity\Project;
use Entvalley\AppBundle\Service\ProjectInviter;
use Entvalley\AppBundle\Service\TemplatedMailer;
use Symfony\Component\HttpFoundation\Request;
use Entvalley\AppBundle\Entity\ProjectInvitation;
use Entvalley\AppBundle\Form\ProjectInvitationType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use JMS\Serializer\SerializerInterface;
use Entvalley\AppBundle\Component\HttpFoundation\JsonResponse;
use Entvalley\AppBundle\Domain\ProjectInvitationList;

class ProjectController extends Controller
{
    private $companyContext;
    private $serializer;
    /**
     * @var \Entvalley\AppBundle\Service\TemplatedMailer
     */
    private $templatedMailer;

    public function __construct(
        ControllerContainer $container,
        SerializerInterface $serializer,
        CompanyContext $companyContext,
        TemplatedMailer $templatedMailer
    ) {
        $this->serializer = $serializer;
        $this->companyContext = $companyContext;
        $this->templatedMailer = $templatedMailer;
        parent::__construct($container);
    }

    public function navigationAction(Project $project = null)
    {
        $em = $this->container->getDoctrine()->getManager();
        $projectRepository = $em->getRepository('Entvalley\AppBundle\Entity\Project');

        $projects = $projectRepository->findByUser($this->container->getUserContext()->getUser());

        return $this->view(
            [
                'projects' => $projects,
                'current_project' => $project
            ]
        );
    }

    public function indexAction()
    {
        $em = $this->container->getDoctrine()->getManager();
        $projectRepository = $em->getRepository('EntvalleyAppBundle:Project');

        $projects = $projectRepository->findByUser($this->container->getUserContext()->getUser());

        return $this->view(
            [
                'projects' => $projects
            ]
        );
    }

    public function createAction()
    {
        $em = $this->container->getDoctrine()->getManager();

        $project = new Project();
        $project->setCompany($this->companyContext->getCompany());

        $form = $this->container->getFormFactory()->create(new ProjectType(), $project);

        if ($this->bindRequestToFormAndValidateIt($form)) {
            $em->persist($project);
            $em->flush();

            return $this->javascript(
                $this->renderView('EntvalleyAppBundle:Project:create_success.html.twig', ['project' => $project])
            );
        }

        return $this->view(
            [
                'form' => $form->createView(),
                'project' => $project,
            ]
        );
    }

    public function settingsAction(Project $project)
    {
        $invitationForm = $this->container->getFormFactory()->create(
            new ProjectInvitationType(),
            new ProjectInvitationList()
        );
        return $this->view(
            [
                'project' => $project,
                'invitation_form' => $invitationForm->createView(),
            ]
        );
    }

    public function fieldsAction(Project $project)
    {
        $invitationForm = $this->container->getFormFactory()->create(
            new ProjectInvitationType(),
            new ProjectInvitationList()
        );
        return $this->view(
            [
                'project' => $project,
                'invitation_form' => $invitationForm->createView(),
            ]
        );
    }

    public function collaboratorsAction(Project $project)
    {
        $collaboratorRepository = $this->container->getDoctrine()->getRepository(
            'EntvalleyAppBundle:ProjectCollaborator'
        );
        $collaborators = $collaboratorRepository->findByProject($project);

        $invitationRepository = $this->container->getDoctrine()->getRepository('EntvalleyAppBundle:ProjectInvitation');
        $invitations = $invitationRepository->findByProject($project);

        return [
                'collaborators' => $collaborators,
                'invitations' => $invitations,
            ];
    }

    public function inviteCollaboratorsAction(Project $project)
    {
        $invitationForm = $this->container->getFormFactory()->create(
            new ProjectInvitationType(),
            new ProjectInvitationList()
        );

        $invitationForm->bind($this->container->getRequest());
        if ($invitationForm->isValid()) {
            $invitationList = $invitationForm->getData();

            $invitations = $invitationList->getInvitations();

            $em = $this->container->getDoctrine()->getManager();
            $inviter = new ProjectInviter($em, $this->templatedMailer, $this->container->getRouter());
            $inviter->invite($project, $invitations, $this->container->getUserContext()->getUser());
            $em->flush();

            return [
                    'invitees' => $invitations,
                ];
        }
    }

    public function acceptInvitationAction(Company $company, $hash)
    {
        $em = $this->container->getDoctrine()->getManager();
        $inviter = new ProjectInviter($em, $this->templatedMailer, $this->container->getRouter());


        $invitationRepository = $this->container->getDoctrine()->getRepository('EntvalleyAppBundle:ProjectInvitation');
        /**
         * @var ProjectInvitation $invitation
         */
        $invitation = $invitationRepository->findByHash($company, $hash);
        $inviter->accept($invitation, $this->container->getUserContext()->getUser());



        //$em->flush();

        return $this->view();


        return new Response("thanks!");
        $userRepository = $this->container->getDoctrine()->getRepository('EntvalleyUserBundle:User');
        $user = $userRepository->find(7);
        $to = $userRepository->find(1);


        $this->container->getAclManager()->grant($user, MaskBuilder::MASK_VIEW, $to);
    }
}
