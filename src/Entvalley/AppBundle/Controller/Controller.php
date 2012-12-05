<?php
namespace Entvalley\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Entvalley\AppBundle\Response\JsonResponseMessage;
use Entvalley\AppBundle\Templating\ViewTemplateResolver;
USE Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class Controller
{
    protected $templateExtension = "html.twig";

    /**
     * @var ControllerContainer
     */
    protected $container;

    /**
     * @param ControllerContainer $container
     */
    public function __construct(ControllerContainer $container)
    {
        $this->container = $container;
    }

    /**
     * Creates a Response instance that contains javascript code to be executed by the browser
     *
     * @param string  $content The Response body
     * @param integer $status  The status code
     * @param array   $headers An array of HTTP headers
     *
     * @return Response A Response instance
     */
    public function javascript($content = '', $status = 200, array $headers = array())
    {
     	return $this->createResponse($content, $status,
     		array_merge(array('Content-Type' => 'text/javascript'), $headers));
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param  string  $name       The name of the route
     * @param  array   $parameters An array of parameters
     * @param  Boolean $absolute   Whether to generate an absolute URL
     *
     * @return string The generated URL
     */
    public function url($route, array $parameters = array(), $absolute = false)
    {
        $router = $this->container->getRouter();
        if (empty($router)) {
            throw new \RuntimeException('The router service should be set to generate a URL');
        }
        return $router->generate($route, $parameters, $absolute);
    }

    /**
     * Returns an HTTP redirect Response.
     *
     * @return Response A Response instance
     */
    public function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * Returns a rendered view.
     *
     * @param string $view       The view name
     * @param array  $parameters An array of parameters to pass to the view
     *
     * @return string The renderer view
     */
    public function renderView($view, array $parameters = array())
    {
        $templating = $this->container->getTemplating();
        if (empty($templating)) {
            throw new \RuntimeException('The templating service should be set to render the view');
        }
        return $templating->render($view, $parameters);
    }

    /**
     * Renders a view.
     *
     * @param string   $view The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response A response instance
     *
     * @return Response A Response instance
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $templating = $this->container->getTemplating();
        if (empty($templating)) {
            throw new \RuntimeException('The templating service should be set to render the response');
        }
        return $templating->renderResponse($view, $parameters, $response);
    }

    /**
     * Returns the content encoded into JSON
     *
     * @param mixed $content
     * @param integer $status
     * @param array $headers
     * @return Response
     */
    public function json($content = '', $status = 200, array $headers = array())
    {
        if ($content instanceof JsonResponseMessage)
            $content = $content->__toString();
        if (!\is_scalar($content))
            $content = \json_encode ($content);

     	return $this->createResponse($content, $status,
     		array_merge(array('Content-Type' => 'application/json'), $headers));
    }

    /**
     * Creates a Response instance.
     *
     * @param string  $content The Response body
     * @param integer $status  The status code
     * @param array   $headers An array of HTTP headers
     *
     * @return Response A Response instance
     */
    public function createResponse($content = '', $status = 200, array $headers = array())
    {
        $response = new Response;
        $response->setContent($content);
        $response->setStatusCode($status);
        foreach ($headers as $name => $value) {
            $response->headers->set($name, $value);
        }

        return $response;
    }

    /**
     * Renders a view.
     *
     * @param string   $view The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response A response instance
     *
     * @return Response A Response instance
     */
    public function view(array $parameters = array(), Response $response = null, $extension = '')
    {
        return $this->render($this->resolveViewName($extension), $parameters, $response);
    }

    /**
     * Renders a view without creating response
     *
     * @param string   $view The view name
     * @param array    $parameters An array of parameters to pass to the view
     *
     * @return string rendered view
     */
    public function viewContent(array $parameters = array(), $extension = '')
    {
        return $this->renderView($this->resolveViewName($extension), $parameters);
    }

    /**
     * Binds the current request to a form and validates the given form
     *
     *
     * @param \Symfony\Component\Form\Form $form
     * @param boolean $post_only only do validation if the request method is POST
     * @return boolean|void
     */
    public function isValidForm($form, $post_only = true)
    {
        $request = $this->container->getRequest();
        if (empty($request)) {
            throw new \RuntimeException('The request service should be set to validate the form');
        }

        if (($post_only && $request->getMethod() !== 'POST') || !$post_only) {
            return;
        }

        $form->bind($request);
        return $form->isValid();
    }

    /**
     * Resolve the template name (with the right extension)
     *
     * @param $extension
     * @return string
     * @throws \RuntimeException
     */
    private function resolveViewName($extension = null)
    {
        $request = $this->container->getRequest();
        if (empty($request)) {
            throw new \RuntimeException('The request service should be set to render the view');
        }

        $extension = !empty($extension) ? $extension : $this->templateExtension;
        $view = ViewTemplateResolver::resolve($request->get('_controller'), get_called_class());
        return $view . '.' . $extension;
    }
}