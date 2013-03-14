<?php

namespace Entvalley\AppBundle\Controller;

use Entvalley\AppBundle\Service\StatsService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Entvalley\AppBundle\Domain\UserContext;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Entvalley\AppBundle\Response\JsonResponseMessage;
use Symfony\Component\HttpFoundation\Request;
use Entvalley\AppBundle\Service\AclManager;

/**
 * Container that holds common dependencies for controllers
 */
class ControllerContainer
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    protected $templating;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    /**
     * @var \Symfony\Bridge\Doctrine\RegistryInterface
     */
    protected $doctrine;

    /**
     * @var \Symfony\Component\Form\FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var \Entvalley\AppBundle\Domain\UserContext
     */
    protected $userContext;

    /**
     * @var AclManager
     */
    protected $aclManager;

    /**
     * @var \Entvalley\AppBundle\Service\StatsService
     */
    protected $stats;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\Routing\RouterInterface $router
     * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templating
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param \Symfony\Bridge\Doctrine\RegistryInterface $doctrine
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     * @param \Entvalley\AppBundle\Domain\UserContext $userContext
     * @param AclManager $aclManager
     * @param StatsService $stats
     */
    public function __construct(Request $request, RouterInterface $router, EngineInterface $templating, SessionInterface $session, RegistryInterface $doctrine, FormFactoryInterface $formFactory, UserContext $userContext, AclManager $aclManager, StatsService $stats)
    {
        $this->request = $request;
        $this->router = $router;
        $this->templating = $templating;
        $this->session = $session;
        $this->doctrine = $doctrine;
        $this->formFactory = $formFactory;
        $this->userContext = $userContext;
        $this->aclManager = $aclManager;
        $this->stats = $stats;
    }

    /**
     * @return \Symfony\Bridge\Doctrine\RegistryInterface
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * @return \Symfony\Component\Form\FormFactoryInterface
     */
    public function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \Symfony\Component\Routing\RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    public function getTemplating()
    {
        return $this->templating;
    }

    /**
     * @return \Entvalley\AppBundle\Domain\UserContext
     */
    public function getUserContext()
    {
        return $this->userContext;
    }

    /**
     * @return \Entvalley\AppBundle\Service\AclManager
     */
    public function getAclManager()
    {
        return $this->aclManager;
    }

    /**
     * @return \Entvalley\AppBundle\Service\StatsService
     */
    public function getStats()
    {
        return $this->stats;
    }
}
